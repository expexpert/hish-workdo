<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>
        {{ $invoice->invoice_number ? 'Facture ' . $invoice->invoice_number : ($invoice->quote_number ? 'Devis ' . $invoice->quote_number : '') }}
    </title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 12px;
            color: #000;
            margin: 40px;
        }


        /* WATERMARK IMAGE STYLE */
        .watermark {
            position: fixed;
            top: 15%;
            left: 10%;
            width: 80%;
            opacity: 0.1;
            z-index: -1000;
            text-align: center;
        }

        .watermark img {
            width: 800px;
            height: auto;
        }

        /* HEADER */

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-table td {
            vertical-align: top;
        }

        .logo {
            height: 80px;
        }

        .invoice-title {
            font-size: 40px;
            font-weight: bold;
            margin-top: 10px;
        }

        .client-info {
            margin-top: 10px;
        }

        /* CONTACT */

        .contact-list {
            margin: 0;
            padding-left: 18px;
        }

        .contact-list li {
            margin-bottom: 4px;
        }

        /* ARTICLE TABLE */

        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
        }

        .invoice-table th {
            color: #fff;
            padding: 8px;
            text-align: left;
        }

        .invoice-table td {
            border: 1px solid #000;
            padding: 6px;
        }

        /* TOTALS */

        .totals-table {
            width: 40%;
            margin-left: auto;
            margin-top: 10px;
            border-collapse: collapse;
        }

        .totals-table td {
            border: 1px solid #000;
            padding: 6px;
        }

        .total-final {
            color: #fff;
            font-weight: bold;
        }

        /* SIGNATURE */

        .signature {
            margin-top: 70px;
            text-align: right;
        }

        .signature img {
            height: 70px;
        }

        /* FOOTER */

        .footer {
            margin-top: 80px;
            color: #fff;
            text-align: center;
            padding: 15px;
            font-size: 11px;
        }
    </style>
</head>

<body>

    @if(isset($is_logo) && $is_logo == false)
    <div class="watermark">
        @php
        $logoPath = 'uploads/logo/simply-compta.png';
        $watermarkSrc = null;
        $storageLogoPath = storage_path('app/public/' . $logoPath);

        if (is_file($storageLogoPath)) {
        $mime = mime_content_type($storageLogoPath) ?: 'image/png';
        $watermarkSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($storageLogoPath));
        } elseif (\Storage::disk('public')->exists($logoPath)) {
        $watermarkSrc = asset('storage/' . $logoPath);
        }
        @endphp

        @if($watermarkSrc)
        <img src="{{ $watermarkSrc }}">
        @endif
    </div>
    @endif

    <!-- HEADER -->
    <table class="header-table">
        <tr>


            <td width="60%">

                @php
                if(isset($is_logo) && $is_logo == true){
                $logoSrc = $logo_data_uri ?? $logo_url ?? ($company->avatar_url ?? null);
                } else {
                $logoSrc = '#';
                }
                @endphp

                <img src="{{ $logoSrc }}" class="logo">

                <div class="invoice-title">
                    @if($invoice->invoice_number)
                    FACTURE
                    @elseif($invoice->quote_number)
                    DEVIS
                    @endif
                </div>

                <div class="client-info">

                    @if($invoice->client)
                    Client : {{ $invoice->client->client_name }}<br>

                    @if($invoice->client->company_name)
                    Company : {{ $invoice->client->company_name }}<br>
                    @endif

                    @if($invoice->client->city || $invoice->client->postal_code)
                    Adresse : {{ $invoice->client->city ?? '' }} - {{ $invoice->client->postal_code ?? ''}}<br>
                    @endif

                    @if($invoice->client->telephone)
                    Tél : {{ $invoice->client->telephone }}
                    @endif

                    @endif

                </div>

            </td>


            <td width="40%">


                @if($company->contact)
                <i class="fa fa-phone"></i>
                {{ $company->contact }}
                @endif<br>

                @if($company->email)
                <i class="fa fa-envelope"></i>
                {{ $company->email }}
                @endif<br>

                @if($company->website)
                <i class="fa fa-globe"></i>
                {{ $company->website }}
                @endif<br>

                @if($company->address)
                <i class="fa fa-map-marker"></i>
                {{ $company->address }}
                @endif<br>


                <br>

                @if($invoice->invoice_number)
                <strong>N° de facture :</strong> {{ $invoice->invoice_number }}<br>
                @elseif($invoice->quote_number)
                <strong>N° de devis :</strong> {{ $invoice->quote_number }}<br>
                @endif
                <strong>Date de facturation :</strong>
                {{ $invoice->date ? $invoice->date->format('d/m/Y') : '' }}

                @if($invoice->payment_method)
                <br>
                <strong>Mode de paiement :</strong> {{ $invoice->payment_method }}
                @endif

                <br>
                <br>

                <strong>CNSS :</strong> {{ $company->cnss }}<br>
                <strong>RIB :</strong> {{ $company->rib }}<br>

                <!-- @if($invoice->status)
