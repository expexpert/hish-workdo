<?php
$successMessage = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $successMessage = "Merci, votre demande a bien été reçue.";
}
?>
<!doctype html>
<html lang="fr">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SimplyCompta - Comptabilité simplifiée</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <style>
    body {
      font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif
    }

    .pack-card.selected {
      box-shadow: 0 0 0 4px rgb(5 150 105)
    }

    [data-cabinet-pack].selected {
      box-shadow: 0 0 0 4px rgb(37 99 235)
    }


    /* Billing Selector */
    .billing-selector-wrapper {
      display: flex;
      justify-content: center;
      margin-bottom: 3rem;
    }

    .billing-selector {
      display: inline-flex;
      background: #fff;
      border-radius: 0.5rem;
      padding: 0.25rem;
      box-shadow: 0 1px 3px rgba(0, 0, 0, .1);
      border: 1px solid #e5e7eb;
    }

    .billing-btn {
      position: relative;
      padding: 0.75rem 1.5rem;
      border-radius: 0.375rem;
      border: none;
      background: transparent;
      color: #374151;
      cursor: pointer;
      transition: all .3s;
      font-size: 1rem;
      font-weight: 500;
    }

    .billing-btn:hover {
      background: #f3f4f6;
    }

    .billing-btn.active {
      background: #2563eb;
      color: #fff;
      box-shadow: 0 1px 2px rgba(0, 0, 0, .05);
    }

    .save-text {
      font-size: 0.875rem;
    }

    .best-value-badge {
      position: absolute;
      top: -0.5rem;
      right: -0.5rem;
      background: #10b981;
      color: #fff;
      font-size: 0.75rem;
      padding: 0.125rem 0.5rem;
      border-radius: 9999px;
    }


    /* Plans Grid */
    .plans-grid {
      display: grid;
      grid-template-columns: 1fr;
      gap: 15px;
    }


    .plans-column {
      display: grid;
      grid-template-columns: 1fr;
      gap: 15px;
    }

    @media (min-width: 768px) {
      .plans-column {
        grid-template-columns: repeat(4, 1fr);
      }
    }

    /* Plan Cards */
    .plan-card {
      background: #fff;
      border-radius: 0.75rem;
      padding: 1.5rem;
      border: 2px solid #e5e7eb;
      box-shadow: 0 1px 2px rgba(0, 0, 0, .05);
      cursor: pointer;
      transition: all .3s;
      position: relative;
    }

    .plan-card:hover {
      transform: translateY(-4px);
      border-color: #d1d5db;
    }

    .plan-card.recommended {
      border-color: #2563eb;
      box-shadow: 0 0 0 3px rgba(37, 99, 235, .1);
    }

    .plan-card.selected {
      border-color: #60a5fa;
    }

    .plan-card.hidden {
      display: none;
    }

    .recommended-badge {
      position: absolute;
      top: -0.75rem;
      left: 50%;
      transform: translateX(-50%);
      background: #2563eb;
      color: #fff;
      font-size: 0.875rem;
      padding: 0.25rem 1rem;
      border-radius: 9999px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, .1);
      white-space: nowrap;
    }

    .plan-header {
      margin-bottom: 1rem;
    }

    .plan-name {
      font-size: 1.25rem;
      font-weight: bold;
      color: #111827;
      margin-bottom: 0.25rem;
    }

    .plan-description {
      font-size: 0.875rem;
      color: #6b7280;
    }

    /* Price display */
    .plan-price {
      margin-bottom: 1.5rem;
    }

    .price-wrapper {
      display: flex;
      align-items: baseline;
      gap: 0.25rem;
      margin-bottom: 0.25rem;
    }

    .price {
      font-size: 1.875rem;
      font-weight: bold;
      color: #111827;
    }

    .currency {
      color: #6b7280;
      font-size: 1rem;
    }

    .period {
      color: #9ca3af;
      font-size: 1rem;
    }

    .price-detail {
      font-size: 0.875rem;
      color: #9ca3af;
      min-height: 1.25rem;
    }

    .badge-discount {
      display: inline-block;
      background: #d1fae5;
      color: #065f46;
      font-size: 0.75rem;
      padding: 0.1rem 0.4rem;
      border-radius: 9999px;
      margin-left: 0.4rem;
      font-weight: 600;
      vertical-align: middle;
    }

    /* Features */
    .features-list {
      list-style: none;
      margin-bottom: 1.5rem;
    }

    .feature-item {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      margin-bottom: 0.75rem;
      font-size: 0.875rem;
      color: #374151;
    }

    .check {
      color: #10b981;
      font-weight: bold;
      font-size: 1.25rem;
      flex-shrink: 0;
    }

    /* Plan Button */
    .plan-btn {
      width: 100%;
      padding: 0.75rem;
      border-radius: 0.5rem;
      border: none;
      font-weight: 600;
      cursor: pointer;
      transition: all .3s;
      font-size: 1rem;
    }

    .plan-card:not(.recommended) .plan-btn {
      background: #f3f4f6;
      color: #111827;
    }

    .plan-card:not(.recommended) .plan-btn:hover {
      background: #e5e7eb;
    }

    .plan-card.recommended .plan-btn {
      background: #2563eb;
      color: #fff;
      box-shadow: 0 4px 6px rgba(0, 0, 0, .1);
    }

    .plan-card.recommended .plan-btn:hover {
      background: #1d4ed8;
    }

    .plan-card.selected:not(.recommended) .plan-btn {
      background: #2563eb;
      color: #fff;
    }


    /* Overlay */
    #otp-modal {
      display: none;
      position: fixed;
      inset: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.55);
      z-index: 9999;

      /* center content */
      align-items: center;
      justify-content: center;
    }

    /* Modal Box */
    #otp-modal .modal-content {
      width: 100%;
      max-width: 420px;
      background: #ffffff;
      border-radius: 16px;
      padding: 30px;
      box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
      position: relative;
      animation: modalFade .25s ease;
    }

    /* Title */
    #otp-modal .modal-title {
      font-size: 24px;
      font-weight: 700;
      color: #111827;
      margin-bottom: 10px;
    }

    /* Description */
    #otp-modal .modal-text {
      font-size: 14px;
      color: #6b7280;
      margin-bottom: 20px;
      line-height: 1.5;
    }

    /* OTP Input */
    #otp-input {
      width: 100%;
      height: 52px;
      border: 1px solid #d1d5db;
      border-radius: 10px;
      padding: 0 15px;
      font-size: 18px;
      letter-spacing: 4px;
      text-align: center;
      outline: none;
      transition: 0.2s ease;
      margin-bottom: 18px;
      box-sizing: border-box;
    }

    #otp-input:focus {
      border-color: #2563eb;
      box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.12);
    }

    /* Verify Button */
    #submit-otp {
      width: 100%;
      height: 52px;
      border: none;
      border-radius: 10px;
      background: #2563eb;
      color: #fff;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      transition: 0.2s ease;
    }

    #submit-otp:hover {
      background: #1d4ed8;
    }

    .form-error-message {
      display: none;
      background-color: #fef2f2;
      border: 1px solid #fecaca;
      color: #b91c1c;
      padding: 1rem;
      border-radius: 0.5rem;
      margin-bottom: 1.5rem;
      font-size: 0.875rem;
      white-space: pre-line;
    }

    /* Close Button */
    #otp-modal .close-modal {
      position: absolute;
      top: 14px;
      right: 14px;
      width: 34px;
      height: 34px;
      border: none;
      border-radius: 50%;
      background: #f3f4f6;
      font-size: 18px;
      cursor: pointer;
      transition: 0.2s ease;
    }

    #otp-modal .close-modal:hover {
      background: #e5e7eb;
    }

    /* Animation */
    @keyframes modalFade {
      from {
        opacity: 0;
        transform: translateY(15px) scale(0.97);
      }

      to {
        opacity: 1;
        transform: translateY(0) scale(1);
      }
    }
  </style>
</head>

