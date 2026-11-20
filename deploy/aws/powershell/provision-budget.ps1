<#
.SYNOPSIS
    Phase 2 & Phase 19 — AWS Monthly Cost Budget & Email Alert Notification Provisioning
#>
[CmdletBinding()]
param(
    [string]$Profile = "akmoney",
    [string]$Region = "eu-north-1",
    [string]$ExpectedAccountId = "415805875879",
    [string]$NotificationEmail = "zakinstoye@gmail.com",
    [decimal]$MonthlyLimitUsd = 35.00
)

$ErrorActionPreference = "Continue"
$AwsArgs = @("--region", $Region)
if ($Profile) { $AwsArgs += @("--profile", $Profile) }

$IdentityJson = aws sts get-caller-identity @AwsArgs | ConvertFrom-Json
if (-not $IdentityJson -or $IdentityJson.Account -ne $ExpectedAccountId) {
    throw "CRITICAL: Authenticated AWS Account ($($IdentityJson.Account)) does NOT match $ExpectedAccountId."
}

$BudgetJson = @{
    BudgetName = "AkMoney-Monthly-Cost-Budget"
    BudgetLimit = @{
        Amount = "$MonthlyLimitUsd"
        Unit = "USD"
    }
    TimeUnit = "MONTHLY"
    BudgetType = "COST"
} | ConvertTo-Json -Depth 5

$NotificationsJson = @(
    @{
        Notification = @{
            NotificationType = "ACTUAL"
            ComparisonOperator = "GREATER_THAN"
            Threshold = 80.0
            ThresholdType = "PERCENTAGE"
        }
        Subscribers = @(
            @{
                SubscriptionType = "EMAIL"
                Address = $NotificationEmail
            }
        )
    },
    @{
        Notification = @{
            NotificationType = "FORECASTED"
            ComparisonOperator = "GREATER_THAN"
            Threshold = 100.0
            ThresholdType = "PERCENTAGE"
        }
        Subscribers = @(
            @{
                SubscriptionType = "EMAIL"
                Address = $NotificationEmail
            }
        )
    }
) | ConvertTo-Json -Depth 6

$TempBudget = Join-Path $env:TEMP "akmoney-budget.json"
$TempNotif = Join-Path $env:TEMP "akmoney-budget-notif.json"
[System.IO.File]::WriteAllText($TempBudget, $BudgetJson)
[System.IO.File]::WriteAllText($TempNotif, $NotificationsJson)

try {
    $null = aws budgets describe-budget --account-id $ExpectedAccountId --budget-name "AkMoney-Monthly-Cost-Budget" @AwsArgs 2>&1
    if ($LASTEXITCODE -ne 0) {
        Write-Host "Creating AWS Cost Budget (Limit: `$$MonthlyLimitUsd/month, Alert Email: $NotificationEmail)..." -ForegroundColor Yellow
        aws budgets create-budget `
            --account-id $ExpectedAccountId `
            --budget "file://$TempBudget" `
            --notifications-with-subscribers "file://$TempNotif" @AwsArgs 2>&1
        if ($LASTEXITCODE -eq 0) {
            Write-Host "AWS Cost Budget created successfully." -ForegroundColor Green
        } else {
            Write-Host "Warning: AWS Budgets API restricted by SCP or permissions; continuing." -ForegroundColor Yellow
        }
    } else {
        Write-Host "AWS Cost Budget 'AkMoney-Monthly-Cost-Budget' already exists." -ForegroundColor Green
    }
} finally {
    Remove-Item -Force $TempBudget, $TempNotif -ErrorAction SilentlyContinue
}
