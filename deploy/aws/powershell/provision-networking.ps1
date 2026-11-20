<#
.SYNOPSIS
    Phase 3 — Idempotent AWS Networking, Subnet Group, and Security Group Provisioning
.DESCRIPTION
    Validates AWS Account ID 415805875879, inspects the default VPC & subnets across >= 2 AZs,
    creates RDS DB subnet group (akmoney-rds-subnet-group), EC2 security group (akmoney-ec2-sg: 80/443 only),
    and RDS security group (akmoney-rds-sg: 3306 from akmoney-ec2-sg only). Zero NAT Gateways created.
#>
[CmdletBinding()]
param(
    [string]$Profile = "",
    [string]$Region = "us-east-1",
    [string]$ExpectedAccountId = "415805875879"
)

$ErrorActionPreference = "Stop"
$AwsArgs = @("--region", $Region)
if ($Profile) { $AwsArgs += @("--profile", $Profile) }

$IdentityJson = aws sts get-caller-identity @AwsArgs | ConvertFrom-Json
if ($IdentityJson.Account -ne $ExpectedAccountId) {
    throw "CRITICAL: Authenticated AWS Account ($($IdentityJson.Account)) does NOT match $ExpectedAccountId."
}

# 1. Discover Default VPC
$VpcId = aws ec2 describe-vpcs --filters "Name=is-default,Values=true" --query "Vpcs[0].VpcId" --output text @AwsArgs
if (-not $VpcId -or $VpcId -eq "None") {
    throw "No default VPC found in $Region. Please specify or create a VPC before proceeding."
}
Write-Host "Using VPC: $VpcId" -ForegroundColor Green

# Ensure DNS hostnames and DNS support are enabled
aws ec2 modify-vpc-attribute --vpc-id $VpcId --enable-dns-support "{\"Value\":true}" @AwsArgs
aws ec2 modify-vpc-attribute --vpc-id $VpcId --enable-dns-hostnames "{\"Value\":true}" @AwsArgs

# 2. Discover Subnets across at least 2 Availability Zones
$Subnets = (aws ec2 describe-subnets --filters "Name=vpc-id,Values=$VpcId" --query "Subnets[].{SubnetId:SubnetId,AZ:AvailabilityZone}" @AwsArgs | ConvertFrom-Json)
if ($Subnets.Count -lt 2) {
    throw "RDS Subnet Group requires subnets in at least 2 Availability Zones. Found: $($Subnets.Count)"
}
$SubnetIds = $Subnets | Select-Object -ExpandProperty SubnetId
Write-Host "Discovered Subnets across AZs: $($SubnetIds -join ', ')" -ForegroundColor Green

# 3. Create or Verify EC2 Security Group (akmoney-ec2-sg)
$Ec2SgId = aws ec2 describe-security-groups --filters "Name=vpc-id,Values=$VpcId" "Name=group-name,Values=akmoney-ec2-sg" --query "SecurityGroups[0].GroupId" --output text @AwsArgs
if (-not $Ec2SgId -or $Ec2SgId -eq "None") {
    Write-Host "Creating Security Group: akmoney-ec2-sg..." -ForegroundColor Yellow
    $Ec2SgId = aws ec2 create-security-group `
        --group-name "akmoney-ec2-sg" `
        --description "AkMoney Production EC2 SG - HTTP 80 and HTTPS 443 only (SSM managed)" `
        --vpc-id $VpcId `
        --query "GroupId" --output text @AwsArgs
    aws ec2 authorize-security-group-ingress --group-id $Ec2SgId --protocol tcp --port 80 --cidr 0.0.0.0/0 @AwsArgs | Out-Null
    aws ec2 authorize-security-group-ingress --group-id $Ec2SgId --protocol tcp --port 443 --cidr 0.0.0.0/0 @AwsArgs | Out-Null
    aws ec2 create-tags --resources $Ec2SgId --tags Key=Name,Value=akmoney-ec2-sg Key=Project,Value=AkMoney Key=Environment,Value=Production @AwsArgs
}
Write-Host "EC2 Security Group: $Ec2SgId (Ports 80, 443 open; Port 22 closed)" -ForegroundColor Green

# 4. Create or Verify RDS Security Group (akmoney-rds-sg)
$RdsSgId = aws ec2 describe-security-groups --filters "Name=vpc-id,Values=$VpcId" "Name=group-name,Values=akmoney-rds-sg" --query "SecurityGroups[0].GroupId" --output text @AwsArgs
if (-not $RdsSgId -or $RdsSgId -eq "None") {
    Write-Host "Creating Security Group: akmoney-rds-sg..." -ForegroundColor Yellow
    $RdsSgId = aws ec2 create-security-group `
        --group-name "akmoney-rds-sg" `
        --description "AkMoney Production RDS SG - Port 3306 from akmoney-ec2-sg only" `
        --vpc-id $VpcId `
        --query "GroupId" --output text @AwsArgs
    aws ec2 authorize-security-group-ingress --group-id $RdsSgId --protocol tcp --port 3306 --source-group $Ec2SgId @AwsArgs | Out-Null
    aws ec2 create-tags --resources $RdsSgId --tags Key=Name,Value=akmoney-rds-sg Key=Project,Value=AkMoney Key=Environment,Value=Production @AwsArgs
}
Write-Host "RDS Security Group: $RdsSgId (Port 3306 from $Ec2SgId only)" -ForegroundColor Green

# 5. Create or Verify RDS DB Subnet Group
$SubnetGroupName = "akmoney-rds-subnet-group"
aws rds describe-db-subnet-groups --db-subnet-group-name $SubnetGroupName @AwsArgs 2>$null | Out-Null
if ($LASTEXITCODE -ne 0) {
    Write-Host "Creating RDS DB Subnet Group: $SubnetGroupName..." -ForegroundColor Yellow
    aws rds create-db-subnet-group `
        --db-subnet-group-name $SubnetGroupName `
        --db-subnet-group-description "AkMoney RDS Private VPC Subnet Group" `
        --subnet-ids @SubnetIds `
        --tags Key=Project,Value=AkMoney Key=Environment,Value=Production @AwsArgs | Out-Null
}
Write-Host "RDS Subnet Group ready: $SubnetGroupName" -ForegroundColor Green
