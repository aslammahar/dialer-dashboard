# Postman Testing Guide for Recording API

## Base Configuration

**Base URL:** `https://dial4leads.site/api`

**API Key:** Set this in your `.env` file as `RECORDING_API_KEY` and use it in Postman.

**Authentication Methods (All Supported):**
1. **Authorization Header (Recommended):** `Authorization: Bearer your-api-key-here`
2. **X-API-Key Header:** `X-API-Key: your-api-key-here`
3. **Query Parameter:** `?api_key=your-api-key-here`

---

## 1. Get Recordings to Download

### Request Details
- **Method:** `GET`
- **URL:** `https://dial4leads.site/api/recordings/to-download`
- **Headers (Option 1 - Recommended):**
  ```
  Authorization: Bearer your-api-key-here
  Accept: application/json
  ```
- **Headers (Option 2 - Alternative):**
  ```
  X-API-Key: your-api-key-here
  Accept: application/json
  ```

### Postman Setup (Using Authorization Header):
1. Create a new request
2. Set method to **GET**
3. Enter URL: `https://dial4leads.site/api/recordings/to-download`
4. Go to **Authorization** tab
5. Select **Type:** `Bearer Token`
6. Enter **Token:** `your-api-key-here` (from your .env file)
7. Go to **Headers** tab
8. Add header:
   - Key: `Accept`
   - Value: `application/json`

### Postman Setup (Using X-API-Key Header):
1. Create a new request
2. Set method to **GET**
3. Enter URL: `https://dial4leads.site/api/recordings/to-download`
4. Go to **Headers** tab
5. Add header:
   - Key: `X-API-Key`
   - Value: `your-api-key-here` (from your .env file)
6. Add header:
   - Key: `Accept`
   - Value: `application/json`

### Expected Response:
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "recording_link": "http://...",
            "lead_id": "12345",
            "status": null,
            "created_at": "2024-01-15 10:30:00",
            "month": "2024-01"
        }
    ],
    "count": 10
}
```

---

## 2. Update Recording Status (Single)

### Request Details
- **Method:** `POST`
- **URL:** `https://dial4leads.site/api/recordings/update-status`
- **Headers (Option 1 - Recommended):**
  ```
  Authorization: Bearer your-api-key-here
  Content-Type: application/json
  Accept: application/json
  ```
- **Headers (Option 2 - Alternative):**
  ```
  X-API-Key: your-api-key-here
  Content-Type: application/json
  Accept: application/json
  ```
- **Body (JSON):**
```json
{
    "recording_link": "http://server.com/recording.mp3",
    "status": "downloaded"
}
```

### Postman Setup (Using Authorization Header):
1. Create a new request
2. Set method to **POST**
3. Enter URL: `https://dial4leads.site/api/recordings/update-status`
4. Go to **Authorization** tab
5. Select **Type:** `Bearer Token`
6. Enter **Token:** `your-api-key-here`
7. Go to **Headers** tab
8. Add headers:
   - `Content-Type`: `application/json`
   - `Accept`: `application/json`
6. Go to **Body** tab
7. Select **raw** and **JSON**
8. Paste the JSON body above (update values as needed)

### Valid Status Values:
- `downloaded`
- `OK`
- `uploaded`
- `Wrong`

### Expected Response:
```json
{
    "success": true,
    "message": "Recording status updated successfully",
    "data": {
        "id": 1,
        "recording_link": "http://...",
        "status": "downloaded",
        ...
    }
}
```

---

## 3. Batch Update Recording Statuses

### Request Details
- **Method:** `POST`
- **URL:** `https://dial4leads.site/api/recordings/batch-update-status`
- **Headers:**
  ```
  Authorization: Bearer your-api-key-here
  Content-Type: application/json
  Accept: application/json
  ```
- **Body (JSON):**
```json
{
    "updates": [
        {
            "recording_link": "http://server.com/recording1.mp3",
            "status": "downloaded"
        },
        {
            "recording_link": "http://server.com/recording2.mp3",
            "status": "OK"
        }
    ]
}
```

