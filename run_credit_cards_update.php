<?php
/**
 * Run the credit cards table update through the browser
 */

echo "<h2>Running Credit Cards Table Update</h2>";

// Include the update script
include 'database/update_credit_cards_table_add_amount_fields.php';

echo "<p>Update process completed.</p>";
echo "<p><a href='admin/manage_credit_cards.php'>Go to Manage Credit Cards</a></p>";
?>