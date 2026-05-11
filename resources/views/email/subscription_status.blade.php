<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rappel d’abonnement</title>
</head>

<body style="margin:0; padding:0; background-color:#f3f4f6; font-family:Arial, Helvetica, sans-serif;">

<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#f3f4f6">
    <tr>
        <td align="center" style="padding:30px 10px;">

            <!-- Container -->
            <table width="600" border="0" cellspacing="0" cellpadding="0" bgcolor="#ffffff">

                <!-- Header -->
                <tr>
                    <td bgcolor="#0d6efd" align="center" style="padding:25px;">

                        <span style="color:#ffffff; font-size:30px; font-weight:bold;">
                            {{ $title }}
                        </span>

                    </td>
                </tr>

                <!-- Content -->
                <tr>
                    <td style="padding:40px 30px; color:#333333; font-size:16px; line-height:28px;">

                        <p style="margin-top:0;">
                            Bonjour <strong>{{ $customer_name }}</strong>,
                        </p>

                        <div style="color:#555555;">
                            {!! $message_body !!}
                        </div>

                        <!-- Subscription Date -->
                        <table width="100%" border="0" cellspacing="0" cellpadding="0"
                               bgcolor="#f8f9fa"
                               style="margin-top:30px; border:1px solid #dddddd;">

                            <tr>
                                <td style="padding:15px; font-size:15px; color:#333333;">

                                    <strong>Date de fin d’abonnement :</strong>

                                    <span style="color:#dc3545; font-weight:bold;">
                                        {{ $ends_at }}
                                    </span>

                                </td>
                            </tr>

                        </table>

                        @if($type == 'service_stopped')
                        <!-- Warning -->
                        <table width="100%" border="0" cellspacing="0" cellpadding="0"
                               bgcolor="#fff3cd"
                               style="margin-top:25px; border-left:5px solid #ffc107;">

                            <tr>
                                <td style="padding:18px; color:#856404; font-size:14px; line-height:24px;">

                                    <strong>Service suspendu</strong>
                                    <br><br>

                                    Votre abonnement a expiré et l’accès à vos services a été temporairement suspendu.

                                    Veuillez renouveler votre abonnement afin de réactiver votre accès.

                                </td>
                            </tr>

                        </table>
                        @endif

                        @if(isset($upgrade_url))
                        <!-- Button -->
                        <table width="100%" border="0" cellspacing="0" cellpadding="0"
                               style="margin-top:35px;">

                            <tr>
                                <td align="center">

                                    <p style="font-size:14px; color:#666666; line-height:24px;">

                                        Cliquez sur le bouton ci-dessous pour renouveler ou mettre à niveau votre abonnement.

                                        <br>

                                        Ce lien restera valide pendant les 10 prochains jours.

                                    </p>

                                    <table border="0" cellspacing="0" cellpadding="0" style="margin-top:20px;">
                                        <tr>
                                            <td bgcolor="#198754" align="center" style="padding:14px 30px;">

                                                <a href="{{ $upgrade_url }}"
                                                   style="color:#ffffff;
                                                          text-decoration:none;
                                                          font-size:16px;
                                                          font-weight:bold;
                                                          display:inline-block;">

                                                    Mettre à niveau l’abonnement

                                                </a>

                                            </td>
                                        </tr>
                                    </table>

                                </td>
                            </tr>

                        </table>
                        @endif

                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td bgcolor="#f8f9fa"
                        style="padding:25px 30px; border-top:1px solid #dddddd;">

                        <p style="margin:0 0 10px; font-size:13px; color:#777777; line-height:22px;">

                            Ceci est un e-mail automatique envoyé par
                            <strong>{{ $app_name }}</strong>.

                            Merci de ne pas répondre à ce message.

                        </p>

                        <p style="margin:0; font-size:12px; color:#999999;">

                            &copy; {{ date('Y') }} {{ $app_name }}. Tous droits réservés.

                        </p>

                    </td>
                </tr>

            </table>

        </td>
    </tr>
</table>

</body>
</html>