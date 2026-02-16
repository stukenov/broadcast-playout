# Broadcast Playout

A Laravel-based TV/radio broadcast playout management system for scheduling and managing media content playout with HLS streaming support.

## Overview

Broadcast Playout is a comprehensive playout automation system designed for TV and radio broadcasters. It provides tools for media file management, event scheduling, and automated playlist generation with HLS (HTTP Live Streaming) transcoding capabilities.

## Features

- **Media File Management**: Upload and manage video/audio files with automatic duration detection
- **Event Scheduling**: Schedule media files for broadcast with precise start and end times
- **Automatic Transcoding**: Uses FFmpeg to transcode uploaded files to HLS format (H.264 video, AAC audio)
- **Segment Generation**: Automatically generates HLS segments for seamless streaming
- **Playlist Automation**: Automatically creates playlists based on scheduled events
- **Real-time UI**: Built with Laravel Livewire for reactive user interfaces
- **Queue Processing**: Background job processing for transcoding and playlist generation
- **User Authentication**: Secure access with Laravel Jetstream and Fortify

## Tech Stack

- **Framework**: Laravel 10
- **PHP**: 8.1+
- **Frontend**: Livewire 2.11, Tailwind CSS (via Jetstream)
- **Authentication**: Laravel Jetstream with Fortify
- **Database**: MySQL/SQLite support
- **Video Processing**: FFmpeg (required for transcoding)
- **Queue System**: Laravel Queue (configurable)

## System Architecture

### Models

- **Files**: Media files with metadata (name, duration, path)
- **Segments**: HLS segments generated from media files
- **Events**: Scheduled broadcast events linking files to timeslots
- **Playlists**: Generated playlists mapping segments to play times

### Background Jobs

- **TranscodeFile**: Converts uploaded media to HLS format using FFmpeg
- **PrepareSegments**: Processes HLS segments after transcoding
- **CreatePlaylist**: Generates playlists automatically when events are created

### Workflow

1. Upload media file → File record created
2. TranscodeFile job triggers → FFmpeg transcodes to HLS
3. PrepareSegments job processes segments
4. Create event with scheduled time → Event record created
5. CreatePlaylist job generates playlist with precise timings

## Requirements

- PHP 8.1 or higher
- Composer
- Node.js and NPM
- MySQL 5.7+ or SQLite
- FFmpeg (for video transcoding)
- Hardware acceleration support (h264_videotoolbox for macOS, or configure for your platform)

## Installation

1. Clone the repository:
```bash
git clone https://github.com/stukenov/broadcast-playout.git
cd broadcast-playout
```

2. Install dependencies:
```bash
composer install
npm install
```

3. Copy environment file and configure:
```bash
cp .env.example .env
```

4. Generate application key:
```bash
php artisan key:generate
```

5. Configure your database in `.env`:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=broadcast_playout
DB_USERNAME=root
DB_PASSWORD=your_password
```

6. Run migrations:
```bash
php artisan migrate
```

7. Create storage link:
```bash
php artisan storage:link
```

8. Build frontend assets:
```bash
npm run build
# or for development:
npm run dev
```

9. Configure queue driver in `.env` (recommended for production):
```
QUEUE_CONNECTION=database
# or use redis, beanstalkd, etc.
```

10. Start queue worker (in separate terminal):
```bash
php artisan queue:work
```

11. Serve the application:
```bash
php artisan serve
```

Visit `http://localhost:8000` to access the application.

## FFmpeg Configuration

The system uses FFmpeg for transcoding. Default settings in `TranscodeFile.php`:

- Video codec: h264_videotoolbox (macOS hardware acceleration)
- Audio codec: AAC
- Bitrate: 2000k video, 128k audio
- Resolution: 1280x720
- Frame rate: 30fps
- HLS segment duration: 10 seconds

**For Linux servers**, update the FFmpeg command in `app/Jobs/TranscodeFile.php`:
```php
// Replace h264_videotoolbox with:
-c:v libx264  // Software encoding
// or
-c:v h264_nvenc  // NVIDIA GPU encoding
```

## Usage

### File Management

1. Navigate to Files section
2. Click "Upload" to add new media files
3. System automatically transcodes files to HLS format
4. View file list with duration and status

### Event Scheduling

1. Navigate to Events section
2. Click "New Event"
3. Select media file and set start time
4. System automatically calculates end time based on file duration
5. Playlist is generated automatically upon event creation

### Playlists

Playlists are automatically generated when events are created. Each playlist contains:
- Event reference
- Segment references
- Precise play times for each segment

## Configuration

### Storage

Configure storage disk in `config/filesystems.php`. Default uses public disk.

### Queue Connection

For production, configure queue driver in `.env`:
- Database: `QUEUE_CONNECTION=database`
- Redis: `QUEUE_CONNECTION=redis`
- Sync (development only): `QUEUE_CONNECTION=sync`

### Session

Application uses database sessions. Configure in `.env`:
```
SESSION_DRIVER=database
SESSION_LIFETIME=120
```

## Database Schema

### files
- id, name, duration, path, timestamps

### segments
- id, name, position, duration, start, end, file_id, timestamps

### events
- id, file_id, start_time, end_time, timestamps

### playlists
- id, event_id, segments_id, play_time, timestamps

## Development

### Running Tests
```bash
php artisan test
```

### Code Style
```bash
./vendor/bin/pint
```

### Clear Caches
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

## Production Deployment

1. Set environment to production in `.env`:
```
APP_ENV=production
APP_DEBUG=false
```

2. Optimize application:
```bash
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

3. Configure queue worker as system service (systemd example):
```ini
[Unit]
Description=Broadcast Playout Queue Worker

[Service]
User=www-data
WorkingDirectory=/path/to/broadcast-playout
ExecStart=/usr/bin/php artisan queue:work --tries=3

[Install]
WantedBy=multi-user.target
```

4. Set up cron for scheduled tasks:
```
* * * * * cd /path/to/broadcast-playout && php artisan schedule:run >> /dev/null 2>&1
```

## Security

- Keep `.env` file secure and never commit to version control
- Use strong `APP_KEY` (generated by `php artisan key:generate`)
- Configure proper file permissions (storage and bootstrap/cache writable)
- Use HTTPS in production
- Keep dependencies updated regularly

## Troubleshooting

### FFmpeg not found
Install FFmpeg and ensure it's in system PATH:
```bash
# Ubuntu/Debian
sudo apt-get install ffmpeg

# macOS
brew install ffmpeg

# Verify installation
ffmpeg -version
```

### Storage permissions
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### Queue jobs not processing
Ensure queue worker is running:
```bash
php artisan queue:work
```

Check failed jobs:
```bash
php artisan queue:failed
```

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

This project is open-sourced software licensed under the [MIT license](LICENSE).

## Credits

Developed by Saken Tukenov

## Support

For issues and questions, please use the [GitHub issue tracker](https://github.com/stukenov/broadcast-playout/issues).
