<?php
// update_db.php - Comprehensive database update
require_once 'db.php';

try {
    // 1. Settings Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS settings (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        key TEXT UNIQUE NOT NULL,
        value TEXT NOT NULL
    )");

    // Default Settings
    $default_settings = [
        ['site_title', 'Portal de Soporte TI'],
        ['site_subtitle', 'Bienvenidos - Centro de ayuda del Estado Mayor Conjunto'],
        ['footer_text', 'Dirección de Comunicaciones e Informática (C-6)'],
        ['logo_left', 'image-removebg-preview (12).png'],
        ['logo_right', 'image-removebg-preview.png']
    ];

    $stmt = $pdo->prepare("INSERT OR IGNORE INTO settings (key, value) VALUES (?, ?)");
    foreach ($default_settings as $s) {
        $stmt->execute($s);
    }

    // 2. Cards Table (Top Cards)
    $pdo->exec("CREATE TABLE IF NOT EXISTS cards (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title TEXT NOT NULL,
        image_path TEXT NOT NULL,
        button_text TEXT NOT NULL,
        button_url TEXT NOT NULL,
        display_order INTEGER DEFAULT 0
    )");

    // Move static cards to table
    $stmt = $pdo->query("SELECT count(*) FROM cards");
    if ($stmt->fetchColumn() == 0) {
        $initial_cards = [
            ['Sistema de Ticket', 'Sistema ticket.png', 'Enviar ticket', 'https://ticket.ffaa.mil.hn/login', 1],
            ['Correo Institucional', 'correo.png', 'Correo', 'https://correo.ffaa.mil.hn/static/login/', 2]
        ];
        $stmt = $pdo->prepare("INSERT INTO cards (title, image_path, button_text, button_url, display_order) VALUES (?, ?, ?, ?, ?)");
        foreach ($initial_cards as $c) {
            $stmt->execute($c);
        }
    }

    // 4. Contacts Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS contacts (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        nombre TEXT NOT NULL,
        ext TEXT NOT NULL,
        email TEXT NOT NULL
    )");

    // Seed contacts if empty
    $stmt = $pdo->query("SELECT count(*) FROM contacts");
    if ($stmt->fetchColumn() == 0) {
        $initial_contacts = [
            ['Informática', '2405, 2406', 'c-6@ffaa.mil.hn'],
            ['Comunicaciones', '2400, 2409', 'c-6@ffaa.mil.hn']
        ];
        $stmt = $pdo->prepare("INSERT INTO contacts (nombre, ext, email) VALUES (?, ?, ?)");
        foreach ($initial_contacts as $c) {
            $stmt->execute($c);
        }
    }

    echo "Database updated successfully.\n";
} catch (PDOException $e) {
    echo "Error updating database: " . $e->getMessage();
}
?>
