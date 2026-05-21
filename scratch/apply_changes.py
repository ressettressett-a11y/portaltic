"""
Apply all UI modernization changes to index.html:
1. Update CSS variables (premium institutional palette)
2. Update content-header (dark gradient background)
3. Update institutional badge (for dark bg)
4. Update bookmark CSS (smaller, full text visible)
5. Update renderCards JS (use top-card class)
6. Update appendBookmarkCards → renderBookmarks (into #bookmarks-grid)
7. Update deleteBookmark/saveBookmark to call renderBookmarks
"""

with open('index.html', 'r', encoding='utf-8') as f:
    content = f.read()

# ============================================================
# 1. UPDATE CSS VARIABLES
# ============================================================
old_vars = """            --primary: #1e40af;
            --primary-dark: #1e3a8a;
            --primary-light: #eff6ff;
            --accent: #6366f1;
            --accent-light: #eef2ff;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --bg: #f8fafc;
            --surface: rgba(255, 255, 255, 0.9);
            --border: #e2e8f0;
            --text: #0f172a;
            --text-muted: #64748b;
            --nav-bg: rgba(255, 255, 255, 0.8);
            --nav-h: 64px;
            --radius: 16px;
            --radius-sm: 10px;
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-md: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --shadow-hover: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            --transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --gradient-warm: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);"""

new_vars = """            --primary: #0f2b5b;
            --primary-dark: #091d40;
            --primary-light: #e8f0fe;
            --accent: #3b6cf5;
            --accent-light: #edf2ff;
            --success: #059669;
            --warning: #d97706;
            --danger: #dc2626;
            --bg: #f0f4f8;
            --surface: rgba(255, 255, 255, 0.97);
            --border: #d5dde8;
            --text: #1a202c;
            --text-muted: #5a6b82;
            --nav-bg: rgba(15, 43, 91, 0.97);
            --nav-h: 52px;
            --radius: 14px;
            --radius-sm: 8px;
            --shadow: 0 1px 3px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04);
            --shadow-md: 0 4px 12px rgba(0,0,0,0.07), 0 2px 4px rgba(0,0,0,0.04);
            --shadow-hover: 0 8px 24px rgba(0,0,0,0.10), 0 4px 8px rgba(0,0,0,0.06);
            --transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --gradient-warm: linear-gradient(135deg, #0a1f42 0%, #163a72 40%, #1a56db 100%);"""

content = content.replace(old_vars, new_vars)
print("[1] CSS variables updated")

# ============================================================
# 2. UPDATE CONTENT HEADER (dark gradient bg)
# ============================================================
content = content.replace(
    """.content-header {
            background: var(--bg);
            padding: 48px 30px 40px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: var(--text);
            position: relative;
            overflow: hidden;
            border-bottom: 1px solid var(--border);
        }""",
    """.content-header {
            background: var(--gradient-warm);
            padding: 48px 30px 40px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: #fff;
            position: relative;
            overflow: hidden;
            border-bottom: none;
        }"""
)
print("[2] Content header background updated")

# Update the ::before pseudo element for a subtle glow
content = content.replace(
    "background: radial-gradient(circle at 50% -20%, var(--primary-light) 0%, transparent 70%);",
    "background: radial-gradient(circle at 50% 120%, rgba(59,108,245,0.25) 0%, transparent 55%);"
)
print("[2b] Content header ::before updated")

# ============================================================
# 3. UPDATE NAVBAR for dark theme
# ============================================================
content = content.replace(
    """.navbar {
            background: var(--nav-bg);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            height: var(--nav-h);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
            box-shadow: 0 1px 0 var(--border);
            position: sticky;
            top: 0;
            z-index: 100;
            gap: 16px;
        }""",
    """.navbar {
            background: var(--nav-bg);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            height: var(--nav-h);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            position: sticky;
            top: 0;
            z-index: 100;
            gap: 16px;
        }"""
)
print("[3] Navbar updated")

# Update site-title for dark navbar
content = content.replace(
    """#site-title {
            color: var(--text);
            font-size: 1.15rem;""",
    """#site-title {
            color: #fff;
            font-size: 1rem;"""
)
print("[3b] Site title color updated")

