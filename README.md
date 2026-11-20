# AkMoney Backend API & Financial Engine

[![CI/CD Pipeline](https://github.com/akintoyesodeinde/akmoney-backend/actions/workflows/ci-cd.yml/badge.svg)](https://github.com/akintoyesodeinde/akmoney-backend/actions/workflows/ci-cd.yml)

AkMoney is a modern, production-oriented digital money transfer and cross-border remittance fintech platform. This repository contains the standalone PHP 8.2+ / Laravel 11 backend service, providing REST APIs, multi-currency ledger management, double-entry transactional accounting, idempotent payment processing, and administrative controls.

---

## 1. Architecture Overview

- **Framework:** Laravel 11.x (PHP 8.2+)
- **Authentication:** Laravel Sanctum (Bearer Token) & Web Session Guard
- **Database Engine:** MySQL 8.0 / MariaDB (InnoDB Strict Mode)
- **Containerization:** Multi-stage Docker with PHP-FPM, Nginx, and Supervisor
- **CI/CD:** GitHub Actions with AWS IAM OIDC federation (zero long-lived cloud keys)
- **Deployment:** Amazon EC2 via AWS Systems Manager (SSM Run Command)

---

## 2. Directory Structure

```
akmoney-backend/
├── app/                  # Application core (Controllers, Models, Middleware, Helpers)
├── bootstrap/            # Application bootstrap & middleware pipeline
├── config/               # Configuration files (cors, database, sanctum, app)
├── database/             # Migrations, seeders, and model factories
├── deploy/               # AWS deployment specifications
│   ├── aws/iam/          # Least-privilege IAM OIDC trust and permission policies
│   └── aws/ec2/          # Launch template, hardened user-data, deploy & rollback scripts
├── docker/               # Container configuration (nginx.conf, supervisord.conf, entrypoint)
├── public/               # Web root (index.php, static assets, logos, favicon)
├── resources/            # Views (Admin portal, email notifications, language files)
├── routes/               # API, Admin, IPN, Agent, Console, and Web routes
├── storage/              # File storage, sessions, logs, and compiled views
├── tests/                # Automated remediation and financial integrity test suites
├── .env.example          # Safe configuration template (no secrets)
├── Dockerfile            # Production multi-stage Alpine container
└── docker-compose.yml    # Standalone local integration environment with MySQL
```

---

## 3. Local Development Setup

### Prerequisites
- PHP 8.2 or higher with extensions: `pdo_mysql`, `mbstring`, `openssl`, `bcmath`, `gd`, `zip`, `curl`
- Composer 2.7+
- MySQL 8.0 or MariaDB

### Quick Start
```bash
# 1. Clone repository
git clone https://github.com/akintoyesodeinde/akmoney-backend.git
cd akmoney-backend

# 2. Install dependencies
composer install

# 3. Configure environment
cp .env.example .env
php artisan key:generate

# 4. Run database migrations
php artisan migrate

# 5. Execute test suite
php tests/run_tests.php

# 6. Start local development server
php artisan serve --port=8000
```

---

## 4. Docker Deployment

### Run Locally with Docker Compose
```bash
docker compose up -d --build
```
The API will be available at `http://localhost:8000`.

### Health Check Endpoint
```bash
curl http://localhost:8000/api/health
```
Response:
```json
{
  "status": "ok",
  "service": "AkMoney API",
  "timestamp": "2026-10-29T14:35:51+00:00",
  "database": "connected",
  "environment": "production",
  "version": "1.0.0"
}
```

---

## 5. Security & Financial Controls
- **Zero Committed Secrets:** All database credentials, encryption keys, and third-party tokens are delivered via environment variables.
- **Idempotent Webhooks:** Payment gateways are processed atomically with row-level locking (`SELECT ... FOR UPDATE`).
- **Sanctum Token Hashing:** Personal access tokens and API secrets are never returned in plain text or logged.
- **Strict CORS:** Configured via `config/cors.php` and `CORS_ALLOWED_ORIGINS` to permit only the standalone React frontend.
