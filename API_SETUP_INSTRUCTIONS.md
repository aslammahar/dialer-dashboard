# Recording Download API Setup Instructions

## Overview
This solution replaces direct database access with secure API endpoints for downloading recordings and updating the database.

## Setup Steps

### 1. Set API Key in Environment
Add the following to your `.env` file:
```env
RECORDING_API_KEY=your-secret-api-key-here-change-this
```

**Important:** Use a strong, random API key. You can generate one using:
```bash
php -r "echo bin2hex(random_bytes(32));"
```

### 2. Update the PHP Script
Edit `recording-download-script.php` and update these configuration values:

```php
$api_base_url = 'https://your-domain.com/api'; // Change to your Laravel API URL
$api_key = 'your-secret-api-key-here'; // Must match RECORDING_API_KEY in .env
$downloaded_files_dir = '/var/www/RecordingsProgram/recordings'; // Local directory
```

### 3. API Endpoints Available

#### Get Recordings to Download
- **URL:** `GET /api/recordings/to-download`
- **Headers:** `X-API-Key: your-api-key`
- **Returns:** List of recordings that need downloading

#### Update Recording Status
- **URL:** `POST /api/recordings/update-status`
- **Headers:** `X-API-Key: your-api-key`
- **Body:**
```json
{
    "recording_link": "http://...",
    "status": "Downloaded"
}
```

#### Batch Update Recording Statuses
- **URL:** `POST /api/recordings/batch-update-status`
- **Headers:** `X-API-Key: your-api-key`
- **Body:**
```json
{
    "updates": [
        {
            "recording_link": "http://...",
            "status": "Downloaded"
        },
        {
            "recording_link": "http://...",
            "status": "OK"
        }
    ]
}
```

#### Update Avatar Lead Recording Link
- **URL:** `POST /api/avatar-leads/update-recording-link`
- **Headers:** `X-API-Key: your-api-key`
- **Body:**
```json
{
    "lead_id": "12345",
    "recording_link": "http://new-link.com/recording.mp3"
}
```

#### Get Avatar Leads Count
- **URL:** `GET /api/avatar-leads/count`
- **Headers:** `X-API-Key: your-api-key`
- **Returns:** Total count of avatar leads

## Security Notes

1. **API Key Security:**
   - Never commit the API key to version control
   - Use different keys for development and production
   - Rotate keys periodically

2. **HTTPS:**
   - Always use HTTPS in production
   - The script disables SSL verification for development only

3. **IP Whitelisting (Optional):**
   - Consider adding IP whitelisting to the middleware for additional security

## Testing

Test the API endpoints using curl:

```bash
# Get recordings to download
curl -H "X-API-Key: your-api-key" \
     https://your-domain.com/api/recordings/to-download

# Update recording status
curl -X POST \
     -H "X-API-Key: your-api-key" \
     -H "Content-Type: application/json" \
     -d '{"recording_link":"http://...","status":"Downloaded"}' \
     https://your-domain.com/api/recordings/update-status
```

## Troubleshooting

1. **401 Unauthorized:** Check that the API key matches in both `.env` and the PHP script
2. **404 Not Found:** Verify the API base URL is correct
3. **500 Server Error:** Check Laravel logs in `storage/logs/laravel.log`

## Migration from Old Script

The new script (`recording-download-script.php`) is a drop-in replacement that:
- Uses API calls instead of direct database access
- Maintains the same functionality
- Provides better error handling
- Supports batch operations for better performance

Simply replace the old script with the new one and update the configuration.
