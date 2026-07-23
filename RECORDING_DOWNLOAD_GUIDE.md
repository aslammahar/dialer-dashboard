# Recording Download System

## Overview
This system automatically downloads recording files from the `recordings` table and stores them locally in the project. The downloaded files are then linked to the `avatar_leads` table for QA users to access.

## Components

### 1. RecordingDownloadService
**Location:** `app/Services/RecordingDownloadService.php`

This service handles:
- Downloading recordings from URLs in the `recordings` table
- Storing files in `storage/app/public/recordings/`
- Updating `recordings.status` to 'downloaded'
- Updating `avatar_leads.recording_link` with local file path
- Error handling and skipping failed downloads

### 2. DownloadRecordingsCommand
**Location:** `app/Console/Commands/DownloadRecordingsCommand.php`

Artisan command to download recordings via CLI:
```bash
php artisan recordings:download
php artisan recordings:download --limit=100
php artisan recordings:download --batch=50
```

### 3. API Endpoint
**Route:** `POST /api/recordings/download`

**Request:**
```json
{
    "limit": 100  // Optional: limit number of recordings to process
}
```

**Response:**
```json
{
    "success": true,
    "message": "Recording download process completed",
    "data": {
        "total": 50,
        "downloaded": 45,
        "failed": 3,
        "skipped": 2,
        "errors": [...]
    }
}
```

## How It Works

1. **Finds Recordings to Download:**
   - Queries `recordings` table where `status != 'downloaded'` or `status IS NULL`
   - Only processes recordings with both `recording_link` and `lead_id`

2. **Downloads Files:**
   - Downloads MP3 files from the `recording_link` URL
   - Validates file content (checks for valid audio format)
   - Handles timeouts and network errors gracefully
   - Skips files that fail to download

3. **Stores Files:**
   - Saves files to `storage/app/public/recordings/`
   - Filename format: `{recording_filename}.mp3` or `recording_{lead_id}_{timestamp}.mp3`

4. **Updates Database:**
   - Sets `recordings.status = 'downloaded'`
   - Updates `avatar_leads.recording_link = '/storage/recordings/{filename}.mp3'`

## Setup Instructions

### 1. Create Storage Symlink
Make sure the storage symlink exists so files are accessible via web:
```bash
php artisan storage:link
```

This creates a symlink from `public/storage` to `storage/app/public`, allowing files to be accessed via `/storage/recordings/filename.mp3`

### 2. Create Storage Directory
The directory will be created automatically, but you can create it manually:
```bash
mkdir -p storage/app/public/recordings
chmod -R 775 storage/app/public/recordings
```

### 3. Run the Download Command
```bash
# Download all pending recordings
php artisan recordings:download

# Download with limit
php artisan recordings:download --limit=50
```

### 4. Schedule Automatic Downloads (Optional)
Add to `app/Console/Kernel.php`:
```php
protected function schedule(Schedule $schedule)
{
    $schedule->command('recordings:download --limit=100')
             ->hourly()
             ->withoutOverlapping();
}
```

## QA Blade Files

The following blade files use `recording_link` from `avatar_leads` table:
- `resources/views/qa-section/index.blade.php` (Line 177-180)
- `resources/views/qa-section/inline_edit.blade.php` (Line 323-325)

These files already have audio players that will automatically use the local file path once downloads are complete.

## Error Handling

The system handles various error scenarios:
- **Invalid URLs:** Skipped with warning
- **Network timeouts:** Skipped after 30 seconds
- **Invalid file formats:** Skipped if not valid audio
- **Missing lead_id:** Skipped
- **Database errors:** Logged and reported

All errors are logged to `storage/logs/laravel.log` and included in the response statistics.

## Database Schema

### Recordings Table
- `id` - Primary key
- `recording_link` - Original URL to download from
- `status` - 'downloaded' when file is stored locally
- `lead_id` - Links to avatar_leads
- `server_ip`, `recording_filename`, `dialer_name`, `dialer_id` - Additional metadata

### Avatar Leads Table
- `recording_link` - Updated with local path: `/storage/recordings/{filename}.mp3`

## Troubleshooting

### Files Not Accessible
1. Check if symlink exists: `ls -la public/storage`
2. Create symlink: `php artisan storage:link`
3. Check file permissions: `chmod -R 775 storage/app/public`

### Downloads Failing
1. Check network connectivity
2. Verify URLs are accessible
3. Check disk space: `df -h`
4. Review logs: `tail -f storage/logs/laravel.log`

### Status Not Updating
1. Check database connection
2. Verify lead_id exists in avatar_leads table
3. Check for database transaction errors in logs

## Notes

- Files are stored in `storage/app/public/recordings/` for web accessibility
- Original `recording_link` URLs are preserved in the `recordings` table
- The system skips files that already exist locally
- Batch processing is recommended for large numbers of recordings
- Consider setting up a cron job for automatic downloads