<br>
<strong>Statut :</strong> {{ $invoice->status }}
@endif -->

            </td>

        </tr>
    </table>


    <!-- ARTICLES -->

    <table class="invoice-table">

        <thead>
            <tr>
                <th width="10%" style="background:{{ $pdfColor }};">QTE</th>
                <th width="40%" style="background:{{ $pdfColor }};">DESIGNATION</th>
                <th width="40%" style="background:{{ $pdfColor }};">UNIT</th>
                <th width="25%" style="background:{{ $pdfColor }};">PRIX UNIT HT</th>
                <th width="25%" style="background:{{ $pdfColor }};">MONTANT HT</th>
            </tr>
        </thead>

        <tbody>

            @foreach($invoice->articles as $article)

            <tr>
                <td>{{ $article->quantity }}</td>

                <td>{{ $article->designation }}</td>
                <td>{{ $article->product->unit->name ?? '' }}</td>

                <td>
                    MAD {{ number_format($article->unit_price_ht,2,',',' ') }}
                </td>

                <td>
                    MAD {{ number_format($article->total_price_ht,2,',',' ') }}
                </td>
            </tr>

            @endforeach

        </tbody>
    </table>


    <!-- TOTALS -->

    <table class="totals-table">

        <tr>
            <td><strong>TOTAL HT</strong></td>
            <td>
                MAD {{ number_format($totals['total_ht'],2,',',' ') }}
            </td>
        </tr>

        <tr>
            <td><strong>After Discount</strong></td>
            <td>
                MAD {{ number_format($totals['afterDiscount'],2,',',' ') }}
            </td>
        </tr>

        <tr>
            <td><strong>Average TVA<span>({{ $totals['average_tva_percentage'] }}%)</span></strong></td>
            <td>
                MAD {{ number_format($totals['total_tva'],2,',',' ') }}
            </td>
        </tr>

        <tr class="total-final" style="background:{{ $pdfColor }};">
            <td><strong>TOTAL TTC</strong></td>
            <td>
                MAD {{ number_format($totals['total_ttc'],2,',',' ') }}
            </td>
        </tr>

    </table>


    <!-- SIGNATURE -->

    <div class="signature">

        @php
        if(isset($is_logo) && $is_logo == true){
        $sigSrc = $signature_data_uri ?? $signature_url ?? ($company->signature_url ?? null);
        } else {
        $sigSrc = '#';
        }
        @endphp

        <img src="{{ $sigSrc }}" alt="Signature">

    </div>


    <!-- FOOTER -->

    <div class="footer" style="background:{{ $pdfColor }};">

        @if($company->contact)
        TELE : {{ $company->contact }}
        @endif

        @if($company->fax)
        | FAX : {{ $company->fax }}
        @endif

        <br>

        @if($company->vat_number)
        TVA : {{ $company->vat_number }}
        @endif

        @if($company->rc_number)
        | RC : {{ $company->rc_number }}
        @endif

        @if($company->ice_number)
        | ICE : {{ $company->ice_number }}
        @endif

        <br><br>

        Merci de Votre Confiance

    </div>


</body>

</html>