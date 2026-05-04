{{ Form::open([
    'url' => isset($plan) ? route('mobile.plans.update', $plan->id) : route('mobile.plans.store'),
    'method' => isset($plan) ? 'PUT' : 'POST',
    'class' => 'needs-validation',
    'novalidate'
]) }}

@if(isset($plan))
@method('PUT')
@endif

<div class="modal-body">
    <div class="row">

        {{-- PLAN NAME --}}
        <div class="form-group col-md-6">
            <label class="form-label">Plan Name</label>
            <input type="text" name="name" value="{{ $plan->name ?? '' }}" class="form-control" required>
        </div>

        {{-- SLUG --}}
        <div class="form-group col-md-6">
            <label class="form-label">Slug</label>
            <input type="text" name="slug" value="{{ $plan->slug ?? '' }}" class="form-control">
        </div>

        <hr>

        {{-- LIMITS --}}
        <h6 class="mb-3">Limits</h6>

        <div class="col-md-4">
            <input type="number" name="invoice_limit" value="{{ $plan->invoice_limit ?? '' }}" class="form-control" placeholder="Invoices">
        </div>

        <div class="col-md-4">
            <input type="number" name="quote_limit" value="{{ $plan->quote_limit ?? '' }}" class="form-control" placeholder="Quotes">
        </div>

        <div class="col-md-4">
            <input type="number" name="expense_limit" value="{{ $plan->expense_limit ?? '' }}" class="form-control" placeholder="Expenses">
        </div>

        <div class="col-md-4 mt-2">
            <input type="number" name="receipt_limit" value="{{ $plan->receipt_limit ?? '' }}" class="form-control" placeholder="Receipts">
        </div>

        <div class="col-md-4 mt-2">
            <input type="number" name="ocr_limit" value="{{ $plan->ocr_limit ?? '' }}" class="form-control" placeholder="OCR Limit">
        </div>

        <div class="col-md-4 mt-2">
            <input type="number" name="storage_limit_mb" value="{{ $plan->storage_limit_mb ?? '' }}" class="form-control" placeholder="Storage (MB)" required>
        </div>

        <hr>

        {{-- FEATURES --}}
        <h6 class="mb-3">Features</h6>

        <div class="col-md-6">
            <div class="form-check form-switch">
                <input type="checkbox" name="export_enabled" class="form-check-input"
                    {{ isset($plan) && $plan->export_enabled ? 'checked' : '' }}>
                <label class="form-check-label">Export Enabled</label>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-check form-switch">
                <input type="checkbox" name="whatsapp_bot_enabled" class="form-check-input"
                    {{ isset($plan) && $plan->whatsapp_bot_enabled ? 'checked' : '' }}>
                <label class="form-check-label">WhatsApp Bot</label>
            </div>
        </div>

        <hr>

        {{-- PRICING --}}
        <h6 class="mb-3">Pricing (Max 3)</h6>

        <div id="pricing-wrapper">

            @php
            $existingPrices = isset($plan) ? $plan->prices->keyBy('billing_cycle') : collect();
            $cycles = ['monthly', 'quarterly', 'yearly'];
            @endphp

            @foreach($cycles as $index => $cycle)
            @php $price = $existingPrices[$cycle] ?? null; @endphp

            <div class="row pricing-row mb-2">

                {{-- hidden ID for edit --}}
                <input type="hidden" name="prices[{{ $index }}][id]" value="{{ $price->id ?? '' }}">

                <div class="col-md-4">
                    <select name="prices[{{ $index }}][billing_cycle]" class="form-control" readonly>
                        <option value="{{ $cycle }}" selected>
                            {{ ucfirst($cycle) }}
                        </option>
                    </select>
                </div>

                <div class="col-md-4">
                    <input type="number"
                        step="0.01"
                        name="prices[{{ $index }}][price]"
                        value="{{ $price->price ?? '' }}"
                        class="form-control"
                        placeholder="Price" required>
                </div>

                <div class="col-md-4">
                    <input type="number"
                        name="prices[{{ $index }}][discount_percentage]"
                        value="{{ $price->discount_percentage ?? '' }}"
                        class="form-control"
                        placeholder="% OFF">
                </div>

            </div>
            @endforeach

        </div>

    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
    <button type="submit" class="btn btn-primary">
        {{ isset($plan) ? 'Update' : 'Create' }}
    </button>
</div>

{{ Form::close() }}

{{-- ✅ jQuery FIX --}}
<script>
    $(document).ready(function() {

        let maxPrices = 3;
        let priceRowCounter = $('#pricing-wrapper .pricing-row').length;

        // ✅ Add Price
        $(document).on('click', '#add-price', function() {

            let currentRows = $('#pricing-wrapper .pricing-row').length;

            if (currentRows >= maxPrices) {
                alert('You can only add 3 pricing options (Monthly, Quarterly, Yearly)');
                return;
            }

            let i = priceRowCounter++;

            let html = `
        <div class="row pricing-row mb-2">
            <div class="col-md-4">
                <select name="prices[${i}][billing_cycle]" class="form-control billing-cycle" required>
                    <option value="">Select</option>
                    <option value="monthly">Monthly</option>
                    <option value="quarterly">Quarterly</option>
                    <option value="yearly">Yearly</option>
                </select>
            </div>

            <div class="col-md-4">
                <input type="number" step="0.01" name="prices[${i}][price]" class="form-control" placeholder="Price" required>
            </div>

            <div class="col-md-3">
                <input type="number" name="prices[${i}][discount_percentage]" class="form-control" placeholder="% OFF">
            </div>

            <div class="col-md-1">
                <button type="button" class="btn btn-danger remove-row">×</button>
            </div>
        </div>`;

            $('#pricing-wrapper').append(html);

            toggleAddButton();
        });

        // ✅ Remove Row
        $(document).on('click', '.remove-row', function() {
            $(this).closest('.pricing-row').remove();

            reindexRows();
            toggleAddButton();
        });

        // ✅ Prevent duplicate billing cycle
        $(document).on('change', '.billing-cycle', function() {

            let selectedValues = [];

            $('.billing-cycle').each(function() {
                let val = $(this).val();
                if (val) {
                    if (selectedValues.includes(val)) {
                        alert('This billing cycle is already selected.');
                        $(this).val('');
                    } else {
                        selectedValues.push(val);
                    }
                }
            });
        });

        // ✅ Reindex after remove
        function reindexRows() {
            $('#pricing-wrapper .pricing-row').each(function(index) {
                $(this).find('input, select').each(function() {
                    let name = $(this).attr('name');
                    if (name) {
                        $(this).attr('name', name.replace(/prices\[\d+\]/, 'prices[' + index + ']'));
                    }
                });
            });

            priceRowCounter = $('#pricing-wrapper .pricing-row').length;
        }

        // ✅ Disable button when 3 reached
        function toggleAddButton() {
            if ($('#pricing-wrapper .pricing-row').length >= maxPrices) {
                $('#add-price').prop('disabled', true);
            } else {
                $('#add-price').prop('disabled', false);
            }
        }

        // Init on load
        toggleAddButton();

    });
</script>