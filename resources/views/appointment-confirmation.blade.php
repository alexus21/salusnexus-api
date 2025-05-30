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

        .confirmation-banner {
            background-color: #ebffef;
            border-left: 4px solid #28a745;
            padding: 20px;
            margin-bottom: 30px;
            border-radius: 0 8px 8px 0;
        }

        .confirmation-title {
            color: #28a745;
            font-size: 26px;
            font-weight: 700;
            margin-top: 0;
            margin-bottom: 20px;
            text-align: center;
            word-break: break-word;
        }

        .confirmation-message {
            line-height: 1.7;
            font-size: 16px;
            margin-bottom: 25px;
            color: #555;
        }

        .appointment-details {
            background-color: #f0f3ff;
            padding: 25px;
            border-radius: 8px;
            margin-bottom: 30px;
            border: 1px solid #e0e6ff;
        }

        .appointment-heading {
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
            min-width: 120px;
            margin-right: 10px;
        }

        .detail-value {
            color: #444;
            flex: 1;
            min-width: 50%;
            word-break: break-word;
        }

        .action-buttons {
            display: flex;
            gap: 15px;
            margin: 30px 0;
            flex-wrap: wrap;
        }

        .button {
            display: block;
            text-align: center;
            padding: 14px 20px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            flex: 1;
            min-width: 140px;
        }

        .primary-button {
            background-color: #343a87;
            color: #ffffff;
            border: 2px solid #343a87;
        }

        .secondary-button {
            background-color: #ffffff;
            color: #343a87;
            border: 2px solid #343a87;
        }

        .location-map {
            background-color: #f8f9ff;
            padding: 20px;
            border-radius: 8px;
            margin-top: 30px;
            text-align: center;
        }

        .map-title {
            color: #343a87;
            font-weight: 600;
            margin-top: 0;
            margin-bottom: 15px;
            word-break: break-word;
        }

        .map-image {
            width: 100%;
            max-width: 500px;
            height: auto;
            border-radius: 8px;
            margin-bottom: 10px;
        }

        .reminder-notice {
            background-color: #fff8e6;
            border-left: 3px solid #ffb100;
            padding: 15px;
            margin-top: 25px;
            border-radius: 0 8px 8px 0;
        }

        .divider {
            height: 1px;
            background-color: #e1e1e1;
            margin: 30px 0;
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

        .help-text {
            margin-top: 25px;
            font-size: 14px;
            color: #666;
            text-align: center;
        }

        /* Media queries para responsividad */
        @media only screen and (min-width: 768px) {
            .wrapper {
                max-width: 90%;
            }

            .detail-value {
                min-width: auto;
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

            .header-text, .confirmation-title {
                font-size: 20px;
            }

            .confirmation-banner, .appointment-details, .reminder-notice {
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

            .action-buttons {
                flex-direction: column;
            }

            .button {
                margin-bottom: 10px;
                width: 100%;
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
            <div class="confirmation-banner">
                <h2 class="confirmation-title">¡Cita Confirmada!</h2>
                <p class="confirmation-message">
                    Hola, {{ $details['patient_name'] }}. Tu cita ha sido programada exitosamente.
                    Aquí están los detalles de tu próxima cita:
                </p>
            </div>

            <div class="appointment-details">
                <h3 class="appointment-heading">Detalles de la Cita</h3>

                <div class="detail-row">
                    <div class="detail-icon">👨‍⚕️</div>
                    <div class="detail-label">Profesional:</div>
                    <div class="detail-value">{{ $details['doctor_name'] }}</div>
                </div>

{{--                <div class="detail-row">
                    <div class="detail-icon">🏥</div>
                    <div class="detail-label">Especialidad:</div>
                    <div class="detail-value">{{ $details['specialty'] }}</div>
                </div>--}}

                <div class="detail-row">
                    <div class="detail-icon">📅</div>
                    <div class="detail-label">Fecha:</div>
                    <div class="detail-value">{{ $details['appointment_date'] }}</div>
                </div>

{{--                <div class="detail-row">
                    <div class="detail-icon">⏰</div>
                    <div class="detail-label">Hora:</div>
                    <div class="detail-value">{{ $details['appointment_time'] }}</div>
                </div>--}}

                <div class="detail-row">
                    <div class="detail-icon">📍</div>
                    <div class="detail-label">Dirección:</div>
                    <div class="detail-value">{{ $details['clinic_address'] }}</div>
                </div>

                @if(isset($details['notes']) && !empty($details['notes']))
                <div class="detail-row">
                    <div class="detail-icon">📝</div>
                    <div class="detail-label">Notas:</div>
                    <div class="detail-value">{{ $details['notes'] }}</div>
                </div>
                @endif
            </div>

            {{--<div class="action-buttons">
                <a href="{{ $details['calendar_link'] }}" class="button primary-button">Añadir a Calendario</a>
                <a href="{{ $details['reschedule_link'] }}" class="button secondary-button">Reprogramar Cita</a>
            </div>--}}

            @if(isset($details['map_image_url']))
            <div class="location-map">
                <h3 class="map-title">Ubicación de la Clínica</h3>
                <img src="{{ $details['map_image_url'] }}" alt="Mapa de la clínica" class="map-image">
                <a href="{{ $details['map_link'] }}" class="button primary-button" style="margin-top: 15px; display: inline-block; max-width: 250px;">Ver en Google Maps</a>
            </div>
            @endif

            <div class="reminder-notice">
                <p><strong>⏰ Recordatorio:</strong> Recibirás una notificación 24 horas antes de tu cita. Te recomendamos llegar 15 minutos antes de la hora programada.</p>
            </div>

            <div class="divider"></div>

            <div class="help-text">
                <p>Si necesitas cancelar o reprogramar tu cita, hazlo con al menos 24 horas de anticipación.<br>
                Para cualquier consulta, puedes contactarnos al <strong>+503 2222-3333</strong>.</p>
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
