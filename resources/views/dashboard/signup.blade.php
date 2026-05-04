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
    <link rel="stylesheet" href="{{ asset('/css/landingPage.css') }}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        .plan-card {
            transition: all 0.2s ease;
            background: #fff;
        }

        .plan-card:hover {
            transform: translateY(-3px);
        }

        .plan-card.border-primary {
            border-width: 2px !important;
        }

        .cursor-pointer {
            cursor: pointer;
        }

        .plan-option {
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .plan-option:hover {
            background: #f8f9fa;
        }

        .plan-option input[type="radio"] {
            transform: scale(1.2);
            margin-top: 0;
        }

        .is-invalid {
            border: 1px solid red !important;
        }
    </style>

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
        <main class="Simply pt-5">

            <section class="main-container pt-5">
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
                    <form action="{{ route('mobile.customer.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 top-row-form">
                                <label for="name" class="form-label">Nom du cabinet <span class="requires">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Nom de votre cabinet" required>
                            </div>
                            <div class="col-md-6 sec-row-form">
                                <label for="email" class="form-label">Email <span class="requires">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="Votre email" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 top-row-form">
                                <label for="phone" class="form-label">Téléphone <span class="requires">*</span></label>
                                <input type="tel" class="form-control" id="phone" name="phone" placeholder="+212" required>
                            </div>
                            <div class="col-md-6 sec-row-form">
                                <label for="password" class="form-label">Mot de passe <span class="requires">*</span></label>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Votre mot de passe" required>
                                <input type="hidden" class="form-control" id="referral_code" name="referral_code" placeholder="referral_code" required>

                            </div>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="data-usage" name="data-usage" required>
                            <label class="form-check-label" for="data-usage">
                                J'accepte que mes données soient utilisées pour traiter ma demande.
                            </label>
                        </div>
                        <div class="form-btn">
                            <button type="submit" class="btn btn-primary w-20" id="next-btn">Submit</button>
                        </div>
                    </form>
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


    <script>
        window.addEventListener("scroll", function() {
            const header = document.querySelector("header");

            if (window.scrollY > 0) {
                header.classList.add("sticky");
            } else {
                header.classList.remove("sticky");
            }
        });

        $(document).ready(function() {
            const params = new URLSearchParams(window.location.search);
            const ref = params.get('ref');

            if (ref) {
                $('#referral_code').val(ref);
            }
        });
    </script>
</body>

</html>