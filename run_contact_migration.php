<?php
// Script to run the contact_messages table migration
echo "Running contact_messages table migration...\n";

// Include the migration file
include 'database/update_contact_messages_table_add_user_id.php';

echo "Migration completed.\n";
?>