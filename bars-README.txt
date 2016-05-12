Appendear a /etc/hosts:
	127.0.0.1	www.bars.dev

----------------------------------------------------
Crear el archivo /etc/apache2/sites-available/bars.dev.conf con el siguiente contenido:

<VirtualHost *:80>
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

----------------------------------------
Habilitar sitio:
(Deshabilitarlo primero si ya existia: sudo a2dissite bars.dev.conf)
sudo a2ensite bars.dev.conf
sudo service apache2 restart
----------------------------------------------------------

Archivos WordPress que tengo que revisar por cada nueva instalación:
	/wp-config.php, /index.php


ENLACE DE INTERÉS:
	http://davidwinter.me/articles/2012/04/09/install-and-manage-wordpress-with-git/
