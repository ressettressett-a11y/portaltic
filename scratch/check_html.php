<?php
session_start();
$_SESSION['admin'] = 'admin';
ob_start();
include 'admin.php';
$html = ob_get_clean();

// Search for delete-related HTML
preg_match_all('/.*(?:confirmDelete|btn-danger|delete_card|delete_faq).*/', $html, $matches);
echo "=== DELETE BUTTON HTML FOUND ===\n";
foreach ($matches[0] as $i => $m) {
    echo "[$i] " . trim($m) . "\n";
}

echo "\n=== CONFIRM DELETE FUNCTION ===\n";
if (strpos($html, 'function confirmDelete') !== false) {
    echo "FOUND: confirmDelete function exists in output\n";
} else {
    echo "NOT FOUND: confirmDelete function is MISSING from output!\n";
}

// Check if there's a redirect header being sent
echo "\n=== CHECKING FOR REDIRECT ===\n";
$headers = headers_list();
foreach ($headers as $h) {
    echo "Header: $h\n";
}
