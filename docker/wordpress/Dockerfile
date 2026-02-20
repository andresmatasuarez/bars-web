FROM wordpress:apache

# Install MySQL client (needed by WP-CLI's db commands: wp db export, wp db query, etc.)
RUN apt-get update && apt-get install -y default-mysql-client && rm -rf /var/lib/apt/lists/*

# Install WP-CLI
RUN curl -o /usr/local/bin/wp \
      https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar \
    && chmod +x /usr/local/bin/wp

# Disable OpCache for development (replaces Bitnami's PHP_OPCACHE_ENABLED=0)
RUN echo "opcache.enable=0" > /usr/local/etc/php/conf.d/opcache-dev.ini

# Custom entrypoint wrapper (runs official entrypoint + init scripts)
COPY init-entrypoint.sh /usr/local/bin/init-entrypoint.sh
RUN chmod +x /usr/local/bin/init-entrypoint.sh

ENTRYPOINT ["init-entrypoint.sh"]
CMD ["apache2-foreground"]
