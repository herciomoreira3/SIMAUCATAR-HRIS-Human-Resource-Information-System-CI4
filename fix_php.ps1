$phpDir = "C:\php"
$phpIni = "$phpDir\php.ini"

if (-Not (Test-Path $phpDir)) {
    Write-Host "Folder $phpDir tidak ditemukan!" -ForegroundColor Red
    exit
}

# Buat file php.ini dari template development
Copy-Item "$phpDir\php.ini-development" $phpIni -Force

# Aktifkan ekstensi yang dibutuhkan
(Get-Content $phpIni) `
-replace ';extension_dir = "ext"', 'extension_dir = "ext"' `
-replace ';extension=curl', 'extension=curl' `
-replace ';extension=gd', 'extension=gd' `
-replace ';extension=intl', 'extension=intl' `
-replace ';extension=mbstring', 'extension=mbstring' `
-replace ';extension=mysqli', 'extension=mysqli' `
-replace ';extension=openssl', 'extension=openssl' `
-replace ';extension=pdo_mysql', 'extension=pdo_mysql' | Set-Content $phpIni

Write-Host "-------------------------------------------"
Write-Host "Konfigurasi PHP Berhasil Diperbarui!" -ForegroundColor Green
Write-Host "Sekarang Anda bisa menjalankan: php spark serve"
Write-Host "-------------------------------------------"
pause
