<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Demo Reset in Progress</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
        }
        .container {
            text-align: center;
            padding: 2rem;
            max-width: 500px;
        }
        .icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 2rem;
            animation: spin 2s linear infinite;
        }
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        h1 {
            font-size: 1.75rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: #fff;
        }
        p {
            color: #a0aec0;
            line-height: 1.6;
            margin-bottom: 2rem;
        }
        .countdown {
            font-size: 0.875rem;
            color: #718096;
            margin-bottom: 1.5rem;
        }
        .countdown span {
            color: #7843E9;
            font-weight: 600;
        }
        .btn {
            display: inline-block;
            background: #7843E9;
            color: #fff;
            padding: 0.875rem 2rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            cursor: pointer;
            border: none;
            font-size: 1rem;
            transition: background 0.2s, transform 0.2s;
        }
        .btn:hover {
            background: #6a3ad1;
            transform: translateY(-1px);
        }
        .btn:active {
            transform: translateY(0);
        }
        .progress-bar {
            width: 100%;
            height: 4px;
            background: #2d3748;
            border-radius: 2px;
            margin-top: 2rem;
            overflow: hidden;
        }
        .progress-bar-inner {
            height: 100%;
            background: #7843E9;
            border-radius: 2px;
            animation: progress 5s linear infinite;
        }
        @keyframes progress {
            0% { width: 0%; }
            100% { width: 100%; }
        }
    </style>
</head>
<body>
    <div class="container">
        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="#7843E9" stroke-width="2">
            <path d="M23 4v6h-6M1 20v-6h6"/>
            <path d="M3.51 9a9 9 0 0114.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0020.49 15"/>
        </svg>

        <h1>Demo Reset in Progress</h1>

        <p>
            The demo database is being reinitialized with fresh data.
            This usually takes just a few seconds.
        </p>

        <div class="countdown">
            Auto-refresh in <span id="timer">5</span> seconds
        </div>

        <button class="btn" onclick="window.location.reload()">
            Refresh Now
        </button>

        <div class="progress-bar">
            <div class="progress-bar-inner"></div>
        </div>
    </div>

    <script>
        let seconds = 5;
        const timerEl = document.getElementById('timer');

        const countdown = setInterval(() => {
            seconds--;
            timerEl.textContent = seconds;

            if (seconds <= 0) {
                clearInterval(countdown);
                window.location.reload();
            }
        }, 1000);
    </script>
</body>
</html>