# Update navbar clock for dark bg
content = content.replace(
    """#navbar-clock {
            color: var(--text-muted);
            font-size: 0.85rem;
            font-weight: 500;
            min-width: 60px;
            text-align: center;
            font-variant-numeric: tabular-nums;
            background: var(--bg);
            padding: 6px 12px;
            border-radius: 99px;
            border: 1px solid var(--border);
        }""",
    """#navbar-clock {
            color: rgba(255,255,255,0.7);
            font-size: 0.8rem;
            font-weight: 500;
            min-width: 60px;
            text-align: center;
            font-variant-numeric: tabular-nums;
            background: rgba(255,255,255,0.08);
            padding: 5px 12px;
            border-radius: 99px;
            border: 1px solid rgba(255,255,255,0.12);
        }"""
)
print("[3c] Clock updated")

# Update nav-btn-premium for dark navbar
content = content.replace(
    """.nav-btn-premium {
            background: var(--primary);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 99px;
            font-size: 0.85rem;
            font-weight: 600;
            transition: all var(--transition);
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 12px rgba(30, 64, 175, 0.2);
            cursor: pointer;
        }""",
    """.nav-btn-premium {
            background: rgba(255,255,255,0.12);
            color: rgba(255,255,255,0.9);
            border: 1px solid rgba(255,255,255,0.15);
            padding: 6px 14px;
            border-radius: 99px;
            font-size: 0.8rem;
            font-weight: 600;
            transition: all var(--transition);
            display: flex;
            align-items: center;
            gap: 6px;
            box-shadow: none;
            cursor: pointer;
        }"""
)
print("[3d] Nav button updated")

content = content.replace(
    """.nav-btn-premium:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(30, 64, 175, 0.3);
        }""",
    """.nav-btn-premium:hover {
            background: rgba(255,255,255,0.2);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }"""
)
print("[3e] Nav button hover updated")

# ============================================================
# 4. UPDATE INSTITUTIONAL BADGE for dark bg
# ============================================================
content = content.replace(
    """.institutional-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            background: var(--primary-light);
            color: var(--primary);
            border-radius: 99px;
            font-size: 0.75rem;
            font-weight: 700;
            margin-bottom: 24px;
            position: relative;
            z-index: 1;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }""",
    """.institutional-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            background: rgba(255,255,255,0.1);
            color: rgba(255,255,255,0.85);
            border-radius: 99px;
            font-size: 0.72rem;
            font-weight: 700;
            margin-bottom: 24px;
            position: relative;
            z-index: 1;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            border: 1px solid rgba(255,255,255,0.12);
            backdrop-filter: blur(4px);
        }"""
)
print("[4] Institutional badge updated")

# ============================================================
# 5. UPDATE BOOKMARK CSS (smaller, full text)
# ============================================================
content = content.replace(
    """.bookmarks-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 12px;
        }""",
    """.bookmarks-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 8px;
        }"""
)
print("[5a] Bookmarks grid updated")

content = content.replace(
    """.bookmark-card {
            display: flex;
            align-items: center;
            gap: 12px;
            background: var(--surface);
            border: 1px solid var(--border);
            padding: 10px 14px;
            border-radius: var(--radius-sm);
            text-decoration: none;
            color: var(--text);
            transition: all var(--transition);
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
            position: relative;
            overflow: hidden;
        }""",
    """.bookmark-card {
            display: flex;
            align-items: center;
            gap: 8px;
            background: var(--surface);
            border: 1px solid var(--border);
            padding: 6px 10px;
            border-radius: var(--radius-sm);
            text-decoration: none;
            color: var(--text);
            transition: all var(--transition);
            box-shadow: var(--shadow);
            position: relative;
            overflow: hidden;
        }"""
)
print("[5b] Bookmark card padding updated")

content = content.replace(
    """.bookmark-icon {
            font-size: 1rem;
            width: 28px;
            height: 28px;
            min-width: 28px;""",
    """.bookmark-icon {
            font-size: 0.85rem;
            width: 24px;
            height: 24px;
            min-width: 24px;"""
)
print("[5c] Bookmark icon size updated")

