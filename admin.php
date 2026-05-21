<?php
/**
 * admin.php - PREMIUM ADMIN PANEL V2
 * REBUILT FROM SCRATCH FOR STABILITY AND DESIGN
 */
session_start();
require_once 'db.php';

// 1. AUTHENTICATION
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

$upload_dir = 'uploads/';
if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

// 2. HELPER FUNCTIONS
function uploadImage($file, $dir) {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) return null;
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
    if (!in_array($ext, $allowed)) return null;
    
    $name = time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    if (move_uploaded_file($file['tmp_name'], $dir . $name)) return $dir . $name;
    return null;
}

function deleteFile($path) {
    if ($path && file_exists($path) && is_file($path)) {
        unlink($path);
    }
}

// 3. ACTION DISPATCHER (POST ONLY for destructive actions)
$success_msg = "";
$error_msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = $_POST['id'] ?? null;

    $tab_map = [
        'update_settings' => 'settings',
        'save_card' => 'cards',
        'delete_card' => 'cards',
        'save_faq' => 'faqs',
        'delete_faq' => 'faqs',
        'save_video' => 'videos',
        'delete_video' => 'videos',
        'save_bookmark' => 'bookmarks',
        'delete_bookmark' => 'bookmarks',
        'save_contact' => 'contacts',
        'delete_contact' => 'contacts',
        'save_user' => 'users',
        'delete_user' => 'users'
    ];
    $redirect_tab = $tab_map[$action] ?? 'dashboard';

    try {
        switch ($action) {
            case 'update_settings':
                $pdo->prepare("UPDATE settings SET value = ? WHERE key = 'site_title'")->execute([$_POST['site_title']]);
                $pdo->prepare("UPDATE settings SET value = ? WHERE key = 'site_subtitle'")->execute([$_POST['site_subtitle']]);
                $pdo->prepare("UPDATE settings SET value = ? WHERE key = 'footer_text'")->execute([$_POST['footer_text']]);
                
                if ($img = uploadImage($_FILES['logo_left_file'] ?? null, $upload_dir)) {
                    // Optional: delete old logo
                    $pdo->prepare("UPDATE settings SET value = ? WHERE key = 'logo_left'")->execute([$img]);
                }
                if ($img = uploadImage($_FILES['logo_right_file'] ?? null, $upload_dir)) {
                    $pdo->prepare("UPDATE settings SET value = ? WHERE key = 'logo_right'")->execute([$img]);
                }
                $success_msg = "Configuración actualizada.";
                break;

            case 'save_card':
                $img = $_POST['existing_image'] ?? '';
                if ($new_img = uploadImage($_FILES['card_image'] ?? null, $upload_dir)) {
                    if ($img) deleteFile($img); // Delete old image
                    $img = $new_img;
                }
                if ($id) {
                    $pdo->prepare("UPDATE cards SET title = ?, image_path = ?, button_text = ?, button_url = ?, display_order = ? WHERE id = ?")
                         ->execute([$_POST['title'], $img, $_POST['button_text'], $_POST['button_url'], $_POST['display_order'], $id]);
                    $success_msg = "Tarjeta actualizada.";
                } else {
                    $pdo->prepare("INSERT INTO cards (title, image_path, button_text, button_url, display_order) VALUES (?, ?, ?, ?, ?)")
                         ->execute([$_POST['title'], $img, $_POST['button_text'], $_POST['button_url'], $_POST['display_order']]);
                    $success_msg = "Tarjeta creada.";
                }
                break;

            case 'delete_card':
                if ($id) {
                    $stmt = $pdo->prepare("SELECT image_path FROM cards WHERE id = ?");
                    $stmt->execute([$id]);
                    $img = $stmt->fetchColumn();
                    deleteFile($img);
                    $pdo->prepare("DELETE FROM cards WHERE id = ?")->execute([$id]);
                    $success_msg = "Tarjeta eliminada.";
                }
                break;

            case 'save_faq':
                if ($id) {
                    $pdo->prepare("UPDATE faqs SET question = ?, answer = ? WHERE id = ?")->execute([$_POST['question'], $_POST['answer'], $id]);
                    $success_msg = "FAQ actualizada.";
                } else {
                    $pdo->prepare("INSERT INTO faqs (question, answer) VALUES (?, ?)")->execute([$_POST['question'], $_POST['answer']]);
                    $success_msg = "FAQ creada.";
                }
                break;

            case 'delete_faq':
                if ($id) {
                    $pdo->prepare("DELETE FROM faqs WHERE id = ?")->execute([$id]);
                    $success_msg = "FAQ eliminada.";
                }
                break;

            case 'save_video':
                if ($id) {
                    $pdo->prepare("UPDATE videos SET title = ?, source = ? WHERE id = ?")->execute([$_POST['title'], $_POST['source'], $id]);
                    $success_msg = "Video actualizado.";
                } else {
                    $pdo->prepare("INSERT INTO videos (title, source) VALUES (?, ?)")->execute([$_POST['title'], $_POST['source']]);
                    $success_msg = "Video añadido.";
                }
                break;

            case 'delete_video':
                if ($id) {
                    $pdo->prepare("DELETE FROM videos WHERE id = ?")->execute([$id]);
                    $success_msg = "Video eliminado.";
                }
                break;

            case 'save_bookmark':
                if ($id) {
                    $pdo->prepare("UPDATE bookmarks SET title = ?, url = ? WHERE id = ?")->execute([$_POST['title'], $_POST['url'], $id]);
                    $success_msg = "Marcador actualizado.";
                } else {
                    $pdo->prepare("INSERT INTO bookmarks (title, url) VALUES (?, ?)")->execute([$_POST['title'], $_POST['url']]);
                    $success_msg = "Marcador añadido.";
                }
                break;

            case 'delete_bookmark':
                if ($id) {
                    $pdo->prepare("DELETE FROM bookmarks WHERE id = ?")->execute([$id]);
                    $success_msg = "Marcador eliminado.";
                }
                break;

            case 'save_contact':
                $nombre = $_POST['nombre'] ?? '';
                $ext = $_POST['ext'] ?? '';
                $email = $_POST['email'] ?? '';
                if ($id) {
                    $pdo->prepare("UPDATE contacts SET nombre = ?, ext = ?, email = ? WHERE id = ?")
                         ->execute([$nombre, $ext, $email, $id]);
                    $success_msg = "Contacto actualizado.";
                } else {
                    $pdo->prepare("INSERT INTO contacts (nombre, ext, email) VALUES (?, ?, ?)")
                         ->execute([$nombre, $ext, $email]);
                    $success_msg = "Contacto añadido.";
                }
                break;

            case 'delete_contact':
                if ($id) {
                    $pdo->prepare("DELETE FROM contacts WHERE id = ?")->execute([$id]);
                    $success_msg = "Contacto eliminado.";
                }
                break;

            case 'save_user':
                $username = $_POST['username'] ?? '';
                $password = $_POST['password'] ?? '';
                
                if ($id) {
                    if (!empty($password)) {
                        $hashed = password_hash($password, PASSWORD_DEFAULT);
                        $pdo->prepare("UPDATE users SET username = ?, password = ? WHERE id = ?")->execute([$username, $hashed, $id]);
                    } else {
                        $pdo->prepare("UPDATE users SET username = ? WHERE id = ?")->execute([$username, $id]);
                    }
                    $success_msg = "Usuario actualizado.";
                } else {
                    if (empty($password)) throw new Exception("La contraseña es requerida para nuevos usuarios.");
                    $hashed = password_hash($password, PASSWORD_DEFAULT);
                    $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)")->execute([$username, $hashed]);
                    $success_msg = "Usuario creado.";
                }
                break;

            case 'delete_user':
                if ($id) {
                    // Prevent self-deletion if needed, or just allow it (session will still be active until logout)
                    $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$id]);
                    $success_msg = "Usuario eliminado.";
                }
                break;
        }
        
        // PRG Pattern to avoid resubmission
        if ($success_msg) {
            $_SESSION['toast'] = ['type' => 'success', 'msg' => $success_msg];
            header("Location: admin.php?tab=" . urlencode($redirect_tab));
            exit;
        }
    } catch (Exception $e) {
        $error_msg = "Error: " . $e->getMessage();
        $_SESSION['toast'] = ['type' => 'error', 'msg' => $error_msg];
        header("Location: admin.php?tab=" . urlencode($redirect_tab));
        exit;
    }
}

