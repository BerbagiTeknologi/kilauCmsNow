<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Balasan Kolaborasi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            background: #fff;
            padding: 20px;
            max-width: 600px;
            margin: 0 auto;
            border-radius: 5px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #1572E8;
        }
        p {
            font-size: 16px;
            line-height: 1.5;
            color: #333;
        }
        .footer {
            margin-top: 20px;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Halo, {{ $nama }}</h2>
        <p>{{ $balasan }}</p>
        <p>Terima kasih telah menghubungi Kilau Indonesia. Kami berharap dapat berkolaborasi dengan Anda.</p>
        <div class="footer">
            <p><strong>Kilau Indonesia</strong></p>
            <p><a href="https://home.kilauindonesia.org/">www.kilauindonesia.org</a></p>
        </div>
    </div>
</body>
</html>
