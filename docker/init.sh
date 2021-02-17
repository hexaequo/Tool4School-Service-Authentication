wait.sh t4s_authentication_postgres:5432 -- symfony console doctrine:database:create --if-not-exists
wait.sh t4s_authentication_postgres:5432 -- symfony console doctrine:migrations:migrate --no-interaction --allow-no-migration
wait.sh t4s_authentication_postgres:5432 -- symfony console doctrine:database:create --if-not-exists --env=test
wait.sh t4s_authentication_postgres:5432 -- symfony console doctrine:migrations:migrate --no-interaction --allow-no-migration --env=test
wait.sh t4s_rabbitmq:5672 -- echo "Rabbitmq ready"

chown -R www-data ./
