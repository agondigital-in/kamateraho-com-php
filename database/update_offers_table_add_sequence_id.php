<?php
// Add sequence_id column to offers table
include '../config/db.php';

try {
    // Check if sequence_id column already exists
    $stmt = $pdo->prepare("SHOW COLUMNS FROM offers LIKE 'sequence_id'");
    $stmt->execute();
    $columnExists = $stmt->fetch();
    
    if (!$columnExists) {
        // Add sequence_id column to offers table with a better default
        $sql = "ALTER TABLE offers ADD COLUMN sequence_id INT(11) DEFAULT 0 AFTER is_active";
        $pdo->exec($sql);
        
        echo "Column 'sequence_id' added to 'offers' table successfully.\n";
        
        // Create index on sequence_id for better performance
        $sql = "CREATE INDEX idx_offers_sequence_id ON offers(sequence_id)";
        $pdo->exec($sql);
        
        echo "Index 'idx_offers_sequence_id' created successfully.\n";
        
        // Set sequential sequence_id values for existing offers
        $stmt = $pdo->query("SELECT id FROM offers ORDER BY created_at ASC");
        $offers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($offers as $index => $offer) {
            $sequence_id = $index + 1;
            $updateStmt = $pdo->prepare("UPDATE offers SET sequence_id = ? WHERE id = ?");
            $updateStmt->execute([$sequence_id, $offer['id']]);
        }
        
        echo "Set sequential sequence_id values for " . count($offers) . " existing offers.\n";
    } else {
        echo "Column 'sequence_id' already exists in 'offers' table.\n";
    }
    
    echo "Migration completed successfully!\n";
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>