ESTRUCTURA DEL PROYECTO
hoteles_api/
│
--app/
│
│----- Http/Controllers/Api/
│
-------------------------- HotelController.php
-------------------------- HabitacionController.php
-------------------------- HabitacionTipoController.php
-------------------------- AcomodacionController.php
│
│
│----- Models/
│
------------- Hotel.php
------------- Habitacion.php
------------- HabitacionTipo.php
------------- Acomodacion.php
------------- HabitacionTipoController.php
│
│
--routes/
│
-----------api.php
│
.env
│
│

cambios archivo 

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=hoteles_itbf
DB_USERNAME=xxxxx #<su_usuario>
DB_PASSWORD=xxxxx #<su_clave>

INSTALACION  DEL  PROYECTO
Instalación una vez instalada y cargada la base de datos
1 Clona el repositorio:
	https://github.com/rbchus/itbf_backend.git
2 cd hoteles_api
3 composer install
4 configura el archivo .env
5 php artisan key:generate
6 php artisan serve
