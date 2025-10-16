# Credit Card UI Alignment with All Offers

## Overview
This implementation aligns the credit card UI in product_details.php with the UI design used in all_offers.php to ensure consistency across the application.

## Changes Made

### 1. CSS Styles Updated
- Added consistent button styles matching all_offers.php:
  - `.btn-earn-money` with gradient background
  - `.btn-outline-primary` for refer & earn button
- Added responsive styles for mobile devices
- Updated price tag and badge styling

### 2. Credit Card Display Section
- Modified the credit card information display to match the layout in all_offers.php
- Added category badge display
- Improved price/amount presentation with consistent styling
- Added proper spacing and alignment

### 3. Button Layout
- Changed from a single full-width button to a dual-button layout matching all_offers.php:
  - "Apply for This Offer" button (primary action)
  - "Refer & Earn" button (secondary action)
- Added copy link functionality to the refer button

### 4. JavaScript Functionality
- Added copy link functionality that matches the implementation in all_offers.php
- Implemented visual feedback when links are copied
- Added proper error handling

### 5. Responsive Design
- Ensured consistent responsive behavior across different screen sizes
- Added mobile-specific styles for buttons and text

## UI Elements Aligned

### Before (Old Credit Card UI)
- Single full-width "Apply for This Offer" button
- Basic amount display without proper styling
- No copy link functionality
- Inconsistent button styling

### After (Aligned with All Offers UI)
- Dual-button layout with "Apply for This Offer" and "Refer & Earn"
- Consistent price tag styling with proper formatting
- Copy link functionality with visual feedback
- Matching button styles and hover effects
- Category badges for better information hierarchy
- Responsive design that works on all device sizes

## Implementation Details

### CSS Changes
```css
.btn-earn-money {
    border: 2px solid #0d6efd !important;
    background: linear-gradient(135deg, #4361ee, #3a0ca3) !important;
    color: white !important;
    font-size: 0.85rem;
    padding: 0.375rem 0.5rem;
}

.btn-earn-money:hover {
    transform: translateY(-2px) !important;
    box-shadow: 0 4px 8px rgba(67, 97, 238, 0.3) !important;
}

.btn-outline-primary {
    font-size: 0.85rem;
    padding: 0.375rem 0.5rem;
}
```

### HTML Structure
```php
<div class="d-flex gap-2">
    <form method="POST" class="flex-grow-1">
        <button type="submit" name="apply_now" class="btn btn-earn-money w-100">
            <i class="fas fa-paper-plane me-2"></i>Apply for This Offer
        </button>
    </form>
    <button class="btn btn-outline-primary copy-link-btn"
            data-link="<?php echo isset($_SESSION['user_id']) ? htmlspecialchars($item['redirect_url'] . $_SESSION['user_id']) : ''; ?>"
            <?php echo !isset($_SESSION['user_id']) ? 'disabled' : ''; ?>>
        <?php echo isset($_SESSION['user_id']) ? 'Refer & Earn' : 'Login to Copy'; ?>
    </button>
</div>
```

### JavaScript Functionality
```javascript
// Copy link functionality (same as all_offers)
document.querySelectorAll('.copy-link-btn').forEach(button => {
    button.addEventListener('click', function() {
        const link = this.getAttribute('data-link');
        if (link) {
            navigator.clipboard.writeText(link).then(() => {
                // Show feedback to user
                const originalText = this.innerHTML;
                this.innerHTML = '<i class="fas fa-check"></i> Copied!';
                this.classList.remove('btn-outline-primary');
                this.classList.add('btn-success');
                
                // Reset button after 2 seconds
                setTimeout(() => {
                    this.innerHTML = originalText;
                    this.classList.remove('btn-success');
                    this.classList.add('btn-outline-primary');
                }, 2000);
            }).catch(err => {
                console.error('Failed to copy: ', err);
                alert('Failed to copy link. Please try again.');
            });
        }
    });
});
```

## Testing

After implementing these changes, the credit card product details page should:
1. Have a consistent UI with the all_offers page
2. Display amount information in the same format
3. Have matching button styles and behaviors
4. Include copy link functionality
5. Be responsive on all device sizes
6. Provide visual feedback for user actions

The implementation maintains backward compatibility and works with existing credit card data.