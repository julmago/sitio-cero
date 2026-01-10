<?php

declare(strict_types=1);

require __DIR__ . '/includes/db.php';

session_start();

$defaultStats = [
    ['value' => '3.2x', 'label' => 'Crecimiento promedio'],
    ['value' => '48h', 'label' => 'Tiempo de respuesta'],
    ['value' => '92%', 'label' => 'Clientes recurrentes'],
];
$defaultServices = [
    ['title' => 'Branding & Identidad', 'description' => 'Nombres, logo, tono y storytelling coherentes para posicionar tu negocio.'],
    ['title' => 'Diseño Web', 'description' => 'Interfaces modernas, rápidas y responsivas listas para convertir visitas en clientes.'],
    ['title' => 'Marketing Digital', 'description' => 'Campañas en redes sociales y paid media con analítica avanzada para optimizar la inversión.'],
    ['title' => 'Contenido & Foto', 'description' => 'Producción audiovisual profesional para redes, e-commerce y lanzamientos.'],
];
$defaultProcess = [
    ['step' => '01', 'title' => 'Diagnóstico', 'description' => 'Analizamos tu marca, competencia y objetivos para definir oportunidades reales.'],
    ['step' => '02', 'title' => 'Concepto creativo', 'description' => 'Diseñamos una propuesta visual y narrativa alineada a tu audiencia.'],
    ['step' => '03', 'title' => 'Ejecución + medición', 'description' => 'Lanzamos, medimos y ajustamos para maximizar impacto y ventas.'],
];
$defaultTestimonials = [
    ['quote' => '"Nos ayudaron a relanzar nuestra marca y duplicamos las ventas en tres meses."', 'author' => 'Ana Torres · Retail Fashion'],
    ['quote' => '"El nuevo sitio es rápido, elegante y los leads aumentaron desde la primera semana."', 'author' => 'Camilo Vargas · SaaS B2B'],
    ['quote' => '"Nos encanta el equipo: creativos, ordenados y siempre con propuestas frescas."', 'author' => 'Lucía Gómez · Gastronomía'],
];

function merge_list(array $current, array $defaults): array
{
    $merged = [];
    foreach ($defaults as $index => $template) {
        $entry = $current[$index] ?? [];
        $merged[] = array_merge($template, is_array($entry) ? $entry : []);
    }

    return $merged;
}

function clean_text(string $value): string
{
    return trim($value);
}

$isLoggedIn = isset($_SESSION['admin_id']);
$message = null;

if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header('Location: /admin.php');
    exit;
}

if (!$isLoggedIn && $_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'login') {
    $username = clean_text($_POST['username'] ?? '');
    $password = (string) ($_POST['password'] ?? '');

    $stmt = db()->prepare('SELECT id, password_hash FROM admins WHERE username = ?');
    $stmt->execute([$username]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin && password_verify($password, $admin['password_hash'])) {
        $_SESSION['admin_id'] = (int) $admin['id'];
        header('Location: /admin.php');
        exit;
    }

    $message = 'Credenciales inválidas. Inténtalo nuevamente.';
}

if (!$isLoggedIn && $_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'recover') {
    $username = clean_text($_POST['recovery_username'] ?? '');
    $recoveryCode = clean_text($_POST['recovery_code'] ?? '');
    $storedRecoveryCode = get_setting('admin_recovery_code', '');

    if ($storedRecoveryCode === '' || $recoveryCode === '' || !hash_equals($storedRecoveryCode, $recoveryCode)) {
        $message = 'Código de recuperación inválido. Verifica la información.';
    } else {
        $stmt = db()->prepare('SELECT id FROM admins WHERE username = ?');
        $stmt->execute([$username]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$admin) {
            $message = 'No se encontró el usuario solicitado.';
        } else {
            try {
                $newPassword = 'temp-' . bin2hex(random_bytes(4));
            } catch (Exception $exception) {
                $newPassword = 'temp-' . bin2hex(openssl_random_pseudo_bytes(4));
            }

            $update = db()->prepare('UPDATE admins SET password_hash = ? WHERE id = ?');
            $update->execute([password_hash($newPassword, PASSWORD_DEFAULT), (int) $admin['id']]);
            $message = 'Tu nueva contraseña temporal es: ' . $newPassword . '. Cámbiala al iniciar sesión.';
        }
    }
}

