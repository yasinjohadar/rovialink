<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الموقع قيد الصيانة</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, sans-serif; margin: 0; min-height: 100vh; display: flex; align-items: center; justify-content: center; background: #f1f5f9; color: #1e293b; padding: 1rem; }
        .box { max-width: 420px; text-align: center; padding: 2rem; background: #fff; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,.08); }
        h1 { font-size: 1.5rem; margin: 0 0 1rem; color: #334155; }
        p { margin: 0; color: #64748b; line-height: 1.6; }
    </style>
</head>
<body>
    <div class="box">
        <h1>الموقع قيد الصيانة</h1>
        <p>{{ $message ?? 'نعود قريباً.' }}</p>
    </div>
</body>
</html>
