# Cloudinary Setup for Blog Images

## Step 1: Get Cloudinary Credentials

1. Go to https://cloudinary.com/
2. Sign up for a free account (or login)
3. Go to Dashboard
4. Copy these values:
   - Cloud Name
   - API Key
   - API Secret

## Step 2: Add to .env File

Open your `.env` file and add these lines:

```env
CLOUDINARY_CLOUD_NAME=your_cloud_name_here
CLOUDINARY_API_KEY=your_api_key_here
CLOUDINARY_API_SECRET=your_api_secret_here
```

Replace the values with your actual Cloudinary credentials.

## Step 3: Update config/env.php

Make sure your `config/env.php` loads these variables:

```php
$_ENV['CLOUDINARY_CLOUD_NAME'] = getenv('CLOUDINARY_CLOUD_NAME');
$_ENV['CLOUDINARY_API_KEY'] = getenv('CLOUDINARY_API_KEY');
$_ENV['CLOUDINARY_API_SECRET'] = getenv('CLOUDINARY_API_SECRET');
```

## Step 4: Test Upload

1. Go to `admin/manage_blog.php`
2. Click "Upload Image" button
3. Select an image file
4. Image will be uploaded to Cloudinary
5. URL will be automatically filled in the Featured Image field

## Features:

- ✅ Direct upload to Cloudinary
- ✅ Automatic URL insertion
- ✅ Supports JPG, PNG, GIF, WebP
- ✅ Max file size: 5MB
- ✅ Images stored in `blog_images` folder on Cloudinary
- ✅ Progress indicator during upload

## Free Tier Limits:

- 25 GB storage
- 25 GB bandwidth per month
- 25,000 transformations per month

Perfect for your blog!
