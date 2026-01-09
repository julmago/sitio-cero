<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Studio Cero | Agencia Creativa</title>
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
    }

    img {
      max-width: 100%;
      display: block;
    }

    a {
      text-decoration: none;
      color: inherit;
    }

    .container {
      width: min(1120px, 90%);
      margin: 0 auto;
    }

    .nav {
      position: sticky;
      top: 0;
      z-index: 10;
      background: rgba(248, 250, 252, 0.9);
      backdrop-filter: blur(12px);
      border-bottom: 1px solid rgba(148, 163, 184, 0.2);
    }

    .nav-content {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 20px 0;
      gap: 24px;
    }

    .logo {
      font-weight: 700;
      font-size: 1.2rem;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .logo span {
      color: var(--primary);
    }

    .nav-links {
      display: flex;
      gap: 20px;
      font-weight: 500;
      color: var(--muted);
    }

    .nav-links a:hover {
      color: var(--primary);
    }

    .hero {
      padding: 90px 0 70px;
      background: radial-gradient(circle at top, rgba(79, 70, 229, 0.12), transparent 60%);
    }

    .hero-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      align-items: center;
      gap: 40px;
    }

    .hero h1 {
      font-size: clamp(2.5rem, 3vw + 1rem, 3.5rem);
      line-height: 1.1;
      margin-bottom: 20px;
    }

    .hero p {
      color: var(--muted);
      margin-bottom: 30px;
    }

    .hero-actions {
      display: flex;
      gap: 16px;
      flex-wrap: wrap;
    }

    .btn {
      padding: 14px 24px;
      border-radius: 999px;
      font-weight: 600;
      border: 1px solid transparent;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
    }

    .btn-primary {
      background: var(--primary);
      color: #fff;
      box-shadow: var(--shadow);
    }

    .btn-primary:hover {
      background: var(--primary-dark);
      transform: translateY(-2px);
    }

    .btn-outline {
      border-color: rgba(79, 70, 229, 0.3);
      color: var(--primary);
      background: #fff;
    }

    .btn-outline:hover {
      transform: translateY(-2px);
      box-shadow: var(--shadow);
    }

    .hero-card {
      background: var(--surface);
      border-radius: 28px;
      padding: 32px;
      box-shadow: var(--shadow);
      display: grid;
      gap: 20px;
    }

    .hero-card small {
      color: var(--muted);
      font-weight: 500;
    }

    .stats {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
      gap: 20px;
    }

    .stat {
      background: var(--surface-muted);
      padding: 18px;
      border-radius: 18px;
    }

    .stat h3 {
      font-size: 1.5rem;
      margin-bottom: 6px;
    }

    section {
      padding: 70px 0;
    }

    .section-title {
      text-align: center;
      margin-bottom: 50px;
    }

    .section-title h2 {
      font-size: clamp(2rem, 2vw + 1rem, 2.8rem);
      margin-bottom: 10px;
    }

    .section-title p {
      color: var(--muted);
      max-width: 600px;
      margin: 0 auto;
    }

    .services-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
      gap: 24px;
    }

    .service-card {
      padding: 24px;
      background: var(--surface);
      border-radius: 22px;
      box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
      display: grid;
      gap: 14px;
    }

    .service-card h3 {
      font-size: 1.2rem;
    }

    .service-card p {
      color: var(--muted);
    }

    .process {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
      gap: 26px;
    }

    .process-step {
      padding: 24px;
      border-radius: 20px;
      background: linear-gradient(135deg, rgba(79, 70, 229, 0.08), rgba(34, 211, 238, 0.14));
    }

    .process-step span {
      font-weight: 700;
      color: var(--primary);
    }

    .testimonials {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
      gap: 24px;
    }

    .testimonial {
      background: var(--surface);
      border-radius: 20px;
      padding: 24px;
      box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
      display: grid;
      gap: 12px;
    }

    .testimonial strong {
      font-weight: 600;
    }

    .contact {
      background: #0f172a;
      color: #f8fafc;
      border-radius: 36px;
      padding: 50px;
      display: grid;
      gap: 24px;
    }

    .contact p {
      color: rgba(248, 250, 252, 0.7);
    }

    form {
      display: grid;
      gap: 16px;
    }

    .form-group {
      display: grid;
      gap: 8px;
    }

    label {
      font-weight: 500;
    }

    input,
    textarea {
      width: 100%;
      padding: 12px 16px;
      border-radius: 14px;
      border: 1px solid transparent;
      font-family: inherit;
    }

    input:focus,
    textarea:focus {
      outline: 2px solid rgba(34, 211, 238, 0.6);
    }

    textarea {
      min-height: 120px;
      resize: vertical;
    }

    .contact-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
      gap: 30px;
      align-items: center;
    }

    .footer {
      padding: 40px 0;
      text-align: center;
      color: var(--muted);
    }

    @media (max-width: 720px) {
      .nav-links {
        display: none;
      }

      .contact {
        padding: 32px;
      }
    }
  </style>
