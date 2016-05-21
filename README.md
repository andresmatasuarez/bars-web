bars-web
=============================

# Presinstallation
1. Install apache2
  1. Enable port 8082 in apache
  2. Create file at `/etc/apache2/sites-available/bars.dev.conf` with the following content:
    ```
    <VirtualHost *:8082>
      DocumentRoot /home/amatasuarez/workspace/bars-web
      ServerAdmin admin@localhost
      ServerName bars.dev
      ServerAlias www.bars.dev
      ErrorLog ${APACHE_LOG_DIR}/error.log
      CustomLog ${APACHE_LOG_DIR}/access.log combined
      <Directory /home/amatasuarez/workspace/bars-web>
        Options Indexes FollowSymLinks Includes ExecCGI
        AllowOverride All
        Require all granted
      </Directory>
    </VirtualHost>
    ```
2. Install SQL and PHP extension and enable it for apache
3. Add domain to `/etc/hosts`: `127.0.0.1 www.bars.dev`
4. Enable site in apache2:
  1. `sudo a2ensite bars.dev.conf`
  2. `sudo service apache2 reload`
5. Review `/wp-config.php` and update configuration accordingly.

# Installation & Development
1. `npm install`
3. `php composer.phar install`
4. `grunt dev`
