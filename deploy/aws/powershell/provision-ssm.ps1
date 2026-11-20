<#
.SYNOPSIS
    Phase 5 — Secure AWS SSM Parameter Store Hierarchy Provisioning
.DESCRIPTION
    Validates AWS Account ID 415805875879 and securely prompts via Read-Host -AsSecureString
    for sensitive values so secrets never appear in terminal logs, command history, or source control.
#>
[CmdletBinding()]
param(
    [string]$Profile = "",
    [string]$Region = "us-east-1",
    [string]$ExpectedAccountId = "415805875879",
    [string]$DockerHubUsername = "akintoyesodeinde",
    [string]$AppUrl = "http://localhost"
)

$ErrorActionPreference = "Stop"
$AwsArgs = @("--region", $Region)
if ($Profile) { $AwsArgs += @("--profile", $Profile) }

$IdentityJson = aws sts get-caller-identity @AwsArgs | ConvertFrom-Json
if ($IdentityJson.Account -ne $ExpectedAccountId) {
    throw "CRITICAL: Authenticated AWS Account ($($IdentityJson.Account)) does NOT match $ExpectedAccountId."
}

function Set-SecureSsmParam {
    param([string]$Name, [string]$PromptText)
    $Sec = Read-Host -Prompt $PromptText -AsSecureString
    $Bstr = [System.Runtime.InteropServices.Marshal]::SecureStringToBSTR($Sec)
    $Plain = [System.Runtime.InteropServices.Marshal]::PtrToStringAuto($Bstr)
    try {
        if (-not [string]::IsNullOrWhiteSpace($Plain)) {
            aws ssm put-parameter --name $Name --type "SecureString" --tier "Standard" --value $Plain --overwrite @AwsArgs | Out-Null
            Write-Host "Stored SecureString parameter: $Name" -ForegroundColor Green
        }
    } finally {
        [System.Runtime.InteropServices.Marshal]::ZeroFreeBSTR($Bstr)
        $Plain = $null
    }
}

function Set-StringSsmParam {
    param([string]$Name, [string]$Value)
    aws ssm put-parameter --name $Name --type "String" --tier "Standard" --value $Value --overwrite @AwsArgs | Out-Null
    Write-Host "Stored String parameter: $Name" -ForegroundColor Green
}

Write-Host "=== Populating Non-Sensitive String Parameters ===" -ForegroundColor Cyan
Set-StringSsmParam -Name "/akmoney/prod/backend/app-url" -Value $AppUrl
Set-StringSsmParam -Name "/akmoney/prod/backend/app-env" -Value "production"
Set-StringSsmParam -Name "/akmoney/prod/backend/db-port" -Value "3306"
Set-StringSsmParam -Name "/akmoney/prod/backend/db-name" -Value "akmoney"
Set-StringSsmParam -Name "/akmoney/prod/backend/db-username" -Value "akmoney_app"
Set-StringSsmParam -Name "/akmoney/ci/backend/dockerhub-username" -Value $DockerHubUsername
Set-StringSsmParam -Name "/akmoney/ci/frontend/dockerhub-username" -Value $DockerHubUsername
Set-StringSsmParam -Name "/akmoney/prod/registry/dockerhub-username" -Value $DockerHubUsername

Write-Host "=== Secure Secret Entry (Values are masked and never logged) ===" -ForegroundColor Cyan
Set-SecureSsmParam -Name "/akmoney/prod/backend/app-key" -PromptText "Enter Laravel APP_KEY (e.g. base64:...)"
Set-SecureSsmParam -Name "/akmoney/prod/backend/db-password" -PromptText "Enter Application DB Password for 'akmoney_app'"
Set-SecureSsmParam -Name "/akmoney/prod/backend/payment-api-key" -PromptText "Enter Payment Provider API Key"
Set-SecureSsmParam -Name "/akmoney/prod/backend/webhook-secret" -PromptText "Enter Payment Webhook Signing Secret"
Set-SecureSsmParam -Name "/akmoney/ci/backend/dockerhub-token" -PromptText "Enter Docker Hub CI Read/Write Token (Backend)"
Set-SecureSsmParam -Name "/akmoney/ci/frontend/dockerhub-token" -PromptText "Enter Docker Hub CI Read/Write Token (Frontend)"
Set-SecureSsmParam -Name "/akmoney/prod/registry/dockerhub-pull-token" -PromptText "Enter Docker Hub Read-Only Pull Token (EC2 Runtime)"

Write-Host "=== All SSM Parameters Configured Successfully ===" -ForegroundColor Green
