<#
.SYNOPSIS
    Phase 6 — Idempotent Amazon RDS MySQL (db.t3.micro, Single-AZ, 20 GiB gp3, Encrypted, Private) Provisioning
#>
[CmdletBinding()]
param(
    [string]$Profile = "akmoney",
    [string]$Region = "eu-north-1",
    [string]$ExpectedAccountId = "415805875879",
    [string]$MasterUsername = "akmoney_admin"
)

$ErrorActionPreference = "Continue"
$AwsArgs = @("--region", $Region)
if ($Profile) { $AwsArgs += @("--profile", $Profile) }

$IdentityJson = aws sts get-caller-identity @AwsArgs | ConvertFrom-Json
if (-not $IdentityJson -or $IdentityJson.Account -ne $ExpectedAccountId) {
    throw "CRITICAL: Authenticated AWS Account ($($IdentityJson.Account)) does NOT match $ExpectedAccountId."
}

$DbIdentifier = "akmoney-prod-db"
$ExistingDb = aws rds describe-db-instances --db-instance-identifier $DbIdentifier @AwsArgs 2>&1
if ($LASTEXITCODE -eq 0 -and $ExistingDb) {
    $DbObj = ($ExistingDb | ConvertFrom-Json).DBInstances[0]
    Write-Host "RDS Instance $DbIdentifier already exists (Status: $($DbObj.DBInstanceStatus))." -ForegroundColor Green
    return
}

$VpcId = (aws ec2 describe-vpcs --filters "Name=is-default,Values=true" --query "Vpcs[0].VpcId" --output text @AwsArgs).Trim()
$RdsSgId = (aws ec2 describe-security-groups --filters "Name=vpc-id,Values=$VpcId" "Name=group-name,Values=akmoney-rds-sg" --query "SecurityGroups[0].GroupId" --output text @AwsArgs).Trim()
if (-not $RdsSgId -or $RdsSgId -eq "None") {
    throw "RDS Security Group 'akmoney-rds-sg' not found. Run provision-networking.ps1 first."
}

# Prompt securely for Master DB password (or read from SSM if already initialized)
$ExistingPass = aws ssm get-parameter --name "/akmoney/prod/backend/db-password" --with-decryption --query "Parameter.Value" --output text @AwsArgs 2>&1
if ($LASTEXITCODE -eq 0 -and $ExistingPass -and $ExistingPass -notmatch "ParameterNotFound") {
    $PlainMasterPass = $ExistingPass.Trim()
} else {
    $SecureMasterPass = Read-Host -Prompt "Enter RDS Master Password for '$MasterUsername' (min 16 chars)" -AsSecureString
    $Bstr = [System.Runtime.InteropServices.Marshal]::SecureStringToBSTR($SecureMasterPass)
    $PlainMasterPass = [System.Runtime.InteropServices.Marshal]::PtrToStringAuto($Bstr)
    [System.Runtime.InteropServices.Marshal]::ZeroFreeBSTR($Bstr)
}

try {
    Write-Host "Provisioning Amazon RDS MySQL db.t3.micro (Single-AZ, 20 GiB gp3, Encrypted, Private) in $Region..." -ForegroundColor Yellow
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

    if ($LASTEXITCODE -ne 0) {
        throw "Failed to create RDS instance $DbIdentifier"
    }

    Write-Host "RDS instance '$DbIdentifier' creation initiated." -ForegroundColor Green
} finally {
    $PlainMasterPass = $null
}