### Postman Setup:
1. Create a new request
2. Set method to **POST**
3. Enter URL: `https://dial4leads.site/api/recordings/batch-update-status`
4. Add same headers as above
5. Body: JSON with array of updates

### Expected Response:
```json
{
    "success": true,
    "message": "Updated 2 recordings",
    "updated": 2,
    "failed": []
}
```

---

## 4. Update Avatar Lead Recording Link

### Request Details
- **Method:** `POST`
- **URL:** `https://dial4leads.site/api/avatar-leads/update-recording-link`
- **Headers:**
  ```
  Authorization: Bearer your-api-key-here
  Content-Type: application/json
  Accept: application/json
  ```
- **Body (JSON):**
```json
{
    "lead_id": "12345",
    "recording_link": "https://your-server.com/recordings/recording.mp3"
}
```

### Postman Setup:
1. Create a new request
2. Set method to **POST**
3. Enter URL: `https://dial4leads.site/api/avatar-leads/update-recording-link`
4. Add headers (same as above)
5. Body: JSON with lead_id and recording_link

### Expected Response:
```json
{
    "success": true,
    "message": "Recording link updated successfully",
    "data": {
        "lead_id": "12345",
        "recording_link": "https://your-server.com/recordings/recording.mp3"
    }
}
```

---

## 5. Get Avatar Leads Count

### Request Details
- **Method:** `GET`
- **URL:** `https://dial4leads.site/api/avatar-leads/count`
- **Headers:**
  ```
  Authorization: Bearer your-api-key-here
  Accept: application/json
  ```

### Postman Setup:
1. Create a new request
2. Set method to **GET**
3. Enter URL: `https://dial4leads.site/api/avatar-leads/count`
4. Add headers (X-API-Key and Accept)

### Expected Response:
```json
{
    "success": true,
    "count": 1234
}
```

---

## Postman Collection Setup (Recommended)

### Create a Postman Environment:

1. Click **Environments** in left sidebar
2. Click **+** to create new environment
3. Name it: `Dial4Leads Production`
4. Add variables:
   - `base_url`: `https://dial4leads.site/api`
   - `api_key`: `your-api-key-here`
5. Save

### Use Environment Variables:

In your requests, use:
- URL: `{{base_url}}/recordings/to-download`
- Header value: `{{api_key}}`

This makes it easy to switch between environments (dev/staging/production).

---

## Common Error Responses

### 401 Unauthorized
```json
{
    "success": false,
    "message": "Invalid or missing API key"
}
```
**Solution:** Check that your API key matches the one in `.env` file.

### 404 Not Found
**Solution:** Verify the URL is correct and includes `/api` prefix.

### 422 Validation Error
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "recording_link": ["The recording link field is required."],
        "status": ["The selected status is invalid."]
    }
}
```
**Solution:** Check that all required fields are present and status values are valid.

### 500 Server Error
```json
{
    "success": false,
    "message": "Error retrieving recordings: ..."
}
```
**Solution:** Check Laravel logs at `storage/logs/laravel.log` for details.

---

## Quick Test Checklist

- [ ] API key is set in `.env` file
- [ ] API key matches in Postman header
- [ ] Base URL includes `/api` prefix
- [ ] Content-Type header is set for POST requests
- [ ] JSON body is properly formatted
- [ ] Status values use lowercase (e.g., `downloaded` not `Downloaded`)

---

## Testing Tips

1. **Start with GET requests** - They're simpler and help verify API key works
2. **Test with invalid API key first** - Should return 401
3. **Check response status codes** - 200 = success, 401 = auth error, 422 = validation error
4. **Use Postman Console** - View > Show Postman Console to see full request/response
5. **Save successful requests** - Create a collection for easy reuse

---

## Example: Complete Test Flow

1. **Get count** → Verify API connection works
2. **Get recordings** → See what needs downloading
3. **Update single status** → Test single update
4. **Batch update** → Test multiple updates
5. **Update avatar lead link** → Test link update

---

## Need Help?

- Check Laravel logs: `storage/logs/laravel.log`
- Verify API key in `.env`: `RECORDING_API_KEY=...`
- Test with curl first to isolate Postman issues
- Ensure CORS is configured if testing from browser
