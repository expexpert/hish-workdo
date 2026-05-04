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
                    <form action="{{ route('subscription.upgrade') }}" method="POST">
                        @csrf
                        <div id="mobile-plans">
                            <div class="mobile-plans row pt-4">
                                <input type="hidden" name="customer_id" value="{{ $customerId }}">

                                @foreach($mobilePlans as $plan)
                                <div class="col-md-6 mb-4">
                                    <div class="plan-card p-4 border rounded h-100 {{ $plan->slug == 'pro' ? 'border-primary shadow' : '' }}">

                                        {{-- PLAN HEADER --}}
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <h5 class="plan-title mb-0">{{ $plan->name }}</h5>

                                            @if($plan->slug == 'pro')
                                            <span class="badge bg-primary">Recommended</span>
                                            @endif
                                        </div>

                                        {{-- DESCRIPTION --}}
                                        @if(!empty($plan->description))
                                        <p class="text-muted small mb-3">{{ $plan->description }}</p>
                                        @endif

                                        {{-- FEATURES --}}
                                        <ul class="list-unstyled mb-3 small">

                                            <li>📄 Invoices:
                                                <strong>
                                                    {{ is_null($plan->invoice_limit) ? 'Unlimited' : $plan->invoice_limit }}
                                                </strong>
                                            </li>

                                            <li>🧾 Quotes:
                                                <strong>
                                                    {{ is_null($plan->quote_limit) ? 'Unlimited' : $plan->quote_limit }}
                                                </strong>
                                            </li>

                                            <li>💸 Expenses:
                                                <strong>
                                                    {{ is_null($plan->expense_limit) ? 'Unlimited' : $plan->expense_limit }}
                                                </strong>
                                            </li>

                                            <li>🧾 Receipts:
                                                <strong>
                                                    {{ is_null($plan->receipt_limit) ? 'Unlimited' : $plan->receipt_limit }}
                                                </strong>
                                            </li>

                                            <li>🤖 OCR:
                                                <strong>{{ $plan->ocr_limit ?? 'Unlimited' }}</strong>
                                            </li>

                                            <li>💾 Storage:
                                                <strong>{{ $plan->storage_limit_mb }} MB</strong>
                                            </li>

                                            @if($plan->export_enabled)
                                            <li>✅ Export Enabled</li>
                                            @endif

                                            @if($plan->whatsapp_bot_enabled)
                                            <li>📲 WhatsApp Bot</li>
                                            @endif

                                        </ul>

                                        {{-- PRICING --}}
                                        @if($plan->prices && $plan->prices->count())
                                        <div class="plan-prices">

                                            @foreach($plan->prices as $price)
                                            <label
                                                class="plan-option d-flex justify-content-between align-items-center border rounded p-2 mb-2">

                                                <div class="d-flex align-items-center gap-2">

                                                    <input class="form-check-input m-0" type="radio" name="mobile_plan_price_id"
                                                        value="{{ $price->id }}">

                                                    <span class="fw-semibold text-capitalize pl-5">
                                                        {{ $price->billing_cycle }}
                                                    </span>

                                                    @if($price->discount_percentage > 0)
                                                    <span class="badge bg-success">
                                                        {{ $price->discount_percentage }}% OFF
                                                    </span>
                                                    @endif

                                                </div>

                                                <div>
                                                    <strong>{{ number_format($price->price, 2) }} €</strong>
                                                </div>

                                            </label>
                                            @endforeach

                                        </div>
                                        @endif

                                    </div>
                                </div>
                                @endforeach

                            </div>
                            <div class="form-btn">
                                <button type="submit" class="btn btn-primary w-20">Soumettre</button>
                            </div>
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
    </script>
</body>

</html>