<#
.SYNOPSIS
    Phase 6 — Idempotent Amazon RDS MySQL (db.t3.micro, Single-AZ, 20 GiB gp3, Encrypted, Private) Provisioning
.DESCRIPTION
    Validates AWS Account ID 415805875879 and provisions exactly one Amazon RDS MySQL instance:
    - Identifier: akmoney-prod-db
    - Class: db.t3.micro
    - Single-AZ, 20 GiB gp3 encrypted, PubliclyAccessible = false, DeletionProtection = true
    - 7-day automated backup retention, no storage autoscaling
#>
[CmdletBinding()]
param(
    [string]$Profile = "",
    [string]$Region = "us-east-1",
    [string]$ExpectedAccountId = "415805875879",
    [string]$MasterUsername = "akmoney_admin"
)

$ErrorActionPreference = "Stop"
$AwsArgs = @("--region", $Region)
if ($Profile) { $AwsArgs += @("--profile", $Profile) }

$IdentityJson = aws sts get-caller-identity @AwsArgs | ConvertFrom-Json
if ($IdentityJson.Account -ne $ExpectedAccountId) {
    throw "CRITICAL: Authenticated AWS Account ($($IdentityJson.Account)) does NOT match $ExpectedAccountId."
}

$DbIdentifier = "akmoney-prod-db"
$ExistingDb = aws rds describe-db-instances --db-instance-identifier $DbIdentifier @AwsArgs 2>$null
if ($LASTEXITCODE -eq 0 -and $ExistingDb) {
    $DbObj = ($ExistingDb | ConvertFrom-Json).DBInstances[0]
    Write-Host "RDS Instance $DbIdentifier already exists (Status: $($DbObj.DBInstanceStatus))." -ForegroundColor Green
    return
}

$VpcId = aws ec2 describe-vpcs --filters "Name=is-default,Values=true" --query "Vpcs[0].VpcId" --output text @AwsArgs
$RdsSgId = aws ec2 describe-security-groups --filters "Name=vpc-id,Values=$VpcId" "Name=group-name,Values=akmoney-rds-sg" --query "SecurityGroups[0].GroupId" --output text @AwsArgs
if (-not $RdsSgId -or $RdsSgId -eq "None") {
    throw "RDS Security Group 'akmoney-rds-sg' not found. Run provision-networking.ps1 first."
}

# Prompt securely for Master DB password (never logged or passed in plain text script files)
$SecureMasterPass = Read-Host -Prompt "Enter RDS Master Password for '$MasterUsername' (min 16 chars)" -AsSecureString
$Bstr = [System.Runtime.InteropServices.Marshal]::SecureStringToBSTR($SecureMasterPass)
$PlainMasterPass = [System.Runtime.InteropServices.Marshal]::PtrToStringAuto($Bstr)

try {
    Write-Host "Provisioning Amazon RDS MySQL db.t3.micro (Single-AZ, 20 GiB gp3, Encrypted, Private)..." -ForegroundColor Yellow
    aws rds create-db-instance `
        --db-instance-identifier $DbIdentifier `
        --db-instance-class "db.t3.micro" `
        --engine "mysql" `
        --engine-version "8.0" `
        --master-username $MasterUsername `
        --master-user-password $PlainMasterPass `
        --db-name "akmoney" `
        --allocated-storage 20 `
        --storage-type "gp3" `
        --storage-encrypted `
        --no-multi-az `
        --no-publicly-accessible `
        --vpc-security-group-ids $RdsSgId `
        --db-subnet-group-name "akmoney-rds-subnet-group" `
        --backup-retention-period 7 `
        --port 3306 `
        --deletion-protection `
        --copy-tags-to-snapshot `
        --auto-minor-version-upgrade `
        --tags Key=Name,Value=akmoney-prod-db Key=Project,Value=AkMoney Key=Environment,Value=Production Key=ManagedBy,Value=AkMoney-Deployment `
        @AwsArgs | Out-Null

    Write-Host "Waiting for RDS instance '$DbIdentifier' to become available..." -ForegroundColor Cyan
    aws rds wait db-instance-available --db-instance-identifier $DbIdentifier @AwsArgs

    $Endpoint = aws rds describe-db-instances --db-instance-identifier $DbIdentifier --query "DBInstances[0].Endpoint.Address" --output text @AwsArgs
    Write-Host "RDS MySQL Instance Available at Private Endpoint: $Endpoint" -ForegroundColor Green

    # Store RDS endpoint in SSM Parameter Store
    aws ssm put-parameter --name "/akmoney/prod/backend/db-host" --type "String" --value $Endpoint --overwrite @AwsArgs | Out-Null
    aws ssm put-parameter --name "/akmoney/prod/backend/db-port" --type "String" --value "3306" --overwrite @AwsArgs | Out-Null
    aws ssm put-parameter --name "/akmoney/prod/backend/db-name" --type "String" --value "akmoney" --overwrite @AwsArgs | Out-Null
} finally {
    [System.Runtime.InteropServices.Marshal]::ZeroFreeBSTR($Bstr)
    $PlainMasterPass = $null
}
