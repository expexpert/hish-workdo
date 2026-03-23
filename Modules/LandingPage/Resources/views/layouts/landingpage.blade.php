<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simply Compta</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('storage/uploads/new-landing-page/header_logo.png') }}">
    <link rel="stylesheet" href="{{ asset('/css/landingPage.css') }}" />
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
                            <a class="nav-link" href="#">Fonctionnalités</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Mission</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Contact</a>
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
            <div class="hero-outer">
                <div class="hero-container">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h1 class="main-heading">La <strong>nouvelle interface</strong> qui <strong>redéfinit la relation</strong>
                                entre le <strong>cabinet comptable</strong> et ses clients</h1>
                            <p class="hero-text">Simplifiez la gestion. Valorisez votre expertise.</p>
                            <p class="hero-text">La digitalisation de la relation client devient un standard</p>
                            <div class="btn-container mt-4">
                                <a href="#" class="btn btn-primary">Demandez une démo</a>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <img src="{{ asset('storage/uploads/new-landing-page/hero_img.png')}}" alt="Simply Compta Main Image" class="img-fluid">
                        </div>
                    </div>
                </div>
            </div>

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
                <a href="#" class="btn btn-success">Demandez une démo</a>
            </section>

            <!-- Application Section -->
            <section class="application-section">
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
                            <img src="{{ asset('storage/uploads/new-landing-page/section_logo3.png')}}" alt="Icon 3" class="transformation-icon1">
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
                        <img src="{{ asset('storage/uploads/new-landing-page/Mobile-new.png')}}" alt="SimplyCompta App Mockup" class="simplycompta-phone-img">
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
                            <button class="simplycompta-button">Demandez une démo</button>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Client Experience Section -->
            <section class="client-experience-section">
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
            <section class="business-section" style="background-image: url('images/banner_image.png');">
                <div class="bussiness-container">
                    <div class="business-overlay">
                        <div class="business-content">
                            <h1><strong>SIMPLYCOMPTA</strong> réduit les frictions opérationnelles et centralise les flux.</h1>
                            <p>Vos collaborateurs peuvent ainsi se concentrer sur des missions à plus forte valeur :</p>
                            <ul>
                                <li>
                                    <p>l'analyse</p>
                                </li>
                                <li>
                                    <p>l'accompagnement</p>
                                </li>
                                <li>
                                    <p>le conseil.</p>
                                </li>
                            </ul>
                            <a href="#" class="cta-btn">Demandez un rdv</a>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Solution Section -->
            <section class="solution-section" style="background-image: url(images/rectangle-8@3x.png);">
                <div class="solution-image">
                    <img src="{{ asset('storage/uploads/new-landing-page/Mobile2.png')}}" alt="Phone Mockup" class="solution-phone-img">
                </div>
                <div class="solution-content">
                    <h1><strong>La solution idéale </strong>entre vous et votre cabinet comptable</h1>
                    <p class="solution-text"><strong>SIMPLYCOMPTA</strong> simplifie les échanges et le partage de<br> documents
                        entre les entrepreneurs et leur cabinet<br> comptable.</p>
                    <ul class="solution-features">
                        <p><img src="{{ asset('storage/uploads/new-landing-page/blue_logo.png')}}" alt="Blue Icon" class="check-icon"><span class="blue-text">Connexion
                                sécurisée</span> et partagée en temps réel</li>
                        </p>
                        <p><img src="{{ asset('storage/uploads/new-landing-page/blue_logo.png')}}" alt="Blue Icon" class="check-icon"><span class="blue-text">Transmission
                                facile</span> de vos relevés et justificatifs</li>
                        </p>
                        <p><img src="{{ asset('storage/uploads/new-landing-page/blue_logo.png')}}" alt="Blue Icon" class="check-icon"><span class="blue-text">Accès
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
                        <p class="modele-desc">Aucune complexité</p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="modele-feature2 feature-invoices">
                        <div class="modele-icon">
                            <img src="{{ asset('storage/uploads/new-landing-page/modele2.svg')}}" alt="Factures et pièces">
                        </div>
                        <p class="modele-desc">Aucun risque économique</p>
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
                    <button class="cta-button">Demandez une démo</button>
                </div>
            </section>

            <!-- personnalisée Section -->
            <section class="personnalisée-container">
                <div class="row align-items-center">
                    <!-- Left Column -->
                    <div class="col-md-4 text-center text-md-start">
                        <h2 class="personnalisée-title"><strong>Une mise en place personnalisée,</strong> fluide et sécurisée</h2>
                        <p class="personnalisée-description">Chaque cabinet possède ses méthodes, son identité et son organisation.
                            <strong>SimplyCompta s’y adapte pleinement.</strong>
                        </p>
                        <div class="btn-container mt-4">
                            <button class="btn btn-primary">Demandez une démo</button>
                        </div>
                    </div>

                    <!-- Middle Column -->
                    <div class="col-md-4">
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
                        <img src="{{ asset('storage/uploads/new-landing-page/control_img.svg')}}" alt="Control Image" />
                    </div>
                </div>
            </section>

            <!-- Form Section -->
            <section class="main-container">
                <h2 class="form-section-title text-center">Prêt à moderniser votre cabinet ?</h2>
                <p class="text-center">Demandez votre devis gratuit ou planifiez une démonstration</p>
                <div class="form-container">
                    <form>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="cabinet-name" class="form-label">Nom du cabinet</label>
                                <input type="text" class="form-control" id="cabinet-name" placeholder="Nom de votre cabinet" required>
                            </div>
                            <div class="col-md-6">
                                <label for="contact-name" class="form-label">Nom du contact</label>
                                <input type="text" class="form-control" id="contact-name" placeholder="Votre nom" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" placeholder="Votre email" required>
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Téléphone</label>
                                <input type="tel" class="form-control" id="phone" placeholder="+212" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Type de demande</label>
                            <div class="checkbox-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="demo" id="demo" name="request-type">
                                    <label class="form-check-label" for="demo">
                                        Démonstration
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="devis" id="devis" name="request-type">
                                    <label class="form-check-label" for="devis">
                                        Devis
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="info" id="info" name="request-type">
                                    <label class="form-check-label" for="info">
                                        Informations complémentaires
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="trial" id="trial" name="request-type">
                                    <label class="form-check-label" for="trial">
                                        Essai de 7 jours
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">Message</label>
                            <textarea class="form-control" id="message" rows="4" placeholder="Votre message" required></textarea>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="data-usage">
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
            <section class="contact_container">
                <div class="contact-section text-center">
                    <h3 class="contact-text">Contactez-nous dès maintenant :</h3>
                    <div class="contact-grid">
                        <!-- Feature 1 -->
                        <div class="contact-feature feature-documents">
                            <div class="contact-icon">
                                <img src="{{ asset('storage/uploads/new-landing-page/whatsapp.svg')}}" alt="Documents essentiels">
                            </div>
                            <p class="contact-desc">+212 (0)655023474</p>
                        </div>

                        <!-- Feature 2 -->
                        <div class="contact-feature2 feature-invoices">
                            <div class="contact-icon">
                                <img src="{{ asset('storage/uploads/new-landing-page/email.svg')}}" alt="Factures et pièces">
                            </div>
                            <p class="contact-desc">contact@simply-compta.com</p>
                        </div>
                    </div>
                    <p class="contact-desc"><strong>Suivez-nous sur les réseaux sociaux</strong></p>
                    <div class="social-icons">
                        <img src="{{ asset('storage/uploads/new-landing-page/facebook.svg')}}" alt="Facebook" class="social">
                        <img src="{{ asset('storage/uploads/new-landing-page/linkedin.svg')}}" alt="Linkedin" class="social">
                        <img src="{{ asset('storage/uploads/new-landing-page/instagram.svg')}}" alt="Instagram" class="social">
                    </div>
                </div>
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

    <!-- Bootstrap JS Link -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>


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
</body>

</html>