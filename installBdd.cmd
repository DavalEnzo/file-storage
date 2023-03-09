echo Mise … jour de la BDD
php bin/console d:d:d --force
php bin/console d:d:c
php bin/console doctrine:migrations:migrate --no-interaction

echo La BDD a ‚t‚ mise … jour, lancement du serveur
php bin/console h:f:l -q
symfony serve
