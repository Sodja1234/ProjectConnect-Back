<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Activation de votre compte</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f2f4f6; color: #51545E;">
<table width="100%" cellpadding="0" cellspacing="0" role="presentation">
    <tr>
        <td align="center">
            <table width="570" cellpadding="0" cellspacing="0" role="presentation" style="background-color: #ffffff; margin: 20px auto; padding: 35px; border-radius: 6px;">
                <tr>
                    <td>
                        <h1 style="font-size: 22px; font-weight: bold; color: #333333;">Activation de votre compte</h1>
                        <p style="font-size: 16px; line-height: 1.5em;">
                            Bonjour <strong>{{ $user->name }}</strong>,
                        </p>
                        <p style="font-size: 16px; line-height: 1.5em;">
                            Merci de vous être inscrit(e) !<br>
                            Pour activer votre compte, veuillez confirmer votre adresse email en cliquant sur le bouton ci-dessous.
                        </p>

                        <!-- Bouton -->
                        <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="margin: 30px 0;">
                            <tr>
                                <td align="center">
                                    <a href="{{ $url }}"
                                       style="display: inline-block; background-color: #3869D4; color: #ffffff; padding: 12px 24px; font-size: 16px; border-radius: 4px; text-decoration: none;">
                                        Activer mon compte
                                    </a>
                                </td>
                            </tr>
                        </table>

                        <!-- OTP -->
                        <p style="font-size: 16px; margin-bottom: 10px;">
                            Si vous utilisez un appareil mobile ou une application ne supportant pas les liens, vous pouvez entrer ce code OTP pour vérifier votre adresse e-mail :
                        </p>
                        <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="background-color: #f2f4f6; border-radius: 5px; padding: 16px; margin: 20px 0;">
                            <tr>
                                <td align="center" style="font-size: 24px; font-weight: bold; color: #333333;">
                                     {{$otp}}
                                </td>
                            </tr>
                        </table>

                        <p style="font-size: 14px; color: #6B7280;">
                            Ce code expirera dans <strong>10 minutes</strong>.
                        </p>

                        <p style="font-size: 14px; color: #6B7280;">
                            Si vous n’avez pas créé de compte, aucune action n’est requise.
                        </p>

                        <p style="font-size: 14px; margin-top: 40px;">
                            Merci,<br>
                            <strong>{{ config('app.name') }}</strong>
                        </p>
                    </td>
                </tr>
            </table>

            <table width="570" cellpadding="0" cellspacing="0" role="presentation" align="center">
                <tr>
                    <td align="center" style="padding: 20px; font-size: 12px; color: #6B7280;">
                        &copy; {{ date('Y') }} {{ config('app.name') }}. Tous droits réservés.
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
