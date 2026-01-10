# Script para corrigir problemas de cache do Laravel
Write-Host "Corrigindo problemas de cache do Laravel..." -ForegroundColor Yellow

# Navegar para o diretório do projeto
$projectPath = "C:\Users\Alexandre\Desktop\estetica"
Set-Location $projectPath

# Garantir que bootstrap/cache existe
if (-not (Test-Path "bootstrap\cache")) {
    Write-Host "Criando diretório bootstrap\cache..." -ForegroundColor Green
    New-Item -ItemType Directory -Path "bootstrap\cache" -Force | Out-Null
}

# Garantir que bootstrap/cache/.gitignore existe
if (-not (Test-Path "bootstrap\cache\.gitignore")) {
    Write-Host "Criando bootstrap\cache\.gitignore..." -ForegroundColor Green
    Set-Content -Path "bootstrap\cache\.gitignore" -Value "*`n!*.gitignore"
}

# Garantir que storage/framework/cache existe
if (-not (Test-Path "storage\framework\cache")) {
    Write-Host "Criando diretório storage\framework\cache..." -ForegroundColor Green
    New-Item -ItemType Directory -Path "storage\framework\cache" -Force | Out-Null
}

# Garantir que storage/framework/sessions existe
if (-not (Test-Path "storage\framework\sessions")) {
    Write-Host "Criando diretório storage\framework\sessions..." -ForegroundColor Green
    New-Item -ItemType Directory -Path "storage\framework\sessions" -Force | Out-Null
}

# Garantir que storage/framework/views existe
if (-not (Test-Path "storage\framework\views")) {
    Write-Host "Criando diretório storage\framework\views..." -ForegroundColor Green
    New-Item -ItemType Directory -Path "storage\framework\views" -Force | Out-Null
}

# Garantir que storage/logs existe
if (-not (Test-Path "storage\logs")) {
    Write-Host "Criando diretório storage\logs..." -ForegroundColor Green
    New-Item -ItemType Directory -Path "storage\logs" -Force | Out-Null
}

Write-Host "`nLimpando caches do Laravel..." -ForegroundColor Yellow
php artisan optimize:clear 2>&1 | Out-Null

Write-Host "Recriando caches..." -ForegroundColor Yellow
php artisan config:cache 2>&1 | Out-Null
php artisan route:cache 2>&1 | Out-Null

Write-Host "`nVerificando se está tudo ok..." -ForegroundColor Yellow
php artisan about 2>&1

Write-Host "`nConcluído! Tente acessar o servidor novamente." -ForegroundColor Green


