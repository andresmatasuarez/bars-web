# bars-web

## Initial setup

1. `nvm install`
1. `npm install`
1. `composer install` (instructions: https://stackoverflow.com/a/22586674)
1. Create a new file `.env` by duplicating `.env-example`. Adjust values to match your local settings.

### Seed data

Initial setup consists of getting hold of existing data from the live site and seeding it into the new local installation.

#### 1. Exporting data

1. Head over to https://rojosangre.quintadimension.com/2.0/wordpress/wp-admin/ and login with admin credentials.
1. In the left-side menu, go to Tools > Export.
1. Perform an export selecting **"All content"**.
1. Rename the downloaded XML file to "backup.xml" and place it inside `<project-root>/init-scripts`.

#### 2. Downloading assets

We still need to download the images and files associated with the data we just exported to XML. For that, we'll need to use a FTP client such as [Filezilla](https://filezilla-project.org/).

1. Log into the BARS server using the appropriate credentials.
2. Download the entire remote directory `/2.0/wp-content/uploads` into local folder `<project-root>/wp-content/uploads`.
3. As of October 2025, there's over 10k assets so it'll probably be a very long process ¯\\\_(ツ)\_/¯.

### ⚠️ First and second runs

An initial run is needed to prepare and setup the containers and volumes accordingly, with test data. This process involves two separate runs:

1.  Wordpress installation
1.  Site initialization

#### 1. Wordpress installation (first run)

Base Wordpress files need to be installed in the containers before anything else. So first, you should:

1. Open to `docker-compose.yml`.
1. Comment out the volumes marked with comments for such purpose inside `services.wordpress.volumes` and save. Do not commit these changes.
1. Run:

   ```sh
   docker compose -f docker-compose.yml down -v
   docker compose -f docker-compose.yml up -d
   ```

1. Wait until after Wordpress has successfully been installed and then run:
   ```sh
   docker compose -f docker-compose.yml down
   ```
1. You can now uncomment back the volumes hidden in **step 2** and save.

#### 2. Site initialization (second run)

Once Wordpress basic installation is ready, we now need to initialize our site accordingly with our theme and plugins, seed test data and configure overall settings like permalink structure.

For that, we rely on the scripts found in `<project-root>/init-scripts`, expected to be run once and never again. You just need to run:

```
docker compose -f docker-compose.yml up -d
```

This process might and probably will take considerable time to finish, especially due to the seeding data part in which all the uploads are imported to the site's media library. So be patient ⏳⏳⏳.

Subsequent runs won't take as long since the setup process is only performed the first time.

##### Marker files

Both the `bitnami/wordpress` image and our custom init scripts rely on marker files to detect whether stuff has been done or not.

These marker files are stored in the `bars-web_bars-wordpress-data` volume and you can play around with their existence to perform again or completely skip steps in this initialization process.

1. Run `docker volume inspect bars-web_bars-wordpress-data` and take note of the value of `Mountpoint`. It's a path to the volume folder in the host system.
1. Then, depending on your needs, you may run one or more of the following:

   ```sh
   # Tells the container to re-run initial scripts.
   sudo rm <mountpoint>/.user_scripts_initialized

   # Tells the container to do no importing.
   sudo touch <mountpoint>/.import_done
   ```

1. Now, you can run:
   ```sh
   docker compose -f docker-compose.yml up -d
   ```

## Development

### Directory structure

```bash
bars-web/
├─ assets/ # 🛠️
│  ├─ fonts/ # Fonts
│  ├─ images/ # Images not related to any specific festival edition
│  ├─ javascripts/ # Static js/ts files
│  ├─ react-apps/ # More complex features done with React (e.g. selection page)
│  ├─ resources/ # Edition-specific assets, like poster, programme and sponsors.
│  ├─ stylesheets/ # CSS/Less styles
├─ init-scripts/ # Scripts run on "Site initialization" phase (second run)
│  ├─ uploads/ # Place the backup uploads here
├─ php/ # 🛠️ PHP files
├─ raw/ # Folder to store raw assets, .psd files or misc stuff.
├─ vite/ # Vite configuration goes here.
├─ wp-plugins/ # Custom plugins
│  ├─ bars-commons/
│  ├─ jury-post-type/
│  ├─ movie-post-type/
├─ wp-themes/
│  ├─ deprecated_bars_2013/ # Old, deprecated theme
│  ├─ output/ # ⚠️ output theme, DO NOT MAKE CHANGES TO FILES INSIDE THIS FOLDER
```

Directories marked with "🛠️" are processed/compiled with Vite and npm scripts and output into `<project-root>/wp-themes/output`.

### Run local version

Running a local version of the site involves two different processes:

1. Listen for changes and output theme files:
   ```
   npm run dev
   ```
1. Start the wordpress/mysql services:

   ```
   docker compose -f docker-compose.yml up -d
   ```

   Assuming we're using the default values in `.env-example`:

   - Site will be available at `http://localhost:8083`
   - You can connect to the database from your host machine via:

   ```sh
   mysql -h127.0.0.1 -u root -P3307 -p barsweb_docker
   ```

## Deploy

1. Using a FTP client, log into the BARS server using the appropriate credentials.
1. Upload the following files:

- `<project-root>/wp-plugins` to `/2.0/wp-content/plugins`
- `<project-root>/wp-themes/output` to `/2.0/wp-content/themes/bars2013`