</head>
<body>
  <header class="nav">
    <div class="container nav-content">
      <a class="logo" href="#hero">Studio <span>Cero</span></a>
      <nav class="nav-links">
        <a href="#servicios">Servicios</a>
        <a href="#proceso">Proceso</a>
        <a href="#testimonios">Testimonios</a>
        <a href="#contacto">Contacto</a>
      </nav>
      <a class="btn btn-outline" href="#contacto">Agenda una demo</a>
    </div>
  </header>

  <main>
    <section class="hero" id="hero">
      <div class="container hero-grid">
        <div>
          <p><strong>Agencia creativa para negocios modernos</strong></p>
          <h1>Diseñamos experiencias digitales que elevan tu marca.</h1>
          <p>Desde identidad visual hasta campañas omnicanal, acompañamos a tu negocio con estrategias medibles, contenido cautivador y tecnología lista para escalar.</p>
          <div class="hero-actions">
            <a class="btn btn-primary" href="#contacto">Quiero una propuesta</a>
            <a class="btn btn-outline" href="#servicios">Ver servicios</a>
          </div>
        </div>
        <div class="hero-card">
          <small>Resultados en 2024</small>
          <h2>+120 marcas impulsadas en LATAM</h2>
          <p>Equipo multidisciplinario con estrategia, diseño, performance y producción. Todo en un solo lugar.</p>
          <div class="stats">
            <div class="stat">
              <h3>3.2x</h3>
              <p>Crecimiento promedio</p>
            </div>
            <div class="stat">
              <h3>48h</h3>
              <p>Tiempo de respuesta</p>
            </div>
            <div class="stat">
              <h3>92%</h3>
              <p>Clientes recurrentes</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section id="servicios">
      <div class="container">
        <div class="section-title">
          <h2>Servicios que potencian tu negocio</h2>
          <p>Creamos soluciones integrales para marcas en crecimiento: estrategia, diseño y marketing digital enfocados en resultados.</p>
        </div>
        <div class="services-grid">
          <article class="service-card">
            <h3>Branding &amp; Identidad</h3>
            <p>Nombres, logo, tono y storytelling coherentes para posicionar tu negocio.</p>
          </article>
          <article class="service-card">
            <h3>Diseño Web</h3>
            <p>Interfaces modernas, rápidas y responsivas listas para convertir visitas en clientes.</p>
          </article>
          <article class="service-card">
            <h3>Marketing Digital</h3>
            <p>Campañas en redes sociales y paid media con analítica avanzada para optimizar la inversión.</p>
          </article>
          <article class="service-card">
            <h3>Contenido &amp; Foto</h3>
            <p>Producción audiovisual profesional para redes, e-commerce y lanzamientos.</p>
          </article>
        </div>
      </div>
    </section>

    <section id="proceso">
      <div class="container">
        <div class="section-title">
          <h2>Un proceso claro, con foco en resultados</h2>
          <p>Trabajamos junto a tu equipo para entregar rápido, medir y optimizar.</p>
        </div>
        <div class="process">
          <div class="process-step">
            <span>01</span>
            <h3>Diagnóstico</h3>
            <p>Analizamos tu marca, competencia y objetivos para definir oportunidades reales.</p>
          </div>
          <div class="process-step">
            <span>02</span>
            <h3>Concepto creativo</h3>
            <p>Diseñamos una propuesta visual y narrativa alineada a tu audiencia.</p>
          </div>
          <div class="process-step">
            <span>03</span>
            <h3>Ejecución + medición</h3>
            <p>Lanzamos, medimos y ajustamos para maximizar impacto y ventas.</p>
          </div>
        </div>
      </div>
    </section>

    <section id="testimonios">
      <div class="container">
        <div class="section-title">
          <h2>Historias de clientes satisfechos</h2>
          <p>Negocios reales que elevaron su presencia con Studio Cero.</p>
        </div>
        <div class="testimonials">
          <article class="testimonial">
            <p>"Nos ayudaron a relanzar nuestra marca y duplicamos las ventas en tres meses."</p>
            <strong>Ana Torres · Retail Fashion</strong>
          </article>
          <article class="testimonial">
            <p>"El nuevo sitio es rápido, elegante y los leads aumentaron desde la primera semana."</p>
            <strong>Camilo Vargas · SaaS B2B</strong>
          </article>
          <article class="testimonial">
            <p>"Nos encanta el equipo: creativos, ordenados y siempre con propuestas frescas."</p>
            <strong>Lucía Gómez · Gastronomía</strong>
          </article>
        </div>
      </div>
    </section>

    <section id="contacto">
      <div class="container">
        <div class="contact">
          <div class="contact-grid">
            <div>
              <h2>Hablemos de tu próximo proyecto</h2>
              <p>Cuéntanos sobre tu negocio y te responderemos en menos de 48 horas.</p>
              <p><strong>Email:</strong> hola@studiocero.com<br><strong>Teléfono:</strong> +56 9 1234 5678</p>
            </div>
            <form>
              <div class="form-group">
                <label for="nombre">Nombre</label>
                <input id="nombre" name="nombre" type="text" placeholder="Tu nombre" required>
              </div>
              <div class="form-group">
                <label for="email">Correo</label>
                <input id="email" name="email" type="email" placeholder="tu@email.com" required>
              </div>
              <div class="form-group">
                <label for="mensaje">Mensaje</label>
                <textarea id="mensaje" name="mensaje" placeholder="Cuéntanos sobre tu proyecto" required></textarea>
              </div>
              <button class="btn btn-primary" type="submit">Enviar mensaje</button>
            </form>
          </div>
        </div>
      </div>
    </section>
  </main>

  <footer class="footer">
    <div class="container">
      <p>© 2024 Studio Cero. Todos los derechos reservados.</p>
    </div>
  </footer>
</body>
</html>
