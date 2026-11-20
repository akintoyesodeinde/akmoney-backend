<#
.SYNOPSIS
    Phase 15, 18 & 19 — End-to-End AWS Infrastructure, SSM Registration, and Application Health Verification
#>
[CmdletBinding()]
param(
    [string]$Profile = "akmoney",
    [string]$Region = "eu-north-1",
    [string]$ExpectedAccountId = "415805875879"
)

$ErrorActionPreference = "Continue"
$AwsArgs = @("--region", $Region)
if ($Profile) { $AwsArgs += @("--profile", $Profile) }

$IdentityJson = aws sts get-caller-identity @AwsArgs | ConvertFrom-Json
if (-not $IdentityJson -or $IdentityJson.Account -ne $ExpectedAccountId) {
    throw "CRITICAL: Authenticated AWS Account ($($IdentityJson.Account)) does NOT match $ExpectedAccountId."
}

Write-Host "=== 1. Checking AkMoney EC2 Instance ($Region) ===" -ForegroundColor Cyan
$Instance = (aws ec2 describe-instances `
    --filters "Name=tag:Name,Values=akmoney-prod-server" "Name=tag:Project,Values=AkMoney" "Name=instance-state-name,Values=running" `
    --query "Reservations[0].Instances[0].{Id:InstanceId,Type:InstanceType,PublicIp:PublicIpAddress,IMDSv2:MetadataOptions.HttpTokens}" `
    @AwsArgs | ConvertFrom-Json)

if (-not $Instance -or -not $Instance.Id) {
    throw "No running AkMoney EC2 instance found."
}
Write-Host "EC2 Instance: $($Instance.Id) | Type: $($Instance.Type) | Public IP: $($Instance.PublicIp) | IMDSv2: $($Instance.IMDSv2)" -ForegroundColor Green

Write-Host "=== 2. Checking AWS Systems Manager (SSM) Managed Instance Status ===" -ForegroundColor Cyan
$SsmStatus = (aws ssm describe-instance-information `
    --filters "Key=InstanceIds,Values=$($Instance.Id)" `
    --query "InstanceInformationList[0].PingStatus" --output text @AwsArgs).Trim()
Write-Host "SSM PingStatus: $SsmStatus" -ForegroundColor Green

Write-Host "=== 3. Checking Amazon RDS Instance ===" -ForegroundColor Cyan
$Rds = (aws rds describe-db-instances `
    --db-instance-identifier "akmoney-prod-db" `
    --query "DBInstances[0].{Status:DBInstanceStatus,Class:DBInstanceClass,Public:PubliclyAccessible,Encrypted:StorageEncrypted,MultiAZ:MultiAZ,Endpoint:Endpoint.Address}" `
    @AwsArgs | ConvertFrom-Json)
Write-Host "RDS Status: $($Rds.Status) | Class: $($Rds.Class) | PubliclyAccessible: $($Rds.Public) | Encrypted: $($Rds.Encrypted) | Endpoint: $($Rds.Endpoint)" -ForegroundColor Green

Write-Host "=== 4. Checking Application HTTP Health Endpoints ===" -ForegroundColor Cyan
if ($Instance.PublicIp) {
    try {
        $FrontendHealth = Invoke-WebRequest -Uri "http://$($Instance.PublicIp)/health" -UseBasicParsing -TimeoutSec 10
        $BackendHealth = Invoke-RestMethod -Uri "http://$($Instance.PublicIp)/api/health" -TimeoutSec 10
        Write-Host "Frontend /health: $($FrontendHealth.StatusCode) ($($FrontendHealth.Content))" -ForegroundColor Green
        Write-Host "Backend /api/health: status=$($BackendHealth.status), database=$($BackendHealth.database)" -ForegroundColor Green
    } catch {
        Write-Host "HTTP health check pending container startup: $($_.Exception.Message)" -ForegroundColor Yellow
    }
}
