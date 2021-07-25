@ECHO OFF
setlocal DISABLEDELAYEDEXPANSION
SET BIN_TARGET=%~dp0/../../vendor-bin/php-scoper/vendor/humbug/php-scoper/bin/php-scoper
php "%BIN_TARGET%" %*
