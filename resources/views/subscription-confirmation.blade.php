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
            background: linear-gradient(135deg, #343a87 0%, #5a61c5 100%);
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
        
        .premium-banner {
            background: linear-gradient(135deg, #f8f9ff 0%, #e6eaff 100%);
            border-left: 4px solid #343a87;
            padding: 25px;
            margin-bottom: 30px;
            border-radius: 0 8px 8px 0;
            text-align: center;
        }
        
        .premium-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }
        
        .premium-title {
            color: #343a87;
            font-size: 26px;
            font-weight: 700;
            margin-top: 0;
            margin-bottom: 20px;
            word-break: break-word;
        }
        
        .premium-message {
            line-height: 1.7;
            font-size: 16px;
            margin-bottom: 25px;
            color: #555;
        }
        
        .subscription-details {
            background-color: #f0f3ff;
            padding: 25px;
            border-radius: 8px;
            margin-bottom: 30px;
            border: 1px solid #e0e6ff;
        }
        
        .subscription-heading {
            color: #343a87;
            font-size: 20px;
            font-weight: 600;
            margin-top: 0;
            margin-bottom: 20px;
            border-bottom: 1px solid #d8e0ff;
            padding-bottom: 10px;
            word-break: break-word;
        }
        
        .detail-row {
            display: flex;
            margin-bottom: 15px;
            align-items: flex-start;
            flex-wrap: wrap;
        }
        
        .detail-icon {
            color: #343a87;
            font-size: 20px;
            margin-right: 15px;
            min-width: 25px;
            text-align: center;
        }
        
        .detail-label {
            font-weight: 600;
            color: #343a87;
            min-width: 140px;
            margin-right: 10px;
        }
        
        .detail-value {
            color: #444;
            flex: 1;
            min-width: 50%;
            word-break: break-word;
        }
        
        .highlight {
            font-weight: 600;
            color: #343a87;
        }
        
        .features-section {
            margin: 30px 0;
        }
        
        .features-title {
            color: #343a87;
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 20px;
            text-align: center;
            word-break: break-word;
        }
        
        .features-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            justify-content: center;
        }
        
        .feature-card {
            flex: 1 1 100%;
            min-width: 230px;
            background-color: #f8f9ff;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            border: 1px solid #e0e6ff;
            margin-bottom: 10px;
        }
        
        .feature-icon {
            font-size: 32px;
            color: #343a87;
            margin-bottom: 10px;
        }
        
        .feature-title {
            color: #343a87;
            font-weight: 600;
            margin-top: 0;
            margin-bottom: 10px;
            word-break: break-word;
        }
        
        .feature-description {
            color: #555;
            font-size: 14px;
            line-height: 1.6;
        }
        
        .cta-section {
            text-align: center;
            margin: 35px 0;
        }
        
        .cta-button {
            display: inline-block;
            background-color: #343a87;
            color: #ffffff;
            text-align: center;
            padding: 16px 30px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 18px;
            transition: background-color 0.3s;
            width: 80%;
            max-width: 300px;
        }
        
        .cta-button:hover {
            background-color: #292e6c;
        }
        
        .payment-badge {
            display: inline-block;
            background-color: #ebf8ff;
            color: #0086d3;
            font-size: 14px;
            font-weight: 600;
            padding: 8px 15px;
            border-radius: 20px;
            margin-top: 20px;
        }
        
        .testimonial {
            background-color: #f9f9f9;
            border-radius: 8px;
            padding: 20px;
            margin: 30px 0;
            font-style: italic;
            position: relative;
        }
        
        .testimonial:before {
            content: '"';
            font-size: 60px;
            position: absolute;
            left: 10px;
            top: -15px;
            color: #e0e0e0;
            font-family: serif;
        }
        
        .testimonial-content {
            padding-left: 20px;
            color: #555;
            line-height: 1.7;
        }
        
        .testimonial-author {
            text-align: right;
            margin-top: 10px;
            font-weight: 600;
            color: #343a87;
            font-style: normal;
        }
        
        .divider {
            height: 1px;
            background-color: #e1e1e1;
            margin: 30px 0;
        }
        
        .help-section {
            background-color: #f0f3ff;
            padding: 20px;
            border-radius: 8px;
            margin-top: 30px;
        }
        
        .help-title {
            color: #343a87;
            font-weight: 600;
            margin-top: 0;
            margin-bottom: 15px;
            font-size: 18px;
            word-break: break-word;
        }
        
        .contact-methods {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .contact-method {
            flex: 1 1 calc(50% - 15px);
            min-width: 200px;
        }
        
        .contact-label {
            font-weight: 600;
            color: #343a87;
            margin-bottom: 5px;
        }
        
        .footer {
            background: linear-gradient(135deg, #343a87 0%, #5a61c5 100%);
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
        
        /* Media queries para responsividad */
        @media only screen and (min-width: 768px) {
            .wrapper {
                max-width: 90%;
            }
            
            .feature-card {
                flex: 1 1 45%;
            }
            
            .detail-value {
                min-width: auto;
            }
            
            .contact-method {
                flex: 1 1 calc(50% - 15px);
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
                padding: 25px 15px;
            }
            
            .header-text, .premium-title {
                font-size: 20px;
            }
            
            .premium-banner, .subscription-details, .feature-card, .testimonial, .help-section {
                padding: 15px;
            }
            
            .detail-row {
                flex-direction: column;
                margin-bottom: 20px;
            }
            
            .detail-label {
                margin-bottom: 5px;
            }
            
            .detail-value {
                margin-top: 5px;
            }
            
            .cta-button {
                width: 100%;
                padding: 14px 20px;
                font-size: 16px;
            }
            
            .contact-method {
                flex: 1 1 100%;
            }
            
            .premium-icon {
                font-size: 36px;
            }
            
            .testimonial:before {
                font-size: 40px;
                left: 5px;
                top: -10px;
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
            <div class="premium-banner">
                <div class="premium-icon">🌟</div>
                <h2 class="premium-title">¡Bienvenido a SalusNexus Premium!</h2>
                <p class="premium-message">
                    Hola {{ $details['customer_name'] }}, gracias por tu suscripción Premium.
                    Tu cuenta ha sido actualizada con éxito y ahora tienes acceso a todas nuestras funciones avanzadas.
                </p>
            </div>
            
            <div class="subscription-details">
                <h3 class="subscription-heading">Detalles de tu Suscripción</h3>
                
                <div class="detail-row">
                    <div class="detail-icon">📋</div>
                    <div class="detail-label">Plan:</div>
                    <div class="detail-value">{{ $details['plan_name'] }}</div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-icon">💰</div>
                    <div class="detail-label">Precio:</div>
                    <div class="detail-value">{{ $details['plan_price'] }}</div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-icon">📅</div>
                    <div class="detail-label">Fecha de inicio:</div>
                    <div class="detail-value">{{ $details['start_date'] }}</div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-icon">⏱️</div>
                    <div class="detail-label">Próximo cobro:</div>
                    <div class="detail-value">{{ $details['next_billing_date'] }}</div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-icon">💳</div>
                    <div class="detail-label">Método de pago:</div>
                    <div class="detail-value">{{ $details['payment_method'] }}</div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-icon">📧</div>
                    <div class="detail-label">Email de facturación:</div>
                    <div class="detail-value">{{ $details['email'] }}</div>
                </div>
            </div>
            
            <div class="features-section">
                <h3 class="features-title">Disfruta de estos beneficios exclusivos</h3>
                
                <div class="features-grid">
                    <div class="feature-card">
                        <div class="feature-icon">📊</div>
                        <h4 class="feature-title">Historial completo</h4>
                        <p class="feature-description">Accede a todo tu historial de citas y tratamientos médicos.</p>
                    </div>
                    
                    <div class="feature-card">
                        <div class="feature-icon">⭐</div>
                        <h4 class="feature-title">Reseñas detalladas</h4>
                        <p class="feature-description">Lee opiniones completas de otros pacientes sobre profesionales.</p>
                    </div>
                    
                    <div class="feature-card">
                        <div class="feature-icon">🏆</div>
                        <h4 class="feature-title">Soporte prioritario</h4>
                        <p class="feature-description">Atención preferencial para todas tus consultas y solicitudes.</p>
                    </div>
                    
                    <div class="feature-card">
                        <div class="feature-icon">🔔</div>
                        <h4 class="feature-title">Recordatorios personalizados</h4>
                        <p class="feature-description">Configura alertas para medicamentos y próximas citas.</p>
                    </div>
                </div>
            </div>
            
            <div class="cta-section">
                <a href="{{ $details['dashboard_link'] }}" class="cta-button">Ir a Mi Dashboard</a>
                <div class="payment-badge">Pago Seguro Verificado ✓</div>
            </div>
            
            <div class="testimonial">
                <p class="testimonial-content">SalusNexus Premium mejoró completamente mi experiencia con los servicios médicos. Ahora puedo gestionar todas mis citas y seguir mi historial de salud en un solo lugar.</p>
                <p class="testimonial-author">- Ana Martínez, miembro Premium</p>
            </div>
            
            <div class="divider"></div>
            
            <div class="help-section">
                <h3 class="help-title">¿Necesitas ayuda con tu suscripción?</h3>
                
                <div class="contact-methods">
                    <div class="contact-method">
                        <p class="contact-label">Email de soporte:</p>
                        <p>soporte@salusnexus.com</p>
                    </div>
                    
                    <div class="contact-method">
                        <p class="contact-label">Teléfono:</p>
                        <p>+503 2222-3333</p>
                    </div>
                    
                    <div class="contact-method">
                        <p class="contact-label">Horario de atención:</p>
                        <p>Lunes a Viernes, 8:00 AM - 6:00 PM</p>
                    </div>
                    
                    <div class="contact-method">
                        <p class="contact-label">Centro de ayuda:</p>
                        <p><a href="#" style="color: #343a87;">Visitar Centro de Ayuda</a></p>
                    </div>
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