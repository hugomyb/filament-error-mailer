<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error Details - #{{ $error['id'] ?? 'N/A' }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #121212;
            color: #ffffff;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 1100px;
            margin: 50px auto;
            padding: 20px;
            background-color: #1e1e1e;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.5);
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #2e2e2e;
            padding: 10px 20px;
            border-radius: 8px;
        }

        .header h1 {
            font-size: 20px;
            color: #e63946;
            margin: 0;
        }

        .header .badge {
            background-color: #4caf50;
            color: #ffffff;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 14px;
        }

        .share-button {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #4caf50;
            color: #ffffff;
            border: none;
            padding: 10px 15px;
            font-size: 14px;
            border-radius: 5px;
            cursor: pointer;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
        }

        .share-button:hover {
            background-color: #43a047;
        }

        .error-message {
            margin: 20px 0;
            padding: 15px;
            background-color: #2e2e2e;
            color: #e63946;
            border-left: 5px solid #e63946;
            border-radius: 5px;
        }

        .error-context {
            display: flex;
            gap: 20px;
            margin-top: 20px;
        }

        .error-context .panel {
            flex: 1;
            background-color: #2e2e2e;
            padding: 15px;
            border-radius: 5px;
        }

        .error-context .panel h2 {
            margin-top: 0;
            font-size: 18px;
            color: #4caf50;
        }

        .code-block {
            margin-top: 20px;
            background-color: #1e1e1e;
            padding: 15px;
            font-family: monospace;
            font-size: 14px;
            color: #d4d4d4;
            border-radius: 5px;
            overflow-x: auto;
            white-space: pre-wrap;
            border-left: 5px solid #4caf50;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            color: #ffffff;
        }

        table th,
        table td {
            border: 1px solid #3a3a3a;
            padding: 10px;
            text-align: left;
        }

        table th {
            background-color: #2e2e2e;
            color: #ffffff;
        }

        a {
            color: #4caf50;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 14px;
            color: #888;
        }
    </style>
    <script>
        function shareUrl() {
            const url = window.location.href;
            navigator.clipboard.writeText(url);
        }
    </script>
</head>
<body>
<button class="share-button" onclick="shareUrl()">Share URL</button>

<div class="container">
    <div class="header">
        <h1>Error Details - #{{ $error['id'] ?? 'N/A' }}</h1>
        <span class="badge">Critical Error</span>
    </div>

    <div class="error-message">
        <strong>Message:</strong> {{ $error['message'] ?? 'N/A' }}
    </div>

    <div class="error-context">
        <div class="panel">
            <h2>Context</h2>
            <p><strong>Environment:</strong> <a href="{{ config('app.url') ?? '#' }}" target="_blank">{{ config('app.url') ?? 'N/A' }}</a></p>
            <p><strong>File:</strong> {{ $error['file'] ?? 'N/A' }}</p>
            <p><strong>Line:</strong> {{ $error['line'] ?? 'N/A' }}</p>
            <p><strong>URL:</strong> <a href="{{ $error['url'] ?? '#' }}" target="_blank">{{ $error['url'] ?? 'N/A' }}</a></p>
        </div>
        <div class="panel">
            <h2>Request</h2>
            <p><strong>Method:</strong> {{ $error['method'] ?? 'N/A' }}</p>
            <p><strong>IP:</strong> {{ $error['ip'] ?? 'N/A' }}</p>
            <p><strong>User Agent:</strong> {{ $error['userAgent'] ?? 'N/A' }}</p>
            <p><strong>Referrer:</strong> <a href="{{ $error['referrer'] ?? '#' }}" target="_blank">{{ $error['referrer'] ?? 'N/A' }}</a></p>
            <p><strong>Request Time:</strong> {{ $error['requestTime'] ?? 'N/A' }}</p>
            <p><strong>Request URI:</strong> {{ $error['requestUri'] ?? 'N/A' }}</p>
        </div>
    </div>

    <h2>Authenticated User:</h2>
    @if(!empty($error['authUser']))
        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
            </tr>
            <tr>
                <td>{{ $error['authUser']['id'] }}</td>
                <td>{{ $error['authUser']['name'] }}</td>
                <td>{{ $error['authUser']['email'] }}</td>
            </tr>
        </table>
    @else
        <p>No authenticated user</p>
    @endif

    <h2>Trace:</h2>
    <div class="code-block">
        {{ $error['stackTrace'] ?? 'N/A' }}
    </div>

    <div class="footer">
        &copy; {{ date('Y') }} {{ config('app.name') }}
    </div>
</div>
</body>
</html>
