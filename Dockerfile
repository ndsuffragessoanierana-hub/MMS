# --- Étape 1 : build des assets front (Vite + Tailwind) ---
FROM node:20-alpine AS assets
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

# --- Étape 2 : image finale PHP (Nginx + PHP-FPM) ---
FROM richarvey/nginx-php-fpm:3.1.6

COPY . .
COPY --from=assets /app/public/build /var/www/html/public/build

# Config image
ENV SKIP_COMPOSER 0
ENV WEBROOT /var/www/html/public
ENV PHP_ERRORS_STDERR 1
ENV RUN_SCRIPTS 1
ENV REAL_IP_HEADER 1

# Config Laravel
ENV APP_ENV production
ENV APP_DEBUG false
ENV LOG_CHANNEL stderr

ENV COMPOSER_ALLOW_SUPERUSER 1

CMD ["/start.sh"]