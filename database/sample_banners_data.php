<?php
// Add sample banners data
include __DIR__ . '/../config/db.php';

if (!$pdo) {
    die("Database connection failed");
}

try {
    // Check if banners table exists and is empty
    $stmt = $pdo->query("SELECT COUNT(*) FROM banners");
    $count = $stmt->fetchColumn();
    
    if ($count == 0) {
        // Insert sample banners
        $banners = [
            [
                'title' => 'Amazon Special Offer',
                'image_url' => 'https://res.cloudinary.com/dep67o63b/image/upload/v1760608969/2_qpu9gr.png',
                'redirect_url' => 'https://www.amazon.in/?&linkCode=sl2&tag=n220b-21&linkId=a288d593a323ccb9fb0371b1ddde6e48&language=en_IN&ref_=as_li_ss_tl_?id=1&user_id=',
                'sequence_id' => 1,
                'is_active' => 1
            ],
            [
                'title' => 'Amazon Deals',
                'image_url' => 'https://res.cloudinary.com/dep67o63b/image/upload/v1760608964/4_yihxur.png',
                'redirect_url' => 'https://www.amazon.in/?&linkCode=sl2&tag=n220b-21&linkId=a288d593a323ccb9fb0371b1ddde6e48&language=en_IN&ref_=as_li_ss_tl_?id=2&user_id=',
                'sequence_id' => 2,
                'is_active' => 1
            ],
            [
                'title' => 'Amazon Exclusive',
                'image_url' => 'https://res.cloudinary.com/dep67o63b/image/upload/v1760608962/1_jmmalq.png',
                'redirect_url' => 'https://www.amazon.in/?&linkCode=sl2&tag=n220b-21&linkId=a288d593a323ccb9fb0371b1ddde6e48&language=en_IN&ref_=as_li_ss_tl_?id=3&user_id=',
                'sequence_id' => 3,
                'is_active' => 1
            ],
            [
                'title' => 'Amazon Flash Sale',
                'image_url' => 'https://res.cloudinary.com/dep67o63b/image/upload/v1760608963/3_j2su4t.png',
                'redirect_url' => 'https://www.amazon.in/?&linkCode=sl2&tag=n220b-21&linkId=a288d593a323ccb9fb0371b1ddde6e48&language=en_IN&ref_=as_li_ss_tl_?id=4&user_id=',
                'sequence_id' => 4,
                'is_active' => 1
            ]
        ];
        
        $stmt = $pdo->prepare("INSERT INTO banners (title, image_url, redirect_url, sequence_id, is_active) VALUES (?, ?, ?, ?, ?)");
        
        foreach ($banners as $banner) {
            $stmt->execute([
                $banner['title'],
                $banner['image_url'],
                $banner['redirect_url'],
                $banner['sequence_id'],
                $banner['is_active']
            ]);
        }
        
        echo "Sample banners data inserted successfully!";
    } else {
        echo "Banners table already contains data. Skipping sample data insertion.";
    }
} catch(PDOException $e) {
    echo "Error inserting sample data: " . $e->getMessage();
}
?>