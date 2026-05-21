<?php
// seed_bookmarks.php - Populate internal corporate bookmarks in SQLite database
require_once 'db.php';

try {
    // 1. Create table if not exists (just in case)
    $pdo->exec("CREATE TABLE IF NOT EXISTS bookmarks (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title TEXT NOT NULL,
        url TEXT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // Clear existing to avoid duplicates if re-run
    $pdo->exec("DELETE FROM bookmarks");

    // 2. Initial Corporate Sites
    $initial_bookmarks = [
        ['Autenticación de Carbonio', 'http://172.21.4.30:6071'],
        ['Metabase', 'http://172.21.4.116:3000'],
        ['Xorcom CompletePBX', 'http://172.21.4.15'],
        ['TOCKEN VPN', 'http://172.21.100.1:7777'],
        ['Command Center', 'http://172.21.100.10:81/data/worktable/'],
        ['Acceso de Red 172.21.100.1:4445', 'http://172.21.100.1:4445'],
        ['Metabase (INV EMC)', 'http://172.21.4.116:3000/public/dashboard/ab45e287-e...'],
        ['Metabase (TICKET)', 'http://172.21.4.116:3000/public/dashboard/1160ceec-1...']
    ];

    $stmt = $pdo->prepare("INSERT INTO bookmarks (title, url) VALUES (?, ?)");
    foreach ($initial_bookmarks as $bm) {
        $stmt->execute($bm);
        echo "Inserted bookmark: {$bm[0]} -> {$bm[1]}\n";
    }

    echo "Bookmarks seeded successfully.\n";

} catch (PDOException $e) {
    echo "Error seeding bookmarks: " . $e->getMessage() . "\n";
}
?>
