# Blog Management System with Quill Editor

## Overview
A complete database-driven blog management system with rich text editing using Quill.js editor.

## Features
- ✅ Rich text editor (Quill.js) with full formatting options
- ✅ Database storage for all blog content
- ✅ Create, Edit, Delete blog posts
- ✅ Draft and Published status
- ✅ SEO-friendly slugs (auto-generated from title)
- ✅ Featured images
- ✅ Excerpt support
- ✅ Responsive design
- ✅ Pagination on blog listing page

## Setup Instructions

### 1. Run Database Migration
Visit: `https://kamateraho.com/setup_blog.php`

This will create the `blog_posts` table with the following structure:
- id (Primary Key)
- title
- slug (URL-friendly, unique)
- excerpt
- content (Rich HTML from Quill)
- image_url
- author
- status (draft/published)
- created_at
- updated_at

### 2. Access Admin Panel
Go to: `https://kamateraho.com/admin/manage_blog.php`

Here you can:
- Create new blog posts with Quill editor
- Edit existing posts
- Delete posts
- Change status (draft/published)
- Auto-generate slugs from titles

### 3. View Blog Posts

**Blog Listing Page:**
`https://kamateraho.com/blog_list.php`
- Shows all published posts
- Pagination support
- Grid layout

**Individual Blog Post:**
`https://kamateraho.com/blog.php?slug=your-post-slug`
- Clean, readable layout
- Displays rich formatted content
- SEO-friendly URLs

## Files Created/Modified

### New Files:
1. `admin/manage_blog.php` - Admin interface with Quill editor
2. `blog.php` - Single blog post display
3. `blog_list.php` - Blog listing with pagination
4. `setup_blog.php` - Database setup script
5. `database/create_blog_table.sql` - SQL schema
6. `database/migrate_blog.php` - Migration script

## Quill Editor Features

The editor includes:
- Headers (H1-H6)
- Font styles and sizes
- Bold, Italic, Underline, Strike
- Text color and background
- Lists (ordered/unordered)
- Indentation
- Text alignment
- Blockquotes and code blocks
- Links, images, and videos
- Clean formatting option

## Usage

### Creating a Blog Post:
1. Go to admin/manage_blog.php
2. Fill in the title (slug auto-generates)
3. Add excerpt for listing page
4. Add featured image URL
5. Use Quill editor to write rich content
6. Select status (draft/published)
7. Click "Create Blog Post"

### Editing a Blog Post:
1. Click "Edit" button in the blog list
2. Modify content in Quill editor
3. Click "Update Blog Post"

### Viewing Posts:
- Published posts appear on blog_list.php
- Click "Read More" to view full post
- Direct access via slug: blog.php?slug=post-slug

## Database Schema

```sql
CREATE TABLE blog_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    excerpt TEXT,
    content LONGTEXT NOT NULL,
    image_url VARCHAR(500),
    author VARCHAR(100) DEFAULT 'Admin',
    status ENUM('draft', 'published') DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

## Benefits Over Old System

### Old System (File-based):
- ❌ Hard-coded HTML in PHP files
- ❌ Manual file management
- ❌ No rich text editor
- ❌ Difficult to edit
- ❌ No draft system

### New System (Database-driven):
- ✅ All data in database
- ✅ Rich text editor (Quill)
- ✅ Easy content management
- ✅ Draft/Published workflow
- ✅ SEO-friendly slugs
- ✅ Scalable and maintainable

## Security Notes

- All user inputs are sanitized
- PDO prepared statements prevent SQL injection
- XSS protection with htmlspecialchars()
- Only published posts visible to public
- Admin authentication required (via existing admin system)

## Next Steps (Optional Enhancements)

1. Add categories/tags
2. Add search functionality
3. Add comments system
4. Add social sharing buttons
5. Add related posts
6. Add image upload functionality
7. Add SEO meta fields
8. Add reading time estimate

## Support

For issues or questions, check the database connection in `config/db.php` and ensure the blog_posts table exists.
