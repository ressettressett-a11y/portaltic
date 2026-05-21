"""Resize top-cards: 10% larger"""
with open('index.html', 'r', encoding='utf-8') as f:
    content = f.read()

# Update top-card dimensions
old_card = """        .top-card {
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

new_card = """        .top-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: var(--surface);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 18px 14px 12px;
            text-decoration: none;
            color: var(--text);
            transition: all 0.25s ease;
            white-space: nowrap;
            box-shadow: var(--shadow);
            cursor: pointer;
            width: 98px;
            position: relative;
        }"""

content = content.replace(old_card, new_card)
print("[1] Top-card size updated")

# Update icon size
old_icon = """        .top-card-icon {
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

new_icon = """        .top-card-icon {
            width: 54px;
            height: 54px;
            min-width: 54px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--primary-light);
            border-radius: 16px;
            font-size: 1.6rem;
            color: var(--primary);
            transition: transform 0.25s ease;
        }"""

content = content.replace(old_icon, new_icon)
print("[2] Icon size updated")

# Update icon image size
old_img = """        .top-card-icon img {
            width: 28px;
            height: 28px;
            object-fit: contain;
        }"""

new_img = """        .top-card-icon img {
            width: 32px;
            height: 32px;
            object-fit: contain;
        }"""

content = content.replace(old_img, new_img)
print("[3] Icon img size updated")

# Update label size
old_label = """        .top-card-label {
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

new_label = """        .top-card-label {
            font-weight: 600;
            font-size: 0.7rem;
            color: var(--text-muted);
            margin-top: 8px;
            text-align: center;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 84px;
            line-height: 1.2;
            transition: all 0.25s ease;
        }"""

content = content.replace(old_label, new_label)
print("[4] Label size updated")

with open('index.html', 'w', encoding='utf-8') as f:
    f.write(content)

print("\nDone! Top-cards are now 10% larger.")
