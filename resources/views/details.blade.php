<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error Details - {{ $error['id'] ?? 'N/A' }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --bg-primary: #f3f4f6;
            --bg-secondary: #ffffff;
            --bg-tertiary: #f9fafb;
            --bg-error: #fef2f2;
            --bg-code: #1f2937;
            --text-primary: #1f2937;
            --text-secondary: #6b7280;
            --text-error: #991b1b;
            --text-code: #e5e7eb;
            --border-color: #e5e7eb;
            --shadow: rgba(0, 0, 0, 0.1);
            --dropdown-bg: #ffffff;
            --dropdown-hover: #f3f4f6;
        }

        [data-theme="dark"] {
            --bg-primary: #111827;
            --bg-secondary: #1f2937;
            --bg-tertiary: #374151;
            --bg-error: #7f1d1d;
            --bg-code: #0f172a;
            --text-primary: #f9fafb;
            --text-secondary: #9ca3af;
            --text-error: #fca5a5;
            --text-code: #e5e7eb;
            --border-color: #374151;
            --shadow: rgba(0, 0, 0, 0.3);
            --dropdown-bg: #374151;
            --dropdown-hover: #4b5563;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: var(--bg-primary);
            padding: 2rem;
            line-height: 1.6;
            transition: background-color 0.3s ease;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: var(--bg-secondary);
            border-radius: 8px;
            box-shadow: 0 1px 3px var(--shadow);
            overflow: hidden;
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
        }
        .header {
            background: #ef4444;
            color: white;
            padding: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header-content h1 { font-size: 1.5rem; margin-bottom: 0.5rem; }
        .header-content p { opacity: 0.9; }
        .header-actions {
            display: flex;
            gap: 0.5rem;
        }
        .btn {
            padding: 0.5rem 1rem;
            border: 2px solid white;
            background: transparent;
            color: white;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .btn:hover {
            background: white;
            color: #ef4444;
        }
        .btn svg {
            width: 16px;
            height: 16px;
        }
        .dropdown {
            position: relative;
            display: inline-block;
        }
        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background-color: var(--dropdown-bg);
            min-width: 160px;
            box-shadow: 0 8px 16px var(--shadow);
            border-radius: 6px;
            z-index: 1;
            margin-top: 0.5rem;
            overflow: hidden;
            transition: background-color 0.3s ease;
        }
        .dropdown-content.show {
            display: block;
        }
        .dropdown-item {
            color: var(--text-primary);
            padding: 0.75rem 1rem;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
            font-size: 0.875rem;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        .dropdown-item:hover {
            background-color: var(--dropdown-hover);
        }
        .dropdown-item svg {
            width: 16px;
            height: 16px;
        }
        .content { padding: 2rem; }
        .section { margin-bottom: 2rem; }
        .section h2 {
            font-size: 1.25rem;
            color: var(--text-primary);
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--border-color);
            transition: color 0.3s ease, border-color 0.3s ease;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
        }
        .info-item {
            padding: 1rem;
            background: var(--bg-tertiary);
            border-radius: 6px;
            transition: background-color 0.3s ease;
        }
        .info-label {
            font-size: 0.875rem;
            color: var(--text-secondary);
            margin-bottom: 0.25rem;
            transition: color 0.3s ease;
        }
        .info-value {
            color: var(--text-primary);
            font-weight: 500;
            transition: color 0.3s ease;
        }
        .error-message {
            background: var(--bg-error);
            border-left: 4px solid #ef4444;
            padding: 1rem;
            border-radius: 4px;
            color: var(--text-error);
            margin-bottom: 1rem;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        .stack-trace {
            background: var(--bg-code);
            color: var(--text-code);
            padding: 1rem;
            border-radius: 6px;
            overflow-x: auto;
            font-family: 'Courier New', monospace;
            font-size: 0.875rem;
            white-space: pre-wrap;
            word-wrap: break-word;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-content">
                <h1>Error Details</h1>
                <p>Error ID: {{ $error['id'] ?? 'N/A' }}</p>
            </div>
            <div class="header-actions">
                <button class="btn" onclick="toggleTheme()" id="themeToggle">
                    <svg id="sunIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    <svg id="moonIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                    </svg>
                </button>
                <div class="dropdown">
                    <button class="btn" onclick="toggleDropdown(event)">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                        Copy
                    </button>
                    <div class="dropdown-content" id="copyDropdown">
                        <button class="dropdown-item" onclick="copyAsMarkdown()">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                            Copy as Markdown
                        </button>
                        <button class="dropdown-item" onclick="copyAsJson()">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                            </svg>
                            Copy as JSON
                        </button>
                    </div>
                </div>
                <button class="btn" onclick="shareError(event)">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path>
                    </svg>
                    Share
                </button>
            </div>
        </div>

        <div class="content">
            <div class="section">
                <h2>Error Information</h2>
                <div class="error-message">
                    {{ $error['message'] ?? 'No message available' }}
                </div>
                <div class="info-grid">
                    @if(isset($error['appFile']) && $error['appFile'])
                    <div class="info-item">
                        <div class="info-label">Application File</div>
                        <div class="info-value">{{ $error['appFile'] }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Application Line</div>
                        <div class="info-value">{{ $error['appLine'] ?? 'N/A' }}</div>
                    </div>
                    @endif
                    <div class="info-item">
                        <div class="info-label">{{ isset($error['appFile']) && $error['appFile'] ? 'Origin File' : 'File' }}</div>
                        <div class="info-value">{{ $error['file'] ?? 'N/A' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">{{ isset($error['appFile']) && $error['appFile'] ? 'Origin Line' : 'Line' }}</div>
                        <div class="info-value">{{ $error['line'] ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>

            <div class="section">
                <h2>Request Information</h2>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Method</div>
                        <div class="info-value">{{ $error['method'] ?? 'N/A' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">URL</div>
                        <div class="info-value">{{ $error['url'] ?? 'N/A' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">IP Address</div>
                        <div class="info-value">{{ $error['ip'] ?? 'N/A' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Request Time</div>
                        <div class="info-value">{{ $error['requestTime'] ?? 'N/A' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">User Agent</div>
                        <div class="info-value">{{ $error['userAgent'] ?? 'N/A' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Referrer</div>
                        <div class="info-value">{{ $error['referrer'] ?? 'Direct access' }}</div>
                    </div>
                </div>
            </div>

            @if(isset($error['authUser']))
            <div class="section">
                <h2>User Information</h2>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">User ID</div>
                        <div class="info-value">{{ $error['authUser']['id'] ?? 'Guest' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Name</div>
                        <div class="info-value">{{ $error['authUser']['name'] ?? 'Guest' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Email</div>
                        <div class="info-value">{{ $error['authUser']['email'] ?? 'Guest' }}</div>
                    </div>

                </div>
            </div>
            @endif

            <div class="section">
                <h2>Stack Trace</h2>
                <div class="stack-trace">{{ $error['stackTrace'] ?? 'No stack trace available' }}</div>
            </div>
        </div>
    </div>

    <script>
        const errorData = @json($error);

        // Theme management
        function initTheme() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);
            updateThemeIcon(savedTheme);
        }

        function toggleTheme() {
            const currentTheme = document.documentElement.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            updateThemeIcon(newTheme);
        }

        function updateThemeIcon(theme) {
            const sunIcon = document.getElementById('sunIcon');
            const moonIcon = document.getElementById('moonIcon');
            if (theme === 'dark') {
                sunIcon.style.display = 'block';
                moonIcon.style.display = 'none';
            } else {
                sunIcon.style.display = 'none';
                moonIcon.style.display = 'block';
            }
        }

        // Initialiser le thème au chargement de la page
        initTheme();

        function toggleDropdown(event) {
            event.stopPropagation();
            const dropdown = document.getElementById('copyDropdown');
            dropdown.classList.toggle('show');
        }

        // Fermer le dropdown si on clique ailleurs
        window.onclick = function(event) {
            if (!event.target.matches('.btn')) {
                const dropdowns = document.getElementsByClassName('dropdown-content');
                for (let i = 0; i < dropdowns.length; i++) {
                    dropdowns[i].classList.remove('show');
                }
            }
        }

        function copyText(text, successMessage = 'Copied!', targetButton = null) {
            const textarea = document.createElement('textarea');
            textarea.value = text;
            textarea.style.position = 'fixed';
            textarea.style.opacity = '0';
            document.body.appendChild(textarea);
            textarea.select();

            try {
                const successful = document.execCommand('copy');
                if (successful) {
                    // Fermer le dropdown
                    document.getElementById('copyDropdown').classList.remove('show');

                    // Afficher le message de succès sur le bon bouton
                    const btn = targetButton || document.querySelector('.dropdown .btn');
                    const originalText = btn.innerHTML;
                    btn.innerHTML = '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> ' + successMessage;
                    setTimeout(() => {
                        btn.innerHTML = originalText;
                    }, 2000);
                } else {
                    alert('Failed to copy');
                }
            } catch (err) {
                alert('Failed to copy');
            } finally {
                document.body.removeChild(textarea);
            }
        }

        function copyAsMarkdown() {
            let errorInfo = `## Error Information
- **Message:** ${errorData.message || 'No message available'}`;

            if (errorData.appFile) {
                errorInfo += `
- **Application File:** ${errorData.appFile}
- **Application Line:** ${errorData.appLine || 'N/A'}
- **Origin File:** ${errorData.file || 'N/A'}
- **Origin Line:** ${errorData.line || 'N/A'}`;
            } else {
                errorInfo += `
- **File:** ${errorData.file || 'N/A'}
- **Line:** ${errorData.line || 'N/A'}`;
            }

            const markdown = `# Error Details

**Error ID:** ${errorData.id || 'N/A'}

${errorInfo}

## Request Information
- **Method:** ${errorData.method || 'N/A'}
- **URL:** ${errorData.url || 'N/A'}
- **IP Address:** ${errorData.ip || 'N/A'}
- **Request Time:** ${errorData.requestTime || 'N/A'}
- **User Agent:** ${errorData.userAgent || 'N/A'}
- **Referrer:** ${errorData.referrer || 'Direct access'}

${errorData.authUser ? `## User Information
- **User ID:** ${errorData.authUser.id || 'Guest'}
- **Name:** ${errorData.authUser.name || 'Guest'}
- **Email:** ${errorData.authUser.email || 'Guest'}
` : ''}
## Stack Trace
\`\`\`
${errorData.stackTrace || 'No stack trace available'}
\`\`\`

---
**Error URL:** ${window.location.href}`;

            copyText(markdown, 'Copied as Markdown!');
        }

        function copyAsJson() {
            const json = JSON.stringify(errorData, null, 2);
            copyText(json, 'Copied as JSON!');
        }

        function shareError(event) {
            const url = window.location.href;
            const title = 'Error Details - ' + (errorData.id || 'N/A');
            const text = 'Error: ' + (errorData.message || 'No message');

            if (navigator.share) {
                navigator.share({
                    title: title,
                    text: text,
                    url: url
                }).catch(err => {
                    console.log('Error sharing:', err);
                });
            } else {
                // Fallback: copy URL sur le bouton Share
                const shareBtn = event.target.closest('.btn');
                copyText(url, 'URL Copied!', shareBtn);
            }
        }
    </script>
</body>
</html>

