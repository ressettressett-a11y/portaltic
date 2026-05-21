<?php
session_start();
$_SESSION['admin'] = 'admin';
ob_start();
include 'admin.php';
$html = ob_get_clean();

// Check for modals that might be overlapping
echo "=== CHECKING MODAL CSS ===\n";
preg_match_all('/\.modal\s*\{[^}]+\}/', $html, $modalCSS);
foreach ($modalCSS[0] as $m) {
    echo $m . "\n\n";
}

// Check if any modal starts with display:flex (visible by default)
echo "=== CHECKING IF MODALS START VISIBLE ===\n";
preg_match_all('/<div id="(\w+)" class="modal"/', $html, $modalIds);
echo "Modal IDs found: " . implode(', ', $modalIds[1]) . "\n";

// Check window.onclick handler
echo "\n=== WINDOW.ONCLICK HANDLER ===\n";
preg_match('/window\.onclick[^}]+\}/', $html, $winClick);
if ($winClick) echo $winClick[0] . "\n";

// Check if there are z-index conflicts  
echo "\n=== Z-INDEX VALUES ===\n";
preg_match_all('/z-index:\s*(\d+)/', $html, $zindexes);
if ($zindexes[1]) echo "Z-indexes found: " . implode(', ', $zindexes[1]) . "\n";

// THE KEY CHECK: Does window.onclick intercept the button click?
echo "\n=== CRITICAL: window.onclick check ===\n";
preg_match('/window\.onclick.*?(\}.*?\})/s', $html, $clickHandler);
if ($clickHandler) echo $clickHandler[0] . "\n";

// Check if btn-sm exists in CSS
echo "\n=== BTN-SM CSS ===\n";
if (strpos($html, '.btn-sm') !== false) {
    echo "FOUND .btn-sm style\n";
} else {
    echo "WARNING: .btn-sm NOT defined in CSS!\n";
}

// Check for pointer-events
echo "\n=== POINTER-EVENTS CHECK ===\n";
if (strpos($html, 'pointer-events') !== false) {
    preg_match_all('/.*pointer-events.*/', $html, $pe);
    foreach ($pe[0] as $p) echo trim($p) . "\n";
} else {
    echo "No pointer-events rules found\n";
}

// Output the exact section around the delete button
echo "\n=== EXACT BUTTON CONTEXT (first 3 lines before/after) ===\n";
$lines = explode("\n", $html);
foreach ($lines as $i => $line) {
    if (strpos($line, "confirmDelete('delete_card', 1") !== false) {
        for ($j = max(0, $i-5); $j <= min(count($lines)-1, $i+5); $j++) {
            echo "L$j: " . trim($lines[$j]) . "\n";
        }
        break;
    }
}
