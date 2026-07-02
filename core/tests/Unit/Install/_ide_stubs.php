<?php

/**
 * IDE-only symbol stubs for install/ — a directory that isn't part of this
 * checkout (removed after installation, see CliInstallTest/
 * ConnectionTemplatePasswordValidationTest). These declarations are wrapped
 * in `if (false)` so they never actually run or get defined; they exist only
 * so static analyzers (Intelephense, PHPStan, etc.) can resolve symbols that
 * are normally loaded from install/cli-install.php and install/src/functions.php
 * at runtime, guarded by file_exists() checks in the tests above.
 */
if (false) {
    function adminPasswordMinLengthMessage(): string
    {
        return '';
    }

    function validateAdminPassword(string $password): void
    {
    }

    class InstallEvo
    {
        public string $databaseServer;
        public string $databaseType;
        public string $database;
        public string $databaseUser;
        public string $databasePassword;
        public string $tablePrefix;
        public string $database_charset;
        public string $database_collation;
        public object $dbh;

        public function __construct(array $config)
        {
        }

        public function writeConfig(): void
        {
        }
    }
}
