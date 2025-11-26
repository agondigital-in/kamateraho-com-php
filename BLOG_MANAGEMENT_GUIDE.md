# Blog Management System Guide

## Overview
This guide explains how to use the new blog management system in the KamateRaho admin panel.

## Accessing Blog Management
1. Log in to the admin panel
2. In the left sidebar, click on "Manage Blog" under the navigation menu

## Creating a New Blog Post
1. Navigate to the "Manage Blog" section
2. Fill in the following fields:
   - **Title**: The main title of your blog post
   - **Date**: Publication date (defaults to current date)
   - **Author**: Author name (defaults to "Admin")
   - **Excerpt**: A short description of the blog post
   - **Image URL**: URL to the featured image for the post
   - **Content**: The full content of your blog post (HTML allowed)
3. Click "Create Blog Post"
4. The system will:
   - Automatically generate the next post number (e.g., post22.php, post23.php)
   - Create the blog post file in `/kamateraho/blog/`
   - Automatically update the blog index page to include the new post

## Managing Existing Blog Posts
The "Manage Blog" page shows a table of all existing blog posts with:
- Post number
- Title
- Date
- Author
- Actions (View, Delete)

## Blog Post Template
A template file is available at `/admin/blog_post_template.php` for reference when creating custom blog posts manually.

## File Structure
- Blog posts are stored in `/kamateraho/blog/post{number}.php`
- The blog index is located at `/kamateraho/blog/index.php`
- All blog posts follow a consistent structure with:
  - Header with title and description
  - Featured image
  - Content area with proper styling
  - Back to blog link

## Best Practices
1. Use high-quality, relevant images for blog posts
2. Write compelling excerpts to encourage clicks
3. Use proper HTML formatting in content (headings, lists, paragraphs)
4. Keep blog posts organized with clear headings
5. Test blog posts after creation to ensure proper display

## Troubleshooting
If a blog post doesn't appear on the index page:
1. Check that the file was created in `/kamateraho/blog/`
2. Verify the file has proper read permissions
3. Clear browser cache to see updates

For any issues with the blog management system, contact the development team.