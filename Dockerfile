FROM php:8.4-apache-bullseye

# Ενημέρωση πακέτων για ασφάλεια
RUN apt-get update && \
    apt-get upgrade -y && \
    apt-get install -y unzip git curl && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

# Εγκατάσταση PHP extensions
RUN docker-php-ext-install pdo pdo_mysql

# Εγκατάσταση Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Ορισμός workdir
WORKDIR /var/www/html

# Αντιγραφή μόνο των composer αρχείων πρώτα για layer caching
COPY ./app/composer.json ./app/composer.lock ./

# Εγκατάσταση dependencies με composer
RUN composer install --no-dev --optimize-autoloader

# Αντιγραφή όλης της εφαρμογής
COPY ./app/ .

# Ορισμός δικαιωμάτων (προαιρετικά)
RUN chown -R www-data:www-data /var/www/html