content = content.replace(
    """.bookmark-name {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text);
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            display: block;
        }""",
    """.bookmark-name {
            font-size: 0.72rem;
            font-weight: 600;
            color: var(--text);
            overflow: visible;
            white-space: normal;
            word-break: break-word;
            display: block;
            line-height: 1.3;
        }"""
)
print("[5d] Bookmark name style updated (smaller, full text)")

content = content.replace(
    """.bookmark-url {
            font-size: 0.65rem;
            color: var(--text-muted);
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            display: block;
            margin-top: 1px;
        }""",
    """.bookmark-url {
            font-size: 0.6rem;
            color: var(--text-muted);
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            display: block;
            margin-top: 1px;
        }"""
)
print("[5e] Bookmark URL style updated")

# ============================================================
# 6. UPDATE renderCards JS to use top-card class
# ============================================================
content = content.replace(
    """function renderCards(cards) {
                var container = document.getElementById('top-cards');
                container.innerHTML = '';
                cards.forEach(function (card, i) {
                    var color = CARD_COLORS[i % CARD_COLORS.length];
                    var el = document.createElement('a');
                    el.href = card.button_url || '#';
                    el.target = '_blank';
                    el.rel = 'noopener noreferrer';
                    el.className = 'info-box fade-in';
                    el.style.animationDelay = (i * 0.06) + 's';
                    var iconHtml = card.image_path
                        ? '<img src="' + esc(card.image_path) + '" alt="" onerror="this.style.display=\\'none\\'">'
                        : '<span style="font-size:1.4rem;">&#128279;</span>';
                    el.innerHTML =
                        '<div class="info-box-icon" style="color:' + color + ';background:' + color + '15;">' + iconHtml + '</div>' +
                        '<div class="info-box-content"><span class="info-box-text">Acceso R\u00e1pido</span>' +
                        '<span class="info-box-number">' + esc(card.title || '') + '</span></div>';
                    container.appendChild(el);
                });
                appendBookmarkCards(container, cards.length);
            }""",
    """function renderCards(cards) {
                var container = document.getElementById('top-cards');
                container.innerHTML = '';
                cards.forEach(function (card, i) {
                    var color = CARD_COLORS[i % CARD_COLORS.length];
                    var el = document.createElement('a');
                    el.href = card.button_url || '#';
                    el.target = '_blank';
                    el.rel = 'noopener noreferrer';
                    el.className = 'top-card fade-in';
                    el.style.animationDelay = (i * 0.06) + 's';
                    el.title = card.title || '';
                    var iconHtml = card.image_path
                        ? '<img src="' + esc(card.image_path) + '" alt="" onerror="this.style.display=\\'none\\'">'
                        : '<span style="font-size:1.2rem;">&#128279;</span>';
                    el.innerHTML =
                        '<div class="top-card-icon" style="color:' + color + ';background:' + color + '15;">' + iconHtml + '</div>' +
                        '<span class="top-card-label">' + esc(card.title || '') + '</span>';
                    container.appendChild(el);
                });
                renderBookmarks();
            }"""
)
print("[6] renderCards updated to use top-card")

