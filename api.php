<?php
// api.php - JSON API for index.html
header('Content-Type: application/json');
require_once 'db.php';

try {
    // Get Settings
    $settings_raw = $pdo->query("SELECT key, value FROM settings")->fetchAll();
    $settings = [];
    foreach ($settings_raw as $s) {
        $settings[$s['key']] = $s['value'];
    }

    // Get Cards
    $cards = $pdo->query("SELECT * FROM cards ORDER BY display_order ASC")->fetchAll();

    // Get FAQs
    $faqs = $pdo->query("SELECT * FROM faqs ORDER BY id ASC")->fetchAll();

    // Get Videos
    $videos = $pdo->query("SELECT * FROM videos ORDER BY id ASC")->fetchAll();

    // Get Bookmarks
    $bookmarks = $pdo->query("SELECT * FROM bookmarks ORDER BY id DESC")->fetchAll();

    echo json_encode([
        'settings' => $settings,
        'cards' => $cards,
        'faqs' => $faqs,
        'videos' => $videos,
        'bookmarks' => $bookmarks
    ]);

} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
