# bars-web

Buenos Aires Rojo Sangre Film Festival website - a monorepo with npm workspaces.

## Initial setup

1. `nvm install`
2. `npm install` (from project root - installs all workspace deps)
3. `cd themes/bars2013 && composer install` (PHP dependencies)
4. Create `.env` by duplicating `.env-example`

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

## Project Structure

```bash
bars-web/
├─ themes/
│  └─ bars2013/              # Main theme (npm workspace)
│     ├─ assets/             # Source files (LESS, TypeScript, React apps, fonts)
│     ├─ php/                # Theme-specific PHP templates
│     ├─ vite/               # Vite configuration
│     ├─ raw/                # Raw assets, .psd files or misc stuff
│     ├─ vendor/             # PHP Composer dependencies
│     ├─ package.json        # Theme-specific npm deps & scripts
│     └─ tsconfig.json       # Theme TS config (extends base)
├─ shared/
│  ├─ resources/             # Edition-specific assets (poster, programme, sponsors)
│  └─ php/                   # Shared PHP utilities (editions.json, helpers.php)
├─ wp-themes/
│  └─ output/                # ⚠️ Build output, DO NOT EDIT
├─ wp-plugins/               # Custom WordPress plugins
│  ├─ bars-commons/
│  ├─ jury-post-type/
│  └─ movie-post-type/
├─ init-scripts/             # Docker initialization scripts
├─ package.json              # Workspace root
└─ tsconfig.base.json        # Shared TypeScript compiler options
```

## Development

### Run local version

Running a local version of the site involves two different processes:

1. Listen for changes and output theme files:
   ```
   npm run dev:bars2013
   ```
   Or from the theme directory:
   ```
   cd themes/bars2013 && npm run dev
   ```

2. Start the wordpress/mysql services:

   ```
   docker compose -f docker-compose.yml up -d
   ```

   Assuming we're using the default values in `.env-example`:

   - Site will be available at `http://localhost:8083`
   - You can connect to the database from your host machine via:

   ```sh
   mysql -h127.0.0.1 -u root -P3307 -p barsweb_docker
   ```

### Available Scripts

From the root:
- `npm run dev:bars2013` - Start development mode for bars2013 theme
- `npm run build:bars2013` - Build bars2013 theme for production
- `npm run lint:bars2013` - Run ESLint on bars2013 theme

From `themes/bars2013`:
- `npm run dev` - Start development mode
- `npm run build` - Build for production
- `npm run lint` - Run ESLint
- `npm run typecheck` - Run TypeScript type checking

## Deploy

1. Using a FTP client, log into the BARS server using the appropriate credentials.
1. Upload the following files:

- `<project-root>/wp-plugins` to `/2.0/wp-content/plugins`
- `<project-root>/wp-themes/output` to `/2.0/wp-content/themes/bars2013`
