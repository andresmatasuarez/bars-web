Debido al dolor de cabeza que me da tener que configurar Apache y MySql y WordPress cada
vez que muevo/clono el repo, me anoto ac� algunas puntas con tal de hacer la tarea m�s
directa para la pr�xima vez.

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

Nota 1: Insertar ese bloque previo a cualquier otra declaraci�n de VirtualHost con la
misma m�scara, es decir, *:80.

Nota 2: Es ULTRA RECOMENDABLE que el path al root del sitio no contenga caracteres
especiales. Debido al tilde de "Andr�s", tuve que lidiar BASTANTE tiempo con eso, y
termin� haciendo un SymLink "C:\Users\Andres" hacia "C:\Users\Andr�s".

----------------------------------------------------------

Archivos WordPress que tengo que revisar por cada nueva instalaci�n:
	/wp-config.php, /index.php



ENLACE DE INTER�S:
	http://davidwinter.me/articles/2012/04/09/install-and-manage-wordpress-with-git/