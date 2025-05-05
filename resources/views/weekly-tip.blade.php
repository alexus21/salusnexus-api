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
            body { font-family: 'Outfit', Arial, sans-serif; }

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
    <body style="font-family: 'Outfit', Arial, sans-serif; background-color: #f8f9fb; margin: 0; padding: 0; color: #333; width: 100%; line-height: 1.6;">
        <table width="100%" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td align="center" style="padding: 20px 0;">
                    <table class="wrapper" width="85%" style="max-width: 1000px; background-color: #ffffff; border-radius: 24px; overflow: hidden; box-shadow: 0 16px 40px rgba(0, 0, 0, 0.12);" cellpadding="0" cellspacing="0" border="0">
                        <!-- HEADER -->
                        <tr>
                            <td class="mobile-padding" align="center" style="background-color: #2a4785; background-image: radial-gradient(circle at 10% 20%, rgba(255, 255, 255, 0.15) 25px, transparent 25px), radial-gradient(circle at 90% 80%, rgba(255, 255, 255, 0.15) 35px, transparent 35px), radial-gradient(circle at 80% 10%, rgba(255, 255, 255, 0.1) 45px, transparent 45px); padding: 60px 40px; position: relative;">
                                <div style="margin-bottom: 30px; position: relative; z-index: 10;">
                                    <img src="{{ asset('images/logos/salusnexus-logo-primary.png') }}" alt="SalusNexus" style="width: 220px; height: auto; filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.2));">
                                    <img src="https://videos.openai.com/vg-assets/assets%2Ftask_01jtfb6npxfspb2n6931pvjg7n%2F1746419312_img_1.webp?st=2025-05-05T03%3A47%3A35Z&se=2025-05-11T04%3A47%3A35Z&sks=b&skt=2025-05-05T03%3A47%3A35Z&ske=2025-05-11T04%3A47%3A35Z&sktid=a48cca56-e6da-484e-a814-9c849652bcb3&skoid=8ebb0df1-a278-4e2e-9c20-f2d373479b3a&skv=2019-02-02&sv=2018-11-09&sr=b&sp=r&spr=https%2Chttp&sig=jKpAm3b3%2BB5Eg6YH%2BWg%2Bp3rnnoUkXXg2h4VHWTG9MRA%3D&az=oaivgprodscus" alt="SalusNexus" style="width: 220px; height: auto; filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.2));">
                                </div>
                                <h1 style="color: #ffffff; font-size: 36px; font-weight: 700; margin: 0; line-height: 1.3; position: relative; z-index: 2; text-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);">Consejos semanales para tu salud</h1>
                            </td>
                        </tr>

                        <!-- GREETING -->
                        <tr>
                            <td class="mobile-padding" style="background: linear-gradient(to right, #f0f4ff, #e8eeff); padding: 40px 50px; border-left: 6px solid #3f5fad; position: relative; z-index: 1; box-shadow: 0 6px 16px rgba(63, 95, 173, 0.08);">
                                <h2 style="font-size: 26px; font-weight: 600; color: #2a4785; margin-bottom: 15px; position: relative;">{{ $details['subject'] }}</h2>
                                <p style="margin: 0; color: #333;">{{ $details['greeting'] }}</p>
                            </td>
                        </tr>

                        <!-- TIP CONTENT -->
                        <tr>
                            <td class="mobile-padding" style="padding: 50px; background-color: #ffffff; position: relative; z-index: 1;">
                                <div style="margin-bottom: 30px; display: flex; align-items: center;">
                                    <img src="{{ asset('images/icons/icon-tip.png') }}" width="28" height="28" style="margin-right: 15px;" alt="Ícono consejo">
                                    <h3 style="font-size: 24px; font-weight: 600; color: #2a4785; margin: 0;">Consejo de esta semana</h3>
                                </div>

                                <div style="background-color: #f8f9ff; padding: 35px; border-radius: 16px; margin-bottom: 40px; border: 1px solid #e0e6ff; font-size: 17px; color: #444; position: relative; box-shadow: 0 8px 20px rgba(63, 95, 173, 0.08); line-height: 1.7;">
                                    <p style="position: relative; z-index: 1;">{{ $details['tip'] }}</p>
                                </div>

                                <div style="text-align: center; margin: 40px 0; position: relative; z-index: 1;">
                                    <a href="https://salusnexus.com/tu-perfil" style="display: inline-block; background: linear-gradient(135deg, #3f5fad 0%, #2a4785 100%); color: white; text-decoration: none; padding: 18px 40px; border-radius: 12px; font-weight: 600; font-size: 17px; box-shadow: 0 8px 20px rgba(42, 71, 133, 0.3);">Accede a tu perfil</a>
                                </div>
                            </td>
                        </tr>

                        <!-- WELLNESS SECTION -->
                        <tr>
                            <td class="mobile-padding" style="background: linear-gradient(135deg, #f0f4ff 0%, #e8eeff 100%); padding: 50px; border-radius: 0 100px 0 0; margin-top: 20px; position: relative; overflow: hidden; z-index: 1;">
                                <div style="margin-bottom: 30px; display: flex; align-items: center;">
                                    <img src="{{ asset('images/icons/icon-wellness.png') }}" width="28" height="28" style="margin-right: 15px;" alt="Ícono bienestar">
                                    <h3 style="font-size: 24px; font-weight: 600; color: #2a4785; margin: 0;">Consejos rápidos de bienestar</h3>
                                </div>

                                <table class="wellness-tips" width="100%" cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td width="48%" valign="top" style="padding-right: 15px;">
                                            <div class="wellness-tip" style="background: white; padding: 30px; border-radius: 16px; box-shadow: 0 10px 25px rgba(63, 95, 173, 0.12); margin-bottom: 20px;">
                                                <div style="font-weight: 600; color: #2a4785; margin-bottom: 15px; display: flex; align-items: center; font-size: 18px;">
                                                    <span style="width: 32px; height: 32px; background: #3f5fad; border-radius: 50%; color: white; display: inline-block; text-align: center; line-height: 32px; margin-right: 12px; font-size: 16px; box-shadow: 0 4px 8px rgba(63, 95, 173, 0.25);">1</span>
                                                    Hidratación
                                                </div>
                                                <p style="color: #555; font-size: 16px; line-height: 1.6; margin: 0;">Bebe al menos 8 vasos de agua al día para mantener tu cuerpo bien hidratado.</p>
                                            </div>
                                        </td>
                                        <td width="48%" valign="top" style="padding-left: 15px;">
                                            <div class="wellness-tip" style="background: white; padding: 30px; border-radius: 16px; box-shadow: 0 10px 25px rgba(63, 95, 173, 0.12);">
                                                <div style="font-weight: 600; color: #2a4785; margin-bottom: 15px; display: flex; align-items: center; font-size: 18px;">
                                                    <span style="width: 32px; height: 32px; background: #3f5fad; border-radius: 50%; color: white; display: inline-block; text-align: center; line-height: 32px; margin-right: 12px; font-size: 16px; box-shadow: 0 4px 8px rgba(63, 95, 173, 0.25);">2</span>
                                                    Descanso
                                                </div>
                                                <p style="color: #555; font-size: 16px; line-height: 1.6; margin: 0;">Procura dormir entre 7-8 horas diarias para una óptima recuperación física y mental.</p>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>

                        <!-- FOOTER -->
                        <tr>
                            <td class="mobile-padding" style="background-color: #2a4785; padding: 60px 40px; text-align: center; color: #ffffff; position: relative; overflow: hidden;">
                                <div style="margin-bottom: 30px; position: relative; z-index: 2;">
                                    <img src="{{ asset('images/logos/salusnexus-logo-white.png') }}" alt="SalusNexus" style="width: 180px; height: auto; filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.2));">
                                </div>

                                <div class="social-icons" style="margin-bottom: 35px; position: relative; z-index: 2; text-align: center;">
                                    {{--<a href="#" class="social-icon" style="display: inline-block; margin: 0 10px;">
                                        <img src="{{ asset('images/social/facebook.png') }}" width="48" height="48" alt="Facebook" style="border-radius: 50%; background-color: rgba(255,255,255,0.1); padding: 10px; box-sizing: border-box;">
                                    </a>--}}
                                    <a href="#" class="social-icon" style="display: inline-block; margin: 0 10px;">
                                        <img src="https://cdn.brandfetch.io/idpKX136kp/w/800/h/800/theme/light/symbol.png?c=1dxbfHSJFAPEGdCLU4o5B" width="48" height="48" alt="Facebook" style="border-radius: 50%; background-color: rgba(255,255,255,0.1); padding: 10px; box-sizing: border-box;">
                                    </a>
                                    {{--<a href="#" class="social-icon" style="display: inline-block; margin: 0 10px;">
                                        <img src="{{ asset('images/social/twitter.svg') }}" width="48" height="48" alt="Twitter" style="border-radius: 50%; background-color: rgba(255,255,255,0.1); padding: 10px; box-sizing: border-box;">
                                    </a>--}}
                                    <a href="#" class="social-icon" style="display: inline-block; margin: 0 10px;">
                                        <img src="https://cdn.brandfetch.io/idS5WhqBbM/w/800/h/723/theme/light/logo.png?c=1dxbfHSJFAPEGdCLU4o5B" width="48" height="48" alt="Twitter" style="border-radius: 50%; background-color: rgba(255,255,255,0.1); padding: 10px; box-sizing: border-box;">
                                    </a>
                                    {{--<a href="#" class="social-icon" style="display: inline-block; margin: 0 10px;">
                                        <img src="{{ asset('images/social/instagram.svg') }}" width="48" height="48" alt="Instagram" style="border-radius: 50%; background-color: rgba(255,255,255,0.1); padding: 10px; box-sizing: border-box;">
                                    </a>--}}
                                    <a href="#" class="social-icon" style="display: inline-block; margin: 0 10px;">
                                        <img src="https://cdn.brandfetch.io/ido5G85nya/w/800/h/800/theme/light/idmP9VWUNi.png?c=1dxbfHSJFAPEGdCLU4o5B" width="48" height="48" alt="Instagram" style="border-radius: 50%; background-color: rgba(255,255,255,0.1); padding: 10px; box-sizing: border-box;">
                                    </a>
{{--                                    <a href="#" class="social-icon" style="display: inline-block; margin: 0 10px;">--}}
{{--                                        <img src="{{ asset('images/social/linkedin.svg') }}" width="48" height="48" alt="LinkedIn" style="border-radius: 50%; background-color: rgba(255,255,255,0.1); padding: 10px; box-sizing: border-box;">--}}
{{--                                    </a>--}}
                                    <a href="#" class="social-icon" style="display: inline-block; margin: 0 10px;">
                                        <img src="https://cdn.brandfetch.io/idJFz6sAsl/w/800/h/683/theme/dark/id745SkyD0.png?c=1dxbfHSJFAPEGdCLU4o5B" width="48" height="48" alt="LinkedIn" style="border-radius: 50%; background-color: rgba(255,255,255,0.1); padding: 10px; box-sizing: border-box;">
                                    </a>
                                </div>

                                <div class="footer-links" style="margin-bottom: 35px; position: relative; z-index: 2;">
                                    <a href="#" style="color: rgba(255, 255, 255, 0.8); text-decoration: none; font-size: 16px; margin: 0 15px;">Términos y Condiciones</a>
                                    <a href="#" style="color: rgba(255, 255, 255, 0.8); text-decoration: none; font-size: 16px; margin: 0 15px;">Política de Privacidad</a>
                                    <a href="#" style="color: rgba(255, 255, 255, 0.8); text-decoration: none; font-size: 16px; margin: 0 15px;">Preferencias</a>
                                    <a href="#" style="color: rgba(255, 255, 255, 0.8); text-decoration: none; font-size: 16px; margin: 0 15px;">Contacto</a>
                                </div>

                                <p style="font-size: 15px; color: rgba(255, 255, 255, 0.7); max-width: 600px; margin: 0 auto; line-height: 1.6; position: relative; z-index: 2;">&copy; {{ date('Y') }} SalusNexus - Tu plataforma de salud personal. Todos los derechos reservados.</p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
</html>
