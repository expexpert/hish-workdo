{{ Form::open([
    'url' => isset($referralCode) ? route('referral.codes.update', $referralCode->id) : route('referral.codes.store'),
    'method' => isset($referralCode) ? 'PUT' : 'POST',
    'class' => 'needs-validation',
    'novalidate'
]) }}

@if(isset($referralCode))
@method('PUT')
@endif

<div class="modal-body">
    <div class="row">

        {{-- BASIC INFO --}}
        <h6 class="mb-3">Basic Information</h6>

        <div class="form-group col-md-6">
            <label class="form-label">Code</label>
            <input type="text" name="code" value="{{ $referralCode->code ?? '' }}" class="form-control" required>
        </div>

        <div class="form-group col-md-6">
            <label class="form-label">Type</label>
            <select name="type" class="form-control" required>
                <option value="influencer" {{ (isset($referralCode) && $referralCode->type == 'influencer') ? 'selected' : '' }}>Influencer</option>
                <option value="partner" {{ (isset($referralCode) && $referralCode->type == 'partner') ? 'selected' : '' }}>Partner</option>
                <option value="user" {{ (isset($referralCode) && $referralCode->type == 'user') ? 'selected' : '' }}>User</option>
            </select>
        </div>

        <hr>

        {{-- OWNER DETAILS --}}
        <h6 class="mb-3">Owner Details</h6>

        <div class="col-md-6">
            <input type="text" name="owner_name" value="{{ $referralCode->owner_name ?? '' }}" class="form-control" placeholder="Owner Name">
        </div>

        <div class="col-md-6">
            <input type="email" name="owner_email" value="{{ $referralCode->owner_email ?? '' }}" class="form-control" placeholder="Owner Email">
        </div>

        <hr>

        {{-- DISCOUNT & COMMISSION --}}
        <h6 class="mb-3">Discount & Commission</h6>

        <div class="col-md-6">
            <input type="number"
                step="0.01"
                name="discount_percentage"
                value="{{ $referralCode->discount_percentage ?? '' }}"
                class="form-control"
                placeholder="Discount (%)">
        </div>

        <div class="col-md-6">
            <input type="number"
                step="0.01"
                name="commission_percentage"
                value="{{ $referralCode->commission_percentage ?? '' }}"
                class="form-control"
                placeholder="Commission (%)">
        </div>

        <hr>

        {{-- ANALYTICS --}}
        @if(isset($referralCode))
        <h6 class="mb-3">Analytics</h6>

        <div class="col-md-6">
            <input type="number" value="{{ $referralCode->clicks }}" class="form-control" readonly placeholder="Clicks">
        </div>

        <div class="col-md-6">
            <input type="number" value="{{ $referralCode->used_count }}" class="form-control" readonly placeholder="Used Count">
        </div>

        <hr>
        @endif

        {{-- STATUS --}}
        <h6 class="mb-3">Status</h6>

        <input type="hidden" name="is_active" value="0">

        <div class="form-check form-switch">
            <input type="checkbox"
                name="is_active"
                value="1"
                class="form-check-input"
                {{ isset($referralCode) ? ($referralCode->is_active ? 'checked' : '') : 'checked' }}>
            <label class="form-check-label">Active</label>
        </div>

    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
    <button type="submit" class="btn btn-primary">
        {{ isset($referralCode) ? 'Update' : 'Create' }}
    </button>
</div>

{{ Form::close() }}