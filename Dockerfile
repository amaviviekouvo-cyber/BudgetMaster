FROM php:8.2-apache

# Extension MySQL pour PDO
RUN docker-php-ext-install pdo_mysql \
    && a2enmod rewrite headers \
    && sed -ri 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf \
    && echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Réglages PHP de production
RUN { \
        echo "display_errors=Off"; \
        echo "log_errors=On"; \
        echo "expose_php=Off"; \
        echo "session.use_strict_mode=1"; \
        echo "date.timezone=Europe/Paris"; \
    } > /usr/local/etc/php/conf.d/budgetmaster.ini

COPY . /var/www/html/

RUN chown -R www-data:www-data /var/www/html

# Les hébergeurs (Render, Railway...) imposent le port via $PORT
CMD sed -i "s/Listen 80/Listen ${PORT:-80}/" /etc/apache2/ports.conf \
    && sed -i "s/:80>/:${PORT:-80}>/" /etc/apache2/sites-available/000-default.conf \
    && apache2-foreground
