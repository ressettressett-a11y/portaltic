<?php
require_once 'db.php';
try {
    $contactos = $pdo->query("SELECT * FROM contacts ORDER BY id ASC")->fetchAll();
} catch (PDOException $e) {
    $contactos = [];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contactos de Soporte Técnico</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 font-sans min-h-screen py-10">

    <div class="max-w-5xl mx-auto px-4">
        
        <!-- Encabezado Institucional -->
        <header class="mb-8 bg-white p-6 rounded-2xl shadow-sm border-l-4 border-indigo-600 flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="text-center md:text-left">
                <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Directorio de Contactos</h1>
                <p class="text-slate-500 text-sm mt-0.5">Dirección de Tecnologías de la Información y Comunicaciones</p>
            </div>
            <span class="px-3 py-1 bg-indigo-50 text-indigo-700 text-xs font-semibold rounded-full uppercase tracking-wider">
                Soporte Técnico
            </span>
        </header>

        <!-- Grid de la Lista de Contactos -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            <?php foreach ($contactos as $contacto): ?>
                <div class="bg-white p-5 rounded-2xl shadow-sm flex items-start space-x-4 border border-slate-200/60 hover:shadow-md hover:border-indigo-200 transition duration-200">
                    
                    <!-- Icono Técnico Visual -->
                    <div class="p-3 bg-indigo-50 rounded-xl text-indigo-600 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 3h6m-6 4h6m-6 4h6m-6 4h6m-3 4h.01M9 16h.01M15 16h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    
                    <!-- Información del departamento -->
                    <div class="flex-1 min-w-0">
                        <h2 class="text-base font-bold text-slate-900 mb-1 break-words">
                            <?php echo $contacto['nombre']; ?>
                        </h2>
                        
                        <!-- Extensión -->
                        <div class="flex items-center space-x-1.5 mb-1">
                            <span class="text-[10px] font-bold bg-slate-100 text-slate-600 px-1.5 py-0.5 rounded uppercase tracking-wider">Ext</span>
                            <p class="text-sm font-semibold text-indigo-600 break-words">
                                <?php echo $contacto['ext']; ?>
                            </p>
                        </div>
                        
                        <!-- Correo Electrónico -->
                        <p class="text-xs text-slate-500 font-medium break-all">
                            <?php echo $contacto['email']; ?>
                        </p>
                    </div>

                </div> <!-- Cierre correcto del contenedor del contacto -->
            <?php endforeach; ?>
        </div>

    </div>

</body>
</html>