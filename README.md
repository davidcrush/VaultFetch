# VaultFetch

VaultFetch is a local media downloader built with Laravel. Paste a supported video URL, queue a download with [yt-dlp](https://github.com/yt-dlp/yt-dlp), and stream or save the file from your browser. Downloads are private to each signed-in user and kept on disk for a configurable retention period.

## Features

- **Video fetching** — Probe metadata and queue downloads via yt-dlp (YouTube supported by default).
- **Background jobs** — Downloads run on the queue so the UI stays responsive.
- **Authentication** — Login required; no public registration. Users are created via migrations.
- **Per-user downloads** — Each user sees only their own recent fetches; direct URLs are scoped to the owner.
- **In-browser playback** — View and stream completed downloads.
- **Refetch** — Re-queue expired downloads without losing history.
- **File retention** — Video files are purged after a configurable number of days; database records remain.

## Requirements

- PHP 8.5+
- Node.js and npm (frontend assets)
- PostgreSQL and Redis (via Laravel Sail)
- yt-dlp and ffmpeg (included in the Sail PHP 8.5 image)

## Quick start (Sail)

1. Clone the repository and install dependencies:

   ```bash
   composer install
   cp .env.example .env
   ```

2. Configure `.env`:

   - Set `VAULTFETCH_ADMIN_PASSWORD` before running migrations (creates the initial admin user).
   - Sail uses PostgreSQL and Redis by default — match `DB_*` and `REDIS_*` to your `compose.yaml` setup.

3. Start the stack and finish setup:

   ```bash
   ./vendor/bin/sail up -d
   ./vendor/bin/sail artisan key:generate
   ./vendor/bin/sail artisan migrate
   ./vendor/bin/sail npm install
   ./vendor/bin/sail npm run build
   ```

4. Run the queue worker (required for downloads to complete):

   ```bash
   ./vendor/bin/sail artisan queue:work
   ```

   Or use the combined dev script from the host:

   ```bash
   composer run dev
   ```

5. Open the app (default Sail URL: `http://localhost`) and sign in with the bootstrap account:

   - Email: `david@davidcrush.com`
   - Password: value of `VAULTFETCH_ADMIN_PASSWORD` in `.env`

## Configuration

| Variable | Description | Default |
|----------|-------------|---------|
| `VAULTFETCH_ADMIN_PASSWORD` | Password for the initial admin user (migration) | — |
| `VAULTFETCH_RECENT_LIMIT` | Max recent downloads shown on the home page | `25` |
| `YT_DLP_BINARY` | Path to yt-dlp executable | `/usr/local/bin/yt-dlp` |
| `YT_DLP_OUTPUT_DIR` | Storage subdirectory for downloaded files | `downloads` |
| `YT_DLP_FORMAT` | yt-dlp format selector | `bv*+ba/b` |
| `YT_DLP_MERGE_OUTPUT_FORMAT` | Container format after merge | `mp4` |
| `YT_DLP_PROBE_TIMEOUT` | Metadata probe timeout (seconds) | `30` |
| `YT_DLP_DOWNLOAD_TIMEOUT` | Download timeout (seconds) | `600` |
| `YT_DLP_RETENTION_DAYS` | Days to keep files on disk | `7` |
| `YT_DLP_COOKIES_FILE` | Optional Netscape cookies file for yt-dlp | — |
| `YT_DLP_PROXY` | Optional HTTP/HTTPS/SOCKS proxy for yt-dlp (e.g. `socks5://127.0.0.1:1080`) | — |

Allowed hosts are defined in [`config/vaultfetch.php`](config/vaultfetch.php).

## Users

There is no sign-up form. New users are added through Laravel migrations (see [`database/migrations/2026_06_02_021135_create_initial_admin_user.php`](database/migrations/2026_06_02_021135_create_initial_admin_user.php) for the bootstrap admin). Set passwords via `.env` or dedicated migration config keys — never commit real passwords.

## Scheduled tasks

Expired download files are purged daily:

```bash
php artisan downloads:purge-expired
```

In production, ensure the Laravel scheduler is running (`schedule:work` or a cron entry for `schedule:run`).

## Testing

```bash
php artisan test
```

Or with Sail:

```bash
./vendor/bin/sail artisan test
```

## License

MIT
