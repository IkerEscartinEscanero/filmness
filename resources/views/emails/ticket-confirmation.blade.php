<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <style>
            body {
                font-family: 'Segoe UI', Arial, sans-serif;
                background-color: #0f172a;
                color: #f1f5f9;
                margin: 0;
                padding: 0;
                -webkit-text-size-adjust: 100%;
            }
            .wrapper {
                width: 100%;
                max-width: 600px;
                margin: 0 auto;
                padding: 24px 12px;
                background-color: #0f172a;
                box-sizing: border-box;
            }

            /* Header */
            .header {
                text-align: center;
                padding: 28px 20px;
                background-color: #0f172a;
                border-radius: 16px;
                margin-bottom: 12px;
            }
            .header .logo {
                font-size: 20px;
                font-weight: 800;
                letter-spacing: 4px;
                color: #eab308;
                text-transform: uppercase;
            }
            .header .badge {
                display: inline-block;
                margin-top: 10px;
                font-size: 10px;
                font-weight: 600;
                letter-spacing: 3px;
                text-transform: uppercase;
                color: #34d399;
            }
            .header h1 {
                margin: 8px 0 0;
                font-size: 22px;
                font-weight: 700;
                color: #f1f5f9;
                line-height: 1.3;
            }
            .header p {
                margin: 8px 0 0;
                color: #94a3b8;
                font-size: 14px;
                line-height: 1.6;
            }

            /* Card */
            .card {
                background-color: #1e293b;
                border: 1px solid rgba(255,255,255,0.07);
                border-radius: 16px;
                padding: 20px 16px;
                margin-bottom: 12px;
                box-sizing: border-box;
            }
            .card-label {
                font-size: 10px;
                font-weight: 600;
                letter-spacing: 3px;
                text-transform: uppercase;
                color: #64748b;
                margin-bottom: 14px;
            }

            /* Summary table */
            .summary-grid {
                width: 100%;
                border-collapse: collapse;
            }
            .summary-grid td {
                padding: 8px 0;
                font-size: 14px;
                vertical-align: top;
                border-bottom: 1px solid rgba(255,255,255,0.05);
                word-break: break-word;
            }
            .summary-grid tr:last-child td {
                border-bottom: none;
            }
            .summary-grid .s-label {
                color: #94a3b8;
                width: 40%;
                padding-right: 8px;
            }
            .summary-grid .s-value { color: #f1f5f9; font-weight: 600; }

            /* Seat pills */
            .seat-pill {
                display: inline-block;
                background-color: #334155;
                border: 1px solid rgba(255,255,255,0.08);
                color: #f1f5f9;
                font-size: 12px;
                font-weight: 600;
                padding: 3px 10px;
                border-radius: 20px;
                margin: 3px 3px 3px 0;
            }

            /* QR block */
            .qr-block { text-align: center; padding: 4px 0; }
            .qr-block img {
                border-radius: 12px;
                background: #fff;
                padding: 10px;
                max-width: 100%;
                height: auto;
            }
            .qr-hint {
                margin-top: 10px;
                font-size: 12px;
                color: #64748b;
            }

            /* Google Calendar */
            .gcal-block { text-align: center; }
            .gcal-block p { color: #94a3b8; font-size: 13px; margin: 0 0 12px; }
            .gcal-btn {
                display: inline-block;
                background-color: #4285f4;
                color: #ffffff !important;
                text-decoration: none;
                padding: 12px 24px;
                border-radius: 12px;
                font-size: 14px;
                font-weight: 600;
            }

            /* Footer */
            .footer {
                text-align: center;
                padding-top: 20px;
                font-size: 12px;
                color: #475569;
                line-height: 1.7;
            }

            /* Mobile */
            @media only screen and (max-width: 480px) {
                .wrapper { padding: 16px 8px; }
                .header { padding: 24px 16px; }
                .header h1 { font-size: 20px; }
                .card { padding: 16px 14px; }
                .summary-grid td { font-size: 13px; }
                .summary-grid .s-label { width: 38%; }
                .qr-block img { width: 180px !important; height: 180px !important; }
                .gcal-btn { display: block; text-align: center; padding: 14px 16px; }
            }
        </style>
    </head>
    <body>
        <div class="wrapper">

            <!-- HEADER -->
            <div class="header">
                <div class="logo">Filmness</div>
                <div class="badge">Compra confirmada</div>
                <h1>¡Tus entradas están listas!</h1>
                <p>
                    Aquí tienes tu entrada para
                    <strong style="color:#f1f5f9;">{{ $film->title }}</strong>.
                    Muestra el código QR en taquilla.
                </p>
            </div>

            <!-- Purchase summary -->
            <div class="card">
                <div class="card-label">Resumen de tu compra</div>
                <table class="summary-grid" cellpadding="0" cellspacing="0">
                    <tr>
                        <td class="s-label">Nº de pedido</td>
                        <td class="s-value">#{{ $purchase->id }}</td>
                    </tr>
                    <tr>
                        <td class="s-label">Película</td>
                        <td class="s-value">{{ $film->title }}</td>
                    </tr>
                    <tr>
                        <td class="s-label">Sesión</td>
                        <td class="s-value">{{ $session->date->format('d/m/Y · H:i') }}</td>
                    </tr>
                    <tr>
                        <td class="s-label">Sala</td>
                        <td class="s-value">{{ $session->room->name }}</td>
                    </tr>
                    <tr>
                        <td class="s-label">Butacas</td>
                        <td class="s-value">
                            @foreach($seats as $seat)
                                <span class="seat-pill">{{ $seat }}</span>
                            @endforeach
                        </td>
                    </tr>
                    <tr>
                        <td class="s-label">Total pagado</td>
                        <td class="s-value">{{ number_format((float) $purchase->total, 2, ',', '.') }}€</td>
                    </tr>
                </table>
            </div>

            <!-- QR único para todas las butacas -->
            <div class="card">
                <div class="card-label">Tu entrada · {{ $seats->count() }} {{ $seats->count() === 1 ? 'butaca' : 'butacas' }}</div>
                <div class="qr-block">
                    <img src="{{ $qrUrl }}" alt="Código QR de tu entrada" width="220" height="220" style="display:inline-block;">
                    <div class="qr-hint">Muestra este código en taquilla para acceder</div>
                </div>
            </div>

            <!-- Google Calendar -->
            @if($googleCalendarUrl)
            <div class="card">
                <div class="gcal-block">
                    <p>¿No quieres olvidarte de la película?</p>
                    <a href="{{ $googleCalendarUrl }}" class="gcal-btn" target="_blank">
                        Añadir a Google Calendar
                    </a>
                </div>
            </div>
            @endif

            <!-- Footer -->
            <div class="footer">
                <p>Filmness · Este email fue enviado a {{ $purchase->contact_email }}</p>
                <p>Si no realizaste esta compra, ignora este mensaje.</p>
            </div>

        </div>
    </body>
</html>