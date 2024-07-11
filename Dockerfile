# Usa la imagen oficial de Node.js para el frontend
FROM node:14 AS frontend

# Crea un directorio de trabajo
WORKDIR /usr/src/app

# Copia el package.json y package-lock.json
COPY package*.json ./

# Instala las dependencias
RUN npm install

# Copia el resto del código de la aplicación
COPY . .

# Expone el puerto que usa la aplicación
EXPOSE 3003

# Comando para ejecutar la aplicación
CMD ["npm", "run", "dev"]

# Usa la imagen oficial de PHP 8.2 para el backend
FROM php:8.2-cli AS backend

# Instala las herramientas necesarias y extensiones de PHP
RUN apt-get update && \
    apt-get install -y zip unzip git libzip-dev && \
    docker-php-ext-install zip

# Instala Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Crea un directorio de trabajo
WORKDIR /var/www/html

# Copia los archivos de la aplicación PHP
COPY . .

# Instala las dependencias de PHP
RUN composer install

# Expone el puerto que usa la aplicación
EXPOSE 8000

# Comando para ejecutar la aplicación
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
