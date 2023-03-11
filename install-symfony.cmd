@echo off

echo Suppression du cache Symfony
rd /s /q var
rd /s /q vendor
del composer.lock

echo Installation des dependencies Symfony
call composer install

echo Installation des dependencies Node.js
call npm install

echo Compilation des assets
call npm run dev
