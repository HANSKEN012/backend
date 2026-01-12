<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>RIO STREAMING API - Documentation</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
        font-family: 'Inter', sans-serif;
        background: #0f111a;
        color: #e0e0e0;
        line-height: 1.6;
        padding: 40px 20px;
    }
    .container {
        max-width: 1100px;
        margin: 0 auto;
    }
    h1 {
        font-size: 3rem;
        margin-bottom: 0.3em;
        background: linear-gradient(90deg, #00f0ff, #00ff88);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    .subtitle {
        font-size: 1.2rem;
        color: #aaa;
        margin-bottom: 40px;
    }
    .section {
        background: rgba(255,255,255,0.03);
        border-radius: 14px;
        padding: 30px;
        margin-bottom: 30px;
        border-left: 5px solid #00ff88;
        box-shadow: 0 0 15px rgba(0,255,136,0.1);
    }
    .section h2 {
        font-size: 1.5rem;
        color: #00ff88;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
    }
    .endpoint {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 12px 15px;
        background: rgba(0,0,0,0.25);
        border-radius: 8px;
        margin-bottom: 12px;
        transition: transform 0.15s;
    }
    .endpoint:hover { transform: translateY(-2px); }
    .method {
        padding: 6px 12px;
        border-radius: 6px;
        font-weight: 700;
        font-size: 0.8rem;
        text-align: center;
        min-width: 65px;
    }
    .method.GET { background: #00ff88; color: #0f111a; }
    .method.POST { background: #00d9ff; color: #0f111a; }
    .method.PUT { background: #ffaa00; color: #0f111a; }
    .method.DELETE { background: #ff4757; color: white; }

    .path {
        font-family: 'Courier New', monospace;
        font-size: 0.95rem;
        flex: 1;
        color: #e0e0e0;
    }
    .auth-badge {
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 600;
    }
    .auth-badge.required {
        background: rgba(255, 170, 0, 0.15);
        color: #ffaa00;
        border: 1px solid #ffaa00;
    }
    .auth-badge.public {
        background: rgba(0, 255, 136, 0.15);
        color: #00ff88;
        border: 1px solid #00ff88;
    }

    .quick-start {
        background: rgba(0,217,255,0.05);
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 40px;
        border-left: 5px solid #00d9ff;
    }
    .quick-start h3 { color: #00d9ff; margin-bottom: 15px; }
    .quick-start ol {
        margin-left: 20px;
        line-height: 1.8;
        color: #ccc;
    }
    .quick-start li { margin-bottom: 8px; }

    code {
        background: rgba(0,0,0,0.35);
        padding: 3px 8px;
        border-radius: 4px;
        font-family: 'Courier New', monospace;
        font-size: 0.87em;
    }
    .base-url {
        background: #0f111a;
        padding: 15px;
        border-radius: 8px;
        font-family: 'Courier New', monospace;
        margin: 15px 0;
        color: #00ff88;
        border-left: 4px solid #00ff88;
    }

    .cta-button {
        display: inline-block;
        background: linear-gradient(90deg, #00d9ff, #00ff88);
        color: #0f111a;
        padding: 12px 25px;
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none;
        transition: transform 0.2s;
        margin-top: 15px;
    }
    .cta-button:hover { transform: translateY(-2px); }

    footer {
        text-align: center;
        margin-top: 50px;
        color: #555;
        font-size: 0.9rem;
    }
</style>
</head>
<body>
<div class="container">
    <h1>🚀 RIO STREAMING API</h1>
    <p class="subtitle">Professional Backend-Only Video Platform API – Test all endpoints quickly & securely</p>

    <div class="quick-start">
        <h3>⚡ Quick Start</h3>
        <ol>
            <li>Start the server: <code>php artisan serve --host=0.0.0.0 --port=8000</code></li>
            <li>Set your API base URL: <code>http://localhost:8000/api</code></li>
            <li>Register a new user: <code>POST /register</code></li>
            <li>Login to get your token: <code>POST /login</code></li>
            <li>Add header: <code>Authorization: Bearer YOUR_TOKEN</code></li>
            <li>Start interacting with all endpoints!</li>
        </ol>
        <div class="base-url"><strong>Base URL:</strong> http://localhost:8000/api</div>
        <a href="#" class="cta-button">📦 Thunder Client Ready</a>
    </div>

    <!-- Authentication Section -->
    <div class="section">
        <h2>🔐 Authentication</h2>
        <div class="endpoint"><span class="method POST">POST</span><span class="path">/register</span><span class="auth-badge public">Public</span></div>
        <p style="margin-left: 75px; color:#aaa;">Body: <code>{"name": "John", "email":"john@example.com", "password":"password123"}</code></p>

        <div class="endpoint"><span class="method POST">POST</span><span class="path">/login</span><span class="auth-badge public">Public</span></div>
        <p style="margin-left: 75px; color:#aaa;">Body: <code>{"email":"john@example.com", "password":"password123"}</code></p>

        <div class="endpoint"><span class="method POST">POST</span><span class="path">/logout</span><span class="auth-badge required">Auth Required</span></div>
        <div class="endpoint"><span class="method GET">GET</span><span class="path">/user</span><span class="auth-badge required">Auth Required</span></div>
    </div>

    <!-- Videos Section -->
    <div class="section">
        <h2>🎬 Videos</h2>
        <div class="endpoint"><span class="method GET">GET</span><span class="path">/videos</span><span class="auth-badge public">Public</span></div>
        <div class="endpoint"><span class="method GET">GET</span><span class="path">/videos/{id}</span><span class="auth-badge public">Public</span></div>
        <div class="endpoint"><span class="method POST">POST</span><span class="path">/videos</span><span class="auth-badge required">Auth Required</span></div>
    </div>

    <footer>
        <p>© 2026 RIO STREAMING | Built with Laravel + Sanctum</p>
    </footer>
</div>
</body>
</html>
