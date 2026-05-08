<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invitation à rejoindre</title>
</head>

<body style="margin:0; padding:0; background-color:#f4f6f9; font-family: Arial, Helvetica, sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f4f6f9; padding:40px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" border="0" style="background:#ffffff; border-radius:12px; overflow:hidden; box-shadow:0 2px 10px rgba(0,0,0,0.08);"> <!-- Header -->
                    <tr>
                        <td align="center" style="background:linear-gradient(135deg, #2563eb, #1d4ed8); padding:35px 20px;">
                            <h1 style="margin:0; color:#ffffff; font-size:28px; font-weight:bold;"> Vous êtes invité </h1>
                        </td>
                    </tr> <!-- Content -->
                    <tr>
                        <td style="padding:40px 35px; color:#374151;">
                            <p style="margin:0 0 20px; font-size:16px; line-height:26px;"> Bonjour <strong>{{ $customer->name }}</strong>, </p>
                            <p style="margin:0 0 20px; font-size:16px; line-height:26px;"> <strong>{{ $accountant->name }}</strong> vous a invité à vous connecter sous son compte comptable sur <strong>{{ config('app.name') }}</strong>. </p>
                            <p style="margin:0 0 30px; font-size:16px; line-height:26px;"> Cliquez sur le bouton ci-dessous pour accepter l’invitation et connecter votre compte en toute sécurité. </p> <!-- Button -->
                            <table cellpadding="0" cellspacing="0" border="0" align="center" style="margin:30px auto;">
                                <tr>
                                    <td align="center" bgcolor="#2563eb" style="border-radius:8px;"> <a href="{{ $url }}" style="display:inline-block; padding:14px 32px; color:#ffffff; text-decoration:none; font-size:16px; font-weight:bold; border-radius:8px;"> Accepter l’invitation </a> </td>
                                </tr>
                            </table> <!-- Expiry -->
                            <div style="background:#f9fafb; border-left:4px solid #2563eb; padding:15px 18px; border-radius:6px; margin-top:20px;">
                                <p style="margin:0; font-size:14px; line-height:22px; color:#6b7280;"> Ce lien d’invitation expirera dans <strong>48 heures</strong>. </p>
                            </div>
                            <p style="margin:30px 0 0; font-size:15px; line-height:24px; color:#6b7280;"> Si vous ne vous attendiez pas à recevoir cette invitation, vous pouvez ignorer cet e-mail en toute sécurité. </p>
                        </td>
                    </tr> <!-- Footer -->
                    <tr>
                        <td align="center" style="padding:25px 20px; background:#f9fafb; border-top:1px solid #e5e7eb;">
                            <p style="margin:0; font-size:14px; color:#9ca3af;"> © {{ date('Y') }} {{ config('app.name') }}. Tous droits réservés. </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>