if ($isLoggedIn && $_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'save') {
    set_setting('hero_tagline', clean_text($_POST['hero_tagline'] ?? ''));
    set_setting('hero_title', clean_text($_POST['hero_title'] ?? ''));
    set_setting('hero_description', clean_text($_POST['hero_description'] ?? ''));
    set_setting('hero_primary_cta', clean_text($_POST['hero_primary_cta'] ?? ''));
    set_setting('hero_secondary_cta', clean_text($_POST['hero_secondary_cta'] ?? ''));
    set_setting('hero_card_title', clean_text($_POST['hero_card_title'] ?? ''));
    set_setting('hero_card_description', clean_text($_POST['hero_card_description'] ?? ''));

    $statsInput = $_POST['stats'] ?? [];
    $stats = [];
    foreach ($defaultStats as $index => $template) {
        $entry = $statsInput[$index] ?? [];
        $stats[] = [
            'value' => clean_text($entry['value'] ?? $template['value']),
            'label' => clean_text($entry['label'] ?? $template['label']),
        ];
    }
    set_setting('hero_stats', json_encode($stats, JSON_UNESCAPED_UNICODE));

    $servicesInput = $_POST['services'] ?? [];
    $services = [];
    foreach ($defaultServices as $index => $template) {
        $entry = $servicesInput[$index] ?? [];
        $services[] = [
            'title' => clean_text($entry['title'] ?? $template['title']),
            'description' => clean_text($entry['description'] ?? $template['description']),
        ];
    }
    set_setting('services', json_encode($services, JSON_UNESCAPED_UNICODE));

    $processInput = $_POST['process'] ?? [];
    $process = [];
    foreach ($defaultProcess as $index => $template) {
        $entry = $processInput[$index] ?? [];
        $process[] = [
            'step' => clean_text($entry['step'] ?? $template['step']),
            'title' => clean_text($entry['title'] ?? $template['title']),
            'description' => clean_text($entry['description'] ?? $template['description']),
        ];
    }
    set_setting('process', json_encode($process, JSON_UNESCAPED_UNICODE));

    $testimonialsInput = $_POST['testimonials'] ?? [];
    $testimonials = [];
    foreach ($defaultTestimonials as $index => $template) {
        $entry = $testimonialsInput[$index] ?? [];
        $testimonials[] = [
            'quote' => clean_text($entry['quote'] ?? $template['quote']),
            'author' => clean_text($entry['author'] ?? $template['author']),
        ];
    }
    set_setting('testimonials', json_encode($testimonials, JSON_UNESCAPED_UNICODE));

    set_setting('contact_title', clean_text($_POST['contact_title'] ?? ''));
    set_setting('contact_description', clean_text($_POST['contact_description'] ?? ''));
    set_setting('contact_email', clean_text($_POST['contact_email'] ?? ''));
    set_setting('contact_phone', clean_text($_POST['contact_phone'] ?? ''));

    $message = 'Cambios guardados correctamente.';
}

$heroTagline = get_setting('hero_tagline', $defaultStats[0]['label']);
$heroTitle = get_setting('hero_title', '');
$heroDescription = get_setting('hero_description', '');
$heroPrimaryCta = get_setting('hero_primary_cta', '');
$heroSecondaryCta = get_setting('hero_secondary_cta', '');
$heroCardTitle = get_setting('hero_card_title', '');
$heroCardDescription = get_setting('hero_card_description', '');

$stats = json_decode(get_setting('hero_stats', json_encode($defaultStats, JSON_UNESCAPED_UNICODE)), true) ?? [];
$stats = merge_list($stats, $defaultStats);

$services = json_decode(get_setting('services', json_encode($defaultServices, JSON_UNESCAPED_UNICODE)), true) ?? [];
$services = merge_list($services, $defaultServices);

$process = json_decode(get_setting('process', json_encode($defaultProcess, JSON_UNESCAPED_UNICODE)), true) ?? [];
$process = merge_list($process, $defaultProcess);

$testimonials = json_decode(get_setting('testimonials', json_encode($defaultTestimonials, JSON_UNESCAPED_UNICODE)), true) ?? [];
$testimonials = merge_list($testimonials, $defaultTestimonials);

$contactTitle = get_setting('contact_title', '');
$contactDescription = get_setting('contact_description', '');
$contactEmail = get_setting('contact_email', '');
$contactPhone = get_setting('contact_phone', '');

