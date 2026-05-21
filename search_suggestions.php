<?php
// search_suggestions.php - Proxy para la API de sugerencias de Google
header('Content-Type: application/json; charset=utf-8');

if (!isset($_GET['q']) || empty(trim($_GET['q']))) {
    echo json_encode(["", []]);
    exit;
}

$query = urlencode($_GET['q']);
// client=firefox devuelve un JSON limpio de la forma: ["query", ["sugerencia1", "sugerencia2", ...]]
$url = "https://suggestqueries.google.com/complete/search?client=firefox&hl=es&q=" . $query;

$response = false;

// Intentar con cURL primero
if (function_exists('curl_init')) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/100.0.0.0 Safari/537.36');
    curl_setopt($ch, CURLOPT_TIMEOUT, 3);
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($http_code !== 200) {
        $response = false;
    }
}

// Alternativa con file_get_contents si cURL falla o está deshabilitado en XAMPP
if ($response === false) {
    $opts = [
        'http' => [
            'method' => 'GET',
            'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/100.0.0.0 Safari/537.36\r\n",
            'timeout' => 3
        ]
    ];
    $context = stream_context_create($opts);
    $response = @file_get_contents($url, false, $context);
}

if ($response !== false) {
    echo $response;
} else {
    // Si falla, responder con lista vacía
    echo json_encode([$_GET['q'], []]);
}
?>
