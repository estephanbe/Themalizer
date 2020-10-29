@ECHO OFF
setlocal DISABLEDELAYEDEXPANSION
SET BIN_TARGET=%~dp0/../../vendor-bin/php-scoper/vendor/nikic/php-parser/bin/php-parse
php "%BIN_TARGET%" %*
