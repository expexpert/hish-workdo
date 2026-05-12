<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SimplyCompta - Choisissez votre plan</title>
    <style>
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
        }

        .toast {
            min-width: 250px;
            background: white;
            border-radius: 12px;
            padding: 16px 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            gap: 12px;
            font-family: 'Baloo 2', cursive;
            margin-bottom: 10px;
            border-left: 5px solid #ff4d4d;
            /* Red for errors */
            transform: translateX(120%);
            transition: transform 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }

        .toast.show {
            transform: translateX(0);
        }

        .toast-error {
            border-left-color: #ff4d4d;
            color: #d8000c;
        }

        .toast-success {
            border-left-color: #2ecc71;
            color: #27ae60;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: ui-sans-serif, system-ui, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
            background-color: #f9fafb;
            color: #111827;
            line-height: 1.6;
        }

        .container {
            max-width: 1360px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        /* Header */
        .header {
            background: #fff;
            border-bottom: 1px solid #e5e7eb;
            padding: 1rem 0;
        }

        .header-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: bold;
            color: #111827;
        }

        .special-offer {
            font-size: 0.875rem;
            color: #6b7280;
        }

        .referral-badge {
            background: #d1fae5;
            color: #065f46;
            padding: 0.5rem 1rem;
            border-radius: 9999px;
            border: 1px solid #a7f3d0;
            font-size: 0.875rem;
        }

        .referral-code {
            font-weight: 600;
        }

        /* Main */
        .main-content {
            padding: 3rem 0;
        }

        .page-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .page-title {
            font-size: 2.25rem;
            font-weight: bold;
            color: #111827;
            margin-bottom: 0.75rem;
        }

        .page-subtitle {
            font-size: 1.125rem;
            color: #6b7280;
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
                grid-template-columns: repeat(3, 1fr);
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

        /* Checkout Panel */
        .checkout-column {
            position: relative;
        }

        .checkout-panel {
            background: #fff;
            border-radius: 0.75rem;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, .1);
            border: 1px solid #e5e7eb;
            position: sticky;
            top: 1.5rem;
        }

        .checkout-title {
            font-size: 1.125rem;
            font-weight: bold;
            color: #111827;
            margin-bottom: 1.5rem;
        }

        /* Form */
        .checkout-form {
            margin-bottom: 1.5rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            color: #374151;
            margin-bottom: 0.25rem;
        }

        .form-input {
            width: 100%;
            padding: 0.5rem 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            font-size: 1rem;
            transition: all .3s;
        }

        .form-input:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, .1);
        }

        /* Summary */
        .summary-divider {
            height: 1px;
            background: #e5e7eb;
            margin: 1rem 0;
        }

        .summary-details {
            margin-bottom: 1.5rem;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.75rem;
            font-size: 0.875rem;
        }

        .summary-label {
            color: #6b7280;
        }

        .summary-value {
            font-weight: 600;
            color: #111827;
        }

        .summary-row.discount .summary-label,
        .summary-row.discount .summary-value {
            color: #059669;
        }

        .summary-row.discount {
            display: none;
        }

        .summary-total {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 0.75rem;
            border-top: 1px solid #e5e7eb;
        }

        .total-label {
            font-weight: bold;
            color: #111827;
        }

        .total-value {
            font-size: 1.5rem;
            font-weight: bold;
            color: #111827;
        }

        /* Renewal */
        .renewal-info {
            margin-bottom: 1.5rem;
        }

        .renewal-text,
        .guarantee-text {
            font-size: 0.75rem;
            color: #6b7280;
            margin-bottom: 0.25rem;
        }

        /* CTA */
        .cta-button {
            width: 100%;
            padding: 0.75rem;
            background: #10b981;
            color: #fff;
            border: none;
            border-radius: 0.5rem;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all .3s;
            box-shadow: 0 4px 6px rgba(0, 0, 0, .1);
            margin-bottom: 1.5rem;
        }

        .cta-button:hover {
            background: #059669;
        }

        /* Trust */
        .trust-indicators {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .trust-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.75rem;
            color: #6b7280;
        }

        .trust-icon {
            font-size: 1rem;
        }

        /* Alerts */
        .alert {
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .alert-danger {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        .alert-danger ul {
            padding-left: 1.25rem;
            margin: 0;
        }

        .form-submit.plan-btn {
            width: auto;
            margin-left: auto;
            display: block;
            padding: 20px;
            background: #2563eb;
        }

        /* Free plan only visible on monthly */
        @media (max-width: 767px) {
            .page-title {
                font-size: 1.875rem;
            }

            .billing-btn {
                padding: 0.5rem 1rem;
                font-size: 0.875rem;
            }

            .save-text {
                display: none;
            }

            .best-value-badge {
                font-size: 0.625rem;
                padding: 0.125rem 0.375rem;
            }

            .plans-column {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <div class="toast-container" id="toastContainer">
        @if (session('error'))
        <div class="toast toast-error" id="referralToast">
            <span>⚠️ {{ session('error') }}</span>
        </div>
        @endif

        @if (isset($referralDiscount))
        <div class="toast toast-success" id="referralToast">
            <span>🎉 Promo activée : -{{ $referralDiscount }}% !</span>
        </div>
        @endif
    </div>

    {{-- Header --}}
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="header-left">
                    <h1 class="logo">SimplyCompta</h1>
                    @if(!empty($referralCode))
                    <span class="special-offer">Offre spéciale appliquée</span>
                    @endif
                </div>
                @if(!empty($referralCode))
                <div class="referral-badge">
                    <span class="referral-code">{{ strtoupper($referralCode) }}</span>
                    @if(!empty($referralDiscount)) · {{ $referralDiscount }}% OFF @endif
                </div>
                @endif
            </div>
        </div>
    </header>

    <main class="main-content">
        <div class="container">

            {{-- Alerts --}}
            @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
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

            {{-- Page Title --}}
            <div class="page-header">
                <h2 class="page-title">Choisissez votre plan</h2>
                <p class="page-subtitle">Commencez aujourd'hui avec une garantie de remboursement de 7 jours.</p>
            </div>

            {{-- Billing Cycle Selector --}}
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

            {{-- Plans Grid --}}
            <div class="plans-grid">
                <div class="plans-column" id="plans-column">

                    @foreach($mobilePlans->where('slug', '!=', 'free') as $plan)
                    @php
                    $isRecommended = $plan->slug === 'pro';
                    $isFree = $plan->slug === 'free';
                    // Index prices by billing_cycle for easy JS access
                    $pricesBycycle = $plan->prices->keyBy('billing_cycle');
                    $monthlyPrice = $pricesBycycle->get('monthly');
                    @endphp

                    <div
                        class="plan-card {{ $isRecommended ? 'recommended' : '' }} {{ $isRecommended ? 'selected' : '' }} {{ $isFree ? 'free-only' : '' }}"
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

                {{-- Checkout Summary Panel --}}

                <form action="{{ route('subscription.upgrade') }}" method="POST" id="checkout-form">
                    @csrf
                    <input type="hidden" name="customer_id" id="customer_id" value="{{ $customerId }}">
                    <input type="hidden" name="mobile_plan_price_id" id="selected-price-id" value="">
                    <input type="hidden" name="plan_slug" id="selected-plan-slug" value="pro">
                    <input type="hidden" name="billing_cycle" id="selected-billing" value="monthly">
                    <input type="hidden" name="referral_discount_amount" id="referral-discount" value="0">
                    <input type="hidden" name="price_after_discount" id="price-after-discount" value="0">

                    <button class="form-submit plan-btn" type="submit">soumettre</button>
                </form>


                <a href="myapp://subscription-success">temp button</a>

                <button onclick="window.location.href='myapp://subscription-success'">Test Deep Link</button>
            </div>
        </div>
    </main>

    <script>
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
        let currentPlan = null;
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

        }

        function resetSummary() {
            document.getElementById('selected-price-id').value = '';
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
            selectPlan('pro', 'Pro');
        });
    </script>
</body>

</html>