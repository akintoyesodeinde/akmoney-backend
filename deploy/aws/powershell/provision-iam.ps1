<#
.SYNOPSIS
    Phase 4 & Phase 15 — Idempotent AWS IAM OIDC Roles, Instance Profile, and SSM Documents Provisioning
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

Write-Host "=== Verifying AWS Identity ===" -ForegroundColor Cyan
$IdentityJson = aws sts get-caller-identity @AwsArgs | ConvertFrom-Json
if (-not $IdentityJson -or $IdentityJson.Account -ne $ExpectedAccountId) {
    throw "CRITICAL: Authenticated AWS Account ($($IdentityJson.Account)) does NOT match required Account ID $ExpectedAccountId. Stopping immediately."
}
Write-Host "Authenticated as $($IdentityJson.Arn) in Account $ExpectedAccountId ($Region)" -ForegroundColor Green

$IamDir = Join-Path $PSScriptRoot "..\iam"
$SsmDir = Join-Path $PSScriptRoot "..\ssm"

# 1. Verify existing GitHub OIDC Identity Provider
$OidcArn = "arn:aws:iam::${ExpectedAccountId}:oidc-provider/token.actions.githubusercontent.com"
Write-Host "Using existing Account GitHub OIDC Provider: $OidcArn" -ForegroundColor Green

# 2. Backend GitHub Actions IAM Role
$BackendRole = "akmoney-backend-github-deploy-role"
$BackendTrust = (Resolve-Path (Join-Path $IamDir "backend-oidc-trust-policy.json")).Path
$BackendPolicy = (Resolve-Path (Join-Path $IamDir "backend-deploy-policy.json")).Path

$null = aws iam get-role --role-name $BackendRole @AwsArgs 2>&1
if ($LASTEXITCODE -ne 0) {
    Write-Host "Creating IAM Role: $BackendRole..." -ForegroundColor Yellow
    aws iam create-role --role-name $BackendRole --assume-role-policy-document "file://$BackendTrust" @AwsArgs | Out-Null
    if ($LASTEXITCODE -ne 0) { throw "Failed to create role $BackendRole" }
} else {
    Write-Host "Updating trust policy for existing IAM Role: $BackendRole..." -ForegroundColor Green
    aws iam update-assume-role-policy --role-name $BackendRole --policy-document "file://$BackendTrust" @AwsArgs | Out-Null
}
aws iam put-role-policy --role-name $BackendRole --policy-name "AkMoneyBackendDeployLeastPrivilege" --policy-document "file://$BackendPolicy" @AwsArgs
if ($LASTEXITCODE -ne 0) { throw "Failed to attach inline policy to $BackendRole" }
Write-Host "Configured Role: arn:aws:iam::${ExpectedAccountId}:role/$BackendRole" -ForegroundColor Green

# 3. Frontend GitHub Actions IAM Role
$FrontendRole = "akmoney-frontend-github-deploy-role"
$FrontendTrust = (Resolve-Path (Join-Path $IamDir "frontend-oidc-trust-policy.json")).Path
$FrontendPolicy = (Resolve-Path (Join-Path $IamDir "frontend-deploy-policy.json")).Path

$null = aws iam get-role --role-name $FrontendRole @AwsArgs 2>&1
if ($LASTEXITCODE -ne 0) {
    Write-Host "Creating IAM Role: $FrontendRole..." -ForegroundColor Yellow
    aws iam create-role --role-name $FrontendRole --assume-role-policy-document "file://$FrontendTrust" @AwsArgs | Out-Null
    if ($LASTEXITCODE -ne 0) { throw "Failed to create role $FrontendRole" }
} else {
    Write-Host "Updating trust policy for existing IAM Role: $FrontendRole..." -ForegroundColor Green
    aws iam update-assume-role-policy --role-name $FrontendRole --policy-document "file://$FrontendTrust" @AwsArgs | Out-Null
}
aws iam put-role-policy --role-name $FrontendRole --policy-name "AkMoneyFrontendDeployLeastPrivilege" --policy-document "file://$FrontendPolicy" @AwsArgs
if ($LASTEXITCODE -ne 0) { throw "Failed to attach inline policy to $FrontendRole" }
Write-Host "Configured Role: arn:aws:iam::${ExpectedAccountId}:role/$FrontendRole" -ForegroundColor Green

# 4. EC2 IAM Role & Instance Profile
$Ec2Role = "akmoney-ec2-role"
$Ec2Profile = "akmoney-ec2-instance-profile"
$Ec2Trust = (Resolve-Path (Join-Path $IamDir "ec2-trust-policy.json")).Path
$Ec2Policy = (Resolve-Path (Join-Path $IamDir "ec2-instance-role-policy.json")).Path

$null = aws iam get-role --role-name $Ec2Role @AwsArgs 2>&1
if ($LASTEXITCODE -ne 0) {
    Write-Host "Creating EC2 IAM Role: $Ec2Role..." -ForegroundColor Yellow
    aws iam create-role --role-name $Ec2Role --assume-role-policy-document "file://$Ec2Trust" @AwsArgs | Out-Null
    if ($LASTEXITCODE -ne 0) { throw "Failed to create role $Ec2Role" }
}
aws iam attach-role-policy --role-name $Ec2Role --policy-arn "arn:aws:iam::aws:policy/AmazonSSMManagedInstanceCore" @AwsArgs
aws iam put-role-policy --role-name $Ec2Role --policy-name "AkMoneyEC2RuntimeSSMAccess" --policy-document "file://$Ec2Policy" @AwsArgs
if ($LASTEXITCODE -ne 0) { throw "Failed to attach inline policy to $Ec2Role" }
Write-Host "Configured EC2 Role: arn:aws:iam::${ExpectedAccountId}:role/$Ec2Role" -ForegroundColor Green

$null = aws iam get-instance-profile --instance-profile-name $Ec2Profile @AwsArgs 2>&1
if ($LASTEXITCODE -ne 0) {
    Write-Host "Creating EC2 Instance Profile: $Ec2Profile..." -ForegroundColor Yellow
    aws iam create-instance-profile --instance-profile-name $Ec2Profile @AwsArgs | Out-Null
    aws iam add-role-to-instance-profile --instance-profile-name $Ec2Profile --role-name $Ec2Role @AwsArgs | Out-Null
} else {
    Write-Host "EC2 Instance Profile already exists: $Ec2Profile" -ForegroundColor Green
}

# 5. Custom Restricted SSM Documents
$SsmDocs = @(
    "AkMoney-Deploy-Backend",
    "AkMoney-Deploy-Frontend",
    "AkMoney-Rollback-Backend",
    "AkMoney-Rollback-Frontend"
)
foreach ($DocName in $SsmDocs) {
    $DocPath = (Resolve-Path (Join-Path $SsmDir "$DocName.json")).Path
    $null = aws ssm describe-document --name $DocName @AwsArgs 2>&1
    if ($LASTEXITCODE -ne 0) {
        Write-Host "Creating SSM Document: $DocName..." -ForegroundColor Yellow
        aws ssm create-document --name $DocName --document-type "Command" --document-format "JSON" --content "file://$DocPath" @AwsArgs | Out-Null
        if ($LASTEXITCODE -ne 0) { throw "Failed to create SSM Document $DocName" }
    } else {
        Write-Host "SSM Document already exists: $DocName" -ForegroundColor Green
    }
}

Write-Host "=== IAM & SSM Document Provisioning Complete ===" -ForegroundColor Green
