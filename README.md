# Prueba Técnica Pokémon API


## Tecnologías Utilizadas

- **Framework**: Symfony 6 o superior
- **Lenguaje**: PHP 8.1+
- **Base de datos**: MySQL
- **ORM**: Doctrine ORM
- **Autenticación**: JWT (JSON Web Tokens) con Lexik JWT Authentication Bundle
- **API Externa**: PokeAPI (https://pokeapi.co/)
- **Arquitectura**: Arquitectura Hexagonal

## Características Principales

- Sistema de autenticación con JWT
- Captura de pokémon aleatorios
- Gestión de equipos de entrenadores
- Sistema de movimientos de pokémon
- Integración con PokeAPI para datos de pokémon
- Arquitectura limpia con separación de responsabilidades


## Instalación y Configuración

### Prerrequisitos

- PHP 8.1 o superior
- Composer
- Git

### 1. Clonar el repositorio

```bash
git clone https://github.com/ArmandoC0718/prueba_tecnica_pokemon.git
cd prueba_tecnica_pokemon
```

### 2. Instalar dependencias

```bash
composer install
```

### 3. Configurar variables de entorno

```bash
# Copiar el archivo de ejemplo
cp .env.example .env

# Editar el archivo .env y configurar las variables necesarias
# Las principales variables a configurar son:
# APP_SECRET=tu_app_secret_aqui
# DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name"

```
### 4. Crear la base de datos
```bash (El nombre que pongas en db_name sera el nombre de la base de datos que se creara con este comando)
php bin/console doctrine:database:create
```

### 5. Ejecutar migraciones

```bash
php bin/console doctrine:migrations:migrate
```

### 6. Poblar la base de datos con datos de PokeAPI

```bash
php bin/console app:populate-database
```

Este comando descargará datos de la PokeAPI e insertará:
- Tipos de pokémon
- Movimientos de pokémon 
- Pokémon con sus estadísticas
- Usuario de prueba

### 7. Generar claves JWT

```bash
# Las claves ya están incluidas en el repositorio para facilitar las pruebas
# php bin/console lexik:jwt:generate-keypair
```

### 8. Iniciar la aplicación

```bash
symfony server:start
```

La API estará disponible en: http://localhost:8000

## Documentación de la API

### Autenticación

#### Iniciar sesión
- **POST** `/api/login`
- **Body**:
```json
{
    "username": "ash_ketchum",
    "password": "pikachu123"
}
```
- **Respuesta**:
```json
{
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9..."
}
```

### Pokémon

#### Obtener pokémon aleatorio
- **GET** `/api/pokemon/random`
- **Headers**: `Authorization: Bearer {token}`
- **Respuesta**:
```json
{
    "id": 1,
    "name": "bulbasaur",
    "types": ["grass", "poison"],
    "stats": {
        "hp": 45,
        "attack": 49,
        "defense": 49,
        "special_attack": 65,
        "special_defense": 65,
        "speed": 45
    }
}
```

#### Capturar pokémon
- **POST** `/api/pokemon/{id}/catch`
- **Headers**: `Authorization: Bearer {token}`
- **Respuesta**:
```json
{
    "message": "¡Pokémon capturado exitosamente!",
    "pokemon": {
        "id": 1,
        "name": "bulbasaur"
    }
}
```

### Equipo de Entrenador

#### Ver equipo del entrenador
- **GET** `/api/trainers/{id}/team`
- **Headers**: `Authorization: Bearer {token}`
- **Respuesta**:
```json
{
    "trainer": "trainer",
    "team": [
        {
            "id": 1,
            "name": "bulbasaur",
            "types": ["grass", "poison"],
            "moves": [
                {
                    "id": 1,
                    "name": "vine-whip",
                    "type": "grass",
                    "power": 45
                }
            ]
        }
    ]
}
```

### Movimientos de Pokémon

#### Enseñar movimiento a pokémon
- **POST** `/api/pokemon/{pokemon_id}/moves`
- **Headers**: `Authorization: Bearer {token}`
- **Body**:
```json
{
    "move_id": 1
}
```

#### Hacer olvidar movimiento a pokémon
- **DELETE** `/api/pokemon/{pokemon_id}/moves/{move_id}`
- **Headers**: `Authorization: Bearer {token}`

##  Estructura del Proyecto

```
src/
├── Application/           # Casos de uso y DTOs
│   ├── DTO/              # Data Transfer Objects
│   ├── Service/          # Servicios de aplicación
│   └── UseCase/          # Casos de uso
├── Command/              # Comandos de consola
├── Domain/               # Lógica de dominio
│   ├── Entity/           # Entidades del dominio
│   ├── Exception/        # Excepciones del dominio
│   ├── Repository/       # Interfaces de repositorios
│   └── Service/          # Servicios del dominio
├── Infrastructure/       # Implementaciones de infraestructura
│   ├── Persistence/      # Configuración de persistencia
│   ├── Repository/       # Implementaciones de repositorios
│   └── Security/         # Configuración de seguridad
└── Presentation/         # Capa de presentación
    └── Controller/       # Controladores HTTP
```

## Comandos Útiles

```bash
# Limpiar caché
php bin/console cache:clear

# Ver rutas disponibles
php bin/console debug:router

# Verificar configuración de Doctrine
php bin/console doctrine:schema:validate

# Crear nueva migración
php bin/console make:migration

# Ver estado de migraciones
php bin/console doctrine:migrations:status
```

## Notas Importantes

1. **Límites del juego**:
   - Máximo 6 pokémon por entrenador
   - Máximo 4 movimientos por pokémon
   - No se pueden capturar pokémon duplicados

2. **Seguridad**:
   - Todas las rutas (excepto login) requieren autenticación JWT
   - Las claves JWT incluidas son solo para desarrollo