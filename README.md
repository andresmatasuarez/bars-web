# bars-web

Buenos Aires Rojo Sangre Film Festival website - a monorepo with npm workspaces.

### Themes

Each theme has its own README with tech stack details, directory structure, and build info:

- [bars2013](themes/bars2013/README.md) - Legacy theme (2013-2025)
- [bars2026](themes/bars2026/README.md) - Current theme (2026+)

## Initial setup

1. `nvm install`
2. `npm install` (from project root - installs all workspace deps)
3. `cd themes/bars2013 && composer install` (PHP dependencies for bars2013 only)
4. Create `.env` by duplicating `.env-example` and adjusting as needed:
   ```sh
   cp .env-example .env
   ```
   The defaults work out of the box for local development (WordPress at port 8083, MySQL at port 3307). Edit `.env` if you need to change ports or credentials.

### Seed data

Initial setup consists of getting hold of existing data from the live site and seeding it into the new local installation.

#### 1. Exporting data

1. Head over to https://rojosangre.quintadimension.com/2.0/wordpress/wp-admin/ and login with admin credentials.
1. In the left-side menu, go to Tools > Export.
1. Perform an export selecting **"All content"**.
1. Rename the downloaded XML file to "backup.xml" and place it inside `<project-root>/scripts/init-site`.

#### 2. Downloading assets

We still need to download the images and files associated with the data we just exported to XML. These assets can be found in the BARS FTP server, in the remote directory `/2.0/wp-content/uploads` and must be downloaded into local folder `<project-root>/scripts/init-site/uploads`.

As of October 2025, there's over 10k assets so downloading will probably be a long process ¯\\\_(ツ)\_/¯.

To achieve this, you can use any FTP client such as [Filezilla](https://filezilla-project.org/) or `lftp` from the command line:

```sh
lftp -u <FTP_USER> -e "set ssl:verify-certificate no; mirror /2.0/wp-content/uploads scripts/init-site/uploads; quit" ftp://<FTP_HOST>
```

`lftp` handles the server's TLS requirement automatically and prompts for the password interactively. Install with `sudo apt install lftp` or `brew install lftp`.

### First and second runs

An initial run is needed to prepare and setup the containers and volumes accordingly, with test data. This process involves two separate runs:

1.  Wordpress installation
1.  Site initialization

#### 1. Wordpress installation (first run)

Base Wordpress files need to be installed in the containers before anything else. So first, you should:

1. Open to `docker-compose.yml`.
1. Comment out the volumes and services marked with comments for such purpose (the `uploads-server` service **and** the volumes inside `services.wordpress.volumes`) and save. Do not commit these changes.
1. Run:

   ```sh
   docker compose down -v
   docker compose up -d
   ```

1. Wait until after Wordpress has successfully been installed and then run:
   ```sh
   docker compose down
   ```
1. You can now uncomment back everything hidden in **step 2** and save.

#### 2. Site initialization (second run)

Once Wordpress basic installation is ready, we now need to initialize our site accordingly with our theme and plugins, seed test data and configure overall settings like permalink structure.

For that, we rely on the scripts found in `<project-root>/scripts/init-site`, expected to be run once and never again. You just need to run:

```
docker compose up -d
```

The `uploads-server` service (an nginx container) serves the local uploads over Docker's internal network, so `wp import` downloads attachments locally instead of from the remote server. This brings import time down from ~3.5 hours to ~15-30 minutes.

Subsequent runs won't take as long since the setup process is only performed the first time. After a successful import, you can comment out or remove the `uploads-server` service — it's only needed during the initial seed.

##### Switching themes

Use `./scripts/switch-theme.sh` to switch the active WordPress theme in the Docker container (e.g. between `bars2013` and `bars2026`).

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
   docker compose up -d
   ```

## Project Structure

```
bars-web/
├─ themes/
│  ├─ bars2013/              # Legacy theme — see themes/bars2013/README.md
│  └─ bars2026/              # Current theme — see themes/bars2026/README.md
├─ shared/
│  ├─ editions.json          # Single source of truth for festival data
│  ├─ resources/             # Edition-specific assets (poster, programme, sponsors)
│  └─ php/                   # Shared PHP utilities (editions.php, helpers.php)
├─ wp-themes/
│  ├─ bars2013/              # Build output — DO NOT EDIT
│  └─ bars2026/              # Build output — DO NOT EDIT
├─ wp-plugins/
│  ├─ bars-commons/
│  ├─ jury-post-type/
│  └─ movie-post-type/
├─ scripts/                  # init-site/, switch-theme.sh
├─ package.json              # Workspace root
└─ tsconfig.base.json
```

## Development

### Run local version

Running a local version of the site involves two different processes:

1. Listen for changes and output theme files:

   ```
   npm run dev:bars2013
   # or
   npm run dev:bars2026
   ```

   Or from the theme directory:

   ```
   cd themes/bars2026 && npm run dev
   ```

2. Start the wordpress/mysql services:

   ```
   docker compose up -d
   ```

   Assuming we're using the default values in `.env-example`:
   - Site will be available at `http://localhost:8083`
   - You can connect to the database from your host machine via:

   ```sh
   mysql -h127.0.0.1 -u root -P3307 -p barsweb_docker
   ```

### Available Scripts

From the root:

- `npm run dev:bars2013` / `npm run dev:bars2026` - Start development mode
- `npm run build:bars2013` / `npm run build:bars2026` - Build for production
- `npm run lint:bars2013` / `npm run lint:bars2026` - Run ESLint

From each theme directory (`themes/bars2013` or `themes/bars2026`):

- `npm run dev` - Start development mode
- `npm run build` - Build for production
- `npm run lint` - Run ESLint
- `npm run lint:autofix` - Run ESLint with auto-fix
- `npm run typecheck` - Run TypeScript type checking

## Deploy

1. Using a FTP client, log into the BARS server using the appropriate credentials.
1. Upload the following files:

- `wp-plugins/` to `/2.0/wp-content/plugins`
- `wp-themes/bars2013/` to `/2.0/wp-content/themes/bars2013`
- `wp-themes/bars2026/` to `/2.0/wp-content/themes/bars2026`
