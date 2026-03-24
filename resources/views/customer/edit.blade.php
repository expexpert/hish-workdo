{{Form::model($customer,array('route' => array('customer.update', $customer->id), 'method' => 'PUT', 'class'=>'needs-validation','novalidate')) }}
<div class="modal-body">

    <h5 class="sub-title">{{__('Basic Info')}}</h5>
    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-6">
            <div class="form-group">
                {{Form::label('name',__('Name'),array('class'=>'form-label')) }}<x-required></x-required>
                <div class="form-icon-user">
                    {{Form::text('name',null,array('class'=>'form-control','required'=>'required', 'placeholder'=>__('Enter Name') ))}}
                </div>
            </div>
        </div>

        <x-mobile div-class="col-md-6" name="contact" label="{{ __('Contact') }}" placeholder="{{ __('Enter Contact') }}" required></x-mobile>

        <div class="col-lg-4 col-md-4 col-sm-6">
            <div class="form-group">
                {{Form::label('email',__('Email'),['class'=>'form-label'])}}<x-required></x-required>
                <div class="form-icon-user">
                    {{Form::text('email',null,array('class'=>'form-control','required'=>'required', 'placeholder'=>__('Enter Email') ))}}
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-6">
            <div class="form-group">
                {{Form::label('tax_number',__('Tax Number'),['class'=>'form-label'])}}
                <div class="form-icon-user">
                    {{Form::text('tax_number',null,array('class'=>'form-control', 'placeholder'=>__('Enter Tax Number') ))}}
                </div>
            </div>
        </div>
        @if(\Auth::user()->type === 'accountant')
        {{ Form::hidden('accountant', \Auth::user()->id) }}
        @else
        <div class="col-lg-4 col-md-4 col-sm-6">
            <div class="form-group">
                {{ Form::label('accountant', __('Accountant'), ['class' => 'form-label']) }}<x-required></x-required>
                <div class="form-icon-user">
                    {{ Form::select('accountant', $accountant, $customer->created_by, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Select Accountant')]) }}
                </div>
            </div>
        </div>
        @endif
        @if(!$customFields->isEmpty())
        <div class="col-lg-4 col-md-4 col-sm-6">
            <div class="tab-pane fade show" id="tab-2" role="tabpanel">
                @include('customFields.formBuilder')
            </div>
        </div>
        @endif
    </div>

    <h5 class="sub-title pt-2">{{__('Other Information')}}</h5>
    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-6">
            <div class="form-group">
                {{Form::label('billing_name',__('Company Name'),array('class'=>'','class'=>'form-label')) }}
                <div class="form-icon-user">
                    {{Form::text('billing_name',null,array('class'=>'form-control', 'placeholder'=>__('Enter Company Name') ))}}
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-md-6 col-sm-6">
            <div class="form-group">
                {{Form::label('bio',__('Biography'),array('class'=>'','class'=>'form-label')) }}
                <div class="form-icon-user">
                    {{Form::text('bio',null,array('class'=>'form-control', 'placeholder'=>__('Enter Biography') ))}}
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-md-6 col-sm-6">
            <div class="form-group">
                {{Form::label('company_type',__('Company Type'),array('class'=>'form-label')) }}
                <div class="input-group">
                    {{ Form::select('company_type',
                        [
                            'Auto-entrepreneur' => 'Auto-entrepreneur',
                            'Entreprise individuelle' => 'Entreprise individuelle',
                            'Société' => 'Société',
                            'Association' => 'Association',
                        ], $customer->company_type, array('class' => 'form-control', 'placeholder' => __('Select Company Type')))
                    }}
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-md-6 col-sm-6">
            <div class="form-group">
                {{Form::label('address',__('Address'),array('class'=>'form-label')) }}
                <div class="input-group">
                    {{Form::text('address',null,array('class'=>'form-control', 'placeholder'=>__('Enter Address') ))}}
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-md-6 col-sm-6">
            <div class="form-group">
                {{Form::label('billing_city',__('City'),array('class'=>'form-label')) }}
                <div class="input-group">
                    {{Form::text('billing_city',null,array('class'=>'form-control', 'placeholder'=>__('Enter City') ))}}
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-md-6 col-sm-6">
            <div class="form-group">
                {{Form::label('billing_zip',__('Postal Code'),array('class'=>'form-label')) }}
                <div class="input-group">
                    {{Form::text('billing_zip',null,array('class'=>'form-control', 'placeholder'=>__('Enter Postal Code') ))}}
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-md-6 col-sm-6">
            <div class="form-group">
                {{Form::label('website',__('Website'),array('class'=>'form-label')) }}
                <div class="input-group">
                    {{Form::text('website',null,array('class'=>'form-control', 'placeholder'=>__('Enter Website') ))}}
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-md-6 col-sm-6">
            <div class="form-group">
                {{Form::label('vat_number',__('VAT Number'),array('class'=>'form-label')) }}
                <div class="input-group">
                    {{Form::text('vat_number',null,array('class'=>'form-control', 'placeholder'=>__('Enter VAT Number') ))}}
                </div>
            </div>
        </div>


        <div class="col-lg-6 col-md-6 col-sm-6">
            <div class="form-group">
                {{Form::label('ice_number',__('ICE Number'),array('class'=>'form-label')) }}
                <div class="input-group">
                    {{Form::text('ice_number',null,array('class'=>'form-control', 'placeholder'=>__('Enter ICE Number') ))}}
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-md-6 col-sm-6">
            <div class="form-group">
                {{Form::label('rc_number',__('RC Number'),array('class'=>'form-label')) }}
                <div class="input-group">
                    {{Form::text('rc_number',null,array('class'=>'form-control', 'placeholder'=>__('Enter RC Number') ))}}
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-md-6 col-sm-6">
            <div class="form-group">
                {{Form::label('patent_number',__('Patent Number'),array('class'=>'form-label')) }}
                <div class="input-group">
                    {{Form::text('patent_number',null,array('class'=>'form-control', 'placeholder'=>__('Enter Patent Number') ))}}
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-md-6 col-sm-6">
            <div class="form-group">
                {{Form::label('if_number',__('IF Number'),array('class'=>'form-label')) }}
                <div class="input-group">
                    {{Form::text('if_number',null,array('class'=>'form-control', 'placeholder'=>__('Enter IF Number') ))}}
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-md-6 col-sm-6">
            <div class="form-group">
                {{Form::label('cnss',__('CNSS Number'),array('class'=>'form-label')) }}
                <div class="input-group">
                    {{Form::text('cnss',null,array('class'=>'form-control', 'placeholder'=>__('Enter CNSS Number') ))}}
                </div>
            </div>
        </div>


    </div>

    <div class="d-none">
        <h5 class="sub-title">{{__('Billing Address')}}</h5>
        <div class="row">

            <x-mobile div-class="col-md-6" name="billing_phone" label="{{ __('Phone') }}" placeholder="{{ __('Enter Phone') }}"></x-mobile>

            <div class="col-md-12">
                <div class="form-group">
                    {{Form::label('billing_address',__('Address'),array('class'=>'form-label')) }}
                    <div class="input-group">
                        {{Form::textarea('billing_address',null,array('class'=>'form-control','rows'=>3, 'placeholder'=>__('Enter Address') ))}}
                    </div>
                </div>
            </div>

            <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="form-group">
                    {{Form::label('billing_state',__('State'),array('class'=>'form-label')) }}
                    <div class="form-icon-user">
                        {{Form::text('billing_state',null,array('class'=>'form-control', 'placeholder'=>__('Enter State') ))}}
                    </div>
                </div>
            </div>

            <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="form-group">
                    {{Form::label('billing_country',__('Country'),array('class'=>'form-label')) }}
                    <div class="form-icon-user">
                        {{Form::text('billing_country',null,array('class'=>'form-control', 'placeholder'=>__('Enter Country') ))}}
                    </div>
                </div>
            </div>

        </div>

        @if(App\Models\Utility::getValByName('shipping_display')=='on')
        <div class="col-md-12 text-end">
            <input type="button" id="billing_data" value="{{__('Shipping Same As Billing')}}" class="btn btn-primary">
        </div>
        <h4 class="sub-title">{{__('Shipping Address')}}</h4>
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="form-group">
                    {{Form::label('shipping_name',__('Name'),array('class'=>'form-label')) }}
                    <div class="form-icon-user">
                        {{Form::text('shipping_name',null,array('class'=>'form-control', 'placeholder'=>__('Enter Name') ))}}
                    </div>
                </div>
            </div>

            <x-mobile div-class="col-md-6" name="shipping_phone" label="{{ __('Phone') }}" placeholder="{{ __('Enter Phone') }}"></x-mobile>

            <div class="col-md-12">
                <div class="form-group">
                    {{Form::label('shipping_address',__('Address'),array('class'=>'form-label')) }}
                    <label class="form-label" for="example2cols1Input"></label>
                    <div class="input-group">
                        {{Form::textarea('shipping_address',null,array('class'=>'form-control','rows'=>3, 'placeholder'=>__('Enter Address') ))}}
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="form-group">
                    {{Form::label('shipping_city',__('City'),array('class'=>'form-label')) }}
                    <div class="form-icon-user">
                        {{Form::text('shipping_city',null,array('class'=>'form-control', 'placeholder'=>__('Enter City') ))}}
                    </div>
                </div>
            </div>

            <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="form-group">
                    {{Form::label('shipping_state',__('State'),array('class'=>'form-label')) }}
                    <div class="form-icon-user">
                        {{Form::text('shipping_state',null,array('class'=>'form-control', 'placeholder'=>__('Enter State') ))}}
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="form-group">
                    {{Form::label('shipping_country',__('Country'),array('class'=>'form-label')) }}
                    <div class="form-icon-user">
                        {{Form::text('shipping_country',null,array('class'=>'form-control', 'placeholder'=>__('Enter Country') ))}}
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="form-group">
                    {{Form::label('shipping_zip',__('Zip Code'),array('class'=>'form-label')) }}
                    <div class="form-icon-user">
                        {{Form::text('shipping_zip',null,array('class'=>'form-control', 'placeholder'=>__('Enter Zip Code') ))}}
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

</div>
<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Update')}}" class="btn btn-primary">
</div>
{{Form::close()}}