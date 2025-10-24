<?php
/**
 * Format price based on its type
 * 
 * @param float $price The price value
 * @param string $price_type The price type (fixed, flat_percent, upto_percent)
 * @return string Formatted price string
 */
function format_price($price, $price_type = 'fixed') {
    switch ($price_type) {
        case 'flat_percent':
            return number_format($price, 2) . '%';
        case 'upto_percent':
            return 'Upto ' . number_format($price, 2) . '%';
        case 'fixed':
        default:
            return '₹' . number_format($price, 2);
    }
}

/**
 * Display price with appropriate symbol based on type
 * 
 * @param float $price The price value
 * @param string $price_type The price type (fixed, flat_percent, upto_percent)
 * @return string HTML formatted price display
 */
function display_price($price, $price_type = 'fixed') {
    $formatted_price = format_price($price, $price_type);
    
    switch ($price_type) {
        case 'flat_percent':
            return '<span class="price-flat-percent">' . $formatted_price . '</span>';
        case 'upto_percent':
            return '<span class="price-upto-percent">' . $formatted_price . '</span>';
        case 'fixed':
        default:
            return '<span class="price-fixed">₹' . number_format($price, 2) . '</span>';
    }
}
?>