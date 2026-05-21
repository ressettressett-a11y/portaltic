"""Fix renderCards to use top-card class"""
with open('index.html', 'r', encoding='utf-8') as f:
    content = f.read()

# Fix renderCards: replace info-box with top-card structure
old_rc = "el.className = 'info-box fade-in';"
new_rc = "el.className = 'top-card fade-in';"
content = content.replace(old_rc, new_rc)
print(f"[1] className: {'OK' if new_rc in content else 'FAIL'}")

# Fix the innerHTML to use top-card structure
old_inner = """var iconHtml = card.image_path
                        ? '<img src="' + esc(card.image_path) + '" alt="" onerror="this.style.display=\\'none\\'">'
                        : '<span style="font-size:1.4rem;">&#128279;</span>';
                    el.innerHTML =
                        '<div class="info-box-icon" style="color:' + color + ';background:' + color + '15;">' + iconHtml + '</div>' +
                        '<div class="info-box-content"><span class="info-box-text">Acceso R\u00e1pido</span>' +
                        '<span class="info-box-number">' + esc(card.title || '') + '</span></div>';"""

new_inner = """el.title = card.title || '';
                    var iconHtml = card.image_path
                        ? '<img src="' + esc(card.image_path) + '" alt="" onerror="this.style.display=\\'none\\'">'
                        : '<span style="font-size:1.2rem;">&#128279;</span>';
                    el.innerHTML =
                        '<div class="top-card-icon" style="color:' + color + ';background:' + color + '15;">' + iconHtml + '</div>' +
                        '<span class="top-card-label">' + esc(card.title || '') + '</span>';"""

if old_inner in content:
    content = content.replace(old_inner, new_inner)
    print("[2] innerHTML: OK")
else:
    # Try with actual unicode character
    old_inner2 = old_inner.replace('Acceso R\u00e1pido', 'Acceso R' + chr(225) + 'pido')
    if old_inner2 in content:
        content = content.replace(old_inner2, new_inner)
        print("[2] innerHTML (unicode): OK")
    else:
        print("[2] innerHTML: FAIL - trying line-by-line")
        # Replace key parts individually
        content = content.replace("'<span style=\"font-size:1.4rem;\">&#128279;</span>';", "'<span style=\"font-size:1.2rem;\">&#128279;</span>';")
        content = content.replace("'<div class=\"info-box-icon\" style=\"color:'", "'<div class=\"top-card-icon\" style=\"color:'")
        content = content.replace("'</div>' +\n                        '<div class=\"info-box-content\"><span class=\"info-box-text\">Acceso R", "'</div>' +\n                        '<span class=\"top-card-label\">'")
        # This needs more careful handling
        print("[2] Trying individual replacements...")

# Fix appendBookmarkCards call
content = content.replace(
    "appendBookmarkCards(container, cards.length);",
    "renderBookmarks();"
)
print(f"[3] appendBookmarkCards->renderBookmarks: {'OK' if 'renderBookmarks();' in content else 'FAIL'}")

with open('index.html', 'w', encoding='utf-8') as f:
    f.write(content)

# Verify
print("\nVerification:")
print(f"  top-card fade-in: {'top-card fade-in' in content}")
print(f"  top-card-icon: {'top-card-icon' in content}")
print(f"  top-card-label: {'top-card-label' in content}")
print(f"  renderBookmarks: {'renderBookmarks()' in content}")
print(f"  info-box fade-in (should be gone): {'info-box fade-in' in content}")
