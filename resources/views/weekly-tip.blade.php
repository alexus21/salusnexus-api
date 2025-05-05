<!DOCTYPE html>
<html>
    <head>
        <title>{{ $details['subject'] }}</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap');

            /* Estilos base que algunos clientes pueden respetar */
            * { margin: 0; padding: 0; }
            body {
                font-family: 'Outfit', Arial, sans-serif;
                background-color: #f8f9fb;
                margin: 0;
                padding: 0;
                color: #333;
                width: 100%;
                line-height: 1.6;
            }

            .wrapper {
                max-width: 1000px;
                background-color: #ffffff;
                border-radius: 24px;
                overflow: hidden;
                box-shadow: 0 16px 40px rgba(0, 0, 0, 0.12);
            }

            .mobile-padding {
                padding: 60px 40px;
            }

            .header {
                background-color: #2a4785;
                background-image: radial-gradient(circle at 10% 20%, rgba(255, 255, 255, 0.15) 25px, transparent 25px), radial-gradient(circle at 90% 80%, rgba(255, 255, 255, 0.15) 35px, transparent 35px), radial-gradient(circle at 80% 10%, rgba(255, 255, 255, 0.1) 45px, transparent 45px);
                position: relative;
            }

            .header-logo {
                margin-bottom: 30px;
                position: relative;
                z-index: 10;
            }

            .header-logo img {
                width: 220px;
                height: auto;
                filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.2));
            }

            .header h1 {
                color: #ffffff;
                font-size: 36px;
                font-weight: 700;
                margin: 0;
                line-height: 1.3;
                position: relative;
                z-index: 2;
                text-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);
            }

            .greeting {
                background: linear-gradient(to right, #f0f4ff, #e8eeff);
                padding: 40px 50px;
                border-left: 6px solid #3f5fad;
                position: relative;
                z-index: 1;
                box-shadow: 0 6px 16px rgba(63, 95, 173, 0.08);
            }

            .greeting h2 {
                font-size: 26px;
                font-weight: 600;
                color: #2a4785;
                margin-bottom: 15px;
                position: relative;
            }

            .greeting p {
                margin: 0;
                color: #333;
            }

            .tip-content {
                padding: 50px;
                background-color: #ffffff;
                position: relative;
                z-index: 1;
            }

            .tip-header {
                margin-bottom: 30px;
                display: flex;
                align-items: center;
            }

            .tip-header img {
                margin-right: 15px;
            }

            .tip-header h3 {
                font-size: 24px;
                font-weight: 600;
                color: #2a4785;
                margin: 0;
            }

            .tip-box {
                background-color: #f8f9ff;
                padding: 35px;
                border-radius: 16px;
                margin-bottom: 40px;
                border: 1px solid #e0e6ff;
                font-size: 17px;
                color: #444;
                position: relative;
                box-shadow: 0 8px 20px rgba(63, 95, 173, 0.08);
                line-height: 1.7;
            }

            .tip-box p {
                position: relative;
                z-index: 1;
            }

            .profile-link {
                text-align: center;
                margin: 40px 0;
                position: relative;
                z-index: 1;
            }

            .profile-link a {
                display: inline-block;
                background: linear-gradient(135deg, #3f5fad 0%, #2a4785 100%);
                color: white;
                text-decoration: none;
                padding: 18px 40px;
                border-radius: 12px;
                font-weight: 600;
                font-size: 17px;
                box-shadow: 0 8px 20px rgba(42, 71, 133, 0.3);
            }

            .wellness-section {
                background: linear-gradient(135deg, #f0f4ff 0%, #e8eeff 100%);
                padding: 50px;
                border-radius: 0 100px 0 0;
                margin-top: 20px;
                position: relative;
                overflow: hidden;
                z-index: 1;
            }

            .wellness-header {
                margin-bottom: 30px;
                display: flex;
                align-items: center;
            }

            .wellness-header img {
                margin-right: 15px;
            }

            .wellness-header h3 {
                font-size: 24px;
                font-weight: 600;
                color: #2a4785;
                margin: 0;
            }

            .wellness-tip {
                background: white;
                padding: 30px;
                border-radius: 16px;
                box-shadow: 0 10px 25px rgba(63, 95, 173, 0.12);
                margin-bottom: 20px;
            }

            .tip-title {
                font-weight: 600;
                color: #2a4785;
                margin-bottom: 15px;
                display: flex;
                align-items: center;
                font-size: 18px;
            }

            .tip-number {
                width: 32px;
                height: 32px;
                background: #3f5fad;
                border-radius: 50%;
                color: white;
                display: inline-block;
                text-align: center;
                line-height: 32px;
                margin-right: 12px;
                font-size: 16px;
                box-shadow: 0 4px 8px rgba(63, 95, 173, 0.25);
            }

            .tip-content p {
                color: #555;
                font-size: 16px;
                line-height: 1.6;
                margin: 0;
            }

            .footer {
                background-color: #2a4785;
                padding: 60px 40px;
                text-align: center;
                color: #ffffff;
                position: relative;
                overflow: hidden;
            }

            .footer-logo {
                margin-bottom: 30px;
                position: relative;
                z-index: 2;
            }

            .footer-logo img {
                width: 180px;
                height: auto;
                filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.2));
            }

            .social-icons {
                margin-bottom: 35px;
                position: relative;
                z-index: 2;
                text-align: center;
            }

            .social-icon {
                display: inline-block;
                margin: 0 10px;
            }

            .social-icon img {
                border-radius: 50%;
                background-color: rgba(255,255,255,0.1);
                padding: 10px;
                box-sizing: border-box;
            }

            .footer-links {
                margin-bottom: 35px;
                position: relative;
                z-index: 2;
            }

            .footer-links a {
                color: rgba(255, 255, 255, 0.8);
                text-decoration: none;
                font-size: 16px;
                margin: 0 15px;
            }

            .footer-copyright {
                font-size: 15px;
                color: rgba(255, 255, 255, 0.7);
                max-width: 600px;
                margin: 0 auto;
                line-height: 1.6;
                position: relative;
                z-index: 2;
            }

            @media only screen and (max-width: 600px) {
                .wrapper { width: 100% !important; margin: 10px !important; }
                .mobile-padding { padding: 30px 20px !important; }
                .wellness-tips { display: block !important; }
                .wellness-tip { width: 100% !important; margin-bottom: 20px !important; }
                .social-icons { text-align: center !important; }
                .social-icon { display: inline-block !important; margin: 0 5px !important; }
                .footer-links a { display: block !important; margin: 10px 0 !important; }
            }
        </style>
    </head>
    <body>
        <table width="100%" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td align="center" style="padding: 20px 0;">
                    <table class="wrapper" width="85%" cellpadding="0" cellspacing="0" border="0">
                        <!-- HEADER -->
                        <tr>
                            <td class="mobile-padding header" align="center">
                                <div class="header-logo">
                                    <img src="https://cdn.jsdelivr.net/gh/alexus21/salusnexus-api@master/public/images/logos/salusnexus-logo-primary.png?raw=true" alt="SalusNexus">
                                </div>
                                <h1>Consejos semanales para tu salud</h1>
                            </td>
                        </tr>

                        <!-- GREETING -->
                        <tr>
                            <td class="mobile-padding greeting">
                                <h2>{{ $details['subject'] }}</h2>
                                <p>{{ $details['greeting'] }}</p>
                            </td>
                        </tr>

                        <!-- TIP CONTENT -->
                        <tr>
                            <td class="mobile-padding tip-content">
                                <div class="tip-header">
                                    <img src="https://cdn-icons-png.flaticon.com/128/2088/2088617.png" width="28" height="28" alt="Ícono consejo">
                                    <h3>Consejo de esta semana</h3>
                                </div>

                                <div class="tip-box">
                                    <p>{{ $details['tip'] }}</p>
                                </div>

                                <div class="profile-link">
                                    <a href="https://patients.salusnexus.online">Accede a tu perfil</a>
                                </div>
                            </td>
                        </tr>

                        <!-- WELLNESS SECTION -->
                        <tr>
                            <td class="mobile-padding wellness-section">
                                <div class="wellness-header">
                                    <img src="https://cdn-icons-png.flaticon.com/128/3004/3004458.png" width="28" height="28" alt="Ícono bienestar">
                                    <h3>Consejos rápidos de bienestar</h3>
                                </div>

                                <table class="wellness-tips" width="100%" cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td width="48%" valign="top" style="padding-right: 15px;">
                                            <div class="wellness-tip">
                                                <div class="tip-title">
                                                    <span class="tip-number">1</span>
                                                    Hidratación
                                                </div>
                                                <p>Bebe al menos 8 vasos de agua al día para mantener tu cuerpo bien hidratado.</p>
                                            </div>
                                        </td>
                                        <td width="48%" valign="top" style="padding-left: 15px;">
                                            <div class="wellness-tip">
                                                <div class="tip-title">
                                                    <span class="tip-number">2</span>
                                                    Descanso
                                                </div>
                                                <p>Procura dormir entre 7-8 horas diarias para una óptima recuperación física y mental.</p>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>

                        <!-- FOOTER -->
                        <tr>
                            <td class="mobile-padding footer">
                                <div class="footer-logo">
                                    <img src="https://cdn.jsdelivr.net/gh/alexus21/salusnexus-api@master/public/images/logos/salusnexus-logo-white.png?raw=true" alt="SalusNexus">
                                </div>

                                <div class="social-icons">
                                    <a href="#" class="social-icon">
                                        <img src="https://cdn.brandfetch.io/idpKX136kp/w/800/h/800/theme/light/symbol.png?c=1dxbfHSJFAPEGdCLU4o5B" width="48" height="48" alt="Facebook">
                                    </a>
                                    <a href="#" class="social-icon">
                                        <img src="https://cdn.brandfetch.io/idS5WhqBbM/w/800/h/723/theme/light/logo.png?c=1dxbfHSJFAPEGdCLU4o5B" width="48" height="48" alt="Twitter">
                                    </a>
                                    <a href="#" class="social-icon">
                                        <img src="https://cdn.brandfetch.io/ido5G85nya/w/800/h/800/theme/light/idmP9VWUNi.png?c=1dxbfHSJFAPEGdCLU4o5B" width="48" height="48" alt="Instagram">
                                    </a>
                                    <a href="#" class="social-icon">
                                        <img src="https://cdn.brandfetch.io/idJFz6sAsl/w/800/h/683/theme/dark/id745SkyD0.png?c=1dxbfHSJFAPEGdCLU4o5B" width="48" height="48" alt="LinkedIn">
                                    </a>
                                </div>

                                <div class="footer-links">
                                    <a href="#">Términos y Condiciones</a>
                                    <a href="#">Política de Privacidad</a>
                                    <a href="#">Preferencias</a>
                                    <a href="#">Contacto</a>
                                </div>

                                <p class="footer-copyright">&copy; {{ date('Y') }} SalusNexus - Tu plataforma de salud personal. Todos los derechos reservados.</p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
</html>
