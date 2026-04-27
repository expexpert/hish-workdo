<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Simply Compta</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;200;300;400;500;600;700;800;900&display=swap"
    rel="stylesheet">
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" type="image/png" href="{{ asset('storage/uploads/new-landing-page/header_logo.png') }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css" />
  <link rel="stylesheet" href="{{ asset('/css/landingPage.css') }}" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>

<body>

  <div class="sc-dash">
    <!-- Header Section -->
    <header>
      <nav class="navbar navbar-expand-lg navbar-light">
        <a class="navbar-brand" href="#">
          <img src="{{ asset('storage/uploads/new-landing-page/header_logo.svg')}}" alt="Simply Compta Logo">
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
          aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav ml-auto">
            <li class="nav-item">
              <a class="nav-link" href="#features">Fonctionnalités</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#assignments">Mission</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#contact-us">Contact</a>
            </li>
            <li class="nav-item">
              <a class="btn btn-success" href="/login">Essai gratuit</a>
            </li>
          </ul>
        </div>
      </nav>
    </header>

    <!-- Main Content Section -->
    <main class="Simply">
      <div class="hero-outer" style="background-image: url('{{ asset('storage/uploads/new-landing-page/hero_bg.png') }}')" !important;">
        <div class="hero-container" style="background-image: url('{{ asset('storage/uploads/new-landing-page/hero_bg.png') }}')" !important;">
          <div class="row align-items-center">
            <div class="col-md-6">
              <h1 class="main-heading">La <strong>nouvelle interface</strong> qui <strong>redéfinit la relation</strong>
                entre le <strong>cabinet comptable</strong> et ses clients</h1>
              <p class="hero-text">Simplifiez la gestion. Valorisez votre expertise.</p>
              <p class="hero-text">La digitalisation de la relation client devient un standard</p>
              <div class="btn-container mt-4">
                <a href="https://wa.me/212655023474" target="_blank" class="btn btn-primary">Demandez une démo</a>
              </div>
            </div>
            <div class="col-md-6 banner-img-wraper">
              <div class="banner-mobile-img"> <img src="{{ asset('storage/uploads/new-landing-page/new-banner (1).png')}}" alt="Simply Compta Main Image"
                  class="img-fluid">
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- slider Section -->
      <section class="banner-down-slider">
        <div class="slider-container">
          <div class="custom-slider">

            <!-- Slide 1 -->
            <div class="slide-box">
              <div class="slide-content">

                <div class="left-text">
                  <p>
                    Vous passez plus de temps à gérer les problèmes... qu’à faire avancer votre cabinet.
                  </p>
                  <p>
                    Entre les relances, les documents manquants, les échanges dispersés et les urgences permanentes,
                    votre organisation vous ralentit au lieu de vous faire avancer.
                  </p>
                </div>

                <div class="center-img">
                  <img src="{{ asset('storage/uploads/new-landing-page/illustration-1-bloc-2.webp')}}" alt="illustration">
                </div>

                <div class="right-text">
                  <h3>Résultat :</h3>
                  <p>stress, perte de temps, et un potentiel largement sous-exploité</p>
                </div>

              </div>
            </div>

            <!-- Slide 2 -->
            <div class="slide-box">
              <div class="slide-content">

                <div class="left-text">
                  <p>
                    Votre équipe est débordée et les processus ne sont pas optimisés.
                  </p>
                  <p>
                    Les tâches répétitives prennent trop de temps et réduisent votre efficacité globale.
                  </p>
                </div>

                <div class="center-img">
                  <img src="{{ asset('storage/uploads/new-landing-page/groupe-53.webp')}}" alt="illustration">
                </div>

                <div class="right-text">
                  <h3>Résultat :</h3>
                  <p>baisse de productivité et frustration au quotidien</p>
                </div>

              </div>
            </div>

          </div>
        </div>

      </section>



      <!-- Transformation Section -->
      <section class="transformation-section">
        <h2 class="section-title">La profession comptable connaît<br> une <strong>transformation profonde</strong></h2>
        <div class="row_1">
          <div class="col-md-4">
            <div class="transformation-box first-tra-box">
              <img src="{{ asset('storage/uploads/new-landing-page/logo1.png')}}" alt="Icon 1" class="transformation-icon">
              <p class="transformation-text">Les attentes des entreprises<br> <strong>évoluent</strong></p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="transformation-box">
              <img src="{{ asset('storage/uploads/new-landing-page/logo2.png')}}" alt="Icon 2" class="transformation-icon">
              <p class="transformation-text">L'accès immédiat à l'information devient<strong><br> la norme</strong></p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="transformation-box">
              <img src="{{ asset('storage/uploads/new-landing-page/logo3.png')}}" alt="Icon 3" class="transformation-icon">
              <p class="transformation-text">La réactivité n'est plus un avantage :<strong><br> elle est
                  attendue</strong>
              </p>
            </div>
          </div>
        </div>
        <p class="transformation-info">Aujourd'hui, les cabinets les plus performants ne cherchent plus seulement à
          gérer
          leurs flux administratifs.</p>
        <p class="p-text">Ils créent un environnement digital capable de valoriser pleinement leur expertise.</p>
        <p class="transformation-highlight">SIMPLYCOMPTA S'INSCRIT DANS CETTE ÉVOLUTION.</p>
        <a href="https://wa.me/212655023474" target="_blank" class="btn btn-success">Demandez une démo</a>
      </section>

      <!-- Paralex slider  desktop-->

      <section class="after-before-paralex-slider">
        <div class="section">
          <div class="slider">

            <div class="progress-wrap">
              <div class="progress-bar">
                <div class="progress"></div>
                <div class="handle">
                  <i class="fas fa-arrows-up-down"></i>
                </div>
              </div>
            </div>

            <div class="slides">

              <div class="slide" id="slide1">
                <div class="content">
                  <div class="top-header-paralex">
                    <h2>AVANT</h2>
                  </div>
                  <div class="paralex-first-slide">
                    <div class="box">
                      <h5>WhatsApp, emails, appels dispersés</h5>
                    </div>
                    <div class="box">
                      <h5>Documents éparpillés</h5>
                    </div>
                    <div class="box">
                      <h5>Relances manuelles oubliées</h5>
                    </div>
                    <div class="box">
                      <h5>Infos clients incomplètes</h5>
                    </div>
                  </div>
                  <div class="box bottom-paralex-btn">
                    <h5> Dépendance totale au cabinet
                    </h5>
                  </div>
                </div>
                <div class="imageds">
                  <img src="{{ asset('storage/uploads/new-landing-page/paralex-one.svg')}}">
                </div>
              </div>

              <div class="slide" id="slide2">
                <div class="content">
                  <h2>APRES</h2>
                  <div class="second-paralex-grid">
                    <div class="shades-boxes shade-one">
                      <h5>Échanges 100% centralisés</h5>
                    </div>
                    <div class="shades-boxes shade-two">
                      <h5>Organisation intelligente</h5>
                    </div>
                    <div class="shades-boxes shade-three">
                      <h5>Automates de relance</h5>
                    </div>
                    <div class="shades-boxes shade-four">
                      <h5>Vue client 360° en 1 clic</h5>
                    </div>
                  </div>
                  <div class="second-bottom-text">
                    <h5>Autonomie &amp; Liberté</h5>
                  </div>
                </div>
                <div class="imageds">
                  <img src="{{ asset('storage/uploads/new-landing-page/paralex-two.png')}}">
                </div>
              </div>

            </div>

          </div>

        </div>

      </section>

      <!-- MOBILE-PARALEX-SLIDER -->
      <section class="mobile-paralex">
        <div class="paralex-mobile-container">
          <div class="para-mobile-slider">
            <div class="before-sec-mobile ">
              <div class="avant-heading">
                <h2>AVANT</h2>
              </div>
              <div class="left-right-grid-avant">
                <div class="left-sideavant-boxes">
                  <div class="top-four-avant-sec">
                    <div class="box-one-avant">
                      <h5>WhatsApp, emails, appels dispersés</h5>
                    </div>
                    <div class="box-one-avant">
                      <h5>Documents éparpillés
                      </h5>
                    </div>
                    <div class="box-one-avant">
                      <h5>Relances manuelles oubliées
                      </h5>
                    </div>
                    <div class="box-one-avant">
                      <h5>Infos clients incomplètes
                      </h5>
                    </div>
                  </div>
                  <div class="bottom-avant-box">
                    <h5>Dépendance totale au cabinet</h5>
                  </div>
                </div>
                <div class="right-side-avant-img">
                  <img src="{{ asset('storage/uploads/new-landing-page/paralex-one.svg')}}" alt="paralex">
                </div>
              </div>
            </div>
            <div class="before-sec-mobile second-partmobile">
              <div class="avant-heading">
                <h2>APRES</h2>
              </div>
              <div class="left-right-grid-avant">
                <div class="left-sideavant-boxes">
                  <div class="top-four-avant-sec">
                    <div class="box-one-avant avant-ones">
                      <h5>Échanges 100% centralisés</h5>
                    </div>
                    <div class="box-one-avant avant-two">
                      <h5>Organisation intelligente</h5>
                    </div>
                    <div class="box-one-avant avant-three">
                      <h5>Automates de relance</h5>
                    </div>
                    <div class="box-one-avant avant-four">
                      <h5>Vue client 360° en 1 clic</h5>
                    </div>
                  </div>
                  <div class="bottom-avant-box last-pointes">
                    <h5>Autonomie &amp; Liberté</h5>
                  </div>
                </div>
                <div class="right-side-avant-img">
                  <img src="{{ asset('storage/uploads/new-landing-page/paralex-two.png')}}" alt="paralex">
                </div>
              </div>
            </div>

          </div>
        </div>
      </section>

      <!-- quote Section -->
      <section class="quote-section-part">
        <div class="quote-content">
          <p>“SimplyCompta ce n’est pas un outil de plus.</p>
          <h6>C’est celui qui remplace tous les autres.”
          </h6>
        </div>

      </section>

      <!-- ai-new-section -->
      <section class="ai-new-section" style="background-image: url('{{ asset('storage/uploads/new-landing-page/new-section-bg.png') }}')">
        <div class="ai-container">
          <div class="top-bar-img-text">
            <div class="left-side-box-content">
              <h2>Intelligence<br>
                Artificielle<br>
                connectée à<br>
                WhatsApp</h2>
              <p class="right-side-border-text">"Avec l’agent IA de Simply Compta, vos clients interagissent...
                simplement en envoyant un message."</p>
              <div class="two-ai-columns">
                <div class="left-ai-column-wraper">
                  <div class="new-image-container-wraper">
                    <img src="{{ asset('storage/uploads/new-landing-page/new-whatsap-one (1).png')}}">
                  </div>
                  <h6>Demander une facture</h6>
                  <p>Ils écrivent. L’IA exécute.
                  </p>
                </div>
                <div class="left-ai-column-wraper">
                  <div class="new-image-container-wraper">
                    <img src="{{ asset('storage/uploads/new-landing-page/new-whatsapp-two.png')}}">
                  </div>
                  <h6>Récupérer leurs documents
                  </h6>
                  <p>Zéro friction, accès immédiat.
                  </p>
                </div>
              </div>
            </div>
            <div class="right-side-whatsapp-img">
              <img src="{{ asset('storage/uploads/new-landing-page/whats-mobile.png')}}" alt="">
            </div>
          </div>
          <div class="anergy-part-ai">
            <h6>⚡ Une expérience plus fluide · plus rapide · plus intuitive</h6>
            <h6>👍 Gain de temps massif · réduction des frictions</h6>
            <div class="green-bootm-headline">
              <h5>“Si vos clients savent envoyer un message...</h5>
              <h5>ils savent utiliser Simply Compta.”</h5>
            </div>
          </div>
        </div>
      </section>


      <!-- Application Section -->
      <section id="features" class="application-section">
        <h2 class="application-title">Une <strong>application tout-en-un </strong>pour piloter votre
          <strong>comptabilité</strong>
        </h2>
        <div class="row_1">
          <div class="col-md-3">
            <div class="transformation-box1">
              <img src="{{ asset('storage/uploads/new-landing-page/section_logo1.png')}}" alt="Icon 1" class="transformation-icon1">
              <h3 class="application-heading">Factures et reçus centralisés</h3>
              <p class="application-text">Importez facilement vos factures et reçus,tout est déjà groupé et classé</p>
            </div>
          </div>
          <div class="col-md-3">
            <div class="transformation-box1">
              <img src="{{ asset('storage/uploads/new-landing-page/section_logo2.png')}}" alt="Icon 2" class="transformation-icon1">
              <h3 class="application-heading">Documents toujours à jour</h3>
              <p class="application-text">Accédez à tous vos documents juridiques et comptable en un clic</p>
            </div>
          </div>
          <div class="col-md-3">
            <div class="transformation-box1">
              <img src="{{ asset('storage/uploads/new-landing-page/new-section_logo3.png')}}" alt="Icon 3" class="transformation-icon1">
              <h3 class="application-heading">Suivi financier en temps réel</h3>
              <p class="application-text">Visualisez vos revenus dépensés et déclarations en un clin d'oeil</p>
            </div>
          </div>
          <div class="col-md-3">
            <div class="transformation-box1">
              <img src="{{ asset('storage/uploads/new-landing-page/section_logo4.png')}}" alt="Icon 4" class="transformation-icon1">
              <h3 class="application-heading">Notifications et rappels</h3>
              <p class="application-text">Restez connecté et recevez des alertes pour vos échéances</p>
            </div>
          </div>
        </div>
      </section>

      <!-- Simplycompta Section -->
      <section class="simplycompta-section">
        <div class="simplycompta-container">
          <!-- Left side (Mobile Image) -->
          <div class="simplycompta-phone">
            <img src="{{ asset('storage/uploads/new-landing-page/platform-new.png')}}" alt="SimplyCompta App Mockup" class="simplycompta-phone-img">
          </div>

          <!-- Right side (Content) -->
          <div class="simplycompta-content">
            <h1 class="simplycompta-title"><strong>Une plateforme pensée</strong> <br> pour le cabinet moderne</h1>
            <p class="simplycompta-description">
              SimplyCompta est la plateforme qui
              <span class="highlight-blue">simplifie la gestion administrative</span>
              et permet aux cabinets comptables de recentrer leur énergie sur leur véritable valeur :
              <span class="highlight-green">le conseil et l'expertise.</span>
            </p>
            <p class="simplycompta-description">
              Plus qu'un outil, SimplyCompta constitue une
              <span class="highlight-blue">véritable infrastructure digitale</span>,
              conçue pour structurer, fluidifier et moderniser la relation entre le cabinet et ses clients.
            </p>

            <div class="simplycompta-box">
              <h3 class="simplycompta-box-title">
                <strong>Faites entrer votre cabinet</strong> dans une nouvelle dimension
              </h3>

              <p class="box-text"><img src="{{ asset('storage/uploads/new-landing-page/check_icon.svg')}}" alt="Benefit Icon" class="check-icon">Moderniser
                votre
                organisation</p>
              <p class="box-text"><img src="{{ asset('storage/uploads/new-landing-page/check_icon.svg')}}" alt="Benefit Icon" class="check-icon">Renforcer votre
                image professionnelle</p>
              <p class="box-text"><img src="{{ asset('storage/uploads/new-landing-page/check_icon.svg')}}" alt="Benefit Icon" class="check-icon">Améliorer
                l'expérience client</p>
              <p class="box-text"><img src="{{ asset('storage/uploads/new-landing-page/check_icon.svg')}}" alt="Benefit Icon" class="check-icon">Optimiser vos
                opérations</p>
              <p class="box-text"><img src="{{ asset('storage/uploads/new-landing-page/check_icon.svg')}}" alt="Benefit Icon" class="check-icon">Créer un
                avantage
                concurrentiel durable</p>
            </div>

            <!-- Button -->
            <div class="simplycompta-btn">
              <button class="simplycompta-button" onclick="window.location.href='https://wa.me/212655023474'">Demandez une démo</button>
            </div>
          </div>
        </div>
      </section>

      <!-- Client Experience Section -->
      <section id="assignments" class="client-experience-section">
        <div class="client-experience-container">
          <!-- Heading -->
          <div class="client-experience-header">
            <h1>Une <strong>expérience client</strong> enfin alignée avec <strong>votre niveau d'exigence</strong></h1>
            <p class="client-experience-subtitle">
              Vos clients accèdent à un espace sécurisé, disponible à <br>tout moment, depuis n’importe où.
            </p>
          </div>

          <!-- Features grid -->
          <div class="client-experience-grid">
            <!-- Feature 1 -->
            <div class="client-feature feature-documents">
              <div class="feature-icon">
                <!-- Replace with actual icon -->
                <img src="{{ asset('storage/uploads/new-landing-page/i1.svg')}}" alt="Documents essentiels">
              </div>
              <h3 class="feature-title">Documents essentiels</h3>
              <p class="feature-desc">Retrouver instantanément leurs documents essentiels</p>
            </div>

            <!-- Feature 2 -->
            <div class="client-feature feature-invoices">
              <div class="feature-icon">
                <img src="{{ asset('storage/uploads/new-landing-page/i2.svg')}}" alt="Factures et pièces">
              </div>
              <h3 class="feature-title">Factures et pièces</h3>
              <p class="feature-desc">Déposer factures et pièces comptables sans contrainte</p>
            </div>

            <!-- Feature 3 -->
            <div class="client-feature feature-bank">
              <div class="feature-icon">
                <img src="{{ asset('storage/uploads/new-landing-page/i3.svg')}}" alt="Relevés bancaires">
              </div>
              <h3 class="feature-title">Relevés bancaires</h3>
              <p class="feature-desc">Transmettre leurs relevés bancaires en quelques secondes</p>
            </div>

            <!-- Feature 4 -->
            <div class="client-feature feature-update">
              <div class="feature-icon">
                <img src="{{ asset('storage/uploads/new-landing-page/i4.svg')}}" alt="Informations à jour">
              </div>
              <h3 class="feature-title">Informations à jour</h3>
              <p class="feature-desc">Mettre à jour leurs informations facilement</p>
            </div>

            <!-- Feature 5 -->
            <div class="client-feature feature-notifications">
              <div class="feature-icon">
                <img src="{{ asset('storage/uploads/new-landing-page/i5.svg')}}" alt="Notifications">
              </div>
              <h3 class="feature-title">Notifications</h3>
              <p class="feature-desc">Recevoir vos notifications importantes</p>
            </div>

            <!-- Feature 6 -->
            <div class="client-feature feature-security">
              <div class="feature-icon">
                <img src="{{ asset('storage/uploads/new-landing-page/i6.svg')}}" alt="Sécurité 24/7">
              </div>
              <h3 class="feature-title">Sécurité 24/7</h3>
              <p class="feature-desc">Accès sécurisé disponible à tout moment</p>
            </div>
          </div>
        </div>
      </section>

      <!-- Value Section -->
      <section class="value-section">
        <h1><strong>Libérez vos équipes</strong> pour ce qui crée<br> réellement de la valeur</h1>
        <div class="columns-container">
          <div class="column1 column-left">
            <h3 class="value-title">Ce que vous subissez encore</h3>
            <p class="box-text"><img src="{{ asset('storage/uploads/new-landing-page/pink_icon.svg')}}" alt="Pink Icon" class="check-icon">Fini les documents
              égarés.</p>
            <p class="box-text"><img src="{{ asset('storage/uploads/new-landing-page/pink_icon.svg')}}" alt="Pink Icon" class="check-icon">Fini les relances
              répétées.</p>
            <p class="box-text"><img src="{{ asset('storage/uploads/new-landing-page/pink_icon.svg')}}" alt="Pink Icon" class="check-icon">Fini les échanges
              dispersés.</p>
          </div>
          <div class="column2 column-right">
            <h3 class="value-title2">Ce que vous gagnez avec SimplyCompta</h3>
            <p class="box-text"><img src="{{ asset('storage/uploads/new-landing-page/check_icon.svg')}}" alt="Cross Icon" class="check-icon">Votre cabinet gagne
              en lisibilité</p>
            <p class="box-text"><img src="{{ asset('storage/uploads/new-landing-page/check_icon.svg')}}" alt="Cross Icon" class="check-icon">Fini les relances
              répétées.</p>
            <p class="box-text"><img src="{{ asset('storage/uploads/new-landing-page/check_icon.svg')}}" alt="Cross Icon" class="check-icon">Votre posture gagne
              en modernité</p>
          </div>
        </div>
      </section>

      <!-- Business Section -->
      <section class="business-section" style="background-image: url('{{ asset('storage/uploads/new-landing-page/new-ai-img.png') }}')">
        <div class="bussiness-container">
          <div class="business-overlay">
            <div class="business-content">
              <h1><strong>SIMPLYCOMPTA</strong> réduit les frictions opérationnelles et centralise les flux.</h1>
              <p>Vos collaborateurs peuvent ainsi se concentrer sur des missions à plus forte valeur :</p>
              <div class="img-boxes">
                <p class="box-text"><img src="{{ asset('storage/uploads/new-landing-page/check_icon.svg')}}" alt="Cross Icon" class="check-icon">l'analyse</p>
                <p class="box-text"><img src="{{ asset('storage/uploads/new-landing-page/check_icon.svg')}}" alt="Cross Icon"
                    class="check-icon">l'accompagnement</p>
                <p class="box-text"><img src="{{ asset('storage/uploads/new-landing-page/check_icon.svg')}}" alt="Cross Icon" class="check-icon">le conseil.</p>
              </div>



              <a href="#" class="cta-btn">Demandez un rdv</a>
            </div>
          </div>
        </div>
      </section>

      <!-- Solution Section -->
      <section class="solution-section" style="background-image: url('{{ asset('storage/uploads/new-landing-page/rectangle-8@3x.png') }}')">
        <div class="solution-image">
          <img src="{{ asset('storage/uploads/new-landing-page/solution-new.png')}}" alt="Phone Mockup" class="solution-phone-img">
        </div>
        <div class="solution-content">
          <h1><strong>La solution idéale </strong>entre vous et votre cabinet comptable</h1>
          <p class="solution-text"><strong>SIMPLYCOMPTA</strong> simplifie les échanges et le partage de<br> documents
            entre les entrepreneurs et leur cabinet<br> comptable.</p>
          <ul class="solution-features">
            <p><img src="{{ asset('storage/uploads/new-landing-page/blue_logo .png')}}" alt="Blue Icon" class="check-icon"><span class="blue-text">Connexion
                sécurisée</span> et partagée en temps réel</li>
            </p>
            <p><img src="{{ asset('storage/uploads/new-landing-page/blue_logo .png')}}" alt="Blue Icon" class="check-icon"><span class="blue-text">Transmission
                facile</span> de vos relevés et justificatifs</li>
            </p>
            <p><img src="{{ asset('storage/uploads/new-landing-page/blue_logo .png')}}" alt="Blue Icon" class="check-icon"><span class="blue-text">Accès
                instantané</span> à vos bilans et déclarations</li>
            </p>
          </ul>
        </div>
      </section>


      <!-- Modele Section -->
      <section class="modele-section">
        <div class="modele-content">
          <h1>Un modèle économique <strong>simple</strong> et <strong>immédiatement rentable</strong></h1>
          <p>La solution s'autofinance naturellement grâce au temps opérationnel qu'elle permet de récupérer</p>
        </div>

        <div class="modele-grid">
          <!-- Feature 1 -->
          <div class="modele-feature feature-documents">
            <div class="modele-icon">
              <img src="{{ asset('storage/uploads/new-landing-page/modele1.svg')}}" alt="Documents essentiels">
            </div>
            <p class="modele-desc">Aucune<br> complexité</p>
          </div>

          <!-- Feature 2 -->
          <div class="modele-feature2 feature-invoices">
            <div class="modele-icon">
              <img src="{{ asset('storage/uploads/new-landing-page/modele2.svg')}}" alt="Factures et pièces">
            </div>
            <p class="modele-desc">Aucun risque<br> économique</p>
          </div>
        </div>
      </section>

      <!-- Cta Section -->
      <section class="cta-sectios">
        <div class="cta-container">
          <div class="cta-text">
            <p class="cta-heading">Vous enrichissez votre offre sans alourdir votre organisation.</p>
            <p class="cta-subheading">C'EST UNE MONTÉE EN VALEUR NETTE POUR VOTRE CABINET.</p>
          </div>
          <button class="cta-button" onclick="window.location.href='https://wa.me/212655023474'">Demandez une démo</button>
        </div>
      </section>

      <!-- personnalisée Section -->
      <section class="personnalisée-container">
        <div class="row align-items-center">
          <!-- Left Column -->
          <div class="col-md-5 text-center text-md-start">
            <h2 class="personnalisée-title"><strong>Une mise en place personnalisée,</strong> fluide et sécurisée</h2>
            <p class="personnalisée-description">Chaque cabinet possède ses méthodes, son identité et son organisation.
              <strong>SimplyCompta s’y adapte pleinement.</strong>
            </p>
            <div class="btn-container mt-4">
              <button class="btn btn-primary" onclick="window.location.href='https://wa.me/212655023474'">Demandez une démo</button>
            </div>
          </div>

          <!-- Middle Column -->
          <div class="col-md-3">
            <div class="row">
              <!-- First Box -->
              <div class="col-12">
                <div class="personnalisée-box">
                  <div class="personnalisée-icon-box">
                    <img src="{{ asset('storage/uploads/new-landing-page/free.svg')}}" alt="Free Icon" class="free_icon">
                  </div>
                  <h5 class="personnalisée-title1">Devis Gratuit</h5>
                  <p class="personnalisée-dec">Construit selon vos besoins spécifiques</p>
                </div>
              </div>
              <!-- Second Box -->
              <div class="col-12">
                <div class="personnalisée-box2">
                  <div class="personnalisée-icon-box">
                    <img src="{{ asset('storage/uploads/new-landing-page/setting.svg')}}" alt="Settings Icon" class="free_icon">
                  </div>
                  <h5 class="personnalisée-title1">Accompagnement</h5>
                  <p class="personnalisée-dec">Dédié pour garantir une adoption rapide</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Right Column -->
          <div class="col-md-4 text-center">
            <img src="{{ asset('storage/uploads/new-landing-page/Phone.png')}}" alt="Image" class="img-personnalisée">
          </div>
        </div>
      </section>







      <!------ 7 DAYS TRAIL NEW SECTION DESIGN START HERE   ------>

      <section>
        <div class="trail-containe-main">
          <div class="trail-text-img">
            <div class="seven-img"><img src="{{ asset('storage/uploads/new-landing-page/image_7 (1).png')}}" alt="7 Days Trial" /></div>
            <div class="sev-trail-sec">
              <div class="sev-trail-text-wraper">
                <h4>7 jours d'essai inclus pour valider votre décision</h4>
                <p>Testez SimplyCompta en conditions réelles, avec vos équipes et vos clients.<br><br>

                  Cette phase vous permet de confirmer concrètement la valeur apportée par la solution</p>
                <p class="engagement-text"><b>Sans engagement :</b> Vous restez libre durant cette période</p>
              </div>

            </div>
          </div>
        </div>
      </section>
      <!------ 7 DAYS TRAIL NEW SECTION DESIGN START END   ------>








      <!-- Trial Section -->
      <section class="trial-section">
        <!-- <div class="trial-container">
          <div class="trial-image">
            <img src="{{ asset('storage/uploads/new-landing-page/image_7.png')}}" alt="7 Days Trial" />
          </div>
          <div class="trial-content">
            <h1 class="trial-text"><strong>7 jours d'essai inclus pour valider votre décision</strong></h1>
            <p class="trial-dec">Testez SimplyCompta en conditions réelles, avec vos équipes et vos clients.</p>
            <p class="trial-dec">Cet phase vous permet de confirmer concrètement la valeur apportée par la solution.</p>
            <p class="trial-dec"><strong>Sans engagement :</strong> Vous restez libre durant cette période.</p>
          </div>
        </div> -->
        <div class="conviction">
          <div class="trial-logo">
            <img src="{{ asset('storage/uploads/new-landing-page/trial_logo.svg')}}" alt="Trial Logo" />
          </div>
          <p>Notre conviction est simple : <strong>une solution performante doit convaincre par son
              utilisation.</strong>
          </p>
          <p>L'adoption se fait naturellement dès les premiers jours.</p>
        </div>
      </section>

      <!-- Control Section -->
      <section class="control-section">
        <div class="container">
          <div class="control-content">
            <h1 class="business-content"><strong>Gardez le contrôle</strong>, en toute circonstance</h1>
            <p class="control-dec">Vous pilotez intégralement l'accès de vos clients. Si une collaboration s'arrête,
              l'accès peut être suspendu immédiatement.</p>
            <ul class="benefits">
              <p><img src="{{ asset('storage/uploads/new-landing-page/green_logo.png')}}" alt="Green Icon" class="check-icon">Aucune dépendance</p>
              <p><img src="{{ asset('storage/uploads/new-landing-page/green_logo.png')}}" alt="Green Icon" class="check-icon">Aucune contrainte</p>
              <p><img src="{{ asset('storage/uploads/new-landing-page/green_logo.png')}}" alt="Green Icon" class="check-icon">Contrôle total</p>
            </ul>
          </div>
          <div class="control-image">
            <img src="{{ asset('storage/uploads/new-landing-page/new-mobile-img.png')}}" alt="Control Image" />
          </div>
        </div>
      </section>

      <!-- Form Section -->
      <section class="main-container">
        <h2 class="form-section-title text-center">Prêt à moderniser votre cabinet ?</h2>
        <p class="text-center">Demandez votre devis gratuit ou planifiez une démonstration</p>
        <div id="contact-us" class="form-container">
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
          <form action="{{ route('landingpage.sendmail') }}" method="POST">
            @csrf
            <div class="row">
              <div class="col-md-6 top-row-form">
                <label for="cabinet-name" class="form-label">Nom du cabinet <span class="requires">*</span></label>
                <input type="text" class="form-control" id="cabinet-name" name="cabinet-name" placeholder="Nom de votre cabinet" required>
              </div>
              <div class="col-md-6 sec-row-form">
                <label for="contact-name" class="form-label">Nom du contact <span class="requires">*</span></label>
                <input type="text" class="form-control" id="contact-name" name="contact-name" placeholder="Votre nom" required>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6 top-row-form">
                <label for="email" class="form-label">Email <span class="requires">*</span></label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Votre email" required>
              </div>
              <div class="col-md-6 sec-row-form">
                <label for="phone" class="form-label">Téléphone <span class="requires">*</span></label>
                <input type="tel" class="form-control" id="phone" name="phone" placeholder="+212" required>
              </div>
            </div>
            <div class="mb-3 form-check-boxes">
              <label class="form-label">Type de demande <span class="requires">*</span></label>
              <div class="checkbox-group">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" value="demo" id="demo" name="request-type[]">
                  <label class="form-check-label" for="demo">
                    Démonstration
                  </label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" value="info" id="info" name="request-type[]">
                  <label class="form-check-label" for="info">
                    Informations complémentaires
                  </label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" value="devis" id="devis" name="request-type[]">
                  <label class="form-check-label" for="devis">
                    Devis
                  </label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" value="trial" id="trial" name="request-type[]">
                  <label class="form-check-label" for="trial">
                    Essai de 7 jours
                  </label>
                </div>
              </div>
            </div>
            <div class="mb-3">
              <label for="message" class="form-label">Message <span class="requires">*</span></label>
              <textarea class="form-control" id="message" name="message" rows="4" required></textarea>
            </div>
            <div class="form-check mb-3">
              <input class="form-check-input" type="checkbox" id="data-usage" name="data-usage" required>
              <label class="form-check-label" for="data-usage">
                J'accepte que mes données soient utilisées pour traiter ma demande.
              </label>
            </div>
            <div class="form-btn">
              <button type="submit" class="btn btn-primary w-20">Demandez un rdv</button>
            </div>
          </form>
        </div>
      </section>

      <!-- Contact Section -->
      <section class="new-cont">
        <section class="contact_container">
          <div class="contact-section text-center">
            <h3 class="contact-text">Contactez-nous dès maintenant :</h3>
            <div class="contact-grid">
              <!-- Feature 1 -->
              <div class="contact-feature feature-documents">
                <div class="contact-icon">
                  <img src="{{ asset('storage/uploads/new-landing-page/whatsapp.svg')}}" alt="Documents essentiels">
                </div>
                <p class="contact-desc"><a target="_blank"
                    href="https://api.whatsapp.com/send/?phone=212655023474&text&type=phone_number&app_absent=0">
                    +212 (0)655023474
                  </a></p>
              </div>

              <!-- Feature 2 -->
              <div class="contact-feature2 feature-invoices">
                <div class="contact-icon">
                  <img src="{{ asset('storage/uploads/new-landing-page/email.svg')}}" alt="Factures et pièces">
                </div>
                <p class="contact-desc"><a href="mailto:contact@simply-compta.com">
                    contact@simply-compta.com
                  </a></p>
              </div>
            </div>
            <p class="contact-desc"><strong>Suivez-nous sur les réseaux sociaux</strong></p>
            <div class="social-icons">
              <a target="_blank" href="https://www.facebook.com/share/1PCo6LoQpo/?mibextid=wwXIfr"> <img src="{{ asset('storage/uploads/new-landing-page/facebook.svg')}}" alt="Facebook" class="social"></a>
              <a target="_blank" href="https://www.linkedin.com/in/simply-compta-6a92923ba?utm_source=share_via&utm_content=profile&utm_medium=member_ios"> <img src="{{ asset('storage/uploads/new-landing-page/linkedin.svg')}}" alt="Linkedin" class="social"></a>
              <a target="_blank" href="https://www.instagram.com/simplycompta?igsh=MW8zeHo4ZHFtdHBvbw=="> <img src="{{ asset('storage/uploads/new-landing-page/instagram.svg')}}" alt="Instagram" class="social"></a>
            </div>
          </div>
        </section>
      </section>


    </main>

    <!-- Footer Section -->
    <footer>
      <div class="container">
        <div class="row d-flex justify-content-between align-items-center SimplyCompta-footer">
          <div class="col-auto">
            <p>© 2026 SimplyCompta. Tous droits réservés.</p>
          </div>
          <div class="col-auto">
            <ul class="footer-links">
              <li><a href="#">Mentions légales</a></li>
              <li><a href="#">Confidentialité</a></li>
              <li><a href="#">CGU</a></li>
            </ul>
          </div>
        </div>
      </div>
    </footer>
  </div>


  <!-- WEB WHATSAPP -->
  <a href="https://api.whatsapp.com/send/?phone=212655023474&text&type=phone_number&app_absent=0" class="whatsapp-float"
    target="_blank">
    <i class="fab fa-whatsapp"></i>
  </a>

  <!-- Bootstrap JS Link -->
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
  <!-- Paralex slide script -->
  <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/ScrollTrigger.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/Draggable.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/ScrollToPlugin.min.js"></script>


  <script>
    window.addEventListener("scroll", function() {
      const header = document.querySelector("header");

      if (window.scrollY > 0) {
        header.classList.add("sticky");
      } else {
        header.classList.remove("sticky");
      }
    });
  </script>
  <script>
    $(document).ready(function() {
      var $slider = $('.custom-slider');

      $slider.slick({
        dots: false,
        arrows: true,
        infinite: false,
        speed: 1000,
        slidesToShow: 1,
        adaptiveHeight: true,

        prevArrow: '<button class="custom-prev"><i class="fa-solid fa-arrow-left"></i></button>',
        nextArrow: '<button class="custom-next"><i class="fa-solid fa-arrow-right"></i></button>'
      });

      // Page load pe prev arrow hide (instant)
      $('.custom-prev').css({
        opacity: 0,
        pointerEvents: 'none'
      });

      // Smooth fade transition after slide change
      $slider.on('afterChange', function(event, slick, currentSlide) {
        if (currentSlide === 0) {
          $('.custom-prev').stop().animate({
            opacity: 0
          }, 300, function() {
            $(this).css('pointer-events', 'none'); // click disable
          });
          $('.custom-next').stop().animate({
            opacity: 1
          }, 300, function() {
            $(this).css('pointer-events', 'auto'); // click enable
          });
        } else {
          $('.custom-prev').stop().animate({
            opacity: 1
          }, 300, function() {
            $(this).css('pointer-events', 'auto');
          });
          $('.custom-next').stop().animate({
            opacity: 0
          }, 300, function() {
            $(this).css('pointer-events', 'none');
          });
        }
      });


      // PARALEX SLIDER JS
      gsap.registerPlugin(ScrollTrigger, Draggable, ScrollToPlugin);
      const section = document.querySelector(".after-before-paralex-slider .section");
      const progress = document.querySelector(".after-before-paralex-slider .progress");
      const handle = document.querySelector(".after-before-paralex-slider .handle");
      const bar = document.querySelector(".after-before-paralex-slider .progress-bar");

      let tl = gsap.timeline({
        scrollTrigger: {
          trigger: section,
          start: "top top",
          end: "+=150%",
          scrub: 1,
          pin: ".after-before-paralex-slider .slider"
        }
      });

      tl.to(".after-before-paralex-slider #slide2", {
        y: "0%",
        ease: "none"
      });

      gsap.to(".after-before-paralex-slider .image", {
        y: -120,
        scrollTrigger: {
          trigger: section,
          start: "top top",
          end: "bottom bottom",
          scrub: true
        }
      });

      let st = ScrollTrigger.create({
        trigger: section,
        start: "top top",
        end: "bottom bottom",
        onUpdate: self => {
          let p = self.progress;
          progress.style.height = (p * 100) + "%";
          handle.style.top = (bar.offsetHeight * p) + "px";
        }
      });

      handle.style.pointerEvents = "none";




      $('.para-mobile-slider').slick({
        dots: true,
        infinite: true,
        arrows: false,
        speed: 300,
        slidesToShow: 1,
        adaptiveHeight: true
      });

    });
  </script>
</body>

</html>