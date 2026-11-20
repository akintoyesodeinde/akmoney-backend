# AkMoney Production AWS Infrastructure & Keyless CI/CD Deployment Guide

This directory (`deploy/`) contains the complete, reproducible infrastructure-as-code specifications, IAM policies, restricted SSM documents, EC2 bootstrap scripts, and Windows PowerShell provisioning scripts for deploying AkMoney to AWS Account `415805875879`.

---

## 1. Directory Layout

```text
deploy/
├── aws/
│   ├── iam/
│   │   ├── backend-oidc-trust-policy.json
│   │   ├── frontend-oidc-trust-policy.json
│   │   ├── backend-deploy-policy.json
│   │   ├── frontend-deploy-policy.json
│   │   ├── ec2-trust-policy.json
│   │   └── ec2-instance-role-policy.json
│   ├── ec2/
│   │   ├── launch-template-data.json
│   │   ├── user-data.sh
│   │   ├── deploy-backend.sh
│   │   ├── deploy-frontend.sh
│   │   ├── deploy-release.sh
│   │   ├── rollback-release.sh
│   │   └── health-check.sh
│   ├── rds/
│   │   ├── rds-specification.json
│   │   └── init-app-user.sql
│   ├── networking/
│   │   └── security-groups-spec.json
│   ├── ssm/
│   │   ├── AkMoney-Deploy-Backend.json
│   │   ├── AkMoney-Deploy-Frontend.json
│   │   ├── AkMoney-Rollback-Backend.json
│   │   └── AkMoney-Rollback-Frontend.json
│   └── powershell/
│       ├── provision-iam.ps1
│       ├── provision-networking.ps1
│       ├── provision-ssm.ps1
│       ├── provision-rds.ps1
│       ├── provision-ec2.ps1
│       ├── provision-budget.ps1
│       └── verify-deployment.ps1
├── docker-compose.production.yml
└── README.md
```

---

## 2. Provisioning Execution Order (Windows PowerShell)

All PowerShell scripts enforce an automatic guard verifying that `aws sts get-caller-identity` returns Account ID `415805875879`.

1. **Authenticate locally in Windows PowerShell:**
   ```powershell
   aws sso login --profile akmoney
   aws sts get-caller-identity --profile akmoney
   ```
2. **Provision IAM OIDC, Roles, Instance Profile, and SSM Documents:**
   ```powershell
   .\deploy\aws\powershell\provision-iam.ps1 -Profile akmoney -Region us-east-1
   ```
3. **Provision Networking & Security Groups (`akmoney-ec2-sg` and `akmoney-rds-sg`):**
   ```powershell
   .\deploy\aws\powershell\provision-networking.ps1 -Profile akmoney -Region us-east-1
   ```
4. **Populate Secure SSM Parameter Store Values:**
   ```powershell
   .\deploy\aws\powershell\provision-ssm.ps1 -Profile akmoney -Region us-east-1
   ```
5. **Provision Single-AZ RDS MySQL (`db.t3.micro`):**
   ```powershell
   .\deploy\aws\powershell\provision-rds.ps1 -Profile akmoney -Region us-east-1
   ```
6. **Provision EC2 Launch Template & Single `t3.micro` Instance:**
   ```powershell
   .\deploy\aws\powershell\provision-ec2.ps1 -Profile akmoney -Region us-east-1
   ```
7. **Verify End-to-End Deployment:**
   ```powershell
   .\deploy\aws\powershell\verify-deployment.ps1 -Profile akmoney -Region us-east-1
   ```