<body>
  <?php if ($successMessage): ?>
    <div class="fixed top-4 left-1/2 -translate-x-1/2 z-[999] bg-emerald-600 text-white px-6 py-3 rounded-xl shadow-lg"><?= htmlspecialchars($successMessage) ?></div>
  <?php endif; ?>
  <div class="size-full overflow-y-auto bg-white">
    <!-- Header -->
    <header class="sticky top-0 z-50 bg-white shadow-sm">
      <nav class="max-w-7xl mx-auto px-6 py-4">
        <div class="flex items-center justify-between">
          <!-- Logo -->
          <div class="text-2xl font-bold text-emerald-600">
            SimplyCompta
          </div>

          <!-- Desktop Navigation -->
          <div class="hidden md:flex items-center gap-8">
            <a href="#fonctionnalites" class="text-slate-700 hover:text-emerald-600 transition-colors">
              Fonctionnalités
            </a>
            <a href="#tarifs" class="text-slate-700 hover:text-emerald-600 transition-colors">
              Tarifs
            </a>
            <a href="#a-propos" class="text-slate-700 hover:text-emerald-600 transition-colors">
              À propos
            </a>
            <a href="#ressources" class="text-slate-700 hover:text-emerald-600 transition-colors">
              Ressources
            </a>
          </div>

          <!-- Right side -->
          <div class="hidden md:flex items-center gap-4">
            <button class="text-slate-700 hover:text-emerald-600 transition-colors">
              Se connecter
            </button>
            <button class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2.5 rounded-lg transition-colors">
              Commencer gratuitement
            </button>
          </div>

          <!-- Mobile menu button -->
          <button
            id="mobileMenuBtn"
            class="md:hidden text-slate-700">
            <i data-lucide="menu" class="w-6 h-6"></i>
          </button>
        </div>

        <!-- Mobile menu -->
        <div id="mobileMenu" class="hidden md:hidden pt-4 pb-3 space-y-3">
          <a href="#fonctionnalites" class="block text-slate-700 hover:text-emerald-600">
            Fonctionnalités
          </a>
          <a href="#tarifs" class="block text-slate-700 hover:text-emerald-600">
            Tarifs
          </a>
          <a href="#a-propos" class="block text-slate-700 hover:text-emerald-600">
            À propos
          </a>
          <a href="#ressources" class="block text-slate-700 hover:text-emerald-600">
            Ressources
          </a>
          <button class="w-full text-left text-slate-700 hover:text-emerald-600">
            Se connecter
          </button>
          <button class="w-full bg-emerald-600 text-white px-6 py-2.5 rounded-lg">
            Commencer gratuitement
          </button>
        </div>
      </nav>
    </header>

    <!-- Hero Section -->
    <section class="max-w-7xl mx-auto px-6 py-20 lg:py-28">
      <div class="grid lg:grid-cols-2 gap-12 items-center">
        <!-- Left side -->
        <div>
          <div class="inline-block bg-emerald-50 text-emerald-700 px-4 py-2 rounded-full text-sm mb-6">
            La comptabilité simplifiée pour les entrepreneurs marocains
          </div>

          <h1 class="text-4xl lg:text-5xl xl:text-6xl mb-6 text-slate-900">
            Gérez votre comptabilité <span class="text-emerald-600">en toute simplicité</span> avec SimplyCompta
          </h1>

          <p class="text-xl text-slate-600 mb-8">
            Avec SimplyCompta, profitez d'une véritable application 360° pensée pour les cabinets comptables et leurs clients.
          </p>

          <!-- Bullet points -->
          <div class="space-y-4 mb-10">
            <div class="flex items-start gap-3">
              <i data-lucide="check-circle" class="w-6 h-6 text-emerald-600 flex-shrink-0 mt-0.5"></i>
              <span class="text-slate-700 text-lg">Conforme Maroc : TVA, ICE, CNSS</span>
            </div>
            <div class="flex items-start gap-3">
              <i data-lucide="check-circle" class="w-6 h-6 text-emerald-600 flex-shrink-0 mt-0.5"></i>
              <span class="text-slate-700 text-lg">Gain de temps au quotidien</span>
            </div>
            <div class="flex items-start gap-3">
              <i data-lucide="check-circle" class="w-6 h-6 text-emerald-600 flex-shrink-0 mt-0.5"></i>
              <span class="text-slate-700 text-lg">Accessible partout, sur mobile et web</span>
            </div>
            <div class="flex items-start gap-3">
              <i data-lucide="check-circle" class="w-6 h-6 text-emerald-600 flex-shrink-0 mt-0.5"></i>
              <span class="text-slate-700 text-lg">Données sécurisées et hébergées au Maroc</span>
            </div>
          </div>

          <!-- CTA buttons -->
          <div class="flex flex-col sm:flex-row gap-4 mb-6">
            <button class="bg-emerald-600 hover:bg-emerald-700 text-white px-8 py-4 rounded-lg text-lg shadow-lg hover:shadow-xl transition-all">
              Commencer gratuitement
            </button>
            <button class="flex items-center justify-center gap-2 border-2 border-slate-300 hover:border-emerald-600 text-slate-700 hover:text-emerald-600 px-8 py-4 rounded-lg text-lg transition-all">
              <i data-lucide="play" width="20" height="20"></i>
              Voir la démo
            </button>
          </div>

          <p class="text-sm text-slate-500">
            Aucune carte bancaire requise · Essai gratuit 15 jours
          </p>
        </div>

        <!-- Right side - Mockup -->
        <div class="relative">
          <!-- Trust badges above mockup -->
          <div class="flex flex-wrap gap-3 mb-6">
            <div class="bg-white border border-slate-200 px-4 py-2 rounded-lg text-sm text-slate-700 shadow-sm">
              ✓ Conforme à la réglementation marocaine
            </div>
            <div class="bg-white border border-slate-200 px-4 py-2 rounded-lg text-sm text-slate-700 shadow-sm">
              🔒 Données sécurisées · Hébergement au Maroc
            </div>
          </div>

          <!-- Dashboard mockup -->
          <div class="bg-gradient-to-br from-slate-50 to-slate-100 rounded-2xl p-8 shadow-2xl">
            <div class="bg-white rounded-xl p-6 shadow-lg">
              <div class="grid grid-cols-2 gap-4 mb-6">
                <div class="bg-emerald-50 p-4 rounded-lg">
                  <p class="text-sm text-slate-600 mb-1">Chiffre d'affaires</p>
                  <p class="text-2xl text-slate-900">245 000 MAD</p>
                </div>
                <div class="bg-red-50 p-4 rounded-lg">
                  <p class="text-sm text-slate-600 mb-1">Dépenses</p>
                  <p class="text-2xl text-slate-900">89 500 MAD</p>
                </div>
              </div>

              <!-- Simple chart visualization -->
              <div class="h-40 bg-gradient-to-t from-emerald-100 to-emerald-50 rounded-lg mb-4 flex items-end justify-around p-4">
                <div class="w-8 bg-emerald-500 rounded-t" style="height: 60%"></div>
                <div class="w-8 bg-emerald-500 rounded-t" style="height: 80%"></div>
                <div class="w-8 bg-emerald-500 rounded-t" style="height: 45%"></div>
                <div class="w-8 bg-emerald-500 rounded-t" style="height: 90%"></div>
                <div class="w-8 bg-emerald-500 rounded-t" style="height: 70%"></div>
              </div>

              <div class="space-y-2">
                <div class="flex justify-between items-center p-3 bg-slate-50 rounded">
                  <span class="text-sm text-slate-700">Facture #1245</span>
                  <span class="text-sm text-emerald-600">12 000 MAD</span>
                </div>
                <div class="flex justify-between items-center p-3 bg-slate-50 rounded">
                  <span class="text-sm text-slate-700">Facture #1244</span>
                  <span class="text-sm text-emerald-600">8 500 MAD</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- User Type Selection Section -->
    <section class="bg-white py-20 lg:py-28">
      <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-16">
          <h2 class="text-3xl lg:text-5xl text-slate-900 mb-6">
            Une seule plateforme, deux expériences adaptées à votre réalité
          </h2>
          <p class="text-xl text-slate-600 max-w-4xl mx-auto">
            Que vous soyez entrepreneur ou cabinet comptable, SimplyCompta simplifie votre gestion et améliore votre collaboration au quotidien.
          </p>
        </div>

        <!-- Two Cards: Entrepreneur vs Cabinet -->
        <div class="grid lg:grid-cols-2 gap-8 mb-20">
          <!-- Card 1: Entrepreneur (B2C) -->
          <div class="group bg-gradient-to-br from-emerald-50 to-green-50 rounded-[20px] p-10 shadow-lg hover:shadow-2xl hover:scale-[1.02] transition-all duration-300 border border-emerald-100">
            <div class="flex flex-col h-full">
              <!-- Icons -->
              <div class="flex gap-3 mb-6">
                <div class="w-14 h-14 bg-emerald-500 rounded-xl flex items-center justify-center">
                  <i data-lucide="smartphone" class="w-7 h-7 text-white" stroke-width="2"></i>
                </div>
                <div class="w-14 h-14 bg-emerald-600 rounded-xl flex items-center justify-center">
                  <i data-lucide="trending-up" class="w-7 h-7 text-white" stroke-width="2"></i>
                </div>
                <div class="w-14 h-14 bg-emerald-700 rounded-xl flex items-center justify-center">
                  <i data-lucide="zap" class="w-7 h-7 text-white" stroke-width="2"></i>
                </div>
              </div>

              <!-- Title & Subtitle -->
              <h3 class="text-3xl mb-3 text-emerald-900">
                Entrepreneur
              </h3>
              <p class="text-lg text-emerald-800 mb-6">
                Gérez votre activité simplement, rapidement et sans stress
              </p>

              <!-- Sales Description -->
              <p class="text-slate-700 mb-8 leading-relaxed">
                Avec SimplyCompta, vous pilotez votre activité en toute autonomie : revenus, dépenses, factures et trésorerie — tout est centralisé dans une application simple, intelligente et accessible partout.
              </p>

              <!-- Feature bullets -->
              <div class="space-y-4 mb-8 flex-grow">
                <div class="flex items-start gap-3">
                  <span class="text-xl">📊</span>
                  <span class="text-slate-700">Suivi en temps réel de votre activité</span>
                </div>
                <div class="flex items-start gap-3">
                  <span class="text-xl">💰</span>
                  <span class="text-slate-700">Gestion des revenus, dépenses, devis et encaissements</span>
                </div>
                <div class="flex items-start gap-3">
                  <span class="text-xl">📄</span>
                  <span class="text-slate-700">Centralisation de tous vos documents (factures, justificatifs…)</span>
                </div>
                <div class="flex items-start gap-3">
                  <span class="text-xl">🤖</span>
                  <span class="text-slate-700">OCR intelligent pour scanner automatiquement vos factures</span>
                </div>
                <div class="flex items-start gap-3">
                  <span class="text-xl">🧠</span>
                  <span class="text-slate-700">Intelligence artificielle pour simplifier vos tâches comptables</span>
                </div>
                <div class="flex items-start gap-3">
                  <span class="text-xl">📲</span>
                  <span class="text-slate-700">Accès mobile et web, où que vous soyez</span>
                </div>
                <div class="flex items-start gap-3">
                  <span class="text-xl">🔔</span>
                  <span class="text-slate-700">Notifications et rappels automatiques</span>
                </div>
              </div>

              <!-- Differentiation line -->
              <div class="bg-emerald-100 border-l-4 border-emerald-600 p-4 rounded-lg mb-8">
                <p class="text-emerald-900">
                  <strong>Plus besoin d'Excel ni de paperasse : tout est automatisé et organisé pour vous.</strong>
                </p>
              </div>

              <!-- CTA Button -->
              <button class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-4 px-6 rounded-xl text-lg transition-all duration-200 shadow-lg hover:shadow-xl">
                Commencer gratuitement
              </button>
            </div>
          </div>

          <!-- Card 2: Cabinet Comptable -->
          <div class="group bg-gradient-to-br from-slate-800 to-slate-900 rounded-[20px] p-10 shadow-lg hover:shadow-2xl hover:scale-[1.02] transition-all duration-300 border border-slate-700">
            <div class="flex flex-col h-full">
              <!-- Icons -->
              <div class="flex gap-3 mb-6">
                <div class="w-14 h-14 bg-blue-500 rounded-xl flex items-center justify-center">
                  <i data-lucide="building" class="w-7 h-7 text-white" stroke-width="2"></i>
                </div>
                <div class="w-14 h-14 bg-blue-600 rounded-xl flex items-center justify-center">
                  <i data-lucide="bar-chart3" class="w-7 h-7 text-white" stroke-width="2"></i>
                </div>
                <div class="w-14 h-14 bg-blue-700 rounded-xl flex items-center justify-center">
                  <i data-lucide="network" class="w-7 h-7 text-white" stroke-width="2"></i>
                </div>
              </div>

              <!-- Title & Subtitle -->
              <h3 class="text-3xl mb-3 text-white">
                Cabinet comptable
              </h3>
              <p class="text-lg text-blue-200 mb-6">
                Gérez vos clients, automatisez votre travail et collaborez efficacement
              </p>

              <!-- Sales Description -->
              <p class="text-slate-300 mb-8 leading-relaxed">
                SimplyCompta transforme votre cabinet en plateforme digitale. Centralisez vos clients, automatisez les tâches répétitives et améliorez la communication avec vos clients en temps réel.
              </p>

              <!-- Feature bullets -->
              <div class="space-y-4 mb-8 flex-grow">
                <div class="flex items-start gap-3">
                  <span class="text-xl">🏢</span>
                  <span class="text-slate-300">Gestion multi-clients depuis un seul dashboard</span>
                </div>
                <div class="flex items-start gap-3">
                  <span class="text-xl">📊</span>
                  <span class="text-slate-300">Vue globale de l'activité de tous vos clients</span>
                </div>
                <div class="flex items-start gap-3">
                  <span class="text-xl">💬</span>
                  <span class="text-slate-300">Communication fluide entre cabinet et clients</span>
                </div>
                <div class="flex items-start gap-3">
                  <span class="text-xl">📲</span>
                  <span class="text-slate-300">WhatsApp Bot intégré pour automatiser les échanges</span>
                </div>
                <div class="flex items-start gap-3">
                  <span class="text-xl">🤖</span>
                  <span class="text-slate-300">Intelligence artificielle pour gagner du temps</span>
                </div>
                <div class="flex items-start gap-3">
                  <span class="text-xl">📄</span>
                  <span class="text-slate-300">Récupération automatique des documents clients</span>
                </div>
                <div class="flex items-start gap-3">
                  <span class="text-xl">⚡</span>
                  <span class="text-slate-300">Réduction des tâches manuelles répétitives</span>
                </div>
              </div>

              <!-- Differentiation line -->
              <div class="bg-blue-900 border-l-4 border-blue-400 p-4 rounded-lg mb-8">
                <p class="text-blue-100">
                  <strong>Vos clients deviennent autonomes, et vous gagnez un temps précieux sur votre cœur de métier.</strong>
                </p>
              </div>

              <!-- CTA Button -->
              <button class="w-full bg-blue-600 hover:bg-blue-700 text-white py-4 px-6 rounded-xl text-lg transition-all duration-200 shadow-lg hover:shadow-xl">
                Accéder à l'espace cabinet
              </button>
            </div>
          </div>
        </div>

        <!-- Communication Section -->
        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-3xl p-12 mb-20">
          <div class="max-w-5xl mx-auto">
            <div class="text-center mb-12">
              <h3 class="text-3xl lg:text-4xl text-slate-900 mb-6">
                Une communication simplifiée entre vous et votre comptable
              </h3>
              <p class="text-xl text-slate-700 leading-relaxed">
                SimplyCompta crée un pont direct entre l'entrepreneur et son cabinet comptable. Fini les échanges dispersés sur WhatsApp, emails ou appels — tout est centralisé, structuré et accessible en temps réel.
              </p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">
              <div class="bg-white p-6 rounded-xl shadow-md">
                <i data-lucide="message-circle" class="w-10 h-10 text-blue-600 mb-3"></i>
                <p class="text-slate-700"><strong>💬 Messagerie intégrée</strong> entre client et comptable</p>
              </div>
              <div class="bg-white p-6 rounded-xl shadow-md">
                <i data-lucide="file-text" class="w-10 h-10 text-blue-600 mb-3"></i>
                <p class="text-slate-700"><strong>📎 Partage instantané</strong> de documents</p>
              </div>
              <div class="bg-white p-6 rounded-xl shadow-md">
                <i data-lucide="bell" class="w-10 h-10 text-blue-600 mb-3"></i>
                <p class="text-slate-700"><strong>🔔 Notifications</strong> en temps réel</p>
              </div>
              <div class="bg-white p-6 rounded-xl shadow-md">
                <i data-lucide="bot" class="w-10 h-10 text-blue-600 mb-3"></i>
                <p class="text-slate-700"><strong>📲 WhatsApp Bot</strong> pour automatiser les interactions</p>
              </div>
              <div class="bg-white p-6 rounded-xl shadow-md">
                <i data-lucide="activity" class="w-10 h-10 text-blue-600 mb-3"></i>
                <p class="text-slate-700"><strong>📊 Suivi clair</strong> des demandes et des actions</p>
              </div>
              <div class="bg-white p-6 rounded-xl shadow-md">
                <i data-lucide="send" class="w-10 h-10 text-blue-600 mb-3"></i>
                <p class="text-slate-700"><strong>⚡ Réponses rapides</strong> et collaboration fluide</p>
              </div>
            </div>

            <!-- Visual illustration -->
            <div class="flex items-center justify-center gap-8 flex-wrap">
              <div class="bg-emerald-600 text-white p-6 rounded-2xl shadow-lg">
                <i data-lucide="smartphone" class="w-12 h-12 mx-auto mb-2"></i>
                <p class="text-center">Client Mobile</p>
              </div>
              <div class="flex items-center gap-2">
                <div class="h-1 w-20 bg-gradient-to-r from-emerald-600 to-blue-600"></div>
                <i data-lucide="network" class="w-8 h-8 text-slate-600"></i>
                <div class="h-1 w-20 bg-gradient-to-r from-emerald-600 to-blue-600"></div>
              </div>
              <div class="bg-slate-800 text-white p-6 rounded-2xl shadow-lg">
                <i data-lucide="building" class="w-12 h-12 mx-auto mb-2"></i>
                <p class="text-center">Cabinet Dashboard</p>
              </div>
            </div>
          </div>
        </div>

        <!-- AI + Automation Section -->
        <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-3xl p-12">
          <div class="max-w-5xl mx-auto text-center">
            <h3 class="text-3xl lg:text-4xl text-slate-900 mb-6">
              Travaillez plus vite grâce à l'automatisation et l'intelligence artificielle
            </h3>
            <p class="text-xl text-slate-700 mb-10">
              SimplyCompta utilise l'OCR et l'intelligence artificielle pour éliminer les tâches manuelles et accélérer votre gestion.
            </p>

            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
              <div class="bg-white p-8 rounded-2xl shadow-lg hover:shadow-xl transition-shadow">
                <div class="text-4xl mb-4">🤖</div>
                <p class="text-lg mb-2"><strong>OCR intelligent</strong></p>
                <p class="text-sm text-slate-600">Scan automatique des factures</p>
              </div>
              <div class="bg-white p-8 rounded-2xl shadow-lg hover:shadow-xl transition-shadow">
                <div class="text-4xl mb-4">🧠</div>
                <p class="text-lg mb-2"><strong>IA</strong></p>
                <p class="text-sm text-slate-600">Catégorisation automatique des dépenses</p>
              </div>
              <div class="bg-white p-8 rounded-2xl shadow-lg hover:shadow-xl transition-shadow">
                <div class="text-4xl mb-4">⚡</div>
                <p class="text-lg mb-2"><strong>Automatisation</strong></p>
                <p class="text-sm text-slate-600">Éliminez les tâches répétitives</p>
              </div>
              <div class="bg-white p-8 rounded-2xl shadow-lg hover:shadow-xl transition-shadow">
                <div class="text-4xl mb-4">📈</div>
                <p class="text-lg mb-2"><strong>Analyse intelligente</strong></p>
                <p class="text-sm text-slate-600">Comprenez votre activité en un coup d'œil</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- 360° Solution Section -->
    <section id="fonctionnalites" class="bg-slate-50 py-20">
      <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-16">
          <div class="inline-block bg-emerald-50 text-emerald-700 px-4 py-2 rounded-full text-sm mb-4">
            Une solution 360°
          </div>
          <h2 class="text-3xl lg:text-4xl text-slate-900 mb-4">
            Tout ce dont vous avez besoin, dans une seule application
          </h2>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
          <!-- Card 1 -->
          <div class="bg-white p-8 rounded-2xl shadow-lg hover:shadow-xl transition-shadow">
            <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center mb-4">
              <i data-lucide="activity" class="w-6 h-6 text-emerald-600"></i>
            </div>
            <h3 class="text-xl mb-3 text-slate-900">Suivi en temps réel</h3>
            <p class="text-slate-600">
              Suivez votre activité en temps réel et prenez les bonnes décisions.
            </p>
          </div>

          <!-- Card 2 -->
          <div class="bg-white p-8 rounded-2xl shadow-lg hover:shadow-xl transition-shadow">
            <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center mb-4">
              <i data-lucide="wallet" class="w-6 h-6 text-emerald-600"></i>
            </div>
            <h3 class="text-xl mb-3 text-slate-900">Revenus & Dépenses</h3>
            <p class="text-slate-600">
              Gérez vos revenus, dépenses, devis et encaissements facilement.
            </p>
          </div>

          <!-- Card 3 -->
          <div class="bg-white p-8 rounded-2xl shadow-lg hover:shadow-xl transition-shadow">
            <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center mb-4">
              <i data-lucide="folder" class="w-6 h-6 text-emerald-600"></i>
            </div>
            <h3 class="text-xl mb-3 text-slate-900">Centralisation</h3>
            <p class="text-slate-600">
              Centralisez tous vos documents : factures, justificatifs et contrats.
            </p>
          </div>

          <!-- Card 4 -->
          <div class="bg-white p-8 rounded-2xl shadow-lg hover:shadow-xl transition-shadow">
            <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center mb-4">
              <i data-lucide="message-circle" class="w-6 h-6 text-emerald-600"></i>
            </div>
            <h3 class="text-xl mb-3 text-slate-900">Communication</h3>
            <p class="text-slate-600">
              Échangez facilement avec votre cabinet comptable depuis l'application.
            </p>
          </div>

          <!-- Card 5 -->
          <div class="bg-white p-8 rounded-2xl shadow-lg hover:shadow-xl transition-shadow">
            <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center mb-4">
              <i data-lucide="bell" class="w-6 h-6 text-emerald-600"></i>
            </div>
            <h3 class="text-xl mb-3 text-slate-900">Notifications</h3>
            <p class="text-slate-600">
              Recevez notifications, rappels et demandes instantanément.
            </p>
          </div>

          <!-- Card 6 -->
          <div class="bg-white p-8 rounded-2xl shadow-lg hover:shadow-xl transition-shadow">
            <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center mb-4">
              <i data-lucide="bar-chart3" class="w-6 h-6 text-emerald-600"></i>
            </div>
            <h3 class="text-xl mb-3 text-slate-900">Vision claire</h3>
            <p class="text-slate-600">
              Gardez une vision claire de votre activité grâce à des tableaux de bord intuitifs.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- Security Section -->
    <section class="bg-gradient-to-br from-emerald-50 to-green-50 py-20">
      <div class="max-w-7xl mx-auto px-6">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
          <!-- Left side -->
          <div>
            <div class="inline-block bg-emerald-100 text-emerald-700 px-4 py-2 rounded-full text-sm mb-4">
              Confiance & sécurité
            </div>
            <h2 class="text-3xl lg:text-4xl text-slate-900 mb-6">
              Vos données sont entre de bonnes mains
            </h2>

            <div class="space-y-4 mb-8">
              <div class="flex items-start gap-3">
                <i data-lucide="check-circle" class="w-6 h-6 text-emerald-600 flex-shrink-0 mt-0.5"></i>
                <span class="text-slate-700 text-lg">Hébergement sécurisé au Maroc</span>
              </div>
              <div class="flex items-start gap-3">
                <i data-lucide="check-circle" class="w-6 h-6 text-emerald-600 flex-shrink-0 mt-0.5"></i>
                <span class="text-slate-700 text-lg">Sauvegardes automatiques quotidiennes</span>
              </div>
              <div class="flex items-start gap-3">
                <i data-lucide="check-circle" class="w-6 h-6 text-emerald-600 flex-shrink-0 mt-0.5"></i>
                <span class="text-slate-700 text-lg">Chiffrement de vos données</span>
              </div>
              <div class="flex items-start gap-3">
                <i data-lucide="check-circle" class="w-6 h-6 text-emerald-600 flex-shrink-0 mt-0.5"></i>
                <span class="text-slate-700 text-lg">Conforme à la loi 09-08 sur la protection des données</span>
              </div>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-3 gap-4">
              <div class="bg-white p-4 rounded-xl shadow-md">
                <div class="text-2xl text-emerald-600 mb-1">+500</div>
                <div class="text-sm text-slate-600">Entrepreneurs</div>
              </div>
              <div class="bg-white p-4 rounded-xl shadow-md">
                <div class="text-2xl text-emerald-600 mb-1">10K+</div>
                <div class="text-sm text-slate-600">Factures</div>
              </div>
              <div class="bg-white p-4 rounded-xl shadow-md">
                <div class="text-2xl text-emerald-600 mb-1">99.9%</div>
                <div class="text-sm text-slate-600">Disponibilité</div>
              </div>
            </div>
          </div>

          <!-- Right side - Illustration -->
          <div class="flex justify-center">
            <div class="relative">
              <div class="w-64 h-64 bg-emerald-600 rounded-full flex items-center justify-center shadow-2xl">
                <i data-lucide="shield" class="w-32 h-32 text-white"></i>
              </div>
              <div class="absolute -bottom-4 -right-4 bg-white p-4 rounded-xl shadow-lg">
                <p class="text-sm text-slate-700">🇲🇦 Hébergé au Maroc</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Testimonials Section -->
    <section class="py-20 bg-white">
      <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-16">
          <div class="inline-block bg-emerald-50 text-emerald-700 px-4 py-2 rounded-full text-sm mb-4">
            Ils nous font confiance
          </div>
          <h2 class="text-3xl lg:text-4xl text-slate-900">
            Ce que disent nos utilisateurs
          </h2>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
          <!-- Testimonial 1 -->
          <div class="bg-slate-50 p-8 rounded-2xl shadow-lg">
            <i data-lucide="quote" class="w-10 h-10 text-emerald-600 mb-4"></i>
            <p class="text-slate-700 mb-6">
              "SimplyCompta m'a fait gagner un temps énorme. Je peux maintenant me concentrer sur mon business au lieu de la paperasse."
            </p>
            <div class="flex items-center gap-4">
              <div class="w-12 h-12 bg-emerald-600 rounded-full flex items-center justify-center text-white">
                YB
              </div>
              <div>
                <p class="text-slate-900">Youssef B.</p>
                <p class="text-sm text-slate-600">Entrepreneur</p>
              </div>
            </div>
            <div class="flex gap-1 mt-4">
              <i data-lucide="star" class="w-5 h-5 fill-emerald-600 text-emerald-600"></i><i data-lucide="star" class="w-5 h-5 fill-emerald-600 text-emerald-600"></i><i data-lucide="star" class="w-5 h-5 fill-emerald-600 text-emerald-600"></i><i data-lucide="star" class="w-5 h-5 fill-emerald-600 text-emerald-600"></i><i data-lucide="star" class="w-5 h-5 fill-emerald-600 text-emerald-600"></i>
            </div>
          </div>

          <!-- Testimonial 2 -->
          <div class="bg-slate-50 p-8 rounded-2xl shadow-lg">
            <i data-lucide="quote" class="w-10 h-10 text-emerald-600 mb-4"></i>
            <p class="text-slate-700 mb-6">
              "L'OCR est incroyable, plus besoin de saisir manuellement mes dépenses. Tout est automatique."
            </p>
            <div class="flex items-center gap-4">
              <div class="w-12 h-12 bg-emerald-600 rounded-full flex items-center justify-center text-white">
                SK
              </div>
              <div>
                <p class="text-slate-900">Sara K.</p>
                <p class="text-sm text-slate-600">Consultante</p>
              </div>
            </div>
            <div class="flex gap-1 mt-4">
              <i data-lucide="star" class="w-5 h-5 fill-emerald-600 text-emerald-600"></i><i data-lucide="star" class="w-5 h-5 fill-emerald-600 text-emerald-600"></i><i data-lucide="star" class="w-5 h-5 fill-emerald-600 text-emerald-600"></i><i data-lucide="star" class="w-5 h-5 fill-emerald-600 text-emerald-600"></i><i data-lucide="star" class="w-5 h-5 fill-emerald-600 text-emerald-600"></i>
            </div>
          </div>

          <!-- Testimonial 3 -->
          <div class="bg-slate-50 p-8 rounded-2xl shadow-lg">
            <i data-lucide="quote" class="w-10 h-10 text-emerald-600 mb-4"></i>
            <p class="text-slate-700 mb-6">
              "Enfin une solution comptable pensée pour les entrepreneurs marocains. Je recommande à 100%."
            </p>
            <div class="flex items-center gap-4">
              <div class="w-12 h-12 bg-emerald-600 rounded-full flex items-center justify-center text-white">
                MT
              </div>
              <div>
                <p class="text-slate-900">Mehdi T.</p>
                <p class="text-sm text-slate-600">Commerçant</p>
              </div>
            </div>
            <div class="flex gap-1 mt-4">
              <i data-lucide="star" class="w-5 h-5 fill-emerald-600 text-emerald-600"></i><i data-lucide="star" class="w-5 h-5 fill-emerald-600 text-emerald-600"></i><i data-lucide="star" class="w-5 h-5 fill-emerald-600 text-emerald-600"></i><i data-lucide="star" class="w-5 h-5 fill-emerald-600 text-emerald-600"></i><i data-lucide="star" class="w-5 h-5 fill-emerald-600 text-emerald-600"></i>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Packs Entrepreneurs avec Inscription -->
    <section id="packs-entrepreneurs" class="py-20 bg-gradient-to-br from-emerald-50 to-green-50">
      <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-16">
          <div class="inline-block bg-emerald-600 text-white px-4 py-2 rounded-full text-sm mb-4">
            Packs Entrepreneurs
          </div>
          <h2 class="text-3xl lg:text-4xl text-slate-900 mb-4">
            Choisissez votre pack entrepreneur et créez votre compte
          </h2>
          <p class="text-xl text-slate-600">
            Gérez votre activité simplement avec SimplyCompta
          </p>
        </div>

        <!-- Packs -->
        <div class="grid gap-8 mb-16">
          <div class="billing-selector-wrapper">
            <div class="billing-selector">
              <button class="billing-btn active" data-cycle="monthly" onclick="changeBillingCycle('monthly')">
                Mensuel
              </button>
              <button class="billing-btn" data-cycle="quarterly" onclick="changeBillingCycle('quarterly')">
                Trimestriel <span class="save-text">· Économisez 5%</span>
              </button>
              <button class="billing-btn" data-cycle="yearly" onclick="changeBillingCycle('yearly')">
                Annuel <span class="save-text">· Économisez 15%</span>
                <span class="best-value-badge">Meilleure valeur</span>
              </button>
            </div>
          </div>
          <!-- Starter -->
          <div class="plans-grid">
            <div class="plans-column" id="plans-column">

              @foreach($mobilePlans as $plan)
              @php
              $isRecommended = $plan->slug === 'pro';
              $isFree = $plan->slug === 'free';
              // Index prices by billing_cycle for easy JS access
              $pricesBycycle = $plan->prices->keyBy('billing_cycle');
              $monthlyPrice = $pricesBycycle->get('monthly');
              @endphp

              <div
                class="plan-card"
                data-plan="{{ $plan->slug }}"
                data-free="{{ $isFree ? 'true' : 'false' }}"
                data-prices="{{ json_encode($plan->prices->map(fn($p) => [
                        'cycle'       => $p->billing_cycle,
                        'price'       => $p->price,
                        'price_id'    => $p->id,
                        'currency'    => $p->currency,
                        'discount'    => $p->discount_percentage,
                    ])->keyBy('cycle')) }}"
                onclick="selectPlan('{{ $plan->slug }}', '{{ $plan->name }}')">
                @if($isRecommended)
                <span class="recommended-badge">Recommandé</span>
                @endif

                <div class="plan-header">
                  <h3 class="plan-name">{{ $plan->name }}</h3>
                  <p class="plan-description">
                    @if(!empty($plan->description))
                    {{ $plan->description }}
                    @elseif($isFree)
                    Pour découvrir SimplyCompta
                    @elseif($plan->slug === 'basic')
                    Pour freelancers et petites entreprises
                    @elseif($isRecommended)
                    Pour entreprises en croissance
                    @else
                    Pour utilisateurs avancés
                    @endif
                  </p>
                </div>

                {{-- Price display (updated by JS) --}}
                <div class="plan-price">
                  <div class="price-wrapper">
                    <span class="price js-price" data-plan="{{ $plan->slug }}">
                      {{ $monthlyPrice ? number_format($monthlyPrice->price, 0) : '0' }}
                    </span>
                    <span class="currency">
                      {{ $monthlyPrice ? $monthlyPrice->currency : 'MAD' }}
                    </span>
                    <span class="period">/ <span class="billing-label">mois</span></span>
                  </div>
                  <p class="price-detail js-price-detail" data-plan="{{ $plan->slug }}">
                    @if(!$isFree && $monthlyPrice && $monthlyPrice->discount > 0)
                    <span class="badge-discount">{{ $monthlyPrice->discount }}% OFF</span>
                    @endif
                  </p>
                </div>

                {{-- Features --}}
                <ul class="features-list">
                  <li class="feature-item">
                    <span class="check">✓</span>
                    <span>
                      {{ is_null($plan->invoice_limit) ? 'Factures illimitées' : $plan->invoice_limit . ' factures' }}
                    </span>
                  </li>
                  <li class="feature-item">
                    <span class="check">✓</span>
                    <span>
                      {{ is_null($plan->expense_limit) ? 'Dépenses illimitées' : $plan->expense_limit . ' dépenses' }}
                    </span>
                  </li>
                  @if(!is_null($plan->ocr_limit))
                  <li class="feature-item">
                    <span class="check">✓</span>
                    <span>{{ $plan->ocr_limit }} scans OCR</span>
                  </li>
                  @else
                  <li class="feature-item">
                    <span class="check">✓</span>
                    <span>OCR illimité</span>
                  </li>
                  @endif
                  <li class="feature-item">
                    <span class="check">✓</span>
                    <span>
                      @if($plan->storage_limit_mb >= 1024)
                      {{ round($plan->storage_limit_mb / 1024) }} GB stockage
                      @else
                      {{ $plan->storage_limit_mb }} MB stockage
                      @endif
                    </span>
                  </li>
                  @if($plan->export_enabled)
                  <li class="feature-item">
                    <span class="check">✓</span>
                    <span>Export activé</span>
                  </li>
                  @endif
                  @if($plan->whatsapp_bot_enabled)
                  <li class="feature-item">
                    <span class="check">✓</span>
                    <span>Bot WhatsApp</span>
                  </li>
                  @endif
                </ul>

                <button class="plan-btn" type="button">
                  Commencer avec {{ $plan->name }}
                </button>
              </div>
              @endforeach

            </div>
          </div>

        </div>

        <!-- Formulaire Entrepreneur -->
        <div id="entrepreneurForm" class="hidden max-w-2xl mx-auto bg-white rounded-2xl p-8 shadow-lg">
          <h2 class="text-2xl mb-6 text-slate-900">Créer votre compte entrepreneur</h2>
          <form method="post" id="checkout-form" action="{{ route('mobile.customer.store') }}" class="space-y-6">
            @csrf
            <input type="hidden" name="mobile_plan_price_id" id="selected-price-id" value="">
            <input type="hidden" name="plan_slug" id="selected-plan-slug" value="pro">
            <input type="hidden" name="billing_cycle" id="selected-billing" value="monthly">
            <input type="hidden" name="referral_discount_amount" id="referral-discount" value="0">
            <input type="hidden" name="price_after_discount" id="price-after-discount" value="0">

            <div>
              <label class="block text-sm mb-2 text-slate-700">Nom *</label>
              <input name="full_name"
                type="text"
                required
                class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-600"
                placeholder="Votre nom" />
            </div>

            <div>
              <label class="block text-sm mb-2 text-slate-700">Email *</label>
              <input name="email"
                type="email"
                required
                class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-600"
                placeholder="votre@email.com" />
            </div>

            <div>
              <label class="block text-sm mb-2 text-slate-700">Téléphone *</label>
              <input name="phone"
                type="tel"
                required
                class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-600"
                placeholder="+212 6XX XXX XXX" />
            </div>

            <div class="grid md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm mb-2 text-slate-700">Nom de l'entreprise</label>
                <input name="billing_name"
                  type="text"
                  class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-600"
                  placeholder="Nom de votre entreprise" />
              </div>
              <div>
                <label class="block text-sm mb-2 text-slate-700">ICE</label>
                <input name="ice_number"
                  type="text"
                  class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-600"
                  placeholder="Identifiant commun de l'entreprise" />
              </div>
            </div>

            <div class="grid md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm mb-2 text-slate-700">Mot de passe *</label>
                <input name="password"
                  type="password"
                  required
                  class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-600"
                  placeholder="••••••••" />
              </div>
              <div>
                <label class="block text-sm mb-2 text-slate-700">Confirmer mot de passe *</label>
                <input name="password_confirmation"
                  type="password"
                  required
                  class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-600"
                  placeholder="••••••••" />
              </div>
            </div>

            <div class="bg-emerald-50 p-4 rounded-lg">
              <p class="text-sm text-emerald-800">
                <strong>Pack sélectionné :</strong> <span id="summary-plan">Pack choisi</span>
              </p>
              <p class="text-xs text-emerald-700 mt-2">
                <span id="summary-original"></span>/<span id="summary-billing"></span>
              </p>
            </div>

            <div class="form-error-message entrepreneur-error"></div>

            <button
              type="button" id="cta-btn" form="checkout-form" data-form="user-form"
              class="submit-form w-full bg-emerald-600 hover:bg-emerald-700 text-white py-4 rounded-lg text-lg transition-colors shadow-lg">
              Créer mon compte et commencer
            </button>

            <p class="text-sm text-slate-500 text-center">
              En créant un compte, vous acceptez nos
              <a href="#" class="text-emerald-600 hover:underline">Conditions d'utilisation</a>
              et notre
              <a href="#" class="text-emerald-600 hover:underline">Politique de confidentialité</a>
            </p>
          </form>
        </div>
      </div>
    </section>

    <!-- Packs Cabinets avec Inscription -->
    <section id="packs-cabinets" class="py-20 bg-gradient-to-br from-slate-800 to-slate-900">
      <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-16">
          <div class="inline-block bg-blue-600 text-white px-4 py-2 rounded-full text-sm mb-4">
            Packs Cabinets Comptables
          </div>
          <h2 class="text-3xl lg:text-4xl text-white mb-4">
            Choisissez votre pack cabinet et créez votre espace
          </h2>
          <p class="text-xl text-slate-300">
            Gérez tous vos clients depuis une seule plateforme
          </p>
        </div>

        <!-- Packs -->
        <div class="grid lg:grid-cols-3 gap-8 mb-16">
          @foreach($companyPlans as $plan)
          <!-- Essential -->
          <div data-cabinet-pack="essential" class="company-plan pack-card bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition-all cursor-pointer">
            <div class="selected-badge hidden bg-blue-600 text-white px-3 py-1 rounded-full text-sm mb-4 inline-block">✓ Sélectionné</div>
            <h3 class="text-2xl mb-2 text-slate-900 company-plan-name">{{ $plan->name }}</h3>
            <input type="hidden" class="company-plan-id" value="{{ $plan->id }}">
            @if($plan->slug == 'basic')
            <p class="text-sm text-slate-600 mb-4">Idéal pour les indépendants</p>
            @elseif($plan->slug == 'pro')
            <p class="text-sm text-slate-600 mb-4">Idéal pour les indépendants</p>
            @else
            <p class="text-sm text-slate-600 mb-4">Idéal pour les indépendants</p>
            @endif
            <div class="mb-6">
              <span class="text-4xl text-slate-900 company-plan-price">{{ $plan->price }}</span>
              <span class="text-slate-600 company-plan-duration"> / {{ $plan->duration }}</span>
            </div>
            <ul class="space-y-3">
              <li class="flex items-start gap-2">
                <i data-lucide="check-circle" class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5"></i>
                <span class="text-slate-700">{{ $plan->max_users == -1 ? 'Unlimited' : $plan->max_users }} utilisateurs</span>
              </li>
              <li class="flex items-start gap-2">
                <i data-lucide="check-circle" class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5"></i>
                <span class="text-slate-700">{{ $plan->max_customers == -1 ? 'Unlimited' : $plan->max_customers }} clients</span>
              </li>
              <li class="flex items-start gap-2">
                <i data-lucide="check-circle" class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5"></i>
                <span class="text-slate-700">{{ $plan->max_venders == -1 ? 'Unlimited' : $plan->max_venders }} fournisseurs</span>
              </li>
              <li class="flex items-start gap-2">
                <i data-lucide="check-circle" class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5"></i>
                <span class="text-slate-700">{{ $plan->storage_limit }} de stockage</span>
              </li>
              @if($plan->enable_chatgpt == 'on')
              <li class="flex items-start gap-2">
                <i data-lucide="check-circle" class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5"></i>
                <span class="text-slate-700">ChatGPT</span>
              </li>
              @endif
            </ul>
          </div>
          @endforeach
        </div>

        <!-- Formulaire Cabinet -->
        <div id="cabinetForm" class="hidden max-w-2xl mx-auto bg-white rounded-2xl p-8 shadow-lg">
          <h2 class="text-2xl mb-6 text-slate-900">Créer votre espace cabinet</h2>
          <form method="post" id="company-checkout-form" action="{{ route('company.store') }}" class="space-y-6">
            @csrf
            <input type="hidden" name="company_plan_id" id="plan-id" value="">
            <div>
              <label class="block text-sm mb-2 text-slate-700">Nom du cabinet *</label>
              <input name="name"
                type="text"
                id="company-name"
                required
                class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600"
                placeholder="Cabinet Comptable XYZ" />
            </div>

            <div class="grid md:grid-cols-2 gap-6">
              <div>
              <label class="block text-sm mb-2 text-slate-700">Email *</label>
              <input name="email"
                type="email"
                id="company-email"
                required
                class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600"
                placeholder="cabinet@email.com" />
            </div>
              <div>
                <label class="block text-sm mb-2 text-slate-700">Téléphone *</label>
                <input name="phone"
                  type="tel"
                  id="company-phone"
                  required
                  class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600"
                  placeholder="+212 5XX XXX XXX" />
              </div>
            </div>

            <div class="grid md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm mb-2 text-slate-700">Mot de passe *</label>
                <input name="password"
                  type="password"
                  id="company-password"
                  required
                  class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600"
                  placeholder="••••••••" />
              </div>
              <div>
                <label class="block text-sm mb-2 text-slate-700">Confirmer mot de passe *</label>
                <input name="password_confirmation"
                  type="password"
                  id="company-password-confirmation"
                  required
                  class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600"
                  placeholder="••••••••" />
              </div>
            </div>

            <div class="bg-blue-50 p-4 rounded-lg">
              <p class="text-sm text-blue-800">
                <strong>Pack sélectionné :</strong> <span id="plan-name">Pack choisi</span>
              </p>
              <p class="text-xs text-emerald-700 mt-2">
                <span id="plan-price"></span>/<span id="plan-duration"></span>
              </p>
            </div>

            <div class="form-error-message cabinet-error"></div>

            <button
              type="button" data-form="company-form"
              class="submit-form w-full bg-blue-600 hover:bg-blue-700 text-white py-4 rounded-lg text-lg transition-colors shadow-lg">
              Créer mon espace cabinet
            </button>

            <p class="text-sm text-slate-500 text-center">
              En créant un compte, vous acceptez nos
              <a href="#" class="text-blue-600 hover:underline">Conditions d'utilisation</a>
              et notre
              <a href="#" class="text-blue-600 hover:underline">Politique de confidentialité</a>
            </p>
          </form>
        </div>
      </div>
    </section>

    <!-- Contact Section -->
    <section class="py-20 bg-white">
      <div class="max-w-2xl mx-auto px-6">
        <div class="text-center mb-12">
          <div class="inline-block bg-emerald-50 text-emerald-700 px-4 py-2 rounded-full text-sm mb-4">
            Contactez-nous
          </div>
          <h2 class="text-3xl lg:text-4xl text-slate-900 mb-4">
            Une question ? Nous sommes là pour vous aider
          </h2>
          <p class="text-slate-600">
            Remplissez le formulaire, notre équipe vous répondra dans les plus brefs délais.
          </p>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl p-8 shadow-lg">
          @if(session('success'))
          <div class="alert alert-success">
            {{ session('success') }}
          </div>
          @endif
          @if($errors->any())
          <div class="alert alert-danger">
            <ul>
              @foreach($errors->all() as $error)
              <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
          @endif
          <form class="space-y-6" action="{{ route('landingpage.sendmail') }}" method="POST">
            @csrf
            <div>
              <label class="block text-sm mb-2 text-slate-700">Nom complet</label>
              <input name="contact-name"
                type="text"
                class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-600"
                placeholder="Votre nom" />
            </div>

            <div>
              <label class="block text-sm mb-2 text-slate-700">Email</label>
              <input name="email"
                type="email"
                class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-600"
                placeholder="votre@email.com" />
            </div>

            <div>
              <label class="block text-sm mb-2 text-slate-700">Sujet</label>
              <select name="subject" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-600">
                <option>Demande d'information</option>
                <option>Tarifs</option>
                <option>Démo</option>
                <option>Support</option>
                <option>Partenariat</option>
              </select>
            </div>

            <div>
              <label class="block text-sm mb-2 text-slate-700">Message</label>
              <textarea name="message"
                rows="5"
                class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-600"
                placeholder="Votre message..."></textarea>
            </div>

            <button
              type="submit" id="contact-cta-btn"
              class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-4 rounded-lg transition-colors shadow-lg">
              Envoyer le message
            </button>

            <p class="text-sm text-slate-500 text-center">
              Réponse sous 24h ouvrées.
            </p>
          </form>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <footer class="bg-slate-900 text-white py-16">
      <div class="max-w-7xl mx-auto px-6">
        <div class="grid md:grid-cols-2 lg:grid-cols-5 gap-12 mb-12">
          <!-- Brand -->
          <div class="lg:col-span-2">
            <div class="text-2xl font-bold text-emerald-400 mb-4">
              SimplyCompta
            </div>
            <p class="text-slate-400 mb-6">
              La comptabilité simplifiée pour les entrepreneurs et les cabinets comptables au Maroc.
            </p>
            <div class="flex gap-4">
              <!-- Facebook -->
              <a href="#" class="w-10 h-10 bg-slate-800 rounded-lg flex items-center justify-center hover:bg-emerald-600 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 24 24" class="text-white">
                  <path d="M22 12c0-5.522-4.477-10-10-10S2 6.478 2 12c0 4.991 3.657 9.128 8.437 9.879v-6.988H7.898V12h2.539V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.891h-2.33v6.988C18.343 21.128 22 16.991 22 12z" />
                </svg>
              </a>
              <!-- Instagram -->
              <a href="#" class="w-10 h-10 bg-slate-800 rounded-lg flex items-center justify-center hover:bg-emerald-600 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 24 24" class="text-white">
                  <path d="M12 2.163c3.204 0 3.584.012 4.85.07 1.366.062 2.633.336 3.608 1.31.975.975 1.248 2.242 1.31 3.608.058 1.266.07 1.646.07 4.849s-.012 3.584-.07 4.85c-.062 1.366-.336 2.633-1.31 3.608-.975.975-2.242 1.248-3.608 1.31-1.266.058-1.646.07-4.85.07s-3.584-.012-4.85-.07c-1.366-.062-2.633-.336-3.608-1.31-.975-.975-1.248-2.242-1.31-3.608C2.175 15.584 2.163 15.204 2.163 12s.012-3.584.07-4.85c.062-1.366.336-2.633 1.31-3.608.975-.975 2.242-1.248 3.608-1.31C8.416 2.175 8.796 2.163 12 2.163zm0-2.163C8.741 0 8.332.014 7.052.072 5.197.157 3.355.673 2.014 2.014.673 3.355.157 5.197.072 7.052.014 8.332 0 8.741 0 12c0 3.259.014 3.668.072 4.948.085 1.855.601 3.697 1.942 5.038 1.341 1.341 3.183 1.857 5.038 1.942C8.332 23.986 8.741 24 12 24s3.668-.014 4.948-.072c1.855-.085 3.697-.601 5.038-1.942 1.341-1.341 1.857-3.183 1.942-5.038C23.986 15.668 24 15.259 24 12s-.014-3.668-.072-4.948c-.085-1.855-.601-3.697-1.942-5.038C20.645.673 18.803.157 16.948.072 15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 1 0 0 12.324 6.162 6.162 0 0 0 0-12.324zm0 10.162a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm6.406-11.845a1.44 1.44 0 1 0 0 2.881 1.44 1.44 0 0 0 0-2.881z" />
                </svg>
              </a>
              <!-- LinkedIn -->
              <a href="#" class="w-10 h-10 bg-slate-800 rounded-lg flex items-center justify-center hover:bg-emerald-600 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 24 24" class="text-white">
                  <path d="M20.447 20.452H16.89v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a1.982 1.982 0 0 1-1.98-1.981c0-1.094.887-1.98 1.98-1.98s1.98.886 1.98 1.98a1.982 1.982 0 0 1-1.98 1.981zm1.959 13.019H3.376V9h3.92v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z" />
                </svg>
              </a>
              <!-- YouTube -->
              <a href="#" class="w-10 h-10 bg-slate-800 rounded-lg flex items-center justify-center hover:bg-emerald-600 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 24 24" class="text-white">
                  <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z" />
                </svg>
              </a>
            </div>
          </div>

          <!-- Produit -->
          <div>
            <h4 class="mb-4">Produit</h4>
            <ul class="space-y-3">
              <li><a href="#" class="text-slate-400 hover:text-emerald-400 transition-colors">Fonctionnalités</a></li>
              <li><a href="#" class="text-slate-400 hover:text-emerald-400 transition-colors">Tarifs</a></li>
              <li><a href="#" class="text-slate-400 hover:text-emerald-400 transition-colors">Sécurité</a></li>
              <li><a href="#" class="text-slate-400 hover:text-emerald-400 transition-colors">Mises à jour</a></li>
            </ul>
          </div>

          <!-- Ressources -->
          <div>
            <h4 class="mb-4">Ressources</h4>
            <ul class="space-y-3">
              <li><a href="#" class="text-slate-400 hover:text-emerald-400 transition-colors">Blog</a></li>
              <li><a href="#" class="text-slate-400 hover:text-emerald-400 transition-colors">Guides</a></li>
              <li><a href="#" class="text-slate-400 hover:text-emerald-400 transition-colors">Centre d'aide</a></li>
              <li><a href="#" class="text-slate-400 hover:text-emerald-400 transition-colors">FAQ</a></li>
            </ul>
          </div>

          <!-- Entreprise -->
          <div>
            <h4 class="mb-4">Entreprise</h4>
            <ul class="space-y-3">
              <li><a href="#" class="text-slate-400 hover:text-emerald-400 transition-colors">À propos</a></li>
              <li><a href="#" class="text-slate-400 hover:text-emerald-400 transition-colors">Contact</a></li>
              <li><a href="#" class="text-slate-400 hover:text-emerald-400 transition-colors">Carrières</a></li>
              <li><a href="#" class="text-slate-400 hover:text-emerald-400 transition-colors">Presse</a></li>
            </ul>
          </div>
        </div>

        <!-- Bottom -->
        <div class="pt-8 border-t border-slate-800">
          <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <p class="text-slate-400 text-sm">
              © 2026 SimplyCompta. Tous droits réservés.
            </p>
            <div class="flex gap-6 text-sm">
              <a href="#" class="text-slate-400 hover:text-emerald-400 transition-colors">Mentions légales</a>
              <a href="#" class="text-slate-400 hover:text-emerald-400 transition-colors">Confidentialité</a>
              <a href="#" class="text-slate-400 hover:text-emerald-400 transition-colors">CGU</a>
            </div>
          </div>
        </div>
      </div>
    </footer>

    <!-- Mobile sticky CTA -->
    <div class="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-slate-200 p-4 shadow-lg z-50">
      <button class="w-full bg-emerald-600 text-white py-3 rounded-lg shadow-lg">
        Commencer gratuitement
      </button>
    </div>
  </div>
  <div id="otp-modal">
    <div class="modal-content">

      <button class="close-modal">&times;</button>

      <h2 class="modal-title">
        Vérification Email
      </h2>

      <p class="modal-text">
        Nous avons envoyé un code OTP à votre adresse email.
      </p>

      <input
        type="text"
        id="otp-input"
        maxlength="6"
        placeholder="000000">

      <div class="form-error-message otp-error"></div>

      <button type="button" id="submit-otp">
        Vérifier OTP
      </button>

    </div>
  </div>

  <script>
    $(document).ready(function() {
      const $ctaBtn = $('.submit-form');

      $ctaBtn.on('click', function() {
        const $currentBtn = $(this);
        const formType = $currentBtn.data('form');
        const $form = (formType === 'user-form') ? $('#checkout-form') : $('#company-checkout-form');
        const $errorContainer = $form.find('.form-error-message');

        // Clear previous errors
        $errorContainer.hide().text('');

        // 1. Basic HTML5 Validation Check
        if (!$form[0].checkValidity()) {
          $form[0].reportValidity();
          return;
        }

        let $data = {
          _token: "{{ csrf_token() }}",
          form_type: formType
        };

        if(formType === 'user-form') {
          $.extend($data, {
            email: $form.find('input[name="email"]').val(),
            full_name: $form.find('input[name="full_name"]').val(),
            phone: $form.find('input[name="phone"]').val(),
            billing_name: $form.find('input[name="billing_name"]').val(),
            ice_number: $form.find('input[name="ice_number"]').val(),
            password: $form.find('input[name="password"]').val(),
            password_confirmation: $form.find('input[name="password_confirmation"]').val(),
            mobile_plan_price_id: $form.find('input[name="mobile_plan_price_id"]').val(),
            plan_slug: $form.find('input[name="plan_slug"]').val(),
            billing_cycle: $form.find('input[name="billing_cycle"]').val(),
            referral_discount_amount: $form.find('input[name="referral_discount_amount"]').val(),
            price_after_discount: $form.find('input[name="price_after_discount"]').val(),
          });
        } else {
          $.extend($data, {
            name: $('#company-name').val(),
            email: $('#company-email').val(),
            phone: $('#company-phone').val(),
            company_plan_id: $('#plan-id').val(),
            password: $('#company-password').val(),
            password_confirmation: $('#company-password-confirmation').val(),
          });
        }

        // 2. Visual Loading State
        const originalText = $currentBtn.text();
        $currentBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Envoi en cours...');
        $currentBtn.css('opacity', '0.7');

        $.ajax({
          url: "{{ route('send.otp') }}",
          type: "POST",
          data: $data,
          success: function(response) {
            if (response.success) {
              // Store form type in modal for verification logic
              $('#otp-modal').data('form-type', formType);
              $('.otp-error').hide().text(''); // Clear OTP modal errors
              $('#otp-modal').css('display', 'flex');
            } else {
              $errorContainer.text(response.message || "Erreur lors de l'envoi").show();
            }
          },
          error: function(xhr) {
            // 3. Error Handling
            let errorMessage = "Une erreur est survenue.";
            if (xhr.status === 422) {
              const errors = xhr.responseJSON.errors;
              errorMessage = Object.values(errors).flat().join('\n');
            } else {
              errorMessage = xhr.responseJSON.message || errorMessage;
            }
            $errorContainer.text(errorMessage).show();
            // Scroll to error
            $('html, body').animate({
                scrollTop: $errorContainer.offset().top - 100
            }, 500);
          },
          complete: function() {
            $currentBtn.prop('disabled', false).text(originalText);
            $currentBtn.css('opacity', '1');
          }
        });
      });

      // Close Modal Logic
      $('.close-modal').on('click', function() {
        $('#otp-modal').hide();
      });

      // OTP Verification Logic
      $('#submit-otp').on('click', function() {
        const $otpBtn = $(this);
        const otpVal = $('#otp-input').val();
        const formType = $('#otp-modal').data('form-type');
        const $form = (formType === 'user-form') ? $('#checkout-form') : $('#company-checkout-form');
        const email = (formType === 'user-form') ? $form.find('input[name="email"]').val() : $('#company-email').val();
        const $otpError = $('.otp-error');

        $otpError.hide().text('');

        if (otpVal.length < 4) {
          $otpError.text("Veuillez entrer un code valide.").show();
          return;
        }

        $otpBtn.prop('disabled', true).text('Vérification...');

        $.ajax({
          url: "{{ route('verify.otp') }}",
          type: "POST",
          data: {
            _token: "{{ csrf_token() }}",
            email: email,
            otp: otpVal
          },
          success: function(response) {
            if (response.success) {
              $form.submit();
            } else {
              $otpError.text(response.message).show();
              $otpBtn.prop('disabled', false).text('Vérifier OTP');
            }
          },
          error: function(xhr) {
            let errorMsg = "Erreur de connexion";
            if(xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }
            $otpError.text(errorMsg).show();
            $otpBtn.prop('disabled', false).text('Vérifier OTP');
          }
        });
      });




      $('.company-plan').on('click', function() {
          // Visual feedback
          $('.company-plan').removeClass('selected');
          $('.company-plan').find('.selected-badge').addClass('hidden');
          $(this).addClass('selected');
          $(this).find('.selected-badge').removeClass('hidden');

          // Clear any visible errors
          $('.form-error-message').hide().text('');

          // Show form
        $('#cabinetForm').removeClass('hidden');

        // Update form fields
        $('#plan-id').val($(this).find('.company-plan-id').val());
        $('#plan-name').text($(this).find('.company-plan-name').text());
        $('#plan-price').text($(this).find('.company-plan-price').text());
        $('#plan-duration').text($(this).find('.company-plan-duration').text());
      });
    });
  </script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      if (window.lucide) lucide.createIcons();
      const menuBtn = document.getElementById('mobileMenuBtn');
      const mobileMenu = document.getElementById('mobileMenu');
      if (menuBtn && mobileMenu) menuBtn.addEventListener('click', () => mobileMenu.classList.toggle('hidden'));
    });
  </script>


  <script>
    setTimeout(function() {
      let alerts = document.querySelectorAll('.alert');
      alerts.forEach(function(alert) {
        alert.style.display = 'none';
      });
    }, 5000); // 5 seconds

    window.onload = function() {
      const toast = document.getElementById('referralToast');
      if (toast) {
        // Slide it in
        setTimeout(() => {
          toast.classList.add('show');
        }, 100);

        // Slide it out after 5 seconds
        setTimeout(() => {
          toast.classList.remove('show');
        }, 5000);
      }
    };


    // ── State ──────────────────────────────────────────────────────────────────
    let currentCycle = 'monthly';
    let currentPlan = null; // { slug, name, prices: { monthly:{price_id, price, currency, discount}, ... } }
    const referralDiscount = {{ !empty($referralDiscount) ? (int) $referralDiscount : 0 }};



    // Build a lookup: slug → { name, prices }
    const planData = {};
    document.querySelectorAll('.plan-card').forEach(card => {
      const slug = card.dataset.plan;
      const prices = JSON.parse(card.dataset.prices || '{}');
      planData[slug] = {
        slug,
        name: card.querySelector('.plan-name').textContent.trim(),
        prices
      };
    });

    // ── Billing Cycle ──────────────────────────────────────────────────────────
    function changeBillingCycle(cycle) {
      currentCycle = cycle;

      // Clear any visible errors when changing cycle
      $('.form-error-message').hide().text('');

      // Toggle active button
      document.querySelectorAll('.billing-btn').forEach(b => b.classList.remove('active'));
      document.querySelector(`[data-cycle="${cycle}"]`).classList.add('active');

      // Show/hide free-only cards
      document.querySelectorAll('.free-only').forEach(card => {
        card.classList.toggle('hidden', cycle !== 'monthly');
      });

      // Update all card prices
      document.querySelectorAll('.plan-card').forEach(card => {
        const slug = card.dataset.plan;
        const data = planData[slug];
        if (!data) return;

        const priceObj = data.prices[cycle];
        const priceEl = card.querySelector('.js-price');
        const detailEl = card.querySelector('.js-price-detail');
        const labelEl = card.querySelector('.billing-label');

        if (priceObj) {
          priceEl.textContent = Math.round(priceObj.price);
          if (labelEl) labelEl.textContent = cycleLabel(cycle);
          if (detailEl) {
            detailEl.innerHTML = priceObj.discount > 0 ?
              `<span class="badge-discount">${priceObj.discount}% OFF</span>` :
              '';
          }
        } else {
          // No price for this cycle (e.g. free plan on non-monthly)
          if (priceEl) priceEl.textContent = '—';
          if (detailEl) detailEl.innerHTML = '';
        }
      });

      document.getElementById('selected-billing').value = cycle;

      // Re-run summary if a plan is selected
      if (currentPlan) {
        const data = planData[currentPlan.slug];
        if (data && !data.prices[cycle]) {
          // current plan unavailable in this cycle — deselect
          deselectAllCards();
          currentPlan = null;
          resetSummary();
        } else {
          updateSummary();
        }
      }
    }

    // ── Plan Selection ──────────────────────────────────────────────────────────
    function selectPlan(slug, name) {
      const data = planData[slug];
      if (!data) return;

      // Clear any visible errors when selecting a new plan
      $('.form-error-message').hide().text('');

      // Check plan has a price for current cycle
      if (!data.prices[currentCycle]) {
        // If free plan selected but cycle isn't monthly, switch to monthly
        if (slug === 'free') {
          changeBillingCycle('monthly');
        }
        return;
      }

      currentPlan = data;

      deselectAllCards();
      document.querySelector(`[data-plan="${slug}"]`).classList.add('selected');
      document.getElementById('selected-plan-slug').value = slug;

      updateSummary();

      const form = document.getElementById('entrepreneurForm');
      if (form) form.classList.remove('hidden');
    }

    function deselectAllCards() {
      document.querySelectorAll('.plan-card').forEach(c => c.classList.remove('selected'));
    }

    // ── Summary ─────────────────────────────────────────────────────────────────
    function updateSummary() {
      if (!currentPlan) return resetSummary();

      const priceObj = currentPlan.prices[currentCycle];
      if (!priceObj) return resetSummary();

      const rawPrice = parseFloat(priceObj.price);
      const currency = priceObj.currency || 'MAD';
      const discount = priceObj.discount || 0; // plan-level billing discount (already in price)

      // Apply referral discount on top of the stored price
      const referralAmt = referralDiscount > 0 ? Math.round(rawPrice * referralDiscount / 100) : 0;
      const finalPrice = Math.round(rawPrice - referralAmt);

      document.getElementById('summary-plan').textContent = currentPlan.name;
      document.getElementById('summary-billing').textContent = cycleText(currentCycle);
      document.getElementById('summary-original').textContent = `${Math.round(rawPrice)} ${currency}`;
      document.getElementById('referral-discount').value = referralAmt;
      document.getElementById('price-after-discount').value = finalPrice;
      document.getElementById('selected-price-id').value = priceObj.price_id;

      const referralRow = document.getElementById('referral-row');
      const discountEl = document.getElementById('summary-discount');
      if (referralRow && discountEl) {
        if (referralAmt > 0) {
          referralRow.style.display = 'flex';
          discountEl.textContent = `-${referralAmt} ${currency}`;
        } else {
          referralRow.style.display = 'none';
        }
      }

      const renewalCycleWord = {
        monthly: 'mensuel',
        quarterly: 'trimestriel',
        yearly: 'annuel'
      } [currentCycle] || '';

      // Enable CTA
      const btn = document.getElementById('cta-btn');
      btn.disabled = false;
      btn.style.opacity = '1';
    }

    function resetSummary() {
      document.getElementById('summary-plan').textContent = '—';
      document.getElementById('summary-billing').textContent = cycleText(currentCycle);
      document.getElementById('summary-original').textContent = '—';
      document.getElementById('selected-price-id').value = '';
      const btn = document.getElementById('cta-btn');
      btn.disabled = true;
      btn.style.opacity = '.6';
    }

    // ── Helpers ─────────────────────────────────────────────────────────────────
    function cycleLabel(cycle) {
      return {
        monthly: 'mois',
        quarterly: 'trimestre',
        yearly: 'an'
      } [cycle] || 'mois';
    }

    function cycleText(cycle) {
      return {
        monthly: 'Mensuel',
        quarterly: 'Trimestriel',
        yearly: 'Annuel'
      } [cycle] || 'Mensuel';
    }

    // ── Form submit guard ────────────────────────────────────────────────────────
    document.getElementById('checkout-form').addEventListener('submit', function(e) {
      const priceId = document.getElementById('selected-price-id').value;
      if (!priceId) {
        e.preventDefault();
        alert('Veuillez sélectionner un plan avant de continuer.');
      }
    });

    // ── Init ─────────────────────────────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', function() {
      // Auto-select Pro on load
      // selectPlan('pro', 'Pro');

      // const form = document.getElementById('entrepreneurForm');
      // if (form) form.classList.add('hidden');
    });
  </script>
</body>

</html>