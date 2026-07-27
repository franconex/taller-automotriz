<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Error interno — {{ config('app.name', 'Taller Pro') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Manrope',sans-serif; background:#f5f7fa; display:flex; align-items:center; justify-content:center; min-height:100vh; color:#1e293b; }
        .error-wrap { text-align:center; max-width:460px; padding:2rem; }
        .error-code { font-size:5rem; font-weight:800; color:#e74c3c; line-height:1; margin-bottom:.5rem; }
        .error-icon { font-size:3rem; margin-bottom:1rem; }
        .error-title { font-size:1.5rem; font-weight:700; margin-bottom:.75rem; }
        .error-text { font-size:.95rem; color:#64748b; line-height:1.6; margin-bottom:2rem; }
        .btn-home { display:inline-block; padding:.7rem 1.8rem; background:#1e293b; color:#fff; text-decoration:none; border-radius:8px; font-weight:600; font-size:.9rem; transition:background .2s; }
        .btn-home:hover { background:#334155; }
        .brand { font-weight:800; font-size:1.1rem; color:#0f172a; margin-bottom:2rem; display:block; }
    </style>
</head>
<body>
    <div class="error-wrap">
        <div class="brand">{{ config('app.name', 'Taller Pro') }}</div>
        <div class="error-code">500</div>
        <div class="error-icon">⚠️</div>
        <h1 class="error-title">Error inesperado</h1>
        <p class="error-text">Ocurrió un error inesperado.<br>Nuestro equipo ha sido notificado automáticamente. Intenta de nuevo en unos minutos.</p>
        <a href="{{ url('/') }}" class="btn-home">Volver al inicio</a>
    </div>
</body>
</html>
