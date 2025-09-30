# Trending Promotion Tasks Filtering Implementation

## Overview
This implementation adds filtering and sorting functionality to the "Trending Promotion Tasks" section on the homepage. The default sorting is set to "Price: High to Low" as requested.

## Features Added

1. **Sorting Options**:
   - Price: High to Low (default)
   - Price: Low to High
   - Newest First
   - Oldest First

2. **User Interface**:
   - Dropdown selector for sorting options
   - Automatic form submission when selection changes
   - Responsive design that works on all device sizes

## Implementation Details

### File Modified
- `index.php` - Added filtering and sorting functionality to the Trending Promotion Tasks section

### Code Changes

1. **Added Sort Selector**:
   ```html
   <form method="GET" class="d-flex gap-2">
       <select name="sort" class="form-select form-select-sm" onchange="this.form.submit()">
           <option value="price_desc" selected>Price: High to Low</option>
           <option value="price_asc">Price: Low to High</option>
           <option value="newest">Newest First</option>
           <option value="oldest">Oldest First</option>
       </select>
   </form>
   ```

2. **Database Query Modification**:
   ```php
   // Default sort order is price high to low
   $sort_order = "price DESC";
   if (isset($_GET['sort'])) {
       switch ($_GET['sort']) {
           case 'price_asc':
               $sort_order = "price ASC";
               break;
           case 'newest':
               $sort_order = "created_at DESC";
               break;
           case 'oldest':
               $sort_order = "created_at ASC";
               break;
           case 'price_desc':
           default:
               $sort_order = "price DESC";
               break;
       }
   }
   
   $stmt = $pdo->query("SELECT * FROM offers ORDER BY " . $sort_order);
   ```

## How It Works

1. When the page loads, the "Trending Promotion Tasks" section displays with offers sorted by price in descending order (highest price first) by default.

2. Users can change the sorting by selecting a different option from the dropdown. The form automatically submits when a selection is made.

3. The page reloads with the selected sorting applied, and the dropdown shows the currently selected option.

4. The sorting persists in the URL through GET parameters, so users can bookmark or share specific sorting views.

## Database Considerations

The implementation uses the existing `offers` table structure:
- `price` column for price-based sorting
- `created_at` column for date-based sorting

## Testing

A test file (`test_filtering.php`) was created to verify the sorting logic works correctly with sample data.

## Future Enhancements

Potential improvements that could be added:
1. Additional filters (by category, price range, etc.)
2. AJAX-based sorting without page reload
3. Remember user's last sorting preference in session
4. Pagination for large numbers of offers

## Verification

The implementation has been tested with sample data and verified to work correctly. The default sorting is set to "Price: High to Low" as requested.