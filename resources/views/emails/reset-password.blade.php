<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation du mot de passe</title>
</head>
<body style="margin:0; padding:0; background:#f5f6fa; font-family:Arial,sans-serif;">

<!-- Wrapper -->
<table width="100%" cellpadding="0" cellspacing="0" style="padding:20px;">
    <tr>
        <td align="center">

            <!-- Main Card -->
            <table width="600" style="background:#ffffff; border-radius:10px; overflow:hidden;">

                <!-- Header with logo -->
                <tr>
                    <td style="background:#0d6efd; padding:20px; text-align:center;">
                        <img src="{{env('frontend_url')}}/logo.png" width="120" alt="Logo" />
                    </td>
                </tr>

                <!-- Hero image -->
                <tr>
                    <td style="text-align:center; padding:20px;">
                        <img src="{{env('frontend_url')}}/images/reset-password-hero.png" width="250" alt="Reset Password" />
                    </td>
                </tr>

                <!-- Title -->
                <tr>
                    <td style="text-align:center; padding:10px 30px;">
                        <h2 style="color:#333; margin-bottom:10px;">Réinitialisez votre mot de passe</h2>
                        <p style="color:#555; font-size:16px; line-height:1.5;">
                            Bonjour {{ $user->name ?? '' }},<br>
                            Vous avez demandé à réinitialiser votre mot de passe. Cliquez sur le bouton ci-dessous pour le mettre à jour.
                        </p>
                    </td>
                </tr>

                <!-- Main CTA -->
                <tr>
                    <td align="center" style="padding:20px;">
                        <a href="{{ $url }}"
                           style="display:inline-block; background:#0d6efd; color:#fff;
                                  padding:15px 30px; text-decoration:none;
                                  border-radius:5px; font-weight:bold;">
                            Réinitialiser mon mot de passe
                        </a>
                    </td>
                </tr>

                <!-- Secondary CTA -->
                <tr>
                    <td style="text-align:center; padding:10px 30px;">
                        <p style="font-size:14px; color:#777;">
                            Vous pouvez aussi copier-coller ce lien dans votre navigateur :
                        </p>
                        <a href="{{ $url }}" style="font-size:14px; color:#0d6efd; word-break:break-all;">
                            {{ $url }}
                        </a>
                    </td>
                </tr>

                <!-- Recommended products (Jumia/Amazon style) -->
                <tr>
                    <td style="padding:20px;">
                        <h4 style="margin-bottom:10px; color:#333;">Nos produits populaires</h4>
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td align="center" width="33%" style="padding:5px;">
                                    <a href="{{env('frontend_url')}}/shop/1">
                                        <img src="https://tonsite.com/images/product1.jpg" width="120" alt="Produit 1" style="border-radius:5px;">
                                        <p style="color:#555; font-size:12px;">Produit 1</p>
                                    </a>
                                </td>
                                <td align="center" width="33%" style="padding:5px;">
                                    <a href="{{env('frontend_url')}}/product/2">
                                        <img src="https://tonsite.com/images/product2.jpg" width="120" alt="Produit 2" style="border-radius:5px;">
                                        <p style="color:#555; font-size:12px;">Produit 2</p>
                                    </a>
                                </td>
                                <td align="center" width="33%" style="padding:5px;">
                                    <a href="{{env('frontend_url')}}/product/3">
                                        <img src="{{env('frontend_url')}}/images/product3.jpg" width="120" alt="Produit 3" style="border-radius:5px;">
                                        <p style="color:#555; font-size:12px;">Produit 3</p>
                                    </a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td style="background:#f0f0f0; padding:20px; text-align:center; font-size:12px; color:#777;">
                        <p>
                            Ce lien expirera dans 60 minutes.<br>
                            Si vous n'avez pas demandé cette action, ignorez cet email.
                        </p>
                        <p>
                            &copy; {{ date('Y') }} TonApp. Tous droits réservés.
                        </p>
                    </td>
                </tr>

            </table>
            <!-- End Main Card -->

        </td>
    </tr>
</table>

</body>
</html>
