@extends('layouts.admin')
@section('page-title')
{{ __('Settings') }}
@endsection

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
<li class="breadcrumb-item">{{ __('Settings') }}</li>
@endsection

@php
use App\Models\Utility;
$logo = Utility::get_file('uploads/logo/');
$logo_light = Utility::getValByName('company_logo_light');
$logo_dark = Utility::getValByName('company_logo_dark');
$company_favicon = Utility::getValByName('company_favicon');
$EmailTemplates = App\Models\EmailTemplate::all();
$setting = App\Models\Utility::settings();
$lang = Utility::getValByName('default_language');
$color = !empty($setting['color']) ? $setting['color'] : 'theme-3';
$flag = !empty($setting['color_flag']) ? $setting['color_flag'] : 'false';
@endphp

<style>
    .dash-footer {
        margin-left: 0 !important;
    }
</style>

@push('script-page')
<script type="text/javascript">
    $(".email-template-checkbox").click(function() {

        var chbox = $(this);
        $.ajax({
            url: chbox.attr('data-url'),
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                status: chbox.val()
            },
            type: 'post',
            success: function(response) {
                if (response.is_success) {
                    -
                    // show_toastr('success', '{{ __('Link Copy on Clipboard') }}');
                    show_toastr('success', response.success, 'success');
                    if (chbox.val() == 1) {
                        $('#' + chbox.attr('id')).val(0);
                    } else {
                        $('#' + chbox.attr('id')).val(1);
                    }
                } else {
                    show_toastr('error', response.error, 'error');
                }
            },
            error: function(response) {
                response = response.responseJSON;
                if (response.is_success) {
                    show_toastr('error', response.error, 'error');
                } else {
                    show_toastr('error', response, 'error');
                }
            }
        })
    });
</script>
<script>
    var scrollSpy = new bootstrap.ScrollSpy(document.body, {
        target: '#useradd-sidenav',
        offset: 300
    })

    var themescolors = document.querySelectorAll(".themes-color > a");
    for (var h = 0; h < themescolors.length; h++) {
        var c = themescolors[h];
        c.addEventListener("click", function(event) {
            var targetElement = event.target;
            if (targetElement.tagName == "SPAN") {
                targetElement = targetElement.parentNode;
            }
            var temp = targetElement.getAttribute("data-value");
            removeClassByPrefix(document.querySelector("body"), "theme-");
            document.querySelector("body").classList.add(temp);
        });
    }

    function check_theme(color_val) {
        $('input[value="' + color_val + '"]').prop('checked', true);
        $('a[data-value]').removeClass('active_color');
        $('a[data-value="' + color_val + '"]').addClass('active_color');
    }

    if ($('#cust-darklayout').length > 0) {
        var custthemedark = document.querySelector("#cust-darklayout");
        custthemedark.addEventListener("click", function() {
            if (custthemedark.checked) {
                document.querySelector("#style").setAttribute("href",
                    "{{ asset('assets/css/style-dark.css') }}");

                $('.dash-sidebar .main-logo a img').attr('src', '{{ $logo . $logo_light }}');

            } else {
                document.querySelector("#style").setAttribute("href",
                    "{{ asset('assets/css/style.css') }}");
                $('.dash-sidebar .main-logo a img').attr('src', '{{ $logo . $logo_dark }}');

            }
        });
    }

    if ($('#cust-theme-bg').length > 0) {
        var custthemebg = document.querySelector("#cust-theme-bg");
        custthemebg.addEventListener("click", function() {
            if (custthemebg.checked) {
                document.querySelector(".dash-sidebar").classList.add("transprent-bg");
                document
                    .querySelector(".dash-header:not(.dash-mob-header)")
                    .classList.add("transprent-bg");
            } else {
                document.querySelector(".dash-sidebar").classList.remove("transprent-bg");
                document
                    .querySelector(".dash-header:not(.dash-mob-header)")
                    .classList.remove("transprent-bg");
            }
        });
    }
</script>

<script>
    $(document).on("change", "select[name='invoice_template'], input[name='invoice_color']", function() {
        var template = $("select[name='invoice_template']").val();
        var color = $("input[name='invoice_color']:checked").val();
        $('#invoice_frame').attr('src', '{{ url('/invoices/preview') }}/' + template + '/' + color);
    });

    $(document).on("change", "select[name='proposal_template'], input[name='proposal_color']", function() {
        var template = $("select[name='proposal_template']").val();
        var color = $("input[name='proposal_color']:checked").val();
        $('#proposal_frame').attr('src', '{{ url('/proposal/preview') }}/' + template + '/' + color);
    });

    $(document).on("change", "select[name='bill_template'], input[name='bill_color']", function() {
        var template = $("select[name='bill_template']").val();
        var color = $("input[name='bill_color']:checked").val();
        $('#bill_frame').attr('src', '{{ url('/bill/preview') }}/' + template + '/' + color);
    });

    $(document).on("change", "select[name='retainer_template'], input[name='retainer_color']", function() {
        var template = $("select[name='retainer_template']").val();
        var color = $("input[name='retainer_color']:checked").val();
        $('#retainer_frame').attr('src', '{{ url('/retainer/preview') }}/' + template + '/' + color);
    });
</script>

<script>
    $(".list-group-item").click(function() {
        $('.list-group-item').filter(function() {
            return this.href == id;
        }).parent().removeClass('text-primary');
    });

    function check_theme(color_val) {
        $('#theme_color').prop('checked', false);
        $('input[value="' + color_val + '"]').prop('checked', true);
    }

    $(document).on('change', '[name=storage_setting]', function() {
        if ($(this).val() == 's3') {
            $('.s3-setting').removeClass('d-none');
            $('.wasabi-setting').addClass('d-none');
            $('.local-setting').addClass('d-none');
        } else if ($(this).val() == 'wasabi') {
            $('.s3-setting').addClass('d-none');
            $('.wasabi-setting').removeClass('d-none');
            $('.local-setting').addClass('d-none');
        } else {
            $('.s3-setting').addClass('d-none');
            $('.wasabi-setting').addClass('d-none');
            $('.local-setting').removeClass('d-none');
        }
    });
</script>

<script type="text/javascript">
    $(document).ready(function() {
        var checkBox = document.getElementById('tax_number');
        // Check if the element is selected/checked
        if (checkBox.checked) {
            $('#tax_checkbox_id').removeClass('d-none');
        } else {
            $('#tax_checkbox_id').addClass('d-none');
        }
        $(document).on('change', '#tax_number', function() {

            if ($(this).is(':checked') == true) {
                $('#tax_checkbox_id').removeClass('d-none');
            } else {
                $('#tax_checkbox_id').addClass('d-none');
            }
        });
    });
</script>
<script>
    $(document).on("click", '.send_email', function(e) {

        e.preventDefault();
        var title = $(this).attr('data-title');

        var size = 'md';
        var url = $(this).attr('data-url');
        if (typeof url != 'undefined') {
            $("#commonModal .modal-title").html(title);
            $("#commonModal .modal-dialog").addClass('modal-' + size);
            $("#commonModal").modal('show');

            $.post(url, {
                _token: '{{ csrf_token() }}',
                mail_driver: $("#mail_driver").val(),
                mail_host: $("#mail_host").val(),
                mail_port: $("#mail_port").val(),
                mail_username: $("#mail_username").val(),
                mail_password: $("#mail_password").val(),
                mail_encryption: $("#mail_encryption").val(),
                mail_from_address: $("#mail_from_address").val(),
                mail_from_name: $("#mail_from_name").val(),
            }, function(data) {
                $('#commonModal .body').html(data);
            });
        }
    });

    $(document).on('submit', '#test_email', function(e) {
        e.preventDefault();
        $("#email_sending").show();
        var post = $(this).serialize();
        var url = $(this).attr('action');
        $.ajax({
            type: "post",
            url: url,
            data: post,
            cache: false,
            beforeSend: function() {
                $('#test_email .btn-create').attr('disabled', 'disabled');
            },
            success: function(data) {
                if (data.is_success) {
                    show_toastr('success', data.message, 'success');
                } else {
                    show_toastr('error', data.message, 'error');
                }
                $("#email_sending").hide();
                $('#commonModal').modal('hide');
            },
            complete: function() {
                $('#test_email .btn-create').removeAttr('disabled');
            },
        });
    });
