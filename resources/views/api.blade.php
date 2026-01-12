<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ITechTube API - Documentation</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            color: #e4e4e4;
            min-height: 100vh;
            padding: 40px 20px;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
        }
        h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            background: linear-gradient(90deg, #00d9ff, #00ff88);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .subtitle {
            color: #888;
            font-size: 1.1rem;
            margin-bottom: 40px;
        }
        .section {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 25px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .section h2 {
            color: #00d9ff;
            font-size: 1.3rem;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .section h2::before {
            content: '';
            width: 4px;
            height: 24px;
            background: linear-gradient(180deg, #00d9ff, #00ff88);
            border-radius: 2px;
        }
        .endpoint {
            background: rgba(0, 0, 0, 0.3);
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .method {
            padding: 6px 12px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.75rem;
            min-width: 60px;
            text-align: center;
        }
        .method.GET { background: #00ff88; color: #1a1a2e; }
        .method.POST { background: #00d9ff; color: #1a1a2e; }
        .method.PUT { background: #ffaa00; color: #1a1a2e; }
        .method.DELETE { background: #ff4757; color: white; }

        .path {
            font-family: 'Monaco', 'Menlo', monospace;
            font-size: 0.9rem;
            color: #e4e4e4;
            flex: 1;
        }
        .auth-badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        .auth-badge.required {
            background: rgba(255, 170, 0, 0.2);
            color: #ffaa00;
            border: 1px solid #ffaa00;
        }
        .auth-badge.public {
            background: rgba(0, 255, 136, 0.2);
            color: #00ff88;
            border: 1px solid #00ff88;
        }

        .quick-start {
            background: linear-gradient(135deg, rgba(0, 217, 255, 0.1), rgba(0, 255, 136, 0.1));
            border: 1px solid rgba(0, 217, 255, 0.3);
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 30px;
        }
        .quick-start h3 {
            color: #00d9ff;
            margin-bottom: 15px;
        }
        .quick-start ol {
            margin-left: 20px;
            line-height: 1.8;
        }
        .quick-start li {
            margin-bottom: 8px;
        }
        code {
            background: rgba(0, 0, 0, 0.4);
            padding: 2px 8px;
            border-radius: 4px;
            font-family: 'Monaco', 'Menlo', monospace;
            font-size: 0.85em;
        }
        .base-url {
            background: #1a1a2e;
            padding: 15px;
            border-radius: 8px;
            font-family: 'Monaco', 'Menlo', monospace;
            margin: 15px 0;
            color: #00ff88;
            border-left: 4px solid #00ff88;
        }
        .thunder-link {
            display: inline-block;
            background: linear-gradient(90deg, #ff6b35, #f7931e);
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            margin-top: 20px;
            transition: transform 0.2s;
        }
        .thunder-link:hover {
            transform: translateY(-2px);
        }
        footer {
            text-align: center;
            margin-top: 40px;
            color: #666;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 ITechTube API</h1>
        <p class="subtitle">Backend-Only Video Platform API - Test all endpoints with Thunder Client</p>

        <div class="quick-start">
            <h3>⚡ Quick Start Guide</h3>
            <ol>
                <li>Start Laravel server: <code>php artisan serve</code></li>
                <li>Open Thunder Client (VS Code Extension)</li>
                <li>Set base URL: <code>http://localhost:8000/api</code></li>
                <li>Register a user via <code>POST /register</code></li>
                <li>Login via <code>POST /login</code> to get your token</li>
                <li>Add header: <code>Authorization: Bearer YOUR_TOKEN</code></li>
                <li>Test all authenticated endpoints!</li>
            </ol>
            <div class="base-url">
                <strong>Base URL:</strong> http://localhost:8000/api
            </div>
            <a href="https://www.thunderclient.com/" target="_blank" class="thunder-link">
                📦 Download Thunder Client
            </a>
        </div>

        <div class="section">
            <h2>🔐 Authentication</h2>

            <div class="endpoint">
                <span class="method POST">POST</span>
                <span class="path">/register</span>
                <span class="auth-badge public">Public</span>
            </div>
            <p style="color: #888; margin: 5px 0 15px 75px; font-size: 0.85rem;">
                Body: <code>{"name": "John", "email": "john@example.com", "password": "password123", "password_confirmation": "password123"}</code>
            </p>

            <div class="endpoint">
                <span class="method POST">POST</span>
                <span class="path">/login</span>
                <span class="auth-badge public">Public</span>
            </div>
            <p style="color: #888; margin: 5px 0 15px 75px; font-size: 0.85rem;">
                Body: <code>{"email": "john@example.com", "password": "password123"}</code>
            </p>

            <div class="endpoint">
                <span class="method POST">POST</span>
                <span class="path">/logout</span>
                <span class="auth-badge required">Auth Required</span>
            </div>

            <div class="endpoint">
                <span class="method GET">GET</span>
                <span class="path">/user</span>
                <span class="auth-badge required">Auth Required</span>
            </div>
        </div>

        <div class="section">
            <h2>🎬 Videos</h2>

            <div class="endpoint">
                <span class="method GET">GET</span>
                <span class="path">/videos</span>
                <span class="auth-badge public">Public</span>
            </div>

            <div class="endpoint">
                <span class="method GET">GET</span>
                <span class="path">/videos/{id}</span>
                <span class="auth-badge public">Public</span>
            </div>

            <div class="endpoint">
                <span class="method GET">GET</span>
                <span class="path">/videos/{id}/stream</span>
                <span class="auth-badge public">Public</span>
            </div>

            <div class="endpoint">
                <span class="method GET">GET</span>
                <span class="path">/videos/search?q=keyword</span>
                <span class="auth-badge public">Public</span>
            </div>

            <div class="endpoint">
                <span class="method POST">POST</span>
                <span class="path">/videos</span>
                <span class="auth-badge required">Auth Required</span>
            </div>

            <div class="endpoint">
                <span class="method PUT">PUT</span>
                <span class="path">/videos/{id}</span>
                <span class="auth-badge required">Auth Required</span>
            </div>

            <div class="endpoint">
                <span class="method DELETE">DELETE</span>
                <span class="path">/videos/{id}</span>
                <span class="auth-badge required">Auth Required</span>
            </div>

            <div class="endpoint">
                <span class="method GET">GET</span>
                <span class="path">/my-videos</span>
                <span class="auth-badge required">Auth Required</span>
            </div>

            <div class="endpoint">
                <span class="method POST">POST</span>
                <span class="path">/videos/{id}/watch</span>
                <span class="auth-badge required">Auth Required</span>
            </div>
        </div>

        <div class="section">
            <h2>📁 Categories</h2>

            <div class="endpoint">
                <span class="method GET">GET</span>
                <span class="path">/categories</span>
                <span class="auth-badge public">Public</span>
            </div>

            <div class="endpoint">
                <span class="method GET">GET</span>
                <span class="path">/categories/{id}</span>
                <span class="auth-badge public">Public</span>
            </div>

            <div class="endpoint">
                <span class="method POST">POST</span>
                <span class="path">/categories</span>
                <span class="auth-badge required">Auth Required</span>
            </div>

            <div class="endpoint">
                <span class="method PUT">PUT</span>
                <span class="path">/categories/{id}</span>
                <span class="auth-badge required">Auth Required</span>
            </div>

            <div class="endpoint">
                <span class="method DELETE">DELETE</span>
                <span class="path">/categories/{id}</span>
                <span class="auth-badge required">Auth Required</span>
            </div>
        </div>

        <div class="section">
            <h2>📋 Playlists</h2>

            <div class="endpoint">
                <span class="method GET">GET</span>
                <span class="path">/playlists</span>
                <span class="auth-badge required">Auth Required</span>
            </div>

            <div class="endpoint">
                <span class="method POST">POST</span>
                <span class="path">/playlists</span>
                <span class="auth-badge required">Auth Required</span>
            </div>

            <div class="endpoint">
                <span class="method GET">GET</span>
                <span class="path">/playlists/{id}</span>
                <span class="auth-badge required">Auth Required</span>
            </div>

            <div class="endpoint">
                <span class="method PUT">PUT</span>
                <span class="path">/playlists/{id}</span>
                <span class="auth-badge required">Auth Required</span>
            </div>

            <div class="endpoint">
                <span class="method DELETE">DELETE</span>
                <span class="path">/playlists/{id}</span>
                <span class="auth-badge required">Auth Required</span>
            </div>

            <div class="endpoint">
                <span class="method POST">POST</span>
                <span class="path">/playlists/{id}/videos</span>
                <span class="auth-badge required">Auth Required</span>
            </div>

            <div class="endpoint">
                <span class="method DELETE">DELETE</span>
                <span class="path">/playlists/{id}/videos/{videoId}</span>
                <span class="auth-badge required">Auth Required</span>
            </div>

            <div class="endpoint">
                <span class="method PUT">PUT</span>
                <span class="path">/playlists/{id}/reorder</span>
                <span class="auth-badge required">Auth Required</span>
            </div>
        </div>

        <div class="section">
            <h2>👁️ Watch History</h2>

            <div class="endpoint">
                <span class="method GET">GET</span>
                <span class="path">/history</span>
                <span class="auth-badge required">Auth Required</span>
            </div>

            <div class="endpoint">
                <span class="method POST">POST</span>
                <span class="path">/history</span>
                <span class="auth-badge required">Auth Required</span>
            </div>

            <div class="endpoint">
                <span class="method GET">GET</span>
                <span class="path">/history/video/{videoId}</span>
                <span class="auth-badge required">Auth Required</span>
            </div>

            <div class="endpoint">
                <span class="method PUT">PUT</span>
                <span class="path">/history/video/{videoId}</span>
                <span class="auth-badge required">Auth Required</span>
            </div>

            <div class="endpoint">
                <span class="method DELETE">DELETE</span>
                <span class="path">/history/{id}</span>
                <span class="auth-badge required">Auth Required</span>
            </div>

            <div class="endpoint">
                <span class="method DELETE">DELETE</span>
                <span class="path">/history</span>
                <span class="auth-badge required">Auth Required</span>
            </div>

            <div class="endpoint">
                <span class="method GET">GET</span>
                <span class="path">/history/continue-watching</span>
                <span class="auth-badge required">Auth Required</span>
            </div>
        </div>

        <div class="section">
            <h2>📝 Request Headers (Important!)</h2>
            <p style="margin-left: 10px; line-height: 1.8;">
                <code>Content-Type: application/json</code><br>
                <code>Accept: application/json</code><br>
                <code>Authorization: Bearer YOUR_SANCTUM_TOKEN</code> ← Required for authenticated routes
            </p>
        </div>

        <footer>
            <p>Built with Laravel + Sanctum | Thunder Client Ready</p>
        </footer>
    </div>
</body>
</html>

