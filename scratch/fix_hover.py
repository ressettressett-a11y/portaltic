"""Enhance top-card hover: stronger highlight + show info"""
with open('index.html', 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Enhance hover effect - stronger highlight with glow
old_hover = """.top-card:hover {
            border-color: var(--accent);
            box-shadow: var(--shadow-md);
            transform: translateY(-3px);
            background: #fff;
        }"""

new_hover = """.top-card:hover {
            border-color: var(--accent);
            box-shadow: 0 8px 24px rgba(59,108,245,0.18), 0 0 0 2px rgba(59,108,245,0.12);
            transform: translateY(-4px) scale(1.04);
            background: #fff;
        }"""

content = content.replace(old_hover, new_hover)
print("[1] Hover glow enhanced")

# 2. Make label bigger and more visible on hover
old_label = """.top-card-label {
            font-weight: 600;
            font-size: 0.65rem;
            color: var(--text-muted);
            margin-top: 6px;
            text-align: center;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 72px;
            line-height: 1.2;
            transition: color 0.25s ease;
        }"""

new_label = """.top-card-label {
            font-weight: 600;
            font-size: 0.65rem;
            color: var(--text-muted);
            margin-top: 6px;
            text-align: center;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 72px;
            line-height: 1.2;
            transition: all 0.25s ease;
        }"""

content = content.replace(old_label, new_label)
print("[2] Label transition updated")

old_label_hover = """.top-card:hover .top-card-label {
            color: var(--primary);
        }"""

new_label_hover = """.top-card:hover .top-card-label {
            color: var(--primary);
            font-weight: 700;
        }"""

content = content.replace(old_label_hover, new_label_hover)
print("[3] Label hover bold")

# 3. Add a tooltip that shows the full name on hover (for cards with long names)
# Add CSS right after the label hover
tooltip_css = """

        /* Tooltip showing full name on hover */
        .top-card::after {
            content: attr(data-tooltip);
            position: absolute;
            bottom: calc(100% + 8px);
            left: 50%;
            transform: translateX(-50%) translateY(4px);
            background: var(--primary-dark);
            color: #fff;
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 0.75rem;
            font-weight: 600;
            white-space: nowrap;
            opacity: 0;
            pointer-events: none;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 10;
        }
        .top-card::before {
            content: '';
            position: absolute;
            bottom: calc(100% + 4px);
            left: 50%;
            transform: translateX(-50%);
            border: 5px solid transparent;
            border-top-color: var(--primary-dark);
            opacity: 0;
            pointer-events: none;
            transition: all 0.2s ease;
            z-index: 10;
        }
        .top-card:hover::after,
        .top-card:hover::before {
            opacity: 1;
            transform: translateX(-50%) translateY(0);
        }
        .top-card:hover::before {
            transform: translateX(-50%);
        }"""

# Insert after .top-card:hover .top-card-label block
insert_marker = """.top-card:hover .top-card-label {
            color: var(--primary);
            font-weight: 700;
        }"""

content = content.replace(insert_marker, insert_marker + tooltip_css)
print("[4] Tooltip CSS added")

# 4. Also enhance the icon hover
old_icon_hover = """.top-card:hover .top-card-icon {
            transform: scale(1.08);
        }"""

new_icon_hover = """.top-card:hover .top-card-icon {
            transform: scale(1.1);
            box-shadow: 0 4px 12px rgba(59,108,245,0.15);
        }"""

content = content.replace(old_icon_hover, new_icon_hover)
print("[5] Icon hover enhanced")

# 5. Update JS renderCards to add data-tooltip attribute
old_js = "el.innerHTML ="
# We need to add data-tooltip before innerHTML
# Find it in context of renderCards
idx = content.index('function renderCards')
rc_end = content.index('renderBookmarks();', idx)
rc_block = content[idx:rc_end]

if "el.title = card.title" not in rc_block:
    # Add title attribute for tooltip
    content = content.replace(
        "el.className = 'top-card fade-in';",
        "el.className = 'top-card fade-in';\n                    el.setAttribute('data-tooltip', card.title || '');"
    )
    print("[6] data-tooltip attribute added to JS")
else:
    # Already has title, just add data-tooltip
    content = content.replace(
        "el.className = 'top-card fade-in';",
        "el.className = 'top-card fade-in';\n                    el.setAttribute('data-tooltip', card.title || '');"
    )
    print("[6] data-tooltip attribute added to JS")

with open('index.html', 'w', encoding='utf-8') as f:
    f.write(content)

print("\nDone! Cards now have stronger hover highlight + tooltip with full name.")
