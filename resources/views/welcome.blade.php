
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel Deployment | Nebula</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #38bdf8;
            --secondary: #818cf8;
            --bg: #080d18;
            --card: rgba(17, 26, 45, 0.75);
            --border: rgba(148, 163, 184, 0.15);
            --text: #e2e8f0;
            --muted: #94a3b8;
        }

        body {
            min-height: 100vh;
            background:
                radial-gradient(circle at 20% 10%, #10264b 0, transparent 35%),
                radial-gradient(circle at 80% 90%, #201647 0, transparent 35%),
                var(--bg);
            color: var(--text);
            font-family: Inter, "Segoe UI", Arial, sans-serif;
            display: flex;
            flex-direction: column;
        }

        .container {
            width: min(1100px, 92%);
            margin: auto;
            padding: 40px 0;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            margin-bottom: 80px;
        }

        .brand {
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -1px;
        }

        .brand span {
            color: var(--primary);
        }

        .status {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 14px;
            border: 1px solid rgba(34, 197, 94, .25);
            background: rgba(34, 197, 94, .08);
            border-radius: 50px;
            color: #86efac;
            font-size: 12px;
        }

        .dot {
            width: 7px;
            height: 7px;
            background: #22c55e;
            border-radius: 50%;
            box-shadow: 0 0 12px #22c55e;
        }

        .hero {
            text-align: center;
            max-width: 780px;
            margin: 0 auto 65px;
        }

        .badge {
            display: inline-block;
            padding: 8px 15px;
            border: 1px solid var(--border);
            border-radius: 50px;
            color: var(--primary);
            font-size: 12px;
            font-weight: 600;
            background: rgba(56, 189, 248, .05);
            margin-bottom: 25px;
        }

        h1 {
            font-size: clamp(38px, 7vw, 72px);
            font-weight: 800;
            letter-spacing: -4px;
            line-height: 1.1;
            margin-bottom: 25px;
        }

        h1 span {
            background: linear-gradient(90deg, #38bdf8, #818cf8, #c084fc);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .hero p {
            font-size: 17px;
            line-height: 1.8;
            color: var(--muted);
        }

        .hero p strong {
            color: #cbd5e1;
        }

        .deployment-card {
            max-width: 760px;
            margin: auto;
            padding: 28px;
            border: 1px solid var(--border);
            border-radius: 24px;
            background: var(--card);
            backdrop-filter: blur(20px);
            box-shadow: 0 30px 100px rgba(0, 0, 0, .25);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .card-header h2 {
            font-size: 16px;
        }

        .card-header small {
            color: var(--muted);
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 14px;
        }

        .item {
            padding: 20px;
            border: 1px solid var(--border);
            border-radius: 15px;
            background: rgba(255, 255, 255, .02);
        }

        .item label {
            display: block;
            color: var(--muted);
            font-size: 12px;
            margin-bottom: 10px;
        }

        .item strong {
            font-size: 17px;
            font-weight: 600;
            overflow-wrap: anywhere;
        }

        .item .blue {
            color: var(--primary);
        }

        .progress {
            margin-top: 25px;
            height: 5px;
            background: #1e293b;
            border-radius: 20px;
            overflow: hidden;
        }

        .progress span {
            display: block;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, #38bdf8, #818cf8);
        }

        .footer-note {
            text-align: center;
            margin-top: 35px;
            color: var(--muted);
            font-size: 13px;
        }

        footer {
            text-align: center;
            padding: 30px 15px;
            font-size: 12px;
            color: #64748b;
        }

        footer span {
            color: var(--primary);
        }

        @media (max-width: 600px) {
            header {
                margin-bottom: 55px;
            }

            h1 {
                letter-spacing: -2px;
            }

            .hero p {
                font-size: 15px;
            }

            .deployment-card {
                padding: 20px;
            }

            .grid {
                grid-template-columns: 1fr;
            }

            .item strong {
                font-size: 15px;
            }
        }
    </style>
</head>

<body>

    <div class="container">

        <header>
            <div class="brand">NEBULA<span>.</span></div>

            <div class="status">
                <span class="dot"></span>
                Application Online
            </div>
        </header>

        <section class="hero">

            <div class="badge">
                🚀 CUSTOM LARAVEL DEPLOYMENT
            </div>

            <h1>
                Your application.<br>
                <span>Your next release.</span>
            </h1>

            <p>
                Welcome to your custom Laravel deployment environment.
                Your application is successfully running on
                <strong>Hostinger infrastructure</strong>.
            </p>

        </section>

        <section class="deployment-card">

            <div class="card-header">
                <h2>Deployment Overview</h2>
                <small>Environment Information</small>
            </div>

            <div class="grid">

                <div class="item">
                    <label>Application</label>
                    <strong class="blue">Laravel 13</strong>
                </div>

                <div class="item">
                    <label>PHP Runtime</label>
                    <strong>PHP 8.3</strong>
                </div>

                <div class="item">
                    <label>Deployment Strategy</label>
                    <strong>Release Based</strong>
                </div>

                <div class="item">
                    <label>Hosting Platform</label>
                    <strong>Hostinger</strong>
                </div>

                <div class="item">
                    <label>Environment</label>
                    <strong class="blue">Production</strong>
                </div>

                <div class="item">
                    <label>Deployment Status</label>
                    <strong style="color:#86efac;">Successfully Deployed</strong>
                </div>

            </div>

            <div class="progress">
                <span></span>
            </div>

        </section>

        <div class="footer-note">
            Built with Laravel · Deployed through GitHub
        </div>

    </div>

    <footer>
        © {{ date('Y') }} <span>NEBULA</span> · Laravel Deployment Practice
    </footer>

</body>
</html>
