# Usamos PHP con Apache
FROM php:8.1-apache

# Instalar extensiones necesarias (MySQL, PDO, etc.)
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copiar tu código al contenedor
COPY . /var/www/html/

# Exponer el puerto 8080
EXPOSE 8080

# Comando para iniciar Apache en primer plano
CMD ["apache2-foreground"]
