<?php
$pdo = new PDO('sqlite:database.db');
$cards = $pdo->query("SELECT title, button_url FROM cards")->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($cards, JSON_PRETTY_PRINT);
