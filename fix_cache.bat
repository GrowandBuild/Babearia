@echo off
echo Corrigindo problemas de cache do Laravel...
echo.

cd /d "C:\Users\Alexandre\Desktop\estetica"

echo Criando diretorios necessarios...
if not exist "bootstrap\cache" mkdir "bootstrap\cache"
if not exist "bootstrap\cache\.gitignore" (
    echo * > "bootstrap\cache\.gitignore"
    echo !*.gitignore >> "bootstrap\cache\.gitignore"
)

if not exist "storage\framework\cache" mkdir "storage\framework\cache"
if not exist "storage\framework\sessions" mkdir "storage\framework\sessions"
if not exist "storage\framework\views" mkdir "storage\framework\views"
if not exist "storage\logs" mkdir "storage\logs"

echo.
echo Limpando caches...
php artisan optimize:clear

echo.
echo Recriando caches...
php artisan config:cache
php artisan route:cache

echo.
echo Verificando...
php artisan about

echo.
echo Concluido! Tente acessar o servidor novamente.
pause




