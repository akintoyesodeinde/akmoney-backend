<#
.SYNOPSIS
    Phase 4 & Phase 15 — Idempotent AWS IAM OIDC, Least-Privilege Roles, Instance Profile, and SSM Documents Provisioning
.DESCRIPTION
    Validates AWS Account ID 415805875879 and provisions:
    - GitHub Actions OIDC Identity Provider (token.actions.githubusercontent.com)
    - akmoney-backend-github-deploy-role
    - akmoney-frontend-github-deploy-role
    - akmoney-ec2-role & akmoney-ec2-instance-profile
    - Custom restricted SSM documents (AkMoney-Deploy-Backend, AkMoney-Deploy-Frontend, AkMoney-Rollback-Backend, AkMoney-Rollback-Frontend)
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

Write-Host "=== Verifying AWS Identity ===" -ForegroundColor Cyan
$IdentityJson = aws sts get-caller-identity @AwsArgs | ConvertFrom-Json
if ($IdentityJson.Account -ne $ExpectedAccountId) {
    throw "CRITICAL: Authenticated AWS Account ($($IdentityJson.Account)) does NOT match required Account ID $ExpectedAccountId. Stopping immediately."
}
Write-Host "Authenticated as $($IdentityJson.Arn) in Account $ExpectedAccountId ($Region)" -ForegroundColor Green

$IamDir = Join-Path $PSScriptRoot "..\iam"
$SsmDir = Join-Path $PSScriptRoot "..\ssm"

# 1. OIDC Identity Provider
$OidcUrl = "https://token.actions.githubusercontent.com"
$OidcArn = "arn:aws:iam::${ExpectedAccountId}:oidc-provider/token.actions.githubusercontent.com"
$ExistingProviders = (aws iam list-open-id-connect-providers @AwsArgs | ConvertFrom-Json).OpenIDConnectProviderList.Arn
if ($ExistingProviders -notcontains $OidcArn) {
    Write-Host "Creating GitHub Actions IAM OIDC Provider..." -ForegroundColor Yellow
    aws iam create-open-id-connect-provider `
        --url $OidcUrl `
        --client-id-list "sts.amazonaws.com" `
        --thumbprint-list "6938fd4d98bab03faadb97b34396831e3780aea1" "1c58a3a8518e8759bf075b76b750d4f2df264fcd" `
        @AwsArgs | Out-Null
} else {
    Write-Host "GitHub OIDC Provider already exists: $OidcArn" -ForegroundColor Green
}

# 2. Backend GitHub Actions IAM Role
$BackendRole = "akmoney-backend-github-deploy-role"
$BackendTrust = Join-Path $IamDir "backend-oidc-trust-policy.json"
$BackendPolicy = Join-Path $IamDir "backend-deploy-policy.json"

aws iam get-role --role-name $BackendRole @AwsArgs 2>$null | Out-Null
if ($LASTEXITCODE -ne 0) {
    Write-Host "Creating IAM Role: $BackendRole..." -ForegroundColor Yellow
    aws iam create-role --role-name $BackendRole --assume-role-policy-document "file://$BackendTrust" @AwsArgs | Out-Null
} else {
    aws iam update-assume-role-policy --role-name $BackendRole --policy-document "file://$BackendTrust" @AwsArgs | Out-Null
}
aws iam put-role-policy --role-name $BackendRole --policy-name "AkMoneyBackendDeployLeastPrivilege" --policy-document "file://$BackendPolicy" @AwsArgs

# 3. Frontend GitHub Actions IAM Role
$FrontendRole = "akmoney-frontend-github-deploy-role"
$FrontendTrust = Join-Path $IamDir "frontend-oidc-trust-policy.json"
$FrontendPolicy = Join-Path $IamDir "frontend-deploy-policy.json"

aws iam get-role --role-name $FrontendRole @AwsArgs 2>$null | Out-Null
if ($LASTEXITCODE -ne 0) {
    Write-Host "Creating IAM Role: $FrontendRole..." -ForegroundColor Yellow
    aws iam create-role --role-name $FrontendRole --assume-role-policy-document "file://$FrontendTrust" @AwsArgs | Out-Null
} else {
    aws iam update-assume-role-policy --role-name $FrontendRole --policy-document "file://$FrontendTrust" @AwsArgs | Out-Null
}
aws iam put-role-policy --role-name $FrontendRole --policy-name "AkMoneyFrontendDeployLeastPrivilege" --policy-document "file://$FrontendPolicy" @AwsArgs

# 4. EC2 IAM Role & Instance Profile
$Ec2Role = "akmoney-ec2-role"
$Ec2Profile = "akmoney-ec2-instance-profile"
$Ec2Trust = Join-Path $IamDir "ec2-trust-policy.json"
$Ec2Policy = Join-Path $IamDir "ec2-instance-role-policy.json"

aws iam get-role --role-name $Ec2Role @AwsArgs 2>$null | Out-Null
if ($LASTEXITCODE -ne 0) {
    Write-Host "Creating EC2 IAM Role: $Ec2Role..." -ForegroundColor Yellow
    aws iam create-role --role-name $Ec2Role --assume-role-policy-document "file://$Ec2Trust" @AwsArgs | Out-Null
}
aws iam attach-role-policy --role-name $Ec2Role --policy-arn "arn:aws:iam::aws:policy/AmazonSSMManagedInstanceCore" @AwsArgs
aws iam put-role-policy --role-name $Ec2Role --policy-name "AkMoneyEC2RuntimeSSMAccess" --policy-document "file://$Ec2Policy" @AwsArgs

aws iam get-instance-profile --instance-profile-name $Ec2Profile @AwsArgs 2>$null | Out-Null
if ($LASTEXITCODE -ne 0) {
    Write-Host "Creating EC2 Instance Profile: $Ec2Profile..." -ForegroundColor Yellow
    aws iam create-instance-profile --instance-profile-name $Ec2Profile @AwsArgs | Out-Null
    aws iam add-role-to-instance-profile --instance-profile-name $Ec2Profile --role-name $Ec2Role @AwsArgs | Out-Null
}

# 5. Custom Restricted SSM Documents
$SsmDocs = @(
    "AkMoney-Deploy-Backend",
    "AkMoney-Deploy-Frontend",
    "AkMoney-Rollback-Backend",
    "AkMoney-Rollback-Frontend"
)
foreach ($DocName in $SsmDocs) {
    $DocPath = Join-Path $SsmDir "$DocName.json"
    aws ssm describe-document --name $DocName @AwsArgs 2>$null | Out-Null
    if ($LASTEXITCODE -ne 0) {
        Write-Host "Creating SSM Document: $DocName..." -ForegroundColor Yellow
        aws ssm create-document --name $DocName --document-type "Command" --document-format "JSON" --content "file://$DocPath" @AwsArgs | Out-Null
    } else {
        Write-Host "SSM Document already exists: $DocName" -ForegroundColor Green
    }
}

Write-Host "=== IAM & SSM Document Provisioning Complete ===" -ForegroundColor Green
