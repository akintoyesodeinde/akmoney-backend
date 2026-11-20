-- ==============================================================================
-- AkMoney Least-Privilege Application Database User Provisioning (Phase 6)
-- Executed by RDS Master Administrator account over encrypted TLS connection.
-- The Laravel application uses 'akmoney_app' and never connects as RDS Master.
-- ==============================================================================

CREATE DATABASE IF NOT EXISTS `akmoney`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

-- Replace {{APP_DB_PASSWORD}} at runtime from AWS SSM Parameter Store
CREATE USER IF NOT EXISTS 'akmoney_app'@'%'
  IDENTIFIED BY '{{APP_DB_PASSWORD}}'
  REQUIRE SSL;

GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, ALTER, INDEX, REFERENCES, LOCK TABLES, EXECUTE
  ON `akmoney`.*
  TO 'akmoney_app'@'%';

FLUSH PRIVILEGES;
