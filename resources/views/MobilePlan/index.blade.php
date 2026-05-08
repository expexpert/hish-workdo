    @extends('layouts.admin')
    @section('page-title')
    {{ __('Manage Mobile Plan') }}
    @endsection
    @section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Mobile Plan') }}</li>
    @endsection
    @section('content')
    <div class="row">

        @foreach($mobilePlans as $plan)
        <div class="col-lg-4 col-md-6 d-flex mb-4">
            <div class="card w-100 price-card shadow-sm border-0 {{ $plan->slug == 'pro' ? 'border border-primary' : '' }}">

                <div class="card-body position-relative">

                    {{-- ACTION BUTTONS (TOP RIGHT) --}}
                    <div class="position-absolute top-0 end-0 p-2 d-flex gap-1">

                        {{-- EDIT --}}
                        <a href="#"
                            class="btn btn-sm btn-info"
                            data-url="{{ route('mobile.plans.edit', $plan->id) }}"
                            data-ajax-popup="true"
                            data-title="{{ __('Edit Plan') }}"
                            data-bs-toggle="tooltip"
                            title="{{ __('Edit') }}">
                            <i class="ti ti-pencil"></i>
                        </a>

                        {{-- DELETE --}}
                        <!-- {!! Form::open([
                        'method' => 'DELETE',
                        'route' => ['mobile.plans.destroy', $plan->id],
                        'id' => 'delete-form-' . $plan->id
                        ]) !!}

                        <a href="#"
                            class="btn btn-sm btn-danger bs-pass-para"
                            data-bs-toggle="tooltip"
                            title="{{ __('Delete') }}">
                            <i class="ti ti-trash"></i>
                        </a>

                        {!! Form::close() !!} -->
                    </div>

                    {{-- PLAN NAME --}}
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="price-badge bg-primary">
                            {{ $plan->name }}
                        </span>

                        @if($plan->slug == 'pro')
                        <span class="badge bg-success">Recommended</span>
                        @endif
                    </div>

                    {{-- DESCRIPTION --}}
                    @if(!empty($plan->description))
                    <p class="text-muted small mt-2 mb-3">
                        {{ $plan->description }}
                    </p>
                    @endif

                    {{-- PRICE --}}
                    @php $mainPrice = $plan->prices->first(); @endphp

                    @if($mainPrice)
                    <h2 class="my-3 fw-bold">
                        {{ number_format($mainPrice->price, 2) }} €
                        <small class="text-muted fs-6">
                            / {{ ucfirst($mainPrice->billing_cycle) }}
                        </small>
                    </h2>
                    @endif

                    {{-- FEATURES --}}
                    <ul class="list-unstyled my-4">
                        <li>📄 Invoices :
                            <strong>{{ is_null($plan->invoice_limit) ? 'Unlimited' : $plan->invoice_limit }}</strong>
                        </li>

                        <li>🧾 Quotes :
                            <strong>{{ is_null($plan->quote_limit) ? 'Unlimited' : $plan->quote_limit }}</strong>
                        </li>

                        <li>💸 Expenses :
                            <strong>{{ is_null($plan->expense_limit) ? 'Unlimited' : $plan->expense_limit }}</strong>
                        </li>

                        <li>🧾 Receipts :
                            <strong>{{ is_null($plan->receipt_limit) ? 'Unlimited' : $plan->receipt_limit }}</strong>
                        </li>

                        <li>🤖 OCR :
                            <strong>{{ $plan->ocr_limit ?? 'Unlimited' }}</strong>
                        </li>

                        <li>💾 Storage :
                            <strong>{{ $plan->storage_limit_mb }} MB</strong>
                        </li>

                        <li>👥 Customer :
                            <strong>{{ $plan->client_limit ?? 'Unlimited' }}</strong>
                        </li>

                        <li>🤝 Supplier :
                            <strong>{{ $plan->supplier_limit ?? 'Unlimited' }}</strong>
                        </li>

                        @if($plan->export_enabled)
                        <li>✅ Export Enabled</li>
                        @endif

                        @if($plan->whatsapp_bot_enabled)
                        <li>📲 WhatsApp Bot</li>
                        @endif

                        @if($plan->logo)
                        <li>🎨 Custom Logo</li>
                        @endif
                    </ul>

                </div>
            </div>
        </div>
        @endforeach

    </div>
    @endsection