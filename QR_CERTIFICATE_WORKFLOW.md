# QR Code Certificate Workflow Guide

## Overview
This document describes the new QR code and certificate upload workflow implemented in the IDG system.

## Workflow Steps

### 1. Generate QR Code
- **Location**: Dashboard → Evaluated Artifacts
- **Button**: "Generate QR" (📱 إنشاء رمز QR)
- **Action**: Downloads a PNG QR code file for the specific artifact
- **File Name**: `QR-{artifact_code}.png`
- **Size**: 300x300 pixels
- **Purpose**: To be embedded in external certificate documents

### 2. External Certificate Creation
- Use the downloaded QR code in your external certificate design software
- Embed the QR code into the PDF certificate
- Ensure the QR code is clearly visible and scannable

### 3. Upload Certificate
- **Location**: Dashboard → Evaluated Artifacts  
- **Button**: "Upload Certificate" (📄 رفع شهادة)
- **Requirements**:
  - PDF format only
  - Maximum file size: 10MB
  - QR code must be generated first
- **Action**: Stores the certificate PDF in the system

### 4. QR Code Verification
- **Public URL**: `/verify-artifact/{token}`
- **Access**: Anyone can scan the QR code
- **Display**: Shows either uploaded certificate or evaluation data

## Technical Details

### Database Changes
- Added `qr_token` and `qr_code_path` to `artifacts` table
- Added `uploaded_certificate_path` and `uploaded_at` to `certificates` table

### New Routes
```php
// QR Code Download
GET /artifacts/{artifact}/download-qr

// Certificate Upload  
POST /artifacts/{artifact}/upload-certificate

// Public Verification
GET /verify-artifact/{token}
```

### File Storage
- QR codes: `public/storage/qr-codes/`
- Uploaded certificates: `public/storage/certificates/`

## User Interface

### Arabic/English Support
All new features support both Arabic and English languages:

#### Arabic Translations
- "Generate QR" → "إنشاء رمز QR"
- "Upload Certificate" → "رفع شهادة"
- "Download QR Code" → "تحميل رمز QR"

#### Button Colors
- **Generate QR**: Orange (`bg-orange-600`)
- **Upload Certificate**: Indigo (`bg-indigo-600`)
- **View Report**: Blue (`bg-blue-600`)
- **Print**: Green (`bg-green-600`)
- **Certificate**: Purple (`bg-purple-600`)

## Security Features

### QR Code Security
- Unique token for each artifact
- 32-character random string
- Links to secure verification endpoint

### File Upload Security
- PDF validation
- File size limits (10MB)
- Secure file naming with timestamps
- Protected storage location

## Error Handling

### Common Errors
1. **No QR Code Generated**: Must generate QR code before uploading certificate
2. **Invalid File Type**: Only PDF files are accepted
3. **File Too Large**: Maximum 10MB file size
4. **No Evaluation**: Artifact must be evaluated before QR generation

### Error Messages
- Arabic and English error messages
- Clear user feedback
- Validation at both frontend and backend

## Public Verification

### Verification Page Features
1. **Uploaded Certificate Display**:
   - Shows certificate metadata
   - Provides link to view PDF
   - Verification status indicator

2. **Evaluation Data Display**:
   - Comprehensive artifact information
   - Detailed evaluation results
   - Authentication verification

### Responsive Design
- Mobile-friendly verification pages
- Clear visual indicators
- Professional appearance for public viewing

## Usage Examples

### For Evaluators
1. Complete artifact evaluation
2. Click "Generate QR" to download QR code
3. Provide QR code to certificate designer
4. Receive completed PDF certificate
5. Click "Upload Certificate" to store in system

### For Certificate Designers
1. Receive QR code PNG file
2. Design certificate in external software
3. Embed QR code in appropriate location
4. Export as PDF
5. Return to evaluator for upload

### For Public Verification
1. Scan QR code with mobile device
2. View verification page in browser
3. Confirm certificate authenticity
4. Access detailed evaluation information

## Best Practices

### QR Code Placement
- Place QR code in visible but non-intrusive location
- Ensure sufficient contrast for scanning
- Maintain minimum size for readability

### Certificate Design
- Include IDG branding elements
- Maintain professional appearance
- Ensure QR code doesn't interfere with certificate content

### File Management
- Use descriptive filenames
- Maintain organized file structure
- Regular backup of uploaded certificates

## Troubleshooting

### QR Code Not Downloading
- Check if artifact has completed evaluation
- Verify user permissions
- Check browser download settings

### Upload Failures
- Confirm file is PDF format
- Check file size (must be under 10MB)
- Ensure QR code was generated first
- Verify network connection

### Verification Issues
- Check QR code quality and scannability
- Verify URL is accessible
- Confirm certificate was uploaded successfully

## Future Enhancements

### Planned Features
- Batch QR code generation
- Certificate template system
- Advanced verification analytics
- Integration with external design tools

### API Extensions
- RESTful API for QR generation
- Webhook notifications for uploads
- Third-party integration support

## Support

For technical support or questions about the QR certificate workflow:
- Contact IDG technical team
- Refer to system documentation
- Check error logs for detailed diagnostics 