<?php

declare(strict_types=1);

function db(): PDO
{
    static $pdo;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dataDir = __DIR__ . '/../data';
    if (!is_dir($dataDir)) {
        mkdir($dataDir, 0775, true);
    }

    $dbPath = $dataDir . '/site.db';
    $pdo = new PDO('sqlite:' . $dbPath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdo->exec('CREATE TABLE IF NOT EXISTS settings (key TEXT PRIMARY KEY, value TEXT NOT NULL)');
    $pdo->exec('CREATE TABLE IF NOT EXISTS admins (id INTEGER PRIMARY KEY AUTOINCREMENT, username TEXT UNIQUE NOT NULL, password_hash TEXT NOT NULL)');

    $adminCount = (int) $pdo->query('SELECT COUNT(*) FROM admins')->fetchColumn();
    if ($adminCount === 0) {
        $hash = password_hash('admin123', PASSWORD_DEFAULT);
        $insertAdmin = $pdo->prepare('INSERT INTO admins (username, password_hash) VALUES (?, ?)');
        $insertAdmin->execute(['admin', $hash]);
    }

    $defaultSettings = [
        'hero_tagline' => 'Agencia creativa para negocios modernos',
        'hero_title' => 'Diseñamos experiencias digitales que elevan tu marca.',
        'hero_description' => 'Desde identidad visual hasta campañas omnicanal, acompañamos a tu negocio con estrategias medibles, contenido cautivador y tecnología lista para escalar.',
        'hero_primary_cta' => 'Quiero una propuesta',
        'hero_secondary_cta' => 'Ver servicios',
        'hero_card_title' => '+120 marcas impulsadas en LATAM',
        'hero_card_description' => 'Equipo multidisciplinario con estrategia, diseño, performance y producción. Todo en un solo lugar.',
        'hero_stats' => json_encode([
            ['value' => '3.2x', 'label' => 'Crecimiento promedio'],
            ['value' => '48h', 'label' => 'Tiempo de respuesta'],
            ['value' => '92%', 'label' => 'Clientes recurrentes'],
        ], JSON_UNESCAPED_UNICODE),
        'services' => json_encode([
            ['title' => 'Branding & Identidad', 'description' => 'Nombres, logo, tono y storytelling coherentes para posicionar tu negocio.'],
            ['title' => 'Diseño Web', 'description' => 'Interfaces modernas, rápidas y responsivas listas para convertir visitas en clientes.'],
            ['title' => 'Marketing Digital', 'description' => 'Campañas en redes sociales y paid media con analítica avanzada para optimizar la inversión.'],
            ['title' => 'Contenido & Foto', 'description' => 'Producción audiovisual profesional para redes, e-commerce y lanzamientos.'],
        ], JSON_UNESCAPED_UNICODE),
        'process' => json_encode([
            ['step' => '01', 'title' => 'Diagnóstico', 'description' => 'Analizamos tu marca, competencia y objetivos para definir oportunidades reales.'],
            ['step' => '02', 'title' => 'Concepto creativo', 'description' => 'Diseñamos una propuesta visual y narrativa alineada a tu audiencia.'],
            ['step' => '03', 'title' => 'Ejecución + medición', 'description' => 'Lanzamos, medimos y ajustamos para maximizar impacto y ventas.'],
        ], JSON_UNESCAPED_UNICODE),
        'testimonials' => json_encode([
            ['quote' => '"Nos ayudaron a relanzar nuestra marca y duplicamos las ventas en tres meses."', 'author' => 'Ana Torres · Retail Fashion'],
            ['quote' => '"El nuevo sitio es rápido, elegante y los leads aumentaron desde la primera semana."', 'author' => 'Camilo Vargas · SaaS B2B'],
            ['quote' => '"Nos encanta el equipo: creativos, ordenados y siempre con propuestas frescas."', 'author' => 'Lucía Gómez · Gastronomía'],
        ], JSON_UNESCAPED_UNICODE),
        'contact_title' => 'Hablemos de tu próximo proyecto',
        'contact_description' => 'Cuéntanos sobre tu negocio y te responderemos en menos de 48 horas.',
        'contact_email' => 'hola@studiocero.com',
        'contact_phone' => '+56 9 1234 5678',
    ];

    $select = $pdo->prepare('SELECT value FROM settings WHERE key = ?');
    $insert = $pdo->prepare('INSERT INTO settings (key, value) VALUES (?, ?)');

    foreach ($defaultSettings as $key => $value) {
        $select->execute([$key]);
        if ($select->fetchColumn() === false) {
            $insert->execute([$key, $value]);
        }
    }

    return $pdo;
}

function get_setting(string $key, string $default = ''): string
{
    $pdo = db();
    $stmt = $pdo->prepare('SELECT value FROM settings WHERE key = ?');
    $stmt->execute([$key]);
    $value = $stmt->fetchColumn();

    return $value !== false ? (string) $value : $default;
}

function set_setting(string $key, string $value): void
{
    $pdo = db();
    $stmt = $pdo->prepare('INSERT INTO settings (key, value) VALUES (?, ?) ON CONFLICT(key) DO UPDATE SET value = excluded.value');
    $stmt->execute([$key, $value]);
}
