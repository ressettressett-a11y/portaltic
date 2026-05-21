"""Fix top-cards: bigger size, stable hover (no layout shift)"""
with open('index.html', 'r', encoding='utf-8') as f:
    content = f.read()

# Replace the entire top-card CSS block
old_container = """.top-cards-container {
            display: flex;
            justify-content: center;
            gap: 16px;
            margin-bottom: 32px;
            flex-wrap: wrap;
            padding: 10px;
        }"""

new_container = """.top-cards-container {
            display: flex;
            justify-content: center;
            gap: 14px;
            margin-bottom: 32px;
            flex-wrap: wrap;
            padding: 10px 20px;
        }"""

content = content.replace(old_container, new_container)
print("[1] Container updated")

# Replace top-card base
old_card = """.top-card {
            display: flex;
            align-items: center;
            background: var(--surface);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border: 1px solid var(--border);
            border-radius: 40px;
            padding: 8px;
            text-decoration: none;
            color: var(--text);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            overflow: hidden;
            max-width: 56px; /* Initially icon only */
            white-space: nowrap;
            box-shadow: var(--shadow);
            cursor: pointer;
        }"""

new_card = """.top-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: var(--surface);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 16px 12px 10px;
            text-decoration: none;
            color: var(--text);
            transition: all 0.25s ease;
            white-space: nowrap;
            box-shadow: var(--shadow);
            cursor: pointer;
            width: 88px;
            position: relative;
        }"""

content = content.replace(old_card, new_card)
print("[2] Top-card base updated")

# Replace top-card hover
old_hover = """.top-card:hover {
            max-width: 280px;
            padding-right: 20px;
            border-color: var(--primary);
            box-shadow: var(--shadow-md);
            transform: translateY(-2px);
        }"""

new_hover = """.top-card:hover {
            border-color: var(--accent);
            box-shadow: var(--shadow-md);
            transform: translateY(-3px);
            background: #fff;
        }"""

content = content.replace(old_hover, new_hover)
print("[3] Top-card hover updated")

# Replace top-card-icon
old_icon = """.top-card-icon {
            width: 40px;
            height: 40px;
            min-width: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--primary-light);
            border-radius: 50%;
            font-size: 1.3rem;
            color: var(--primary);
            transition: transform 0.3s ease;
        }"""

new_icon = """.top-card-icon {
            width: 48px;
            height: 48px;
            min-width: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--primary-light);
            border-radius: 14px;
            font-size: 1.4rem;
            color: var(--primary);
            transition: transform 0.25s ease;
        }"""

content = content.replace(old_icon, new_icon)
print("[4] Top-card icon updated")

# Replace icon hover
old_icon_hover = """.top-card:hover .top-card-icon {
            transform: rotate(5deg) scale(1.05);
        }"""

new_icon_hover = """.top-card:hover .top-card-icon {
            transform: scale(1.08);
        }"""

content = content.replace(old_icon_hover, new_icon_hover)
print("[5] Icon hover updated")

# Replace icon img size
old_img = """.top-card-icon img {
            width: 24px;
            height: 24px;
            object-fit: contain;
        }"""

new_img = """.top-card-icon img {
            width: 28px;
            height: 28px;
            object-fit: contain;
        }"""

content = content.replace(old_img, new_img)
print("[6] Icon img size updated")

# Replace top-card-label (now shows below icon as small text, tooltip on hover)
old_label = """.top-card-label {
            margin-left: 12px;
            font-weight: 600;
            font-size: 0.95rem;
            opacity: 0;
            transform: translateX(-15px);
            transition: all 0.3s ease;
            color: var(--text);
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
            transition: color 0.25s ease;
        }"""

content = content.replace(old_label, new_label)
print("[7] Label style updated")

# Replace label hover
old_label_hover = """.top-card:hover .top-card-label {
            opacity: 1;
            transform: translateX(0);
        }"""

new_label_hover = """.top-card:hover .top-card-label {
            color: var(--primary);
        }"""

content = content.replace(old_label_hover, new_label_hover)
print("[8] Label hover updated")

# Write
with open('index.html', 'w', encoding='utf-8') as f:
    f.write(content)

print("\nDone! Cards are now bigger with stable hover (no layout shift).")
