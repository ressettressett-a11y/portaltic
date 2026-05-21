"""Fix the corrupted innerHTML in renderCards"""
with open('index.html', 'r', encoding='utf-8') as f:
    content = f.read()

# Find and replace the broken innerHTML block
# The broken lines are:
#   el.innerHTML =
#       '<div class="top-card-icon" ...>' + iconHtml + '</div>' +
#       '<span class="top-card-label">'ápido</span>' +
#       '<span class="info-box-number">' + esc(card.title || '') + '</span></div>';

import re

# Match from "el.innerHTML =" to the next "container.appendChild"
pattern = r"el\.innerHTML =\s*\n\s*'<div class=\"top-card-icon\".*?container\.appendChild\(el\);"
match = re.search(pattern, content, re.DOTALL)
if match:
    old_block = match.group(0)
    print("Found broken block:")
    print(repr(old_block[:200]))
    
    new_block = """el.innerHTML =
                        '<div class="top-card-icon" style="color:' + color + ';background:' + color + '15;">' + iconHtml + '</div>' +
                        '<span class="top-card-label">' + esc(card.title || '') + '</span>';
                    container.appendChild(el);"""
    
    content = content.replace(old_block, new_block)
    print("\nReplaced with clean block")
else:
    print("Pattern not found, trying alternate approach")
    # Find line by line
    lines = content.split('\n')
    for i, line in enumerate(lines):
        if "top-card-label" in line:
            print(f"  Line {i+1}: {repr(line)}")
        if "info-box-number" in line:
            print(f"  Line {i+1}: {repr(line)}")

with open('index.html', 'w', encoding='utf-8') as f:
    f.write(content)

# Verify
with open('index.html', 'r', encoding='utf-8') as f:
    content = f.read()

idx = content.index('function renderCards')
end = content.index('\n            }', idx+100)
block = content[idx:end+14]
print("\n--- Final renderCards ---")
print(block)
print("--- End ---")

# Check for remaining issues
if "info-box-number" in block:
    print("\nWARNING: info-box-number still present in renderCards!")
if "ápido" in block:
    print("WARNING: corrupted text still present!")
if "top-card-label" in block:
    print("OK: top-card-label present")
