# Promotion Tasks Layout Changes

## Overview
Modified the layout of "Trending Promotion Tasks" and "Best Promotion Tasks For You To Start" sections to start from the left side instead of being centered.

## Changes Made

### 1. Best Promotion Tasks For You To Start Section
- Changed the heading alignment from centered to left-aligned
- Removed `text-center w-100` classes from the h2 element
- Kept only `text-primary` class for styling

### 2. Trending Promotion Tasks Section
- Changed the div class from `text-center` to `text-start`
- This ensures the heading starts from the left side of the container

## Files Modified
- `index.php` - Main homepage file containing the promotion tasks sections

## Verification
Both sections now start from the left side of the page as requested:
- "Best Promotion Tasks For You To Start" section heading is left-aligned
- "Trending Promotion Tasks" section heading is left-aligned
- All other content maintains its original styling and functionality

The changes are minimal and focused only on the alignment, preserving all other design elements and functionality.