// 4. DATA FETCHING
$settings = $pdo->query("SELECT key, value FROM settings")->fetchAll(PDO::FETCH_KEY_PAIR);
$faqs = $pdo->query("SELECT * FROM faqs ORDER BY id DESC")->fetchAll();
$cards = $pdo->query("SELECT * FROM cards ORDER BY display_order ASC")->fetchAll();
$videos = $pdo->query("SELECT * FROM videos ORDER BY id DESC")->fetchAll();
$bookmarks = $pdo->query("SELECT * FROM bookmarks ORDER BY id DESC")->fetchAll();
$contacts = $pdo->query("SELECT * FROM contacts ORDER BY id ASC")->fetchAll();
$users = $pdo->query("SELECT id, username, created_at FROM users ORDER BY id DESC")->fetchAll();

// Get toast from session
$toast = $_SESSION['toast'] ?? null;
unset($_SESSION['toast']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Admin | Portal TI</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #0ea5e9;
            --primary-dark: #0284c7;
            --bg: #f8fafc;
            --sidebar: #0f172a;
            --card-bg: #ffffff;
            --text: #1e293b;
            --text-light: #64748b;
            --border: #e2e8f0;
            --success: #10b981;
            --danger: #ef4444;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); display: flex; min-height: 100vh; }

        /* Sidebar */
        .sidebar { width: 260px; background: var(--sidebar); color: white; display: flex; flex-direction: column; position: fixed; height: 100vh; }
        .sidebar-header { padding: 30px 20px; font-weight: 700; font-size: 1.2rem; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar-nav { padding: 20px 0; flex: 1; }
        .nav-item { padding: 12px 20px; display: flex; align-items: center; gap: 10px; color: rgba(255,255,255,0.7); text-decoration: none; transition: 0.3s; cursor: pointer; }
        .nav-item:hover, .nav-item.active { background: rgba(255,255,255,0.05); color: white; }
        .sidebar-footer { padding: 20px; border-top: 1px solid rgba(255,255,255,0.1); }
        .btn-logout { color: #f87171; text-decoration: none; font-weight: 600; font-size: 0.9rem; }

        /* Main Content */
        .main { margin-left: 260px; flex: 1; padding: 40px; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .header h1 { font-size: 1.8rem; font-weight: 700; }

        /* Sections */
        .section { display: none; animation: fadeIn 0.4s ease; }
        .section.active { display: block; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        /* Card Panels */
        .card { background: var(--card-bg); border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); padding: 25px; margin-bottom: 30px; border: 1px solid var(--border); }
        .card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid var(--border); }
        .card-title { font-size: 1.1rem; font-weight: 600; }

        /* Tables */
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 12px; font-size: 0.85rem; text-transform: uppercase; color: var(--text-light); border-bottom: 1px solid var(--border); }
        td { padding: 15px 12px; border-bottom: 1px solid var(--border); font-size: 0.95rem; }
        .badge { padding: 4px 8px; border-radius: 6px; font-size: 0.75rem; font-weight: 700; background: #f1f5f9; }

        /* Form Controls */
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 500; font-size: 0.9rem; }
        .form-group input, .form-group textarea { width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: 8px; outline: none; transition: 0.3s; font-family: inherit; }
        .form-group input:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(14,165,233,0.1); }
        
        .btn { padding: 10px 20px; border-radius: 8px; border: none; font-weight: 600; cursor: pointer; transition: 0.3s; display: inline-flex; align-items: center; gap: 8px; text-decoration: none; font-size: 0.9rem; }
        .btn-primary { background: var(--primary); color: white; }
        .btn-primary:hover { background: var(--primary-dark); }
        .btn-danger { background: #fee2e2; color: #ef4444; }
        .btn-danger:hover { background: #fecaca; }
        .btn-success { background: var(--success); color: white; }
        .btn-success:hover { background: #059669; }
        .btn-ghost { background: transparent; color: var(--text-light); border: 1px solid var(--border); }
        .btn-ghost:hover { background: #f1f5f9; }

        /* Modals */
        .modal { display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.6); backdrop-filter: blur(4px); z-index: 1000; align-items: center; justify-content: center; }
        .modal-content { background: white; width: 500px; border-radius: 16px; padding: 30px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); position: relative; max-height: 90vh; overflow-y: auto; }
        .modal-header { margin-bottom: 20px; }

        /* Toast Notifications */
        .toast { position: fixed; top: 20px; right: 20px; padding: 16px 24px; border-radius: 10px; background: white; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); display: flex; align-items: center; gap: 12px; transform: translateX(120%); transition: 0.5s cubic-bezier(0.68, -0.55, 0.27, 1.55); z-index: 2000; border-left: 4px solid var(--primary); }
        .toast.show { transform: translateX(0); }
        .toast.success { border-left-color: var(--success); }
        .toast.error { border-left-color: var(--danger); }

        .img-preview { width: 50px; height: 50px; border-radius: 8px; object-fit: cover; background: #f1f5f9; }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            Portal Admin
        </div>
        <div class="sidebar-nav">
            <div class="nav-item active" data-target="dashboard">Dashboard</div>
            <div class="nav-item" data-target="settings">Configuración</div>
            <div class="nav-item" data-target="cards">Tarjetas</div>
            <div class="nav-item" data-target="faqs">FAQs</div>
            <div class="nav-item" data-target="videos">Videos</div>
            <div class="nav-item" data-target="bookmarks">Marcadores</div>
            <div class="nav-item" data-target="contacts">Contactos</div>
            <div class="nav-item" data-target="users">Usuarios</div>
        </div>
        <div class="sidebar-footer">
            <a href="index.html" class="nav-item" style="margin-bottom:10px; padding:8px 0;">Ver Portal &rarr;</a>
            <a href="logout.php" class="btn-logout">Cerrar Sesión</a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main">
        
        <!-- DASHBOARD -->
        <section id="dashboard" class="section active">
            <div class="header">
                <h1>Hola, <?php echo htmlspecialchars($_SESSION['admin']); ?></h1>
            </div>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 20px;">
                <div class="card" style="margin-bottom:0; text-align:center;">
                    <div style="font-size: 2rem; font-weight: 700; color: var(--primary);"><?php echo count($cards); ?></div>
                    <div style="color: var(--text-light); font-size: 0.9rem;">Tarjetas</div>
                </div>
                <div class="card" style="margin-bottom:0; text-align:center;">
                    <div style="font-size: 2rem; font-weight: 700; color: var(--success);"><?php echo count($faqs); ?></div>
                    <div style="color: var(--text-light); font-size: 0.9rem;">Preguntas</div>
                </div>
                <div class="card" style="margin-bottom:0; text-align:center;">
                    <div style="font-size: 2rem; font-weight: 700; color: #f59e0b;"><?php echo count($videos); ?></div>
                    <div style="color: var(--text-light); font-size: 0.9rem;">Videos</div>
                </div>
                <div class="card" style="margin-bottom:0; text-align:center;">
                    <div style="font-size: 2rem; font-weight: 700; color: #8b5cf6;"><?php echo count($bookmarks); ?></div>
                    <div style="color: var(--text-light); font-size: 0.9rem;">Marcadores</div>
                </div>
                <div class="card" style="margin-bottom:0; text-align:center;">
                    <div style="font-size: 2rem; font-weight: 700; color: #10b981;"><?php echo count($contacts); ?></div>
                    <div style="color: var(--text-light); font-size: 0.9rem;">Contactos</div>
                </div>
                <div class="card" style="margin-bottom:0; text-align:center;">
                    <div style="font-size: 2rem; font-weight: 700; color: #ec4899;"><?php echo count($users); ?></div>
                    <div style="color: var(--text-light); font-size: 0.9rem;">Usuarios</div>
                </div>
            </div>
        </section>

        <!-- SETTINGS -->
        <section id="settings" class="section">
            <div class="header"><h1>Configuración General</h1></div>
            <div class="card">
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="update_settings">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="form-group"><label>Título del Portal</label><input type="text" name="site_title" value="<?php echo htmlspecialchars($settings['site_title'] ?? ''); ?>"></div>
                        <div class="form-group"><label>Subtítulo / Bienvenida</label><input type="text" name="site_subtitle" value="<?php echo htmlspecialchars($settings['site_subtitle'] ?? ''); ?>"></div>
                    </div>
                    <div class="form-group"><label>Texto del Pie de Página</label><input type="text" name="footer_text" value="<?php echo htmlspecialchars($settings['footer_text'] ?? ''); ?>"></div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="form-group"><label>Logo Izquierdo</label><input type="file" name="logo_left_file"></div>
                        <div class="form-group"><label>Logo Derecho</label><input type="file" name="logo_right_file"></div>
                    </div>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </form>
            </div>
        </section>

        <!-- CARDS -->
        <section id="cards" class="section">
            <div class="header">
                <h1>Tarjetas de Acceso</h1>
                <button onclick="openModal('cardModal')" class="btn btn-primary">+ Nueva Tarjeta</button>
            </div>
            <div class="card">
                <table>
                    <thead><tr><th>Orden</th><th>Imagen</th><th>Título</th><th>URL</th><th>Acciones</th></tr></thead>
                    <tbody>
                        <?php foreach ($cards as $c): ?>
                        <tr>
                            <td><span class="badge"><?php echo $c['display_order']; ?></span></td>
                            <td><img src="<?php echo $c['image_path']; ?>" class="img-preview"></td>
                            <td><strong><?php echo htmlspecialchars($c['title']); ?></strong></td>
                            <td><code style="font-size:0.8rem;"><?php echo htmlspecialchars($c['button_url']); ?></code></td>
                            <td>
                                <div style="display:flex; gap:8px;">
                                    <button onclick='editCard(<?php echo json_encode($c); ?>)' class="btn btn-ghost btn-sm">Editar</button>
                                    <button onclick="confirmDelete('delete_card', <?php echo $c['id']; ?>, '<?php echo addslashes($c['title']); ?>')" class="btn btn-danger btn-sm">X</button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- FAQS -->
        <section id="faqs" class="section">
            <div class="header">
                <h1>Preguntas Frecuentes</h1>
                <button onclick="openModal('faqModal')" class="btn btn-primary">+ Nueva FAQ</button>
            </div>
            <div class="card">
                <table>
                    <thead><tr><th>Pregunta</th><th>Acciones</th></tr></thead>
                    <tbody>
                        <?php foreach ($faqs as $f): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($f['question']); ?></strong></td>
                            <td>
                                <div style="display:flex; gap:8px;">
                                    <button onclick='editFaq(<?php echo json_encode($f); ?>)' class="btn btn-ghost btn-sm">Editar</button>
                                    <button onclick="confirmDelete('delete_faq', <?php echo $f['id']; ?>, '<?php echo addslashes($f['question']); ?>')" class="btn btn-danger btn-sm">X</button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- VIDEOS -->
        <section id="videos" class="section">
            <div class="header">
                <h1>Videos de Ayuda</h1>
                <button onclick="openModal('videoModal')" class="btn btn-primary">+ Nuevo Video</button>
            </div>
            <div class="card">
                <table>
                    <thead><tr><th>Título</th><th>Ruta / UNC</th><th>Acciones</th></tr></thead>
                    <tbody>
                        <?php foreach ($videos as $v): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($v['title']); ?></strong></td>
                            <td><code><?php echo htmlspecialchars($v['source']); ?></code></td>
                            <td>
                                <div style="display:flex; gap:8px;">
                                    <button onclick='editVideo(<?php echo json_encode($v); ?>)' class="btn btn-ghost btn-sm">Editar</button>
                                    <button onclick="confirmDelete('delete_video', <?php echo $v['id']; ?>, '<?php echo addslashes($v['title']); ?>')" class="btn btn-danger btn-sm">X</button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- BOOKMARKS -->
        <section id="bookmarks" class="section">
            <div class="header">
                <h1>Marcadores de Interés</h1>
                <button onclick="openModal('bmModal')" class="btn btn-primary">+ Nuevo Marcador</button>
            </div>
            <div class="card">
                <table>
                    <thead><tr><th>Título</th><th>URL</th><th>Acciones</th></tr></thead>
                    <tbody>
                        <?php foreach ($bookmarks as $b): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($b['title']); ?></strong></td>
                            <td><a href="<?php echo $b['url']; ?>" target="_blank" style="font-size:0.85rem;"><?php echo htmlspecialchars($b['url']); ?></a></td>
                            <td>
                                <div style="display:flex; gap:8px;">
                                    <button onclick='editBookmark(<?php echo json_encode($b); ?>)' class="btn btn-ghost btn-sm">Editar</button>
                                    <button onclick="confirmDelete('delete_bookmark', <?php echo $b['id']; ?>, '<?php echo addslashes($b['title']); ?>')" class="btn btn-danger btn-sm">X</button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
        </section>

        <!-- CONTACTS -->
        <section id="contacts" class="section">
            <div class="header">
                <h1>Contactos de Soporte Técnico</h1>
                <button onclick="openModal('contactModal')" class="btn btn-primary">+ Nuevo Contacto</button>
            </div>
            <div class="card">
                <table>
                    <thead><tr><th>Nombre Departamento</th><th>Extensión</th><th>Email</th><th>Acciones</th></tr></thead>
                    <tbody>
                        <?php foreach ($contacts as $c): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($c['nombre']); ?></strong></td>
                            <td><code><?php echo htmlspecialchars($c['ext']); ?></code></td>
                            <td><?php echo htmlspecialchars($c['email']); ?></td>
                            <td>
                                <div style="display:flex; gap:8px;">
                                    <button onclick='editContact(<?php echo json_encode($c); ?>)' class="btn btn-ghost btn-sm">Editar</button>
                                    <button onclick="confirmDelete('delete_contact', <?php echo $c['id']; ?>, '<?php echo addslashes($c['nombre']); ?>')" class="btn btn-danger btn-sm">X</button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- USERS -->
        <section id="users" class="section">
            <div class="header">
                <h1>Administración de Usuarios</h1>
                <button onclick="openModal('userModal')" class="btn btn-primary">+ Nuevo Usuario</button>
            </div>
            <div class="card">
                <table>
                    <thead><tr><th>ID</th><th>Usuario</th><th>Fecha Creación</th><th>Acciones</th></tr></thead>
                    <tbody>
                        <?php foreach ($users as $u): ?>
                        <tr>
                            <td><span class="badge">#<?php echo $u['id']; ?></span></td>
                            <td><strong><?php echo htmlspecialchars($u['username']); ?></strong></td>
                            <td><small><?php echo $u['created_at']; ?></small></td>
                            <td>
                                <div style="display:flex; gap:8px;">
                                    <button onclick='editUser(<?php echo json_encode($u); ?>)' class="btn btn-ghost btn-sm">Editar</button>
                                    <?php if ($u['username'] !== $_SESSION['admin']): ?>
                                    <button onclick="confirmDelete('delete_user', <?php echo $u['id']; ?>, '<?php echo addslashes($u['username']); ?>')" class="btn btn-danger btn-sm">X</button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <!-- MODALS -->
    <!-- Card Modal -->
    <div id="cardModal" class="modal">
        <div class="modal-content">
            <h3 id="cardModalTitle">Nueva Tarjeta</h3>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="save_card">
                <input type="hidden" name="id" id="card_id">
                <input type="hidden" name="existing_image" id="card_existing">
                <div class="form-group"><label>Título</label><input type="text" name="title" id="card_title_inp" required></div>
                <div class="form-group"><label>Texto Botón</label><input type="text" name="button_text" id="card_btn_text" required></div>
                <div class="form-group"><label>URL Botón</label><input type="text" name="button_url" id="card_btn_url" required></div>
                <div class="form-group"><label>Orden Visual</label><input type="number" name="display_order" id="card_order" value="0"></div>
                <div class="form-group"><label>Imagen</label><input type="file" name="card_image"></div>
                <div style="display:flex; gap:10px; margin-top:20px;">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                    <button type="button" onclick="closeModals()" class="btn btn-ghost">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- FAQ Modal -->
    <div id="faqModal" class="modal">
        <div class="modal-content">
            <h3 id="faqModalTitle">Nueva FAQ</h3>
            <form method="POST">
                <input type="hidden" name="action" value="save_faq">
                <input type="hidden" name="id" id="faq_id">
                <div class="form-group"><label>Pregunta</label><input type="text" name="question" id="faq_q" required></div>
                <div class="form-group"><label>Respuesta</label><textarea name="answer" id="faq_a" rows="5" required></textarea></div>
                <div style="display:flex; gap:10px;">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                    <button type="button" onclick="closeModals()" class="btn btn-ghost">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Video Modal -->
    <div id="videoModal" class="modal">
        <div class="modal-content">
            <h3 id="videoModalTitle">Nuevo Video</h3>
            <form method="POST">
                <input type="hidden" name="action" value="save_video">
                <input type="hidden" name="id" id="video_id">
                <div class="form-group"><label>Título</label><input type="text" name="title" id="video_t" required></div>
                <div class="form-group"><label>Ruta (UNC o URL)</label><input type="text" name="source" id="video_s" required></div>
                <div style="display:flex; gap:10px;">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                    <button type="button" onclick="closeModals()" class="btn btn-ghost">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bookmark Modal -->
    <div id="bmModal" class="modal">
        <div class="modal-content">
            <h3 id="bmModalTitle">Nuevo Marcador</h3>
            <form method="POST">
                <input type="hidden" name="action" value="save_bookmark">
                <input type="hidden" name="id" id="bm_id">
                <div class="form-group"><label>Título</label><input type="text" name="title" id="bm_t" required></div>
                <div class="form-group"><label>URL</label><input type="text" name="url" id="bm_u" required></div>
                <div style="display:flex; gap:10px;">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                    <button type="button" onclick="closeModals()" class="btn btn-ghost">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Contact Modal -->
    <div id="contactModal" class="modal">
        <div class="modal-content">
            <h3 id="contactModalTitle">Nuevo Contacto</h3>
            <form method="POST">
                <input type="hidden" name="action" value="save_contact">
                <input type="hidden" name="id" id="contact_id">
                <div class="form-group"><label>Nombre del Departamento</label><input type="text" name="nombre" id="contact_nombre" required></div>
                <div class="form-group"><label>Extensión(es)</label><input type="text" name="ext" id="contact_ext" placeholder="Ej: 2405, 2406" required></div>
                <div class="form-group"><label>Correo Electrónico</label><input type="email" name="email" id="contact_email" placeholder="Ej: c-6@ffaa.mil.hn" required></div>
                <div style="display:flex; gap:10px;">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                    <button type="button" onclick="closeModals()" class="btn btn-ghost">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- User Modal -->
    <div id="userModal" class="modal">
        <div class="modal-content">
            <h3 id="userModalTitle">Nuevo Usuario</h3>
            <form method="POST">
                <input type="hidden" name="action" value="save_user">
                <input type="hidden" name="id" id="user_id">
                <div class="form-group"><label>Nombre de Usuario</label><input type="text" name="username" id="user_n" required></div>
                <div class="form-group">
                    <label>Contraseña</label>
                    <input type="password" name="password" id="user_p">
                    <small id="pass_hint" style="color:var(--text-light); display:none;">Dejar en blanco para mantener la actual</small>
                </div>
                <div style="display:flex; gap:10px;">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                    <button type="button" onclick="closeModals()" class="btn btn-ghost">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Toast Notification -->
    <?php if ($toast): ?>
    <div id="toast" class="toast show <?php echo $toast['type']; ?>">
        <div style="font-weight:600;"><?php echo $toast['msg']; ?></div>
    </div>
    <script>
        setTimeout(() => document.getElementById('toast').classList.remove('show'), 3000);
    </script>
    <?php endif; ?>

    <script>
        // Navigation Logic
        document.querySelectorAll('.nav-item[data-target]').forEach(item => {
            item.onclick = () => {
                document.querySelectorAll('.nav-item').forEach(i => i.classList.remove('active'));
                document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
                
                item.classList.add('active');
                document.getElementById(item.dataset.target).classList.add('active');

                // Update URL query parameter without reloading
                const url = new URL(window.location.href);
                url.searchParams.set('tab', item.dataset.target);
                window.history.replaceState(null, '', url.toString());
            };
        });

        // Parse active tab from URL query params
        const urlParams = new URLSearchParams(window.location.search);
        const activeTab = urlParams.get('tab');
        if (activeTab) {
            const tabItem = document.querySelector(`.nav-item[data-target="${activeTab}"]`);
            if (tabItem) {
                document.querySelectorAll('.nav-item').forEach(i => i.classList.remove('active'));
                document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
                
                tabItem.classList.add('active');
                const targetSec = document.getElementById(activeTab);
                if (targetSec) {
                    targetSec.classList.add('active');
                }
            }
        }

        // Modal Logic
        function openModal(id) {
            // Reset forms when opening for "new"
            const modal = document.getElementById(id);
            if (id === 'cardModal') resetCardForm();
            if (id === 'faqModal') resetFaqForm();
            if (id === 'videoModal') resetVideoForm();
            if (id === 'bmModal') resetBmForm();
            if (id === 'contactModal') resetContactForm();
            if (id === 'userModal') resetUserForm();
            modal.style.display = 'flex';
        }

        function closeModals() {
            document.querySelectorAll('.modal').forEach(m => m.style.display = 'none');
        }

        window.onclick = (e) => { if (e.target.className === 'modal') closeModals(); };

        // Edit Functions
        function editCard(data) {
            openModal('cardModal');
            document.getElementById('cardModalTitle').innerText = "Editar Tarjeta";
            document.getElementById('card_id').value = data.id;
            document.getElementById('card_existing').value = data.image_path;
            document.getElementById('card_title_inp').value = data.title;
            document.getElementById('card_btn_text').value = data.button_text;
            document.getElementById('card_btn_url').value = data.button_url;
            document.getElementById('card_order').value = data.display_order;
        }

        function resetCardForm() {
            document.getElementById('cardModalTitle').innerText = "Nueva Tarjeta";
            document.getElementById('card_id').value = "";
            document.getElementById('card_existing').value = "";
            document.getElementById('card_title_inp').value = "";
            document.getElementById('card_btn_text').value = "Acceder";
            document.getElementById('card_btn_url').value = "";
            document.getElementById('card_order').value = "0";
        }

        function editFaq(data) {
            openModal('faqModal');
            document.getElementById('faqModalTitle').innerText = "Editar FAQ";
            document.getElementById('faq_id').value = data.id;
            document.getElementById('faq_q').value = data.question;
            document.getElementById('faq_a').value = data.answer;
        }
        function resetFaqForm() {
            document.getElementById('faqModalTitle').innerText = "Nueva FAQ";
            document.getElementById('faq_id').value = "";
            document.getElementById('faq_q').value = "";
            document.getElementById('faq_a').value = "";
        }

        function editVideo(data) {
            openModal('videoModal');
            document.getElementById('videoModalTitle').innerText = "Editar Video";
            document.getElementById('video_id').value = data.id;
            document.getElementById('video_t').value = data.title;
            document.getElementById('video_s').value = data.source;
        }
        function resetVideoForm() {
            document.getElementById('videoModalTitle').innerText = "Nuevo Video";
            document.getElementById('video_id').value = "";
            document.getElementById('video_t').value = "";
            document.getElementById('video_s').value = "";
        }

        function editBookmark(data) {
            openModal('bmModal');
            document.getElementById('bmModalTitle').innerText = "Editar Marcador";
            document.getElementById('bm_id').value = data.id;
            document.getElementById('bm_t').value = data.title;
            document.getElementById('bm_u').value = data.url;
        }
        function resetBmForm() {
            document.getElementById('bmModalTitle').innerText = "Nuevo Marcador";
            document.getElementById('bm_id').value = "";
            document.getElementById('bm_t').value = "";
            document.getElementById('bm_u').value = "";
        }

        function editContact(data) {
            openModal('contactModal');
            document.getElementById('contactModalTitle').innerText = "Editar Contacto";
            document.getElementById('contact_id').value = data.id;
            document.getElementById('contact_nombre').value = data.nombre;
            document.getElementById('contact_ext').value = data.ext;
            document.getElementById('contact_email').value = data.email;
        }
        function resetContactForm() {
            document.getElementById('contactModalTitle').innerText = "Nuevo Contacto";
            document.getElementById('contact_id').value = "";
            document.getElementById('contact_nombre').value = "";
            document.getElementById('contact_ext').value = "";
            document.getElementById('contact_email').value = "";
        }

        function editUser(data) {
            openModal('userModal');
            document.getElementById('userModalTitle').innerText = "Editar Usuario";
            document.getElementById('user_id').value = data.id;
            document.getElementById('user_n').value = data.username;
            document.getElementById('user_p').required = false;
            document.getElementById('pass_hint').style.display = 'block';
        }
        function resetUserForm() {
            document.getElementById('userModalTitle').innerText = "Nuevo Usuario";
            document.getElementById('user_id').value = "";
            document.getElementById('user_n').value = "";
            document.getElementById('user_p').value = "";
            document.getElementById('user_p').required = true;
            document.getElementById('pass_hint').style.display = 'none';
        }

        // Global Delete function
        function confirmDelete(action, id, name) {
            if (confirm(`¿Seguro que desea eliminar "${name}"?`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'admin.php';
                form.innerHTML = `
                    <input type="hidden" name="action" value="${action}">
                    <input type="hidden" name="id" value="${id}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>

</body>
</html>