</script>
@endpush

@section('content')
<div class="row">
    <!-- [ sample-page ] start -->
    <div class="col-sm-12">
        <div class="row">
            <div class="col-xl-3">
                <div class="card sticky-top" style="top:30px">
                    <div class="list-group list-group-flush" id="useradd-sidenav">
                        <a href="#useradd-1"
                            class="list-group-item list-group-item-action border-0">{{ __('Brand Settings') }}
                            <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                        </a>
                        <a href="#useradd-2"
                            class="list-group-item list-group-item-action border-0">{{ __('System Settings') }}
                            <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                        </a>
                        <a href="#useradd-3"
                            class="list-group-item list-group-item-action border-0">{{ __('Company Settings') }}
                            <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                        </a>
                        <a href="#useradd-12"
                            class="list-group-item list-group-item-action border-0">{{ __('Email Settings') }}
                            <div class="float-end "><i class="ti ti-chevron-right"></i></div>
                        </a>
                        <a href="#useradd-10"
                            class="list-group-item list-group-item-action border-0">{{ __('Retainer Print Settings') }}
                            <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                        </a>
                        <a href="#useradd-5"
                            class="list-group-item list-group-item-action border-0">{{ __('Invoice Print Settings') }}
                            <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                        </a>
                        <a href="#useradd-6"
                            class="list-group-item list-group-item-action border-0">{{ __('Bill Print Settings') }}
                            <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                        </a>
                        <a href="#useradd-8"
                            class="list-group-item list-group-item-action border-0">{{ __('Twilio Settings') }}
                            <div class="float-end "><i class="ti ti-chevron-right"></i></div>
                        </a>
                        <a href="#useradd-9"
                            class="list-group-item list-group-item-action border-0">{{ __('Email Notification Settings') }}
                            <div class="float-end "><i class="ti ti-chevron-right"></i></div>
                        </a>
                        <a href="#useradd-11"
                            class="list-group-item list-group-item-action border-0">{{ __('Webhook Settings') }}
                            <div class="float-end "><i class="ti ti-chevron-right"></i></div>
                        </a>

                    </div>
                </div>  
            </div>


            <div class="col-xl-9">

                <!--Business Setting-->
                <div id="useradd-1" class="card">

                    {{ Form::model($settings, ['route' => 'business.setting', 'class' => 'mb-0', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                    <div class="card-header">
                        <h5>{{ __('Brand Settings') }}</h5>
                        <small class="text-muted">{{ __('Edit your brand details') }}</small>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-4 col-sm-6 col-md-6 dashboard-card">
                                <div class="card">
                                    <div class="card-header">
                                        <h5>{{ __('Logo dark') }}</h5>
                                    </div>
                                    <div class="card-body pt-0">
                                        <div class=" setting-card">
                                            <div class="logo-content mt-4">
                                                <a href="{{ $logo . (isset($logo_dark) && !empty($logo_dark) ? $logo_dark : 'logo-dark.png') . '?' . time() }}"
                                                    target="_blank">
                                                    <img id="blah" alt="your image"
                                                        src="{{ $logo . (isset($logo_dark) && !empty($logo_dark) ? $logo_dark : 'logo-dark.png') . '?' . time() }}"
                                                        width="" class="big-logo">
                                                </a>
                                            </div>
                                            <div class="choose-files mt-5">
                                                <label for="company_logo">
                                                    <div class=" bg-primary company_logo_update "> <i
                                                            class="ti ti-upload px-1"></i>{{ __('Choose file here') }}
                                                    </div>
                                                    <input type="file" name="company_logo_dark" id="company_logo"
                                                        class="form-control file" data-filename="company_logo"
                                                        onchange="document.getElementById('blah').src = window.URL.createObjectURL(this.files[0])">

                                                  
                                                </label>
                                            </div>
                                            @error('company_logo')
                                            <div class="row">
                                                <span class="invalid-logo" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-sm-6 col-md-6 dashboard-card">
                                <div class="card">
                                    <div class="card-header">
                                        <h5>{{ __('Logo Light') }}</h5>
                                    </div>
                                    <div class="card-body pt-0">
                                        <div class=" setting-card">
                                            <div class="logo-content mt-4">
                                                <a href="{{ $logo . (isset($logo_light) && !empty($logo_light) ? $logo_light : 'logo-light.png') . '?' . time() }}"
                                                    target="_blank">
                                                    <img id="blah1" alt="your image"
                                                        src="{{ $logo . (isset($logo_light) && !empty($logo_light) ? $logo_light : 'logo-light.png') . '?' . time() }}"
                                                        width="150px" class="big-logo img_setting">
                                                </a>
                                            </div>
                                            <div class="choose-files mt-5">
                                                <label for="company_logo_light">
                                                    <div class=" bg-primary dark_logo_update "> <i
                                                            class="ti ti-upload px-1"></i>{{ __('Choose file here') }}
                                                    </div>
                                                    <input type="file" name="company_logo_light"
                                                        id="company_logo_light" class="form-control file"
                                                        data-filename="company_logo_light"
                                                        onchange="document.getElementById('blah1').src = window.URL.createObjectURL(this.files[0])">


                                                </label>
                                            </div>
                                            @error('company_logo_light')
                                            <div class="row">
                                                <span class="invalid-logo" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-sm-6 col-md-6 dashboard-card">
                                <div class="card">
                                    <div class="card-header">
                                        <h5>{{ __('Favicon') }}</h5>
                                    </div>
                                    <div class="card-body pt-0">
                                        <div class=" setting-card">
                                            <div class="logo-content mt-4">
                                                <a href="{{ $logo . (isset($company_favicon) && !empty($company_favicon) ? $company_favicon : 'favicon.png') . '?' . time() }}"
                                                    target="_blank">
                                                    <img id="blah2" alt="your image"
                                                        src="{{ $logo . (isset($company_favicon) && !empty($company_favicon) ? $company_favicon : 'favicon.png') . '?' . time() }}"
                                                        width="60px" height="63px" class=" img_setting">
                                                </a>
                                            </div>
                                            <div class="choose-files mt-5">
                                                <label for="company_favicon">
                                                    <div class="bg-primary company_favicon_update "> <i
                                                            class="ti ti-upload px-1"></i>{{ __('Choose file here') }}
                                                    </div>
                                                    <input type="file" name="company_favicon" id="company_favicon"
                                                        class="form-control file"
                                                        data-filename="company_favicon"
                                                        onchange="document.getElementById('blah2').src = window.URL.createObjectURL(this.files[0])">

                                                </label>
                                            </div>
                                            @error('logo')
                                            <div class="row">
                                                <span class="invalid-logo" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                    <div class="row">
                        <div class="form-group col-md-3">
                            {{Form::label('title_text',__('Title Text'),array('class'=>'form-label')) }}
                            {{Form::text('title_text',Utility::getValByName('title_text'),array('class'=>'form-control','placeholder'=>__('Enter Title Text')))}}
                            @error('title_text')
                            <span class="invalid-title_text" role="alert">
                                <strong class="text-danger">{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="col-md-3 form-group">
                            {{Form::label('footer_text',__('Footer Text'),['class'=>'form-label']) }}
                            {{Form::text('footer_text',Utility::getValByName('footer_text'),array('class'=>'form-control','placeholder'=>__('Enter Footer Text')))}}
                            @error('footer_text')
                            <span class="invalid-footer_text" role="alert">
                                <strong class="text-danger">{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {{Form::label('default_language',__('Default Language'),['class'=>'form-label text-dark']) }}
                                <div class="changeLanguage">
                                    <select name="default_language" id="default_language" class="form-control select">
                                        @foreach (\App\Models\Utility::languages() as $code => $language)
                                        <option @if ($lang==$code) selected @endif value="{{ $code }}">
                                            {{ucFirst($language) }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('default_language')
                                <span class="invalid-default_language" role="alert">
                                    <strong class="text-danger">{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>


                        <div class="col-3 form-group">
                            <div class="custom-control custom-switch">
                                <label class=" mb-1 mt-1 " for="SITE_RTL">{{ __('Enable RTL') }}</label>
                                <div class="">
                                    <input type="checkbox" name="SITE_RTL" id="SITE_RTL" data-toggle="switchbutton" {{ $settings['SITE_RTL'] == 'on' ? 'checked="checked"' : '' }} data-onstyle="primary">
                                    <label class="custom-control-label" for="SITE_RTL"></label>
                                </div>
                            </div>
                        </div>


                    </div>
                    <h4 class="small-title">{{ __('Theme Customizer') }}</h4>
                    <div class="setting-card setting-logo-box p-3">
                        <div class="row">
                            <div class="col-lg-4 col-xl-4 col-md-4">
                                <h6 class="mt-2">
                                    <i data-feather="credit-card"
                                        class="me-2"></i>{{ __('Primary color settings') }}
                                </h6>
                                <hr class="my-2" />
                                <div class="color-wrp">
                                    <div class="theme-color themes-color">
                                        <a href="#!"
                                            class="themes-color-change {{ $color == 'theme-1' ? 'active_color' : '' }}"
                                            data-value="theme-1"></a>
                                        <input type="radio" class="theme_color d-none" name="color"
                                            value="theme-1" {{ $color == 'theme-1' ? 'checked' : '' }}>
                                        <a href="#!"
                                            class="themes-color-change {{ $color == 'theme-2' ? 'active_color' : '' }}"
                                            data-value="theme-2"></a>
                                        <input type="radio" class="theme_color d-none" name="color"
                                            value="theme-2" {{ $color == 'theme-2' ? 'checked' : '' }}>
                                        <a href="#!"
                                            class="themes-color-change {{ $color == 'theme-3' ? 'active_color' : '' }}"
                                            data-value="theme-3"></a>
                                        <input type="radio" class="theme_color d-none" name="color"
                                            value="theme-3" {{ $color == 'theme-3' ? 'checked' : '' }}>
                                        <a href="#!"
                                            class="themes-color-change {{ $color == 'theme-4' ? 'active_color' : '' }}"
                                            data-value="theme-4"></a>
                                        <input type="radio" class="theme_color d-none" name="color"
                                            value="theme-4" {{ $color == 'theme-4' ? 'checked' : '' }}>
                                        <a href="#!"
                                            class="themes-color-change {{ $color == 'theme-5' ? 'active_color' : '' }}"
                                            data-value="theme-5"></a>
                                        <input type="radio" class="theme_color d-none" name="color"
                                            value="theme-5" {{ $color == 'theme-5' ? 'checked' : '' }}>
                                        <br>
                                        <a href="#!"
                                            class="themes-color-change {{ $color == 'theme-6' ? 'active_color' : '' }}"
                                            data-value="theme-6"></a>
                                        <input type="radio" class="theme_color d-none" name="color"
                                            value="theme-6" {{ $color == 'theme-6' ? 'checked' : '' }}>
                                        <a href="#!"
                                            class="themes-color-change {{ $color == 'theme-7' ? 'active_color' : '' }}"
                                            data-value="theme-7"></a>
                                        <input type="radio" class="theme_color d-none" name="color"
                                            value="theme-7" {{ $color == 'theme-7' ? 'checked' : '' }}>
                                        <a href="#!"
                                            class="themes-color-change {{ $color == 'theme-8' ? 'active_color' : '' }}"
                                            data-value="theme-8"></a>
                                        <input type="radio" class="theme_color d-none" name="color"
                                            value="theme-8" {{ $color == 'theme-8' ? 'checked' : '' }}>
                                        <a href="#!"
                                            class="themes-color-change {{ $color == 'theme-9' ? 'active_color' : '' }}"
                                            data-value="theme-9"></a>
                                        <input type="radio" class="theme_color d-none" name="color"
                                            value="theme-9" {{ $color == 'theme-9' ? 'checked' : '' }}>
                                        <a href="#!"
                                            class="themes-color-change {{ $color == 'theme-10' ? 'active_color' : '' }}"
                                            data-value="theme-10"></a>
                                        <input type="radio" class="theme_color d-none" name="color"
                                            value="theme-10" {{ $color == 'theme-10' ? 'checked' : '' }}>
                                    </div>
                                    <div class="color-picker-wrp">
                                        <input type="color" value="{{ $color ? $color : '' }}"
                                            class="colorPicker {{ isset($settings['color_flag']) && $settings['color_flag'] == 'true' ? 'active_color' : '' }} image-input"
                                            name="custom_color" data-bs-toggle="tooltip"
                                            data-bs-placement="right"
                                            title="{{ __('Select Your Own Brand Color') }}"
                                            id="color-picker">
                                        <input type="hidden" name="custom-color" id="colorCode">
                                        <input type='hidden' name="color_flag"
                                            value={{ isset($settings['color_flag']) && $settings['color_flag'] == 'true' ? 'true' : 'false' }}>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-xl-4 col-md-4">
                                <h6 class="mt-2">
                                    <i data-feather="layout" class="me-2"></i>{{ __('Sidebar settings') }}
                                </h6>
                                <hr class="my-2" />
                                <div class="form-check form-switch">
                                    <input type="checkbox" class="form-check-input" id="cust-theme-bg"
                                        name="cust_theme_bg"
                                        {{ !empty($settings['cust_theme_bg']) && $settings['cust_theme_bg'] == 'on' ? 'checked' : '' }} />
                                    <label class="form-check-label f-w-600 pl-1"
                                        for="cust-theme-bg">{{ __('Transparent layout') }}</label>
                                </div>
                            </div>
                            <div class="col-lg-4 col-xl-4 col-md-4">
                                <h6 class="mt-2">
                                    <i data-feather="sun" class="me-2"></i>{{ __('Layout settings') }}
                                </h6>
                                <hr class="my-2" />
                                <div class="form-check form-switch mt-2">
                                    <input type="checkbox" class="form-check-input" id="cust-darklayout"
                                        name="cust_darklayout"
                                        {{ !empty($settings['cust_darklayout']) && $settings['cust_darklayout'] == 'on' ? 'checked' : '' }} />
                                    <label class="form-check-label f-w-600 pl-1"
                                        for="cust-darklayout">{{ __('Dark Layout') }}</label>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                </div>
                <div class="card-footer text-end">
                      
                    <input class="btn btn-print-invoice btn-primary" type="submit"
                        value="{{ __('Save Changes') }}">
                       
                </div>
                {{ Form::close() }}
            
        </div>
        <!--System Setting-->
        <div id="useradd-2" class="card">
            <div class="card-header">
                <h5>{{ __('System Settings') }}</h5>
                <small class="text-muted">{{ __('Edit your system details') }}</small>
            </div>

            {{ Form::model($settings, ['route' => 'system.settings', 'class' => 'mb-0', 'method' => 'post']) }}
            <div class="card-body">
                <div class="row">
                    <div class="form-group col-md-6">
                        {{ Form::label('site_currency', __('Currency *'), ['class' => 'form-label']) }}
                        {{ Form::text('site_currency', null, ['class' => 'form-control font-style','placeholder'=>__('Enter Currency')]) }}
                        @error('site_currency')
                        <span class="invalid-site_currency" role="alert">
                            <strong class="text-danger">{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="form-group col-md-6">
                        {{ Form::label('site_currency_symbol', __('Currency Symbol *'), ['class' => 'form-label']) }}
                        {{ Form::text('site_currency_symbol', null, ['class' => 'form-control','placeholder'=>__('Enter Currency Symbol')]) }}
                        @error('site_currency_symbol')
                        <span class="invalid-site_currency_symbol" role="alert">
                            <strong class="text-danger">{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label"
                                for="example3cols3Input">{{ __('Currency Symbol Position') }}</label>
                            <div class="row px-3">
                                <div class="form-check col-md-6">
                                    <input class="form-check-input" type="radio"
                                        name="site_currency_symbol_position" value="pre"
                                        @if (@$settings['site_currency_symbol_position']=='pre' ) checked @endif id="flexCheckDefault"
                                        checked>
                                    <label class="form-check-label" for="flexCheckDefault">
                                        {{ __('Pre') }}
                                    </label>
                                </div>
                                <div class="form-check col-md-6">
                                    <input class="form-check-input" type="radio"
                                        name="site_currency_symbol_position" value="post"
                                        @if (@$settings['site_currency_symbol_position']=='post' ) checked @endif id="flexCheckChecked">
                                    <label class="form-check-label" for="flexCheckChecked">
                                        {{ __('Post') }}
                                    </label>
                                </div>

                                {{-- <div class="col-md-6">
                                            <div class="custom-control custom-radio mb-3">

                                                <input type="radio" id="customRadio5" name="site_currency_symbol_position" value="pre" class="custom-control-input" @if (@$settings['site_currency_symbol_position'] == 'pre') checked @endif>
                                                <label class="custom-control-label" for="customRadio5">{{__('Pre')}}</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="custom-control custom-radio mb-3">
                                <input type="radio" id="customRadio6" name="site_currency_symbol_position" value="post" class="custom-control-input" @if (@$settings['site_currency_symbol_position']=='post' ) checked @endif>
                                <label class="custom-control-label" for="customRadio6">{{__('Post')}}</label>
                            </div>
                        </div> --}}
                    </div>
                </div>
            </div>
            <div class="form-group col-md-6">
                <label for="site_date_format" class="form-label">{{ __('Date Format') }}</label>
                <select type="text" name="site_date_format" class="form-control selectric"
                    id="site_date_format">
                    <option value="M j, Y"
                        @if (@$settings['site_date_format']=='M j, Y' ) selected="selected" @endif>Jan 1,2015</option>
                    <option value="d-m-Y"
                        @if (@$settings['site_date_format']=='d-m-Y' ) selected="selected" @endif>dd-mm-yyyy</option>
                    <option value="m-d-Y"
                        @if (@$settings['site_date_format']=='m-d-Y' ) selected="selected" @endif>mm-dd-yyyy</option>
                    <option value="Y-m-d"
                        @if (@$settings['site_date_format']=='Y-m-d' ) selected="selected" @endif>yyyy-mm-dd</option>
                </select>
            </div>
            <div class="form-group col-md-6">
                <label for="site_time_format" class="form-label">{{ __('Time Format') }}</label>
                <select type="text" name="site_time_format" class="form-control selectric"
                    id="site_time_format">
                    <option value="g:i A"
                        @if (@$settings['site_time_format']=='g:i A' ) selected="selected" @endif>10:30 PM</option>
                    <option value="g:i a"
                        @if (@$settings['site_time_format']=='g:i a' ) selected="selected" @endif>10:30 pm</option>
                    <option value="H:i"
                        @if (@$settings['site_time_format']=='H:i' ) selected="selected" @endif>22:30</option>
                </select>
            </div>
            <div class="form-group col-md-6">
                {{ Form::label('invoice_prefix', __('Invoice Prefix'), ['class' => 'form-label']) }}

                {{ Form::text('invoice_prefix', null, ['class' => 'form-control','placeholder'=>__('Enter Invoice Prefix')]) }}
                @error('invoice_prefix')
                <span class="invalid-invoice_prefix" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="form-group col-md-6">
                {{ Form::label('invoice_starting_number', __('Invoice Starting Number'), ['class' => 'form-label']) }}
                {{ Form::text('invoice_starting_number', null, ['class' => 'form-control','placeholder'=>__('Enter Invoice Starting Number')]) }}
                @error('invoice_starting_number')
                <span class="invalid-invoice_starting_number" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="form-group col-md-6">
                {{ Form::label('proposal_prefix', __('Proposal Prefix'), ['class' => 'form-label']) }}
                {{ Form::text('proposal_prefix', null, ['class' => 'form-control','placeholder'=>__('Enter Proposal Prefix')]) }}
                @error('proposal_prefix')
                <span class="invalid-proposal_prefix" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="form-group col-md-6">
                {{ Form::label('proposal_starting_number', __('Proposal Starting Number'), ['class' => 'form-label']) }}
                {{ Form::text('proposal_starting_number', null, ['class' => 'form-control','placeholder'=>__('Enter Proposal Starting Number')]) }}
                @error('proposal_starting_number')
                <span class="invalid-proposal_starting_number" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="form-group col-md-6">
                {{ Form::label('bill_prefix', __('Bill Prefix'), ['class' => 'form-label']) }}
                {{ Form::text('bill_prefix', null, ['class' => 'form-control','placeholder'=>__('Enter Bill Prefix')]) }}
                @error('bill_prefix')
                <span class="invalid-bill_prefix" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="form-group col-md-6">
                {{ Form::label('retainer_starting_number', __('Retainer Starting Number'), ['class' => 'form-label']) }}
                {{ Form::text('retainer_starting_number', null, ['class' => 'form-control','placeholder'=>__('Enter Retainer Starting Number')]) }}
                @error('retainer_starting_number')
                <span class="invalid-proposal_starting_number" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="form-group col-md-6">
                {{ Form::label('retainer_prefix', __('Retainer Prefix'), ['class' => 'form-label']) }}
                {{ Form::text('retainer_prefix', null, ['class' => 'form-control','placeholder'=>__('Enter Retainer Prefix')]) }}
                @error('retainer_prefix')
                <span class="invalid-bill_prefix" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="form-group col-md-6">
                {{ Form::label('bill_starting_number', __('Bill Starting Number'), ['class' => 'form-label']) }}
                {{ Form::text('bill_starting_number', null, ['class' => 'form-control','placeholder'=>__('Enter Bill Starting Number')]) }}
                @error('bill_starting_number')
                <span class="invalid-bill_starting_number" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="form-group col-md-6">
                {{ Form::label('customer_prefix', __('Customer Prefix'), ['class' => 'form-label']) }}
                {{ Form::text('customer_prefix', null, ['class' => 'form-control','placeholder'=>__('Enter Customer Prefix')]) }}
                @error('customer_prefix')
                <span class="invalid-customer_prefix" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="form-group col-md-6">
                {{ Form::label('vender_prefix', __('Vender Prefix'), ['class' => 'form-label']) }}
                {{ Form::text('vender_prefix', null, ['class' => 'form-control','placeholder'=>__('Enter Vender Prefix')]) }}
                @error('vender_prefix')
                <span class="invalid-vender_prefix" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="form-group col-md-6">
                {{ Form::label('footer_title', __('Invoice/Bill Footer Title'), ['class' => 'form-label']) }}
                {{ Form::text('footer_title', null, ['class' => 'form-control','placeholder'=>__('Enter Invoice/Bill Footer Title')]) }}
                @error('footer_title')
                <span class="invalid-footer_title" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="form-group col-md-6">
                {{ Form::label('decimal_number', __('Decimal Number Format'), ['class' => 'form-label']) }}
                {{ Form::number('decimal_number', null, ['class' => 'form-control','placeholder'=>__('Enter Decimal Number Format')]) }}
                @error('decimal_number')
                <span class="invalid-decimal_number" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="form-group col-md-6">
                {{ Form::label('journal_prefix', __('Journal Prefix'), ['class' => 'form-label']) }}
                {{ Form::text('journal_prefix', null, ['class' => 'form-control','placeholder'=>__('Enter Journal Prefix')]) }}
                @error('journal_prefix')
                <span class="invalid-journal_prefix" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </span>
                @enderror
            </div>


            <div class="form-group col-md-6">
                {{ Form::label('shipping_display', __('Display Shipping in Proposal / Invoice / Bill'), ['class' => 'form-label']) }}
                <div class=" form-switch form-switch-left">
                    <input type="checkbox" class="form-check-input" name="shipping_display"
                        id="email_tempalte_13"
                        {{ $settings['shipping_display'] == 'on' ? 'checked' : '' }}>
                    <label class="form-check-label" for="email_tempalte_13"></label>
                </div>

                @error('shipping_display')
                <span class="invalid-shipping_display" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="form-group col-md-6">
                {{ Form::label('footer_notes', __('Invoice/Bill Footer Notes'), ['class' => 'form-label']) }}
                <textarea class="form-control font-style" col="20" rows="5" name="footer_notes" placeholder="{{__('Enter Invoice/Bill Footer Notes') }}">{!! $settings['footer_notes'] !!}</textarea>
                @error('footer_notes')
                <span class="invalid-footer_notes" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </span>
                @enderror
            </div>
        </div>
    </div>
    <div class="card-footer text-end">
       
        <input class="btn btn-print-invoice  btn-primary" type="submit"
            value="{{ __('Save Changes') }}">
       
    </div>
    {{ Form::close() }}
</div>

<!--Company Setting-->
<div id="useradd-3" class="card">
    <div class="card-header">
        <h5>{{ __('Company Settings') }}</h5>
        <small class="text-muted">{{ __('Edit your company details') }}</small>
    </div>
    {{ Form::model($settings, ['route' => 'company.settings', 'method' => 'post', 'class' => 'mb-0']) }}
    <div class="card-body">
        <div class="row">
            <div class="form-group col-md-6">
                {{ Form::label('company_name *', __('Company Name *'), ['class' => 'form-label']) }}
                {{ Form::text('company_name', null, ['class' => 'form-control font-style','placeholder'=>__('Enter Journal Prefix')]) }}
                @error('company_name')
                <span class="invalid-company_name" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="form-group col-md-6">
                {{ Form::label('company_address', __('Address'), ['class' => 'form-label']) }}
                {{ Form::text('company_address', null, ['class' => 'form-control font-style','placeholder'=>__('Enter Address')]) }}
                @error('company_address')
                <span class="invalid-company_address" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="form-group col-md-6">
                {{ Form::label('company_city', __('City'), ['class' => 'form-label']) }}
                {{ Form::text('company_city', null, ['class' => 'form-control font-style','placeholder'=>__('Enter City')]) }}
                @error('company_city')
                <span class="invalid-company_city" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="form-group col-md-6">
                {{ Form::label('company_state', __('State'), ['class' => 'form-label']) }}
                {{ Form::text('company_state', null, ['class' => 'form-control font-style','placeholder'=>__('Enter State')]) }}
                @error('company_state')
                <span class="invalid-company_state" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="form-group col-md-6">
                {{ Form::label('company_zipcode', __('Zip/Post Code'), ['class' => 'form-label']) }}
                {{ Form::text('company_zipcode', null, ['class' => 'form-control','placeholder'=>__('Enter Zip/Post Code')]) }}
                @error('company_zipcode')
                <span class="invalid-company_zipcode" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="form-group  col-md-6">
                {{ Form::label('company_country', __('Country'), ['class' => 'form-label']) }}
                {{ Form::text('company_country', null, ['class' => 'form-control font-style','placeholder'=>__('Enter Country')]) }}
                @error('company_country')
                <span class="invalid-company_country" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="form-group col-md-6">
                {{ Form::label('company_telephone', __('Telephone'), ['class' => 'form-label']) }}
                {{ Form::text('company_telephone', null, ['class' => 'form-control','placeholder'=>__('Enter Telephone')]) }}
                @error('company_telephone')
                <span class="invalid-company_telephone" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="form-group col-md-6">
                {{ Form::label('registration_number', __('Company Registration Number *'), ['class' => 'form-label']) }}
                {{ Form::text('registration_number', null, ['class' => 'form-control','placeholder'=>__('Enter Company Registration Number')]) }}
                @error('registration_number')
                <span class="invalid-registration_number" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="form-group col-md-6">
                <div class="row mt-4">
                    <div class="col-md-6">
                        {{ Form::label('tax_number', __('Tax Number'), ['class' => 'form-chech-label']) }}
                        <div class="form-check form-switch custom-switch-v1 float-end">
                            <input type="checkbox" class="form-check-input" name="tax_number"
                                id="tax_number"
                                {{ $settings['tax_number'] == 'on' ? 'checked' : '' }}>
                            <label class="form-check-label" for="vat_gst_number_switch"></label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group col-md-6" id="tax_checkbox_id">
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-check form-check-inline form-group mb-3">
                                <input type="radio" id="customRadio8" name="tax_type"
                                    value="VAT" class="form-check-input"
                                    {{ $settings['tax_type'] == 'VAT' ? 'checked' : '' }}>
                                <label class="form-check-label"
                                    for="customRadio8">{{ __('VAT Number') }}</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check form-check-inline form-group mb-3">
                                <input type="radio" id="customRadio7" name="tax_type"
                                    value="GST" class="form-check-input"
                                    {{ $settings['tax_type'] == 'GST' ? 'checked' : '' }}>
                                <label class="form-check-label"
                                    for="customRadio7">{{ __('GST Number') }}</label>
                            </div>
                        </div>
                    </div>
                    {{ Form::text('vat_number', null, ['class' => 'form-control', 'placeholder' => __('Enter VAT / GST Number')]) }}
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer text-end">
        <input class="btn btn-print-invoice btn-primary" type="submit" id="addSig"
            value="{{ __('Save Changes') }}">
    </div>
    {{ Form::close() }}
</div>

<!--Email Setting-->
<div id="useradd-12" class="card">
    <div class="card-header">
        <h5>{{ __('Email Settings') }}</h5>
        <small
            class="text-muted">{{ __('This SMTP will be used for system-level email sending. Additionally, if a company user does not set their SMTP,
                                                                then this SMTP will be used for sending emails.') }}</small>
    </div>
    <div class="card-body">
        {{ Form::model($settings, ['route' => ['company.email.settings'], 'method' => 'post', 'class' => 'mb-0']) }}
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    {{ Form::label('mail_driver', __('Mail Driver'), ['class' => 'form-label']) }}
                    {{ Form::text('mail_driver', null, ['class' => 'form-control', 'placeholder' => __('Enter Mail Driver')]) }}
                    @error('mail_driver')
                    <span class="invalid-mail_driver" role="alert">
                        <strong class="text-danger">{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {{ Form::label('mail_host', __('Mail Host'), ['class' => 'form-label']) }}
                    {{ Form::text('mail_host', null, ['class' => 'form-control ', 'placeholder' => __('Enter Mail Host')]) }}
                    @error('mail_host')
                    <span class="invalid-mail_driver" role="alert">
                        <strong class="text-danger">{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {{ Form::label('mail_port', __('Mail Port'), ['class' => 'form-label']) }}
                    {{ Form::text('mail_port', null, ['class' => 'form-control', 'placeholder' => __('Enter Mail Port')]) }}
                    @error('mail_port')
                    <span class="invalid-mail_port" role="alert">
                        <strong class="text-danger">{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    {{ Form::label('mail_username', __('Mail Username'), ['class' => 'form-label']) }}
                    {{ Form::text('mail_username', null, ['class' => 'form-control', 'placeholder' => __('Enter Mail Username')]) }}
                    @error('mail_username')
                    <span class="invalid-mail_username" role="alert">
                        <strong class="text-danger">{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {{ Form::label('mail_password', __('Mail Password'), ['class' => 'form-label']) }}
                    {{ Form::text('mail_password', null, ['class' => 'form-control', 'placeholder' => __('Enter Mail Password')]) }}
                    @error('mail_password')
                    <span class="invalid-mail_password" role="alert">
                        <strong class="text-danger">{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {{ Form::label('mail_encryption', __('Mail Encryption'), ['class' => 'form-label']) }}
                    {{ Form::text('mail_encryption', null, ['class' => 'form-control', 'placeholder' => __('Enter Mail Encryption')]) }}
                    @error('mail_encryption')
                    <span class="invalid-mail_encryption" role="alert">
                        <strong class="text-danger">{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    {{ Form::label('mail_from_address', __('Mail From Address'), ['class' => 'form-label']) }}
                    {{ Form::text('mail_from_address', null, ['class' => 'form-control', 'placeholder' => __('Enter Mail From Address')]) }}
                    @error('mail_from_address')
                    <span class="invalid-mail_from_address" role="alert">
                        <strong class="text-danger">{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {{ Form::label('mail_from_name', __('Mail From Name'), ['class' => 'form-label']) }}
                    {{ Form::text('mail_from_name', null, ['class' => 'form-control', 'placeholder' => __('Enter Mail From Name')]) }}
                    @error('mail_from_name')
                    <span class="invalid-mail_from_name" role="alert">
                        <strong class="text-danger">{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <!-- <div class="row"> -->
        <div class="card-footer d-flex justify-content-end mb-0">
            <div class="mb-0 me-2">
                <a href="javascript:void(0)" class="btn btn-primary send_email "
                    data-title="{{ __('Send Test Mail') }}"
                    data-url="{{ route('test.mail') }}">
                    {{ __('Send Test Mail') }}
                </a>
            </div>
            <div class=" text-end mb-0">
                <input class="btn btn-primary " type="submit" value="{{ __('Save Changes') }}">
            </div>
        </div>
    <!-- </div> -->
    {{ Form::close() }}
    
</div>

<!--Retainer Print Setting-->
<div id="useradd-10" class="card">
    <div class="card-header">
        <h5>{{ __('Retainer Print Settings') }}</h5>
        <small class="text-muted">{{ __('Edit your company retainer details') }}</small>
    </div>

    <div class="bg-none">
        <div class="row company-setting">
            <div class="col-md-4 d-flex">
                <div class="card-header card-body">
                    <form id="setting-form" method="post"
                        action="{{ route('retainer.template.setting') }}"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="address"
                                class="col-form-label">{{ __('Retainer Print Template') }}</label>
                            <select class="form-control" name="retainer_template">
                                @foreach (App\Models\Utility::templateData()['templates'] as $key => $template)
                                <option value="{{ $key }}"
                                    {{ isset($settings['retainer_template']) && $settings['retainer_template'] == $key ? 'selected' : '' }}>
                                    {{ $template }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        {{-- <div class="col-md-4"> --}}
                        <div class="form-group">
                            <label for="address"
                                class="col-form-label">{{ __('QR Display?') }}</label>
                            <div class="d-flex align-items-center">
                                <div class="form-check form-switch custom-switch-v1 mt-2">
                                    <input type="hidden" name="retainer_qr_display" value="off">
                                    <input type="checkbox" class="form-check-input input-primary"
                                        id="customswitchv1-1 retainer_qr_display"
                                        name="retainer_qr_display"
                                        {{ isset($settings['retainer_qr_display']) && $settings['retainer_qr_display'] == 'on' ? 'checked="checked"' : '' }}>
                                </div>
                            </div>
                        </div>
                        {{-- </div> --}}
                        <div class="form-group">
                            <label class="col-form-label">{{ __('Color Input') }}</label>
                            <div class="row gutters-xs">
                                @foreach (App\Models\Utility::templateData()['colors'] as $key => $color)
                                <div class="col-auto">
                                    <label class="colorinput">
                                        <input name="retainer_color" type="radio"
                                            value="{{ $color }}" class="colorinput-input"
                                            {{ isset($settings['retainer_color']) && $settings['retainer_color'] == $color ? 'checked' : '' }}>
                                        <span class="colorinput-color"
                                            style="background: #{{ $color }}"></span>
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-form-label">{{ __('Retainer Logo') }}</label>
                            <div class="choose-files ">
                                <label for="retainer_logo">
                                    <div class=" bg-primary proposal_logo_update"> <i
                                            class="ti ti-upload px-1"></i>{{ __('Choose file here') }}
                                    </div>
                                    <img id="blah5" class="mt-3" width="50%" />
                                    <input type="file" class="form-control file"
                                        name="retainer_logo" id="retainer_logo"
                                        data-filename="retainer_logo"
                                        onchange="document.getElementById('blah5').src = window.URL.createObjectURL(this.files[0])">
                                </label>
                            </div>
                        </div>
                        <div class="form-group mt-2 text-end">
                            <input type="submit" value="{{ __('Save Changes') }}"
                                class="btn btn-print-invoice  btn-primary">
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-md-8 d-flex">
                <div class="i-frame d-flex w-100">
                    @if (isset($settings['retainer_template']) && isset($settings['retainer_color']))
                    <iframe id="retainer_frame" class="w-100 h-100" frameborder="0"
                        src="{{ route('retainer.preview', [$settings['retainer_template'], $settings['retainer_color']]) }}"></iframe>
                    @else
                    <iframe id="retainer_frame" class="w-100 h-100" frameborder="0"
                        src="{{ route('retainer.preview', ['template1', 'fffff']) }}"></iframe>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>

<!--Invoice Setting-->
<div id="useradd-5" class="card">
    <div class="card-header">
        <h5>{{ __('Invoice Print Settings') }}</h5>
        <small class="text-muted">{{ __('Edit your company invoice details') }}</small>
    </div>

    <div class="bg-none">
        <div class="row company-setting">
            <div class="col-md-4 d-flex">
                <div class="card-header card-body">
                    <form id="setting-form" method="post"
                        action="{{ route('invoice.template.setting') }}"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="address"
                                class="col-form-label">{{ __('Invoice Template') }}</label>
                            <select class="form-control" name="invoice_template">
                                @foreach (Utility::templateData()['templates'] as $key => $template)
                                <option value="{{ $key }}"
                                    {{ isset($settings['invoice_template']) && $settings['invoice_template'] == $key ? 'selected' : '' }}>
                                    {{ $template }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        {{-- <div class="col-md-4"> --}}
                        <div class="form-group">
                            <label for="address"
                                class="col-form-label">{{ __('QR Display?') }}</label>
                            <div class="d-flex align-items-center">
                                <div class="form-check form-switch custom-switch-v1 mt-2">
                                    <input type="hidden" name="invoice_qr_display" value="off">
                                    <input type="checkbox" class="form-check-input input-primary"
                                        id="customswitchv1-1 invoice_qr_display"
                                        name="invoice_qr_display"
                                        {{ isset($settings['invoice_qr_display']) && $settings['invoice_qr_display'] == 'on' ? 'checked="checked"' : '' }}>
                                </div>
                            </div>
                        </div>
                        {{-- </div> --}}
                        <div class="form-group">
                            <label class="col-form-label">{{ __('Color Input') }}</label>
                            <div class="row gutters-xs">
                                @foreach (Utility::templateData()['colors'] as $key => $color)
                                <div class="col-auto">
                                    <label class="colorinput">
                                        <input name="invoice_color" type="radio"
                                            value="{{ $color }}" class="colorinput-input"
                                            {{ isset($settings['invoice_color']) && $settings['invoice_color'] == $color ? 'checked' : '' }}>
                                        <span class="colorinput-color"
                                            style="background: #{{ $color }}"></span>
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-form-label">{{ __('Invoice Logo') }}</label>
                            <div class="choose-files">
                                <label for="invoice_logo">
                                    <div class=" bg-primary invoice_logo_update"> <i
                                            class="ti ti-upload px-1"></i>{{ __('Choose file here') }}
                                    </div>
                                    <img id="blah6" class="mt-3" width="50%" />
                                    <input type="file" class="form-control file"
                                        name="invoice_logo" id="invoice_logo"
                                        data-filename="invoice_logo"
                                        onchange="document.getElementById('blah6').src = window.URL.createObjectURL(this.files[0])">
                                </label>
                            </div>
                        </div>
                        <div class="form-group mt-2 text-end">
                            <input type="submit" value="{{ __('Save Changes') }}"
                                class="btn btn-print-invoice  btn-primary">
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-md-8 d-flex">
                <div class="i-frame d-flex w-100">
                    @if (isset($settings['invoice_template']) && isset($settings['invoice_color']))
                    <iframe id="invoice_frame" class="w-100 h-100" frameborder="0"
                        src="{{ route('invoice.preview', [$settings['invoice_template'], $settings['invoice_color']]) }}"></iframe>
                    @else
                    <iframe id="invoice_frame" class="w-100 h-100" frameborder="0"
                        src="{{ route('invoice.preview', ['template1', 'fffff']) }}"></iframe>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>


    <div id="useradd-6" class="card">
        <div class="card-header">
            <h5>{{ __('Bill Print Settings') }}</h5>
            <small class="text-muted">{{ __('Edit your company bill details') }}</small>
        </div>

        <div class="bg-none">
            <div class="row company-setting">
                <div class="col-md-4 d-flex">
                    <div class="card-header card-body">
                        <form id="setting-form" method="post" action="{{ route('bill.template.setting') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label for="address" class="form-label">{{ __('Bill Template') }}</label>
                                <select class="form-control" name="bill_template">
                                    @foreach (App\Models\Utility::templateData()['templates'] as $key => $template)
                                    <option value="{{ $key }}" {{ isset($settings['bill_template']) && $settings['bill_template'] == $key ? 'selected' : '' }}>
                                        {{ $template }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="address" class="col-form-label">{{ __('QR Display?') }}</label>
                                <div class="d-flex align-items-center">
                                    <div class="form-check form-switch custom-switch-v1 mt-2">
                                        <input type="hidden" name="bill_qr_display" value="off">
                                        <input type="checkbox" class="form-check-input input-primary" id="customswitchv1-1 bill_qr_display" name="bill_qr_display" {{ isset($settings['bill_qr_display']) && $settings['bill_qr_display'] == 'on' ? 'checked="checked"' : '' }}>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-form-label">{{ __('Color Input') }}</label>
                                <div class="row gutters-xs">
                                    @foreach (Utility::templateData()['colors'] as $key => $color)
                                    <div class="col-auto">
                                        <label class="colorinput">
                                            <input name="bill_color" type="radio" value="{{ $color }}" class="colorinput-input" {{ isset($settings['bill_color']) && $settings['bill_color'] == $color ? 'checked' : '' }}>
                                            <span class="colorinput-color" style="background: #{{ $color }}"></span>
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-form-label">{{ __('Bill Logo') }}</label>
                                <div class="choose-files">
                                    <label for="bill_logo">
                                        <div class=" bg-primary bill_logo_update"> <i class="ti ti-upload px-1"></i>{{ __('Choose file here') }}
                                        </div>
                                        <img id="blah7" class="mt-3"  width="50%" />
                                        <input type="file" class="form-control file" name="bill_logo" id="bill_logo" data-filename="bill_logo" onchange="document.getElementById('blah7').src = window.URL.createObjectURL(this.files[0])">
                                    </label>
                                </div>
                            </div>
                            <div class="form-group mt-2 text-end">
                                <input type="submit" value="{{ __('Save Changes') }}" class="btn btn-print-invoice  btn-primary">
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-md-8 d-flex">
                    <div class="i-frame d-flex w-100">
                        @if (isset($settings['bill_template']) && isset($settings['bill_color']))
                        <iframe id="bill_frame" class="w-100 h-100" frameborder="0" src="{{ route('bill.preview', [$settings['bill_template'], $settings['bill_color']]) }}"></iframe>
                        @else
                        <iframe id="bill_frame" class="w-100 h-100" frameborder="0" src="{{ route('bill.preview', ['template1', 'fffff']) }}"></iframe>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--Twilio Setting-->
    <div id="useradd-8" class="card">
        <div class="card-header">
            <h5>{{ __('Twilio Settings') }}</h5>
            <small class="text-muted">{{ __('Edit your company twilio setting details') }}</small>
        </div>

        <div class="card-body">
            {{ Form::model($settings, ['route' => 'twilio.settings', 'method' => 'post', 'class' => 'mb-0']) }}
            <div class="row">

                <div class="col-md-4">
                    <div class="form-group">
                        {{ Form::label('twilio_sid', __('Twilio SID '), ['class' => 'form-label']) }}
                        {{ Form::text('twilio_sid', isset($settings['twilio_sid']) ? $settings['twilio_sid'] : '', ['class' => 'form-control w-100', 'placeholder' => __('Enter Twilio SID'), 'required' => 'required']) }}
                        @error('twilio_sid')
                        <span class="invalid-twilio_sid" role="alert">
                            <strong class="text-danger">{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {{ Form::label('twilio_token', __('Twilio Token'), ['class' => 'form-label']) }}
                        {{ Form::text('twilio_token', isset($settings['twilio_token']) ? $settings['twilio_token'] : '', ['class' => 'form-control w-100', 'placeholder' => __('Enter Twilio Token'), 'required' => 'required']) }}
                        @error('twilio_token')
                        <span class="invalid-twilio_token" role="alert">
                            <strong class="text-danger">{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        {{ Form::label('twilio_from', __('Twilio From'), ['class' => 'form-label']) }}
                        {{ Form::text('twilio_from', isset($settings['twilio_from']) ? $settings['twilio_from'] : '', ['class' => 'form-control w-100', 'placeholder' => __('Enter Twilio From'), 'required' => 'required']) }}
                        @error('twilio_from')
                        <span class="invalid-twilio_from" role="alert">
                            <strong class="text-danger">{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                </div>


                <div class="col-md-12 mt-4 mb-2">
                    <h5 class="small-title">{{ __('Module Settings') }}</h5>
                </div>
                <div class="col-md-4 mb-2">
                    <ul class="list-group">
                        <li class="list-group-item">
                            <div class=" form-switch form-switch-right">
                                <span>{{ __('New Customer') }}</span>
                                {{ Form::checkbox('customer_notification', '1', isset($settings['customer_notification']) && $settings['customer_notification'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'customer_notification']) }}
                                <label class="form-check-label" for="customer_notification"></label>
                            </div>

                        </li>
                        <li class="list-group-item">
                            <div class=" form-switch form-switch-right">
                                <span>{{ __('New Vendor') }}</span>
                                {{ Form::checkbox('vender_notification', '1', isset($settings['vender_notification']) && $settings['vender_notification'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'vender_notification']) }}
                                <label class="form-check-label" for="vender_notification"></label>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="col-md-4 mb-2">
                    <ul class="list-group">
                        <li class="list-group-item">
                            <div class=" form-switch form-switch-right">
                                <span>{{ __('New Invoice') }}</span>
                                {{ Form::checkbox('invoice_notification', '1', isset($settings['invoice_notification']) && $settings['invoice_notification'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'invoice_notification']) }}
                                <label class="form-check-label" for="invoice_notification"></label>
                            </div>
                        </li>

                        <li class="list-group-item">
                            <div class=" form-switch form-switch-right">
                                <span>{{ __('New Revenue') }}</span>
                                {{ Form::checkbox('revenue_notification', '1', isset($settings['revenue_notification']) && $settings['revenue_notification'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'revenue_notification']) }}
                                <label class="form-check-label" for="revenue_notification"></label>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="col-md-4 mb-2">
                    <ul class="list-group">
                        <li class="list-group-item">
                            <div class=" form-switch form-switch-right">
                                <span>{{ __('New Bill') }}</span>
                                {{ Form::checkbox('bill_notification', '1', isset($settings['bill_notification']) && $settings['bill_notification'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'bill_notification']) }}
                                <label class="form-check-label" for="bill_notification"></label>
                            </div>
                        </li>

                        <li class="list-group-item">
                            <div class=" form-switch form-switch-right">
                                <span>{{ __('New Proposal') }}</span>
                                {{ Form::checkbox('proposal_notification', '1', isset($settings['proposal_notification']) && $settings['proposal_notification'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'proposal_notification']) }}
                                <label class="form-check-label" for="proposal_notification"></label>
                            </div>
                        </li>

                    </ul>
                </div>
                <div class="col-md-4 mb-2">
                    <ul class="list-group">
                        <li class="list-group-item">
                            <div class=" form-switch form-switch-right">
                                <span>{{ __('New Payment') }}</span>
                                {{ Form::checkbox('payment_notification', '1', isset($settings['payment_notification']) && $settings['payment_notification'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'payment_notification']) }}
                                <label class="form-check-label" for="payment_notification"></label>
                            </div>
                        </li>

                        <li class="list-group-item">
                            <div class=" form-switch form-switch-right">
                                <span>{{ __('Invoice Reminder') }}</span>
                                {{ Form::checkbox('reminder_notification', '1', isset($settings['reminder_notification']) && $settings['reminder_notification'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'reminder_notification']) }}
                                <label class="form-check-label" for="reminder_notification"></label>
                            </div>
                        </li>
                    </ul>
                </div>



            </div>
           
        </div>
        <div class="card-footer text-end">   
            <input class="btn btn-print-invoice  btn-primary" type="submit"
                value="{{ __('Save Changes') }}">
        </div>
        {{ Form::close() }}

    </div>

    <!--Email Notification Setting-->
    <div id="useradd-9" class="card">

        {{ Form::model($settings, ['route' => ['status.email.language'], 'method' => 'post', 'class' => 'mb-0']) }}
        @csrf
        <div class="col-md-12">
            <div class="card-header">
                <div class="row">
                    <div class="col-lg-8 col-md-8 col-sm-8">
                        <h5>{{ __('Email Notification Settings') }}</h5>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="row">
                    @foreach ($EmailTemplates as $EmailTemplate)
                    <div class="col-lg-4 col-md-6 col-sm-6 form-group">
                        <div class="list-group">
                            <div class="list-group-item form-switch form-switch-right">
                                <label class="form-label"
                                    style="margin-left:5%;">{{ $EmailTemplate->name }}</label>
                                <input class="form-check-input" name='{{ $EmailTemplate->id }}'
                                    @if ($EmailTemplate->template) id="email_tempalte_{{ $EmailTemplate->template->id }}"
                                type="checkbox"
                                @if ($EmailTemplate->template->is_active == 1) checked="checked" @endif
                                type="checkbox" value="1"
                                data-url="{{ route('status.email.language', [$EmailTemplate->template->id]) }}"
                                @endif />
                                <label class="form-check-label"
                                    @if ($EmailTemplate->template) for="email_tempalte_{{ $EmailTemplate->template->id }}" @endif></label>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="card-footer text-end">   
                <input class="btn btn-print-invoice  btn-primary " type="submit"
                    value="{{ __('Save Changes') }}">
            </div>
        </div>
        {{ Form::close() }}
    </div>

    <!--Webhook Setting-->
    <div class="" id="useradd-11">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h5>{{ __('Webhook Settings') }}</h5>
               
                <a data-url="{{ route('webhook.create') }}" style="height: 30px;" class="btn btn-sm btn-primary mx-3 align-items-center  d-inline-flex justify-content-center"
                                        data-bs-toggle="tooltip" data-bs-original-title="{{ __('Create') }}"
                                        data-bs-placement="bottom" data-size="md" data-ajax-popup="true"
                                        data-title="{{ __('Create New Webhook') }}">
                                        <i class="ti ti-plus text-white"></i>
                                    </a>
            </div>
            <div class="card-body table-border-style ">
                <div class="table-responsive">
                    <table class="table" id="pc-dt-simple">
                        <thead>
                            <tr>
                                <th> {{ __('Modules') }}</th>
                                <th> {{ __('url') }}</th>
                                <th> {{ __('Method') }}</th>
                                <th width="200px"> {{ 'Action' }}</th>
                            </tr>
                        </thead>
                        @php
                        $webhooks = App\Models\Webhook::where(
                        'created_by',
                        Auth::user()->id,
                        )->get();
                        @endphp
                        <tbody>
                            @foreach ($webhooks as $webhook)
                            <tr class="action">
                                <td class="sorting_1">
                                    {{ $webhook->module }}
                                </td>
                                <td class="sorting_3">
                                    {{ $webhook->url }}
                                </td>
                                <td class="sorting_2">
                                    {{ $webhook->method }}
                                </td>
                                <td class="">
                                    <div class="action-btn me-2">
                                        <a class="mx-3 btn btn-sm align-items-center bg-info d-inline-flex justify-content-center"
                                            data-url="{{ route('webhook.edit', $webhook->id) }}"
                                            style="height: 30px;"
                                            data-size="md" data-ajax-popup="true"
                                            title="{{ __('Edit') }}"
                                            data-bs-toggle="tooltip"
                                            data-bs-placement="bottom" class="edit-icon"
                                           ><i class="ti ti-pencil text-white"></i></a>
                                    </div>
                        
                                    <div class="action-btn ">
                                        {!! Form::open(['method' => 'DELETE', 'route' => ['webhook.destroy', $webhook->id],'id'=>'delete-form-'. $webhook->id]) !!}
                                            <a href="#!"  class="bs-pass-para bg-danger mx-3 btn btn-sm align-items-center d-inline-flex justify-content-center"
                                            data-bs-toggle="tooltip"
                                            title="{{ __('Delete') }}" data-bs-placement="bottom"
                                            style="height: 30px;">
                                                <i class="ti ti-trash text-white"></i>
                                            </a>
                                        {!! Form::close() !!}
                                    </div>
                                </td>   
                            </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- [ sample-page ] end -->
</div>
<!-- [ Main Content ] end -->
</div>
@endsection


@push('script-page')
<script>
    $(document).on('click', 'input[name="theme_color"]', function() {
        var eleParent = $(this).attr('data-theme');
        $('#themefile').val(eleParent);
        var imgpath = $(this).attr('data-imgpath');
        $('.' + eleParent + '_img').attr('src', imgpath);
    });

    $(document).ready(function() {
        setTimeout(function(e) {
            var checked = $("input[type=radio][name='theme_color']:checked");
            $('#themefile').val(checked.attr('data-theme'));
            $('.' + checked.attr('data-theme') + '_img').attr('src', checked.attr('data-imgpath'));
        }, 300);
    });

    function check_theme(color_val) {

        $('.theme-color').prop('checked', false);
        $('input[value="' + color_val + '"]').prop('checked', true);
        $('#color_value').val(color_val);
    }
</script>

<script>
    $('.colorPicker').on('click', function(e) {
        $('body').removeClass('custom-color');
        if (/^theme-\d+$/) {
            $('body').removeClassRegex(/^theme-\d+$/);
        }
        $('body').addClass('custom-color');
        $('.themes-color-change').removeClass('active_color');
        $(this).addClass('active_color');
        const input = document.getElementById("color-picker");
        setColor();
        input.addEventListener("input", setColor);

        function setColor() {
            $(':root').css('--color-customColor', input.value);
        }

        $(`input[name='color_flag`).val('true');
    });

    $('.themes-color-change').on('click', function() {

        $(`input[name='color_flag`).val('false');

        var color_val = $(this).data('value');
        $('body').removeClass('custom-color');
        if (/^theme-\d+$/) {
            $('body').removeClassRegex(/^theme-\d+$/);
        }
        $('body').addClass(color_val);
        $('.theme-color').prop('checked', false);
        $('.themes-color-change').removeClass('active_color');
        $('.colorPicker').removeClass('active_color');
        $(this).addClass('active_color');
        $(`input[value=${color_val}]`).prop('checked', true);
    });

    $.fn.removeClassRegex = function(regex) {
        return $(this).removeClass(function(index, classes) {
            return classes.split(/\s+/).filter(function(c) {
                return regex.test(c);
            }).join(' ');
        });
    };
</script>
@endpush