?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Administración | Studio Cero</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      color-scheme: light;
      --primary: #4f46e5;
      --primary-dark: #3730a3;
      --accent: #22d3ee;
      --text: #0f172a;
      --muted: #64748b;
      --surface: #ffffff;
      --surface-muted: #f8fafc;
      --shadow: 0 24px 60px rgba(15, 23, 42, 0.12);
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: "Inter", sans-serif;
      color: var(--text);
      background: var(--surface-muted);
      line-height: 1.6;
      padding: 40px 0 60px;
    }

    .container {
      width: min(1100px, 92%);
      margin: 0 auto;
    }

    .card {
      background: var(--surface);
      border-radius: 24px;
      padding: 32px;
      box-shadow: var(--shadow);
    }

    h1 {
      font-size: clamp(2rem, 2vw + 1rem, 2.8rem);
      margin-bottom: 12px;
    }

    h2 {
      margin: 32px 0 12px;
      font-size: 1.3rem;
    }

    p {
      color: var(--muted);
    }

    label {
      font-weight: 600;
      display: block;
      margin-bottom: 6px;
    }

    input,
    textarea {
      width: 100%;
      padding: 12px 14px;
      border-radius: 14px;
      border: 1px solid rgba(148, 163, 184, 0.4);
      font-family: inherit;
    }

    textarea {
      min-height: 120px;
    }

    .grid {
      display: grid;
      gap: 16px;
    }

    .grid-2 {
      grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    }

    .group {
      margin-top: 20px;
      padding: 20px;
      background: var(--surface-muted);
      border-radius: 18px;
    }

    .btn {
      padding: 12px 22px;
      border-radius: 999px;
      border: none;
      font-weight: 600;
      cursor: pointer;
      background: var(--primary);
      color: #fff;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .btn:hover {
      background: var(--primary-dark);
      transform: translateY(-1px);
      box-shadow: var(--shadow);
    }

    .alert {
      padding: 14px 18px;
      border-radius: 14px;
      background: rgba(34, 211, 238, 0.15);
      color: var(--text);
      margin: 20px 0;
    }

    .divider {
      height: 1px;
      background: rgba(148, 163, 184, 0.4);
      margin: 24px 0;
    }

    .nav {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 26px;
    }

    .nav a {
      color: var(--primary);
      font-weight: 600;
    }

    .login-icon {
      width: 72px;
      height: 72px;
      margin: 0 auto 24px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 999px;
      background: rgba(79, 70, 229, 0.12);
      color: var(--primary);
    }

    .login-icon svg {
      width: 36px;
      height: 36px;
    }

    .input-with-icon {
      position: relative;
    }

    .input-with-icon svg {
      position: absolute;
      left: 14px;
      top: 50%;
      width: 18px;
      height: 18px;
      transform: translateY(-50%);
      color: var(--muted);
      pointer-events: none;
    }

    .input-with-icon input {
      padding-left: 44px;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="nav">
      <div>
        <h1>Panel de administración</h1>
        <p>Gestiona los contenidos visibles en el sitio.</p>
      </div>
      <div>
        <?php if ($isLoggedIn): ?>
          <a href="/admin.php?action=logout">Cerrar sesión</a>
        <?php endif; ?>
      </div>
    </div>

    <?php if ($message): ?>
      <div class="alert"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <?php if (!$isLoggedIn): ?>
      <div class="card" style="max-width: 520px;">
        <div class="login-icon" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 3l7 4v5c0 4.2-3 8-7 9-4-1-7-4.8-7-9V7l7-4z"></path>
            <path d="M9.5 12.5l2 2 3.5-3.5"></path>
          </svg>
        </div>
        <form method="post">
          <input type="hidden" name="action" value="login">
          <div class="grid">
            <div class="input-with-icon">
              <label for="username">Usuario</label>
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M20 21a8 8 0 1 0-16 0"></path>
                <circle cx="12" cy="9" r="4"></circle>
              </svg>
              <input id="username" name="username" type="text" required>
            </div>
            <div class="input-with-icon">
              <label for="password">Contraseña</label>
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <rect x="4" y="10" width="16" height="10" rx="2"></rect>
                <path d="M8 10V7a4 4 0 1 1 8 0v3"></path>
              </svg>
              <input id="password" name="password" type="password" required>
            </div>
            <button class="btn" type="submit">Ingresar</button>
            <p>Credenciales iniciales: admin / admin123 (cámbialas en la base de datos).</p>
          </div>
        </form>
        <div class="divider"></div>
        <form method="post">
          <input type="hidden" name="action" value="recover">
          <div class="grid">
            <div>
              <label for="recovery_username">Usuario</label>
              <input id="recovery_username" name="recovery_username" type="text" required>
            </div>
            <div>
              <label for="recovery_code">Código de recuperación</label>
              <input id="recovery_code" name="recovery_code" type="text" required>
            </div>
            <button class="btn" type="submit">Recuperar contraseña</button>
            <p>Solicita el código de recuperación al administrador del sistema.</p>
          </div>
        </form>
      </div>
    <?php else: ?>
      <form method="post" class="card">
        <input type="hidden" name="action" value="save">

        <h2>Hero principal</h2>
        <div class="grid">
          <div>
            <label for="hero_tagline">Etiqueta superior</label>
            <input id="hero_tagline" name="hero_tagline" type="text" value="<?php echo htmlspecialchars($heroTagline); ?>">
          </div>
          <div>
            <label for="hero_title">Título principal</label>
            <input id="hero_title" name="hero_title" type="text" value="<?php echo htmlspecialchars($heroTitle); ?>">
          </div>
          <div>
            <label for="hero_description">Descripción</label>
            <textarea id="hero_description" name="hero_description"><?php echo htmlspecialchars($heroDescription); ?></textarea>
          </div>
        </div>

        <div class="grid grid-2">
          <div>
            <label for="hero_primary_cta">CTA primario</label>
            <input id="hero_primary_cta" name="hero_primary_cta" type="text" value="<?php echo htmlspecialchars($heroPrimaryCta); ?>">
          </div>
          <div>
            <label for="hero_secondary_cta">CTA secundario</label>
            <input id="hero_secondary_cta" name="hero_secondary_cta" type="text" value="<?php echo htmlspecialchars($heroSecondaryCta); ?>">
          </div>
        </div>

        <h2>Tarjeta de resultados</h2>
        <div class="grid">
          <div>
            <label for="hero_card_title">Título</label>
            <input id="hero_card_title" name="hero_card_title" type="text" value="<?php echo htmlspecialchars($heroCardTitle); ?>">
          </div>
          <div>
            <label for="hero_card_description">Descripción</label>
            <textarea id="hero_card_description" name="hero_card_description"><?php echo htmlspecialchars($heroCardDescription); ?></textarea>
          </div>
        </div>
        <div class="grid grid-2">
          <?php foreach ($stats as $index => $stat): ?>
            <div class="group">
              <label>Estadística <?php echo $index + 1; ?></label>
              <div class="grid">
                <input name="stats[<?php echo $index; ?>][value]" type="text" placeholder="Valor" value="<?php echo htmlspecialchars($stat['value']); ?>">
                <input name="stats[<?php echo $index; ?>][label]" type="text" placeholder="Etiqueta" value="<?php echo htmlspecialchars($stat['label']); ?>">
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <h2>Servicios</h2>
        <div class="grid">
          <?php foreach ($services as $index => $service): ?>
            <div class="group">
              <label>Servicio <?php echo $index + 1; ?></label>
              <div class="grid">
                <input name="services[<?php echo $index; ?>][title]" type="text" placeholder="Título" value="<?php echo htmlspecialchars($service['title']); ?>">
                <textarea name="services[<?php echo $index; ?>][description]" placeholder="Descripción"><?php echo htmlspecialchars($service['description']); ?></textarea>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <h2>Proceso</h2>
        <div class="grid">
          <?php foreach ($process as $index => $step): ?>
            <div class="group">
              <label>Paso <?php echo $index + 1; ?></label>
              <div class="grid">
                <input name="process[<?php echo $index; ?>][step]" type="text" placeholder="Número" value="<?php echo htmlspecialchars($step['step']); ?>">
                <input name="process[<?php echo $index; ?>][title]" type="text" placeholder="Título" value="<?php echo htmlspecialchars($step['title']); ?>">
                <textarea name="process[<?php echo $index; ?>][description]" placeholder="Descripción"><?php echo htmlspecialchars($step['description']); ?></textarea>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <h2>Testimonios</h2>
        <div class="grid">
          <?php foreach ($testimonials as $index => $testimonial): ?>
            <div class="group">
              <label>Testimonio <?php echo $index + 1; ?></label>
              <div class="grid">
                <textarea name="testimonials[<?php echo $index; ?>][quote]" placeholder="Cita"><?php echo htmlspecialchars($testimonial['quote']); ?></textarea>
                <input name="testimonials[<?php echo $index; ?>][author]" type="text" placeholder="Autor" value="<?php echo htmlspecialchars($testimonial['author']); ?>">
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <h2>Contacto</h2>
        <div class="grid">
          <div>
            <label for="contact_title">Título</label>
            <input id="contact_title" name="contact_title" type="text" value="<?php echo htmlspecialchars($contactTitle); ?>">
          </div>
          <div>
            <label for="contact_description">Descripción</label>
            <textarea id="contact_description" name="contact_description"><?php echo htmlspecialchars($contactDescription); ?></textarea>
          </div>
          <div>
            <label for="contact_email">Email</label>
            <input id="contact_email" name="contact_email" type="email" value="<?php echo htmlspecialchars($contactEmail); ?>">
          </div>
          <div>
            <label for="contact_phone">Teléfono</label>
            <input id="contact_phone" name="contact_phone" type="text" value="<?php echo htmlspecialchars($contactPhone); ?>">
          </div>
        </div>

        <div style="margin-top: 24px;">
          <button class="btn" type="submit">Guardar cambios</button>
        </div>
      </form>
    <?php endif; ?>
  </div>
</body>
</html>
