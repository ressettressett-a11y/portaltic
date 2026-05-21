import re

with open('index.html', 'r', encoding='utf-8') as f:
    content = f.read()
    lines = content.split('\n')

print(f"Total lines: {len(lines)}")

# Find the corrupted section boundaries
# Line 1547 (0-indexed 1546) is the corrupted merge line
# Line 1624 (0-indexed 1623) is the last </div> before MAIN CONTENT

# Keep everything before line 1547 (lines 0-1545)
before = lines[:1546]

# Find the MAIN CONTENT comment line
main_idx = None
for i, line in enumerate(lines):
    if '<!-- ' in line and 'MAIN CONTENT' in line:
        main_idx = i
        break

print(f"MAIN CONTENT found at line {main_idx + 1}")

# Keep everything from MAIN CONTENT onward
after = lines[main_idx:]

# Build the replacement section
replacement = """        /* --- EMPTY STATE ----------------------------------------- */
        .empty-state {
            text-align: center;
            padding: 30px 0;
            color: var(--text-muted);
        }
        .empty-icon {
            font-size: 2.5rem;
            margin-bottom: 8px;
        }

        /* --- RESPONSIVE ------------------------------------------ */
        @media (max-width: 768px) {
            .dual-row { grid-template-columns: 1fr; }
            .content { padding: 0 16px 16px; }
            .navbar { padding: 0 16px; }
            .content-header { padding: 32px 16px 28px; }
            .bookmarks-grid { grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); }
            .top-cards-container { gap: 10px; }
        }
    </style>
</head>

<body>
    <!-- --- NAVBAR ------------------------------------------------ -->
    <nav class="navbar">
        <div class="navbar-left">
            <img id="nav-logo" src="" alt="Logo">
            <span id="site-title">Portal de Soporte TI</span>
        </div>
        <div class="navbar-right">
            <button class="nav-btn-premium" onclick="openBookmarkModal()">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                Marcador
            </button>
            <span id="navbar-clock"></span>
        </div>
    </nav>

    <!-- --- CONTENT HEADER ---------------------------------------- -->
    <div class="content-header">
        <div class="institutional-badge">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
            Portal Institucional
        </div>
        <div class="header-search-container fade-in" style="animation-delay: 0.2s;">
            <div class="search-glow-wrapper">
                <form id="header-search-form" class="header-search">
                    <div class="search-google-icon">
                        <svg viewBox="0 0 24 24" width="24" height="24">
                            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4" />
                            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853" />
                            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05" />
                            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335" />
                        </svg>
                    </div>
                    <div class="search-divider"></div>
                    <input autofocus type="text" id="header-search-input"
                        placeholder="Buscar en Google o escribir una URL" autocomplete="off">
                    <button type="submit" class="search-submit-btn" title="Buscar" aria-label="Buscar">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                            stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                    </button>
                </form>
                <div id="search-suggestions"></div>
            </div>
            <div class="search-hint">Presiona <kbd>Enter</kbd> para buscar &middot; Escribe una URL para navegar directo</div>
        </div>
    </div>
"""

# Combine
new_lines = before + replacement.split('\n') + after
new_content = '\n'.join(new_lines)

with open('index.html', 'w', encoding='utf-8') as f:
    f.write(new_content)

# Verify
with open('index.html', 'r', encoding='utf-8') as f:
    verify = f.read().split('\n')
print(f"New total lines: {len(verify)}")

# Check for </style> and <body>
for i, line in enumerate(verify):
    if '</style>' in line:
        print(f"</style> found at line {i+1}")
    if '<body>' in line:
        print(f"<body> found at line {i+1}")
    if 'MAIN CONTENT' in line and '<!--' in line:
        print(f"MAIN CONTENT at line {i+1}")
        break
