Boton de Alerta

forma 1 de levantar el scrip php
docker exec -it php-apache-alert bash
php /var/www/html/src/tools/monitor.php

forma 2

docker exec -d php-apache-alert php /var/www/html/src/tools/monitor.php

detenerlo con:
docker exec -it php-apache-alert pkill -f monitor.php