# ============================================================
# 7. REPLACE appendBookmarkCards with renderBookmarks
# ============================================================
content = content.replace(
    """function appendBookmarkCards(container, offset) {
                var bookmarks = getBookmarks();
                if (bookmarks.length === 0) return;
                offset = offset || 0;
                bookmarks.forEach(function (bm, i) {
                    var wrapper = document.createElement('div');
                    wrapper.className = 'info-box-wrapper fade-in';
                    wrapper.style.animationDelay = ((offset + i) * 0.06) + 's';
                    var el = document.createElement('a');
                    el.href = bm.url; el.target = '_blank'; el.rel = 'noopener noreferrer';
                    el.className = 'info-box';
                    el.innerHTML =
                        '<div class="info-box-icon" style="color:#10b981;background:#10b98115;font-size:1.5rem;">' +
                        esc(bm.icon || '') + (bm.icon ? '' : '<span>&#128279;</span>') + '</div>' +
                        '<div class="info-box-content"><span class="info-box-text">Marcador</span>' +
                        '<span class="info-box-number" title="' + esc(bm.title) + '">' + esc(bm.title) + '</span></div>';
                    var delBtn = document.createElement('button');
                    delBtn.className = 'bm-delete-btn'; delBtn.title = 'Eliminar'; delBtn.innerHTML = '&#10005;';
                    delBtn.onclick = function (e) { e.preventDefault(); e.stopPropagation(); deleteBookmark(i); };
                    wrapper.appendChild(el); wrapper.appendChild(delBtn);
                    container.appendChild(wrapper);
                });
            }""",
    """function renderBookmarks() {
                var bookmarks = getBookmarks();
                var grid = document.getElementById('bookmarks-grid');
                if (!grid) return;
                grid.innerHTML = '';
                var section = grid.closest('.bookmarks-section');
                if (bookmarks.length === 0) {
                    if (section) section.style.display = 'none';
                    return;
                }
                if (section) section.style.display = '';
                bookmarks.forEach(function (bm, i) {
                    var card = document.createElement('a');
                    card.href = bm.url;
                    card.target = '_blank';
                    card.rel = 'noopener noreferrer';
                    card.className = 'bookmark-card fade-in';
                    card.style.animationDelay = (i * 0.04) + 's';
                    card.innerHTML =
                        '<div class="bookmark-icon">' + esc(bm.icon || '\\u{1F517}') + '</div>' +
                        '<div class="bookmark-content">' +
                        '<span class="bookmark-name">' + esc(bm.title) + '</span>' +
                        '<span class="bookmark-url">' + esc(bm.url) + '</span></div>';
                    var delBtn = document.createElement('button');
                    delBtn.className = 'bm-delete-btn';
                    delBtn.title = 'Eliminar';
                    delBtn.innerHTML = '&#10005;';
                    delBtn.onclick = function (e) { e.preventDefault(); e.stopPropagation(); deleteBookmark(i); };
                    card.appendChild(delBtn);
                    grid.appendChild(card);
                });
            }"""
)
print("[7] appendBookmarkCards replaced with renderBookmarks")

# ============================================================
# 8. UPDATE deleteBookmark to call renderBookmarks
# ============================================================
content = content.replace(
    """window.deleteBookmark = function (index) {
                var bookmarks = getBookmarks();
                if (index < 0 || index >= bookmarks.length) return;
                var name = bookmarks[index].title;
                bookmarks.splice(index, 1); setBookmarks(bookmarks);
                if (portalData) renderCards(portalData.cards || []);
                renderBookmarkPanel();
                showToast('Marcador eliminado: ' + name, 'error');
            };""",
    """window.deleteBookmark = function (index) {
                var bookmarks = getBookmarks();
                if (index < 0 || index >= bookmarks.length) return;
                var name = bookmarks[index].title;
                bookmarks.splice(index, 1); setBookmarks(bookmarks);
                renderBookmarks();
                showToast('Marcador eliminado: ' + name, 'error');
            };"""
)
print("[8] deleteBookmark updated")

# ============================================================
# 9. UPDATE saveBookmark to call renderBookmarks
# ============================================================
content = content.replace(
    """closeBookmarkModal();
                if (portalData) renderCards(portalData.cards || []);
                if (document.getElementById('quick-links-box')) renderBookmarkPanel();
                showToast('Marcador guardado: ' + title, 'success');""",
    """closeBookmarkModal();
                renderBookmarks();
                showToast('Marcador guardado: ' + title, 'success');"""
)
print("[9] saveBookmark updated")

# ============================================================
# 10. UPDATE renderPortal to call renderBookmarks
# ============================================================
content = content.replace(
    "if (document.getElementById('quick-links-box')) renderBookmarkPanel();",
    "renderBookmarks();"
)
print("[10] renderPortal updated")

# ============================================================
# WRITE
# ============================================================
with open('index.html', 'w', encoding='utf-8') as f:
    f.write(content)

# Verify key changes
checks = [
    ('--primary: #0f2b5b', 'CSS vars'),
    ('gradient-warm', 'gradient'),
    ('top-card fade-in', 'top-card class in JS'),
    ('renderBookmarks()', 'renderBookmarks call'),
    ('font-size: 0.72rem', 'bookmark name size'),
    ('</style>', 'style tag closed'),
    ('<body>', 'body tag'),
]
for needle, label in checks:
    if needle in content:
        print(f"  OK: {label}")
    else:
        print(f"  MISSING: {label}")

print("\nDone! All changes applied.")
