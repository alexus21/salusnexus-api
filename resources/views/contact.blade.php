<!DOCTYPE html>
<html>
<head>
    <title>{{ $details['subject'] }}</title>
    {{--        <title>Prueba</title>--}}
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Poppins', Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            color: #333;
        }
        
        .wrapper {
            max-width: 650px;
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
        }
        
        .main-content {
            padding: 40px 30px;
            background-color: #ffffff;
        }
        
        .welcome-banner {
            background-color: #f6f8ff;
            border-left: 4px solid #343a87;
            padding: 20px;
            margin-bottom: 30px;
            border-radius: 0 8px 8px 0;
        }
        
        .welcome-title {
            color: #343a87;
            font-size: 26px;
            font-weight: 700;
            margin-top: 0;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .welcome-message {
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
        }
        
        .cta-button {
            display: block;
            background-color: #343a87;
            color: #ffffff;
            text-align: center;
            padding: 16px 24px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            margin: 30px auto;
            width: 200px;
        }
        
        .divider {
            height: 1px;
            background-color: #e1e1e1;
            margin: 30px 0;
        }
        
        .features {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .feature {
            flex: 1 1 40%;
            background-color: #f8f9ff;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
        }
        
        .feature-title {
            color: #343a87;
            font-weight: 600;
            margin-top: 0;
            margin-bottom: 10px;
        }
        
        .rating {
            color: #ffc107;
            font-size: 20px;
            margin-top: 20px;
            letter-spacing: 2px;
        }
        
        .footer {
            background-color: #343a87;
            padding: 20px;
            text-align: center;
            color: #ffffff;
            border-bottom-left-radius: 8px;
            border-bottom-right-radius: 8px;
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
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <h1 class="header-text">{{ $details['subject'] }}</h1>
        </div>
        
        <div class="main-content">
            <div class="welcome-banner">
                <h2 class="welcome-title">¡Bienvenido a SalusNexus!</h2>
                <p class="welcome-message">{{ $details['message'] }}</p>
            </div>
            
            <div class="info-box">
                <p><span class="info-label">De:</span> <span class="info-value">{{ $details['name'] }}</span></p>
                <p><span class="info-label">Para:</span> <span class="info-value">{{ $details['email'] }}</span></p>
            </div>
            
            <a href="https://salusnexus.online" class="cta-button">Explorar SalusNexus</a>
            
            <div class="divider"></div>
            
            <div class="features">
                <div class="feature">
                    <h3 class="feature-title">Encuentra médicos</h3>
                    <p>Conecta con los mejores profesionales de salud en tu área.</p>
                </div>
                <div class="feature">
                    <h3 class="feature-title">Agenda citas</h3>
                    <p>Reserva consultas médicas de forma rápida y sencilla.</p>
                </div>
                <div class="feature">
                    <h3 class="feature-title">Historial médico</h3>
                    <p>Mantén un registro completo de tu historial médico.</p>
                </div>
                <div class="feature">
                    <h3 class="feature-title">Reseñas</h3>
                    <p>Califica y lee opiniones sobre profesionales de salud.</p>
                    <div class="rating">★★★★★</div>
                </div>
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
