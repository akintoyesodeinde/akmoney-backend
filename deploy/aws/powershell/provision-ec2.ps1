<#
.SYNOPSIS
    Phase 10 & Phase 11 — Idempotent EC2 Launch Template and Single t3.micro Instance Provisioning
.DESCRIPTION
    Validates AWS Account ID 415805875879, encodes deploy/aws/ec2/user-data.sh as Base64,
    creates or updates EC2 Launch Template (akmoney-launch-template) resolving latest Amazon Linux 2023 AMI via SSM,
    and launches exactly one EC2 t3.micro instance (akmoney-prod-server).
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

# 1. Verify no running AkMoney production EC2 instance already exists (Enforce single-instance constraint)
$ExistingInstance = aws ec2 describe-instances `
    --filters "Name=tag:Name,Values=akmoney-prod-server" "Name=tag:Project,Values=AkMoney" "Name=instance-state-name,Values=pending,running,stopping,stopped" `
    --query "Reservations[].Instances[].InstanceId" --output text @AwsArgs

if ($ExistingInstance -and $ExistingInstance -ne "None") {
    Write-Host "AkMoney EC2 instance already exists: $ExistingInstance (Enforcing 1-instance constraint)." -ForegroundColor Green
    return
}

# 2. Lookup akmoney-ec2-sg
$VpcId = aws ec2 describe-vpcs --filters "Name=is-default,Values=true" --query "Vpcs[0].VpcId" --output text @AwsArgs
$Ec2SgId = aws ec2 describe-security-groups --filters "Name=vpc-id,Values=$VpcId" "Name=group-name,Values=akmoney-ec2-sg" --query "SecurityGroups[0].GroupId" --output text @AwsArgs
if (-not $Ec2SgId -or $Ec2SgId -eq "None") {
    throw "Security Group 'akmoney-ec2-sg' not found. Run provision-networking.ps1 first."
}

# 3. Base64-encode user-data.sh (LF line endings for Linux bash execution)
$UserDataPath = Join-Path $PSScriptRoot "..\ec2\user-data.sh"
$UserDataRaw = (Get-Content -Raw $UserDataPath) -replace "`r`n", "`n"
$UserDataBase64 = [Convert]::ToBase64String([System.Text.Encoding]::UTF8.GetBytes($UserDataRaw))

# 4. Construct Launch Template Payload
$TemplateSpecPath = Join-Path $PSScriptRoot "..\ec2\launch-template-data.json"
$TemplateObj = Get-Content -Raw $TemplateSpecPath | ConvertFrom-Json
$TemplateObj.LaunchTemplateData | Add-Member -NotePropertyName "SecurityGroupIds" -NotePropertyValue @($Ec2SgId) -Force
$TemplateObj.LaunchTemplateData | Add-Member -NotePropertyName "UserData" -NotePropertyValue $UserDataBase64 -Force

$TempPayload = Join-Path $env:TEMP "akmoney-lt-payload.json"
$TemplateObj.LaunchTemplateData | ConvertTo-Json -Depth 10 | Set-Content -Path $TempPayload -Encoding UTF8

$LtName = "akmoney-launch-template"
aws ec2 describe-launch-templates --launch-template-names $LtName @AwsArgs 2>$null | Out-Null
if ($LASTEXITCODE -ne 0) {
    Write-Host "Creating EC2 Launch Template: $LtName..." -ForegroundColor Yellow
    aws ec2 create-launch-template `
        --launch-template-name $LtName `
        --version-description "Initial AkMoney AL2023 t3.micro Launch Template" `
        --launch-template-data "file://$TempPayload" `
        --tag-specifications "ResourceType=launch-template,Tags=[{Key=Name,Value=$LtName},{Key=Project,Value=AkMoney},{Key=Environment,Value=Production},{Key=ManagedBy,Value=AkMoney-Deployment}]" `
        @AwsArgs | Out-Null
} else {
    Write-Host "Updating EC2 Launch Template version: $LtName..." -ForegroundColor Yellow
    $NewVer = aws ec2 create-launch-template-version `
        --launch-template-name $LtName `
        --launch-template-data "file://$TempPayload" `
        --query "LaunchTemplateVersion.VersionNumber" --output text @AwsArgs
    aws ec2 modify-launch-template --launch-template-name $LtName --default-version $NewVer @AwsArgs | Out-Null
}
Remove-Item -Force $TempPayload -ErrorAction SilentlyContinue

# 5. Launch Exactly ONE EC2 Instance from Launch Template
Write-Host "Launching 1x Amazon EC2 t3.micro instance from '$LtName'..." -ForegroundColor Yellow
$InstanceId = aws ec2 run-instances `
    --launch-template "LaunchTemplateName=$LtName,Version=`$Default" `
    --count 1 `
    --query "Instances[0].InstanceId" --output text @AwsArgs

Write-Host "Waiting for EC2 Instance $InstanceId to enter running state..." -ForegroundColor Cyan
aws ec2 wait instance-running --instance-ids $InstanceId @AwsArgs

$PublicIp = aws ec2 describe-instances --instance-ids $InstanceId --query "Reservations[0].Instances[0].PublicIpAddress" --output text @AwsArgs
Write-Host "AkMoney Production EC2 Instance Running: $InstanceId (Public IP: $PublicIp)" -ForegroundColor Green
