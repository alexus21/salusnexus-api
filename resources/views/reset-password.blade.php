<!DOCTYPE html>
<html>
<head>
    <title>{{ $details['subject'] }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        
        * {
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            color: #333;
            width: 100%;
            height: 100%;
        }
        
        .wrapper {
            max-width: 95%;
            width: 95%;
            margin: 0 auto;
            background-color: #ffffff;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        
        .header {
            background-color: #343a87;
            padding: 30px 20px;
            text-align: center;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
        }
        
        .header-text {
            color: #ffffff;
            font-size: 24px;
            font-weight: 600;
            margin: 0;
            word-break: break-word;
        }
        
        .main-content {
            padding: 40px 30px;
            background-color: #ffffff;
        }
        
        .alert-banner {
            background-color: #f6f8ff;
            border-left: 4px solid #343a87;
            padding: 20px;
            margin-bottom: 30px;
            border-radius: 0 8px 8px 0;
        }
        
        .alert-title {
            color: #343a87;
            font-size: 26px;
            font-weight: 700;
            margin-top: 0;
            margin-bottom: 20px;
            text-align: center;
            word-break: break-word;
        }
        
        .alert-message {
            line-height: 1.7;
            font-size: 16px;
            margin-bottom: 25px;
            white-space: pre-line;
            color: #555;
        }
        
        .info-box {
            background-color: #f0f3ff;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        
        .info-label {
            font-weight: 600;
            color: #343a87;
            margin-right: 10px;
        }
        
        .info-value {
            color: #444;
            word-break: break-word;
        }
        
        .reset-button {
            display: block;
            background-color: #343a87;
            color:rgb(255, 255, 255);
            text-align: center;
            padding: 16px 24px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            margin: 30px auto;
            width: 80%;
            max-width: 300px;
            font-size: 18px;
            transition: background-color 0.3s;
        }
        
        .reset-button:hover {
            background-color: #292e6c;
        }
        
        .divider {
            height: 1px;
            background-color: #e1e1e1;
            margin: 30px 0;
        }
        
        .security-notes {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .security-note {
            flex: 1 1 100%;
            background-color: #f8f9ff;
            padding: 20px;
            border-radius: 8px;
            border-left: 3px solid #343a87;
        }
        
        .note-title {
            color: #343a87;
            font-weight: 600;
            margin-top: 0;
            margin-bottom: 10px;
        }
        
        .security-icon {
            font-size: 24px;
            margin-bottom: 10px;
            color: #343a87;
        }
        
        .expiry-notice {
            background-color: #fff8e6;
            border-left: 3px solid #ffb100;
            padding: 15px;
            margin-top: 25px;
            border-radius: 0 8px 8px 0;
        }
        
        .footer {
            background-color: #343a87;
            padding: 20px;
            text-align: center;
            color: #ffffff;
            border-bottom-left-radius: 8px;
            border-bottom-right-radius: 8px;
        }
        
        .help-text {
            background-color: #f0f3ff;
            padding: 15px;
            border-radius: 8px;
            margin-top: 25px;
            font-size: 14px;
            color: #666;
        }
        
        .help-text p:last-child {
            word-break: break-all;
        }
        
        .social-links {
            margin-bottom: 15px;
        }
        
        .social-link {
            display: inline-block;
            margin: 0 10px;
            color: #ffffff;
            text-decoration: none;
        }
        
        .footer-text {
            font-size: 14px;
            margin: 0;
        }
        
        /* Media queries para responsividad */
        @media only screen and (min-width: 768px) {
            .wrapper {
                max-width: 90%;
            }
            
            .security-note {
                flex: 1 1 45%;
            }
        }
        
        @media only screen and (min-width: 992px) {
            .wrapper {
                max-width: 85%;
            }
        }
        
        @media only screen and (min-width: 1200px) {
            .wrapper {
                max-width: 80%;
            }
        }
        
        @media only screen and (max-width: 576px) {
            .main-content {
                padding: 30px 15px;
            }
            
            .header-text, .alert-title {
                font-size: 20px;
            }
            
            .alert-banner, .info-box, .security-note, .expiry-notice {
                padding: 15px;
            }
            
            .reset-button {
                width: 100%;
                padding: 14px 20px;
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <h1 class="header-text">{{ $details['subject'] }}</h1>
        </div>
        
        <div class="main-content">
            <div class="alert-banner">
                <h2 class="alert-title">Cambio de Contraseña</h2>
                <p class="alert-message">{{ $details['message'] }}</p>
            </div>
            
            <div class="info-box">
                <p><span class="info-label">Para:</span> <span class="info-value">{{ $details['email'] }}</span></p>
                <p><span class="info-label">Fecha:</span> <span class="info-value">{{ date('d/m/Y H:i') }}</span></p>
            </div>
            
            <a href="{{ $details['reset_link'] }}" class="reset-button">Cambiar mi Contraseña</a>
            
            <div class="expiry-notice">
                <p><strong>⚠️ Importante:</strong> Este enlace expirará en 60 minutos por razones de seguridad.</p>
            </div>
            
            <div class="divider"></div>
            
            <div class="security-notes">
                <div class="security-note">
                    <div class="security-icon">🔒</div>
                    <h3 class="note-title">Recomendaciones</h3>
                    <p>Usa una contraseña fuerte con combinación de letras, números y símbolos. No la compartas con nadie.</p>
                </div>
                <div class="security-note">
                    <div class="security-icon">⚠️</div>
                    <h3 class="note-title">¿No solicitaste este cambio?</h3>
                    <p>Si no has solicitado este cambio, por favor contacta inmediatamente con nuestro equipo de soporte.</p>
                </div>
            </div>
            
            <div class="help-text">
                <p>Si el botón no funciona, copia y pega el siguiente enlace en tu navegador:</p>
                <p style="color: #343a87;">{{ $details['reset_link'] }}</p>
            </div>
        </div>
        
        <div class="footer">
            <div class="social-links">
                <a href="#" class="social-link">Facebook</a>
                <a href="#" class="social-link">Twitter</a>
                <a href="#" class="social-link">Instagram</a>
            </div>
            <p class="footer-text">&copy; {{ date('Y') }} SalusNexus - Todos los derechos reservados</p>
        </div>
    </div>
</body>
</html> 