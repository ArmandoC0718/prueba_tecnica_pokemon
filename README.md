# Copiar el archivo .env.example a .env
cp .env.example .env

# Editar el archivo .env y configurar la variable DATABASE_URL
# DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name"

# Crear migracion 
php bin/console make:migration

# Ejecutar migraciones 
php bin/console doctrine:migrations:migrate