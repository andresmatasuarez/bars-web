Debido al dolor de cabeza que me da tener que configurar Apache y MySql y WordPress cada
vez que muevo/clono el repo, me anoto acá algunas puntas con tal de hacer la tarea más
directa para la próxima vez.

C:\Windows\System32\Drivers\etc\hosts
	Appendear "127.0.0.1	www.bars.dev"

----------------------------------------------------

%APACHE%\conf\extra\httpd-vhosts.conf:
############## START - BARS WEB PROJECT #########################
<VirtualHost *:80>
	DocumentRoot "C:\Users\Andres\workspace\bars-web"
	ServerName www.bars.dev
	<Directory "C:\Users\Andres\workspace\bars-web">
		Options Indexes FollowSymLinks Includes ExecCGI
		AllowOverride All
		Order allow,deny
		Allow from all
	</Directory>
</VirtualHost>
############## END - BARS WEB PROJECT #########################

Nota 1: Insertar ese bloque previo a cualquier otra declaración de VirtualHost con la
misma máscara, es decir, *:80.

Nota 2: Es ULTRA RECOMENDABLE que el path al root del sitio no contenga caracteres
especiales. Debido al tilde de "Andrés", tuve que lidiar BASTANTE tiempo con eso, y
terminé haciendo un SymLink "C:\Users\Andres" hacia "C:\Users\Andrés".

----------------------------------------------------------

Archivos WordPress que tengo que revisar por cada nueva instalación:
	/wp-config.php, /index.php



ENLACE DE INTERÉS:
	http://davidwinter.me/articles/2012/04/09/install-and-manage-wordpress-with-git/