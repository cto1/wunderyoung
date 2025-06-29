# PDF Download System Testing Guide - Direct API

## Setup Instructions

1. **Import the Postman Collection**
   - Download `PDF_Download_Test.postman_collection.json`
   - Open Postman → Import → Select the JSON file

2. **Configure Variables**
   - Update `base_url` to your server URL (currently set to `https://demo.yeshomework.com`)
   - Update `test_token` to a valid download token
   - Set `jwt_token` to a valid JWT token (get from login endpoint)
   - Set `child_id` to a valid child ID

## Test Sequence - Direct API Routes Only

**Run these tests IN ORDER:**

### 1. API Health Check
**Route:** `GET /api/health`
**Expected Result:** 
```json
{
  "status": "success",
  "message": "Yes Homework API is running",
  "timestamp": "2025-06-29 11:19:58",
  "version": "1.0.0"
}
```

### 2. Create Download Token ⭐ **NEW**
**Route:** `POST /api/download-tokens/create`
**Headers:** `Authorization: Bearer {jwt_token}`
**Body:** 
```json
{
  "child_id": 1,
  "date": "2025-06-29"
}
```
**Expected Result:**
```json
{
  "status": "success",
  "token": "dl_abc123...",
  "download_url": "https://yoursite.com/download.php?token=dl_abc123...",
  "child_id": "1",
  "date": "2025-06-29",
  "message": "Download token created successfully"
}
```

### 3. Get Token Info (Direct API)
**Route:** `GET /api/download-tokens/{token}`
**Expected Result:** 
```json
{
  "status": "success",
  "token_data": {
    "child_name": "Boris",
    "child_id": "123",
    "date": "2025-06-29",
    "age_group": "...",
    "interest1": "...",
    "interest2": "..."
  }
}
```

### 4. Submit Feedback (Direct API)
**Route:** `POST /api/feedback`
**Expected Result:**
```json
{
  "status": "success",
  "message": "Feedback submitted successfully",
  "feedback_id": "5"
}
```

### 5. Test PDF Debug Route ⭐ **KEY TEST**
**Route:** `GET /api/debug/pdf/{token}`
**Expected Result:**
```json
{
  "status": "success",
  "message": "PDF generation components working",
  "child_name": "Boris",
  "content_length": 2259,
  "tcpdf_available": true,
  "dompdf_available": true,
  "worksheet_content": "..."
}
```

### 6. Generate Worksheet from Token
**Route:** `POST /api/download-tokens/{token}/generate`
**Expected Result:**
```json
{
  "status": "success",
  "worksheet_id": 123,
  "message": "Worksheet generated and ready for download"
}
```

### 7. Direct PDF Download ⭐ **ACTUAL PDF TEST**
**Route:** `GET /api/DownloadAPI.php?token={token}`
**Expected Result:** 
- Content-Type: `application/pdf`
- Content-Disposition: `attachment; filename="Boris_Worksheet_2025-06-29.pdf"`
- Binary PDF data (not JSON!)

## Troubleshooting Results

### ✅ If Tests 1-4 Pass:
- **API routing is working**
- **Token creation works**
- **Token validation works**
- **Feedback submission works**

### ✅ If Test 5 (Debug) Shows Libraries Available:
- **TCPDF/DOMPDF are loaded correctly**
- **Content generation works**
- **Problem is in PDF output/streaming**

### ✅ If Test 5 Fails:
- **PDF libraries not available**
- **Autoloader issue**
- **Content generation problem**

### ✅ If Test 7 Returns JSON Instead of PDF:
- **DownloadAPI.php has error handling issue**
- **Headers not being set correctly**
- **PDF stream is being buffered/corrupted**

## Key Differences from Proxy Testing

- **No proxy server involved** - tests core API directly
- **Clean routes** - `/api/health`, `/api/feedback`, etc.
- **Debug route available** - `/api/debug/pdf/{token}` shows what's working
- **Bypasses all proxy complications**

## What This Will Show Us

1. **If API routes work** (Tests 1-4)
2. **If token creation works** (Test 2)
3. **If PDF components are available** (Test 5)
4. **If content generation works** (Test 5)
5. **If PDF download works at all** (Test 7)

**This will tell us if the problem is:**
- ❌ **Core API issue** (Tests 1-4 fail)
- ❌ **Authentication issue** (Test 2 fails)
- ❌ **PDF library issue** (Test 5 fails)
- ❌ **PDF streaming issue** (Test 7 fails)

**Run these and report what Test 5 and Test 7 return!** 