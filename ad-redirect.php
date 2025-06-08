<?php
session_start();
require_once 'db.php';
require_once 'functions.php';

// Get the next URL from query parameters
$nextUrl = isset($_GET['next']) ? $_GET['next'] : getBaseUrl();
$slug = isset($_GET['slug']) ? $_GET['slug'] : '';

// Delay redirect by 2 seconds
header("Refresh: 2; URL=" . $nextUrl);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redirecting...</title>
    <style>
        :root {
            --primary-color: #4f46e5;
            --secondary-color: #818cf8;
            --bg-color: #f9fafb;
            --text-color: #1f2937;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            margin: 0;
            padding: 0;
            background: var(--bg-color);
            color: var(--text-color);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .redirect-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
            text-align: center;
            gap: 30px;
        }
        
        .loading-circle {
            position: relative;
            width: 80px;
            height: 80px;
            margin: 10px auto;
        }
        
        .timer-number {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 24px;
            font-weight: bold;
            color: var(--primary-color);
            z-index: 2;
        }
        
        .circle-loader {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: 4px solid #e5e7eb;
            border-top-color: var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        .redirect-message {
            font-size: 18px;
            font-weight: 600;
            color: var(--primary-color);
            margin: 10px 0;
        }
        
        .redirect-submessage {
            font-size: 14px;
            color: #6b7280;
            max-width: 600px;
            margin: 0 auto 20px;
        }
        
        .ad-container {
            width: 100%;
            max-width: 800px;
            margin: 20px auto;
            text-align: center;
            overflow: hidden;
        }
        
        .ad-label {
            font-size: 12px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }
        
        .ad-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            padding: 20px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        @media (max-width: 768px) {
            .loading-circle {
                width: 60px;
                height: 60px;
            }
            
            .timer-number {
                font-size: 20px;
            }
            
            .redirect-message {
                font-size: 16px;
            }
            
            .redirect-submessage {
                font-size: 12px;
            }
            
            .ad-container {
                margin: 10px auto;
            }
        }
    </style>
    
    <!-- Adsterra Social Bar -->
    <script type='text/javascript' src='//unloadeasier.com/7b/53/ce/7b53ce53800c326028514de8290f4897.js'></script>
</head>
<body>
    <!-- Adsterra Social Bar -->
    <script type='text/javascript' src='//unloadeasier.com/7b/53/ce/7b53ce53800c326028514de8290f4897.js'></script>
    
    <div class="redirect-container">
        <!-- Top Banner Ad -->
        <div class="ad-container">
            <div class="ad-label">SPONSOR</div>
            <script type="text/javascript">
                atOptions = {
                    'key' : 'b209b401adb0bacccf1e3bcae9f0f0c7',
                    'format' : 'iframe',
                    'height' : 50,
                    'width' : 320,
                    'params' : {}
                };
            </script>
            <script type="text/javascript" src="//unloadeasier.com/b209b401adb0bacccf1e3bcae9f0f0c7/invoke.js"></script>
        </div>

        <!-- Featured Ad Top -->
        <div class="ad-container">
            <div class="ad-label">FEATURED CONTENT</div>
            <script type="text/javascript">
                atOptions = {
                    'key' : 'e756f1b62cf9ccc12aaa84187ae61eed',
                    'format' : 'iframe',
                    'height' : 250,
                    'width' : 300,
                    'params' : {}
                };
            </script>
            <script type="text/javascript" src="//unloadeasier.com/e756f1b62cf9ccc12aaa84187ae61eed/invoke.js"></script>
        </div>

        <!-- Loading Circle with Timer -->
        <div class="loading-circle">
            <div class="timer-number" id="timer">2</div>
            <div class="circle-loader"></div>
        </div>

        <div class="redirect-message">Please wait while we process your request...</div>
        <div class="redirect-submessage">You will be redirected automatically in a few seconds.</div>

        <!-- Native Ad Grid -->
        <div class="ad-grid">
            <!-- Native Ad 1 -->
            <div class="ad-container">
                <div class="ad-label">RECOMMENDED</div>
                <script async="async" data-cfasync="false" src="//unloadeasier.com/88b5ea182493af23b9cff796ef946522/invoke.js"></script>
                <div id="container-88b5ea182493af23b9cff796ef946522"></div>
            </div>

            <!-- Native Ad 2 -->
            <div class="ad-container">
                <div class="ad-label">TRENDING NOW</div>
                <script async="async" data-cfasync="false" src="//unloadeasier.com/88b5ea182493af23b9cff796ef946522/invoke.js"></script>
                <div id="container-88b5ea182493af23b9cff796ef946522"></div>
            </div>
        </div>

        <!-- Featured Ad Bottom -->
        <div class="ad-container">
            <div class="ad-label">SPECIAL OFFER</div>
            <script type="text/javascript">
                atOptions = {
                    'key' : 'e756f1b62cf9ccc12aaa84187ae61eed',
                    'format' : 'iframe',
                    'height' : 250,
                    'width' : 300,
                    'params' : {}
                };
            </script>
            <script type="text/javascript" src="//unloadeasier.com/e756f1b62cf9ccc12aaa84187ae61eed/invoke.js"></script>
        </div>
    </div>

    <!-- Bottom Banner Ad -->
    <div class="ad-container">
        <div class="ad-label">SPONSOR</div>
        <script type="text/javascript">
            atOptions = {
                'key' : 'b209b401adb0bacccf1e3bcae9f0f0c7',
                'format' : 'iframe',
                'height' : 50,
                'width' : 320,
                'params' : {}
            };
        </script>
        <script type="text/javascript" src="//unloadeasier.com/b209b401adb0bacccf1e3bcae9f0f0c7/invoke.js"></script>
    </div>

    <!-- Multiple Popunders -->
    <script type='text/javascript' src='//unloadeasier.com/64/fd/c7/64fdc7b9347307fa0d18bee7892e958b.js'></script>
    <script type='text/javascript' src='//unloadeasier.com/7b/53/ce/7b53ce53800c326028514de8290f4897.js'></script>
    <script type='text/javascript' src='//unloadeasier.com/64/fd/c7/64fdc7b9347307fa0d18bee7892e958b.js'></script>

    <script>
        // Update timer
        let secondsLeft = 2;
        function updateTimer() {
            const timerElement = document.getElementById('timer');
            if (timerElement) {
                timerElement.textContent = secondsLeft;
                if (secondsLeft > 0) {
                    secondsLeft--;
                    setTimeout(updateTimer, 1000);
                }
            }
        }
        window.onload = updateTimer;
    </script>
</body>
</html> 