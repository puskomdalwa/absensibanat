<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance</title>

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            height: 100vh;
            background: linear-gradient(135deg, #0f172a, #1e293b);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container {
            text-align: center;
            max-width: 500px;
            padding: 40px;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(15px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }

        .icon {
            font-size: 60px;
            margin-bottom: 20px;
        }

        h1 {
            font-size: 28px;
            margin-bottom: 10px;
            font-weight: 600;
        }

        p {
            font-size: 14px;
            color: #cbd5f5;
            margin-bottom: 25px;
            line-height: 1.6;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 999px;
            background: #22c55e;
            color: #fff;
            text-decoration: none;
            font-size: 14px;
            transition: 0.3s;
        }

        .btn:hover {
            background: #16a34a;
        }

        .footer {
            margin-top: 20px;
            font-size: 12px;
            color: #94a3b8;
        }

        @media (max-width: 480px) {
            .container {
                padding: 25px;
            }

            h1 {
                font-size: 22px;
            }
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="icon">🚧</div>

        <h1>Sedang Maintenance</h1>

        <p>
            Website sedang dalam proses pemeliharaan dan peningkatan sistem.<br>
            Kami akan segera kembali dengan layanan yang lebih baik.
        </p>

        <a href="#" class="btn">Coba Lagi</a>

        <div class="footer">
            © 2026 - UII Dalwa
        </div>
    </div>

</body>
</html>