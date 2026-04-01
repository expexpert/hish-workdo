<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>SimplyCompta - Coming Soon</title>
  <style>
    :root {
      --bg: #0F172A;
      --bg-soft: #111C34;
      --card: rgba(30, 41, 59, 0.78);
      --card-border: rgba(148, 163, 184, 0.14);
      --text: #F8FAFC;
      --muted: #94A3B8;
      --green: #22C55E;
      --green-dark: #16A34A;
      --white-soft: rgba(248, 250, 252, 0.08);
      --shadow: 0 20px 60px rgba(0, 0, 0, 0.35);
      --radius-xl: 28px;
      --radius-lg: 20px;
      --radius-md: 14px;
      --max: 1200px;
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    html, body {
      height: 100%;
      font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
      background:
        radial-gradient(circle at 15% 20%, rgba(34, 197, 94, 0.12), transparent 22%),
        radial-gradient(circle at 85% 15%, rgba(34, 197, 94, 0.08), transparent 18%),
        radial-gradient(circle at 70% 70%, rgba(59, 130, 246, 0.08), transparent 25%),
        linear-gradient(180deg, #0B1220 0%, #0F172A 100%);
      color: var(--text);
      overflow-x: hidden;
    }

    body::before,
    body::after {
      content: "";
      position: fixed;
      width: 420px;
      height: 420px;
      border-radius: 50%;
      filter: blur(120px);
      z-index: 0;
      pointer-events: none;
    }

    body::before {
      top: -100px;
      left: -100px;
      background: rgba(34, 197, 94, 0.10);
    }

    body::after {
      right: -120px;
      bottom: -120px;
      background: rgba(34, 197, 94, 0.08);
    }

    .page {
      position: relative;
      z-index: 1;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    .container {
      width: min(100% - 32px, var(--max));
      margin: 0 auto;
    }

    .nav {
      padding: 24px 0;
    }

    .brand {
      display: inline-flex;
      align-items: center;
      gap: 12px;
      text-decoration: none;
      color: var(--text);
    }

    .brand-mark {
      width: 44px;
      height: 44px;
      border-radius: 14px;
      background: linear-gradient(135deg, #22C55E 0%, #16A34A 100%);
      display: grid;
      place-items: center;
      box-shadow: 0 10px 28px rgba(34, 197, 94, 0.28);
      font-weight: 800;
      color: white;
      letter-spacing: -0.04em;
    }

    .brand span {
      font-size: 1.2rem;
      font-weight: 700;
      letter-spacing: -0.02em;
    }

    .hero {
      flex: 1;
      display: grid;
      grid-template-columns: 1.1fr 0.9fr;
      align-items: center;
      gap: 48px;
      padding: 24px 0 64px;
    }

    .badge {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 10px 14px;
      border-radius: 999px;
      background: rgba(34, 197, 94, 0.10);
      border: 1px solid rgba(34, 197, 94, 0.24);
      color: #BBF7D0;
      font-size: 0.92rem;
      margin-bottom: 22px;
      backdrop-filter: blur(10px);
    }

    .dot {
      width: 8px;
      height: 8px;
      border-radius: 999px;
      background: var(--green);
      box-shadow: 0 0 0 6px rgba(34, 197, 94, 0.12);
      animation: pulse 1.8s infinite;
    }

    @keyframes pulse {
      0%, 100% { transform: scale(1); opacity: 1; }
      50% { transform: scale(1.2); opacity: 0.75; }
    }

    .title {
      font-size: clamp(2.4rem, 5vw, 4.8rem);
      line-height: 0.95;
      letter-spacing: -0.05em;
      font-weight: 800;
      max-width: 760px;
    }

    .title .highlight {
      color: var(--green);
      text-shadow: 0 0 24px rgba(34, 197, 94, 0.22);
    }

    .subtitle {
      margin-top: 22px;
      max-width: 620px;
      color: var(--muted);
      font-size: 1.08rem;
      line-height: 1.7;
    }

    .cta-row {
      display: flex;
      flex-wrap: wrap;
      gap: 14px;
      margin-top: 30px;
    }

    .btn {
      appearance: none;
      border: none;
      text-decoration: none;
      cursor: pointer;
      border-radius: 14px;
      padding: 14px 18px;
      font-weight: 700;
      font-size: 0.98rem;
      transition: transform 0.2s ease, box-shadow 0.2s ease, opacity 0.2s ease;
    }

    .btn:hover {
      transform: translateY(-1px);
    }

    .btn-primary {
      background: linear-gradient(135deg, var(--green) 0%, var(--green-dark) 100%);
      color: white;
      box-shadow: 0 16px 36px rgba(34, 197, 94, 0.24);
    }

    .btn-secondary {
      background: rgba(248, 250, 252, 0.04);
      color: var(--text);
      border: 1px solid rgba(148, 163, 184, 0.16);
      backdrop-filter: blur(10px);
    }

    .meta {
      display: flex;
      flex-wrap: wrap;
      gap: 18px;
      margin-top: 26px;
      color: var(--muted);
      font-size: 0.94rem;
    }

    .meta div {
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .visual {
      position: relative;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 560px;
    }

    .glow {
      position: absolute;
      width: 360px;
      height: 360px;
      border-radius: 50%;
      background: rgba(34, 197, 94, 0.12);
      filter: blur(90px);
      z-index: 0;
    }

    .phone {
      position: relative;
      z-index: 1;
      width: 320px;
      background: linear-gradient(180deg, rgba(15, 23, 42, 0.95) 0%, rgba(13, 20, 36, 0.92) 100%);
      border: 1px solid rgba(148, 163, 184, 0.12);
      border-radius: 36px;
      padding: 18px;
      box-shadow: var(--shadow);
      backdrop-filter: blur(12px);
    }

    .phone::before {
      content: "";
      display: block;
      width: 110px;
      height: 26px;
      border-radius: 999px;
      background: #0A1020;
      margin: 0 auto 18px;
    }

    .screen {
      border-radius: 24px;
      overflow: hidden;
      background:
        radial-gradient(circle at top right, rgba(34, 197, 94, 0.08), transparent 28%),
        linear-gradient(180deg, #081120 0%, #0D172B 100%);
      padding: 18px;
      min-height: 590px;
      display: flex;
      flex-direction: column;
      gap: 14px;
    }

    .screen-top {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 6px;
    }

    .screen-app {
      font-size: 0.9rem;
      color: var(--muted);
    }

    .screen-chip {
      background: rgba(34, 197, 94, 0.12);
      color: #BBF7D0;
      border: 1px solid rgba(34, 197, 94, 0.2);
      padding: 7px 10px;
      border-radius: 999px;
      font-size: 0.75rem;
      font-weight: 700;
    }

    .screen h3 {
      font-size: 1.35rem;
      line-height: 1.2;
      letter-spacing: -0.03em;
      margin-top: 4px;
    }

    .screen p {
      color: var(--muted);
      font-size: 0.9rem;
      line-height: 1.55;
    }

    .stat-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 12px;
      margin-top: 4px;
    }

    .stat-card {
      background: rgba(30, 41, 59, 0.72);
      border: 1px solid var(--card-border);
      border-radius: 16px;
      padding: 14px;
      backdrop-filter: blur(8px);
    }

    .stat-card.large {
      grid-column: 1 / -1;
      background: linear-gradient(135deg, rgba(34, 197, 94, 0.14), rgba(30, 41, 59, 0.82));
    }

    .label {
      font-size: 0.78rem;
      color: var(--muted);
      margin-bottom: 8px;
    }

    .value {
      font-size: 1.35rem;
      font-weight: 800;
      letter-spacing: -0.03em;
    }

    .trend {
      margin-top: 8px;
      font-size: 0.78rem;
      color: #86EFAC;
    }

    .invoice-list {
      display: flex;
      flex-direction: column;
      gap: 10px;
      margin-top: 4px;
    }

    .invoice {
      display: flex;
      justify-content: space-between;
      gap: 12px;
      background: rgba(30, 41, 59, 0.72);
      border: 1px solid var(--card-border);
      border-radius: 16px;
      padding: 13px 14px;
    }

    .invoice strong {
      display: block;
      font-size: 0.92rem;
    }

    .invoice small {
      color: var(--muted);
      display: block;
      margin-top: 5px;
    }

    .amount {
      text-align: right;
      font-weight: 800;
      font-size: 0.95rem;
      white-space: nowrap;
    }

    .status {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      margin-top: 8px;
      padding: 6px 10px;
      border-radius: 999px;
      font-size: 0.72rem;
      font-weight: 700;
    }

    .status.pending {
      background: rgba(245, 158, 11, 0.12);
      color: #FCD34D;
      border: 1px solid rgba(245, 158, 11, 0.22);
    }

    .status.paid {
      background: rgba(34, 197, 94, 0.12);
      color: #86EFAC;
      border: 1px solid rgba(34, 197, 94, 0.22);
    }

    .status.quote {
      background: rgba(59, 130, 246, 0.12);
      color: #93C5FD;
      border: 1px solid rgba(59, 130, 246, 0.22);
    }

    .waitlist {
      margin-top: 24px;
      display: flex;
      gap: 10px;
      padding: 8px;
      border-radius: 18px;
      background: rgba(255, 255, 255, 0.04);
      border: 1px solid rgba(148, 163, 184, 0.12);
      backdrop-filter: blur(10px);
    }

    .waitlist input,
    .waitlist select {
      flex: 1;
      min-width: 0;
      border: none;
      outline: none;
      background: transparent;
      color: var(--text);
      padding: 14px 14px;
      font-size: 0.96rem;
    }

    .waitlist input::placeholder {
      color: #7C8CA5;
    }

    .mini-note {
      margin-top: 10px;
      color: var(--muted);
      font-size: 0.82rem;
    }

    .footer {
      padding: 0 0 36px;
      color: var(--muted);
      font-size: 0.9rem;
    }

    .toast {
      position: fixed;
      right: 20px;
      bottom: 20px;
      background: #101A30;
      color: var(--text);
      border: 1px solid rgba(34, 197, 94, 0.24);
      padding: 14px 16px;
      border-radius: 14px;
      box-shadow: var(--shadow);
      opacity: 0;
      transform: translateY(8px);
      pointer-events: none;
      transition: all 0.25s ease;
      z-index: 50;
    }

    .toast.show {
      opacity: 1;
      transform: translateY(0);
    }

    @media (max-width: 980px) {
      .hero {
        grid-template-columns: 1fr;
        gap: 36px;
        padding-top: 8px;
      }

      .visual {
        min-height: auto;
      }

      .phone {
        width: min(100%, 380px);
      }
    }

    @media (max-width: 640px) {
      .title {
        font-size: 2.5rem;
      }

      .waitlist {
        flex-direction: column;
      }

      .cta-row {
        flex-direction: column;
        align-items: stretch;
      }

      .btn {
        width: 100%;
        text-align: center;
      }

      .meta {
        gap: 12px;
        flex-direction: column;
      }
    }
  </style>
</head>
<body>
  <div class="page">
    <header class="nav">
      <div class="container">
        <a href="#" class="brand">
          <div class="brand-mark">SC</div>
          <span>SimplyCompta</span>
        </a>
      </div>
    </header>

    <main class="container hero">
      <section>
        <div class="badge">
          <span class="dot"></span>
          Lancement prochainement
        </div>

        <h1 class="title">
          La comptabilité devient enfin
          <span class="highlight">simple</span>.
        </h1>

        <p class="subtitle">
          SimplyCompta arrive bientôt avec une expérience moderne pour gérer
          vos factures, vos dépenses, vos documents et votre relation avec votre comptable,
          le tout dans une interface claire, premium et pensée pour le Maroc.
        </p>

        <div class="cta-row">
          <a href="#waitlist" class="btn btn-primary">Rejoindre la liste d’attente</a>
          <a href="#preview" class="btn btn-secondary">Voir l’aperçu</a>
        </div>

        <div class="meta">
          <div>✅ Facturation intelligente</div>
          <div>✅ Dépenses & TVA</div>
          <div>✅ Mobile-first</div>
          <div>✅ WhatsApp intégré</div>
        </div>

        <form class="waitlist" id="waitlist">
          <input type="email" id="email" placeholder="Votre adresse email" required />
          <select id="profile">
            <option value="Entrepreneur">Entrepreneur</option>
            <option value="Comptable">Comptable</option>
            <option value="Cabinet">Cabinet</option>
          </select>
          <button type="submit" class="btn btn-primary">Accès anticipé</button>
        </form>

        <p class="mini-note">
          Aucun spam. Juste les nouveautés, l’accès anticipé et les avantages de lancement.
        </p>
      </section>

      <section class="visual" id="preview">
        <div class="glow"></div>

        <div class="phone">
          <div class="screen">
            <div class="screen-top">
              <div class="screen-app">SimplyCompta</div>
              <div class="screen-chip">Coming Soon</div>
            </div>

            <div>
              <h3>Bonjour Yassine 👋</h3>
              <p>Voici votre résumé financier de mars.</p>
            </div>

            <div class="stat-grid">
              <div class="stat-card">
                <div class="label">Chiffre d’affaires</div>
                <div class="value">12 450 DH</div>
                <div class="trend">+12% vs février</div>
              </div>

              <div class="stat-card">
                <div class="label">Dépenses</div>
                <div class="value">4 200 DH</div>
                <div class="trend">-5% vs février</div>
              </div>

              <div class="stat-card large">
                <div class="label">Résultat</div>
                <div class="value">8 250 DH</div>
                <div class="trend">TVA estimée : 2 150 DH</div>
              </div>
            </div>

            <div class="invoice-list">
              <div class="invoice">
                <div>
                  <strong>Client Alpha</strong>
                  <small>Facture #2024-015</small>
                </div>
                <div class="amount">
                  2 500 DH
                  <div class="status pending">En attente</div>
                </div>
              </div>

              <div class="invoice">
                <div>
                  <strong>Société BETA</strong>
                  <small>Facture #2024-012</small>
                </div>
                <div class="amount">
                  1 200 DH
                  <div class="status quote">Émise</div>
                </div>
              </div>

              <div class="invoice">
                <div>
                  <strong>SARL Gamma</strong>
                  <small>Facture #2024-009</small>
                </div>
                <div class="amount">
                  3 800 DH
                  <div class="status paid">Payée</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </main>

    <footer class="container footer">
      © <span id="year"></span> SimplyCompta — Site en préparation
    </footer>
  </div>

  <div class="toast" id="toast">Merci, vous êtes bien inscrit à la liste d’attente.</div>

  <script>
    const form = document.getElementById("waitlist");
    const toast = document.getElementById("toast");
    const year = document.getElementById("year");

    year.textContent = new Date().getFullYear();

    form.addEventListener("submit", function (e) {
      e.preventDefault();

      const email = document.getElementById("email").value.trim();
      const profile = document.getElementById("profile").value;

      if (!email) return;

      console.log("Waitlist signup:", { email, profile });

      toast.classList.add("show");
      form.reset();

      setTimeout(() => {
        toast.classList.remove("show");
      }, 2600);
    });
  </script>
</body>
</html>