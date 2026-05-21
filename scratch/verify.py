"""Verify all changes are properly applied"""
with open('index.html', 'r', encoding='utf-8') as f:
    c = f.read()

checks = [
    ('</style>', 'Style tag closed'),
    ('<body>', 'Body tag present'),
    ('<nav class=', 'Navbar HTML'),
    ('content-header', 'Content header'),
    ('id="top-cards"', 'Top cards container'),
    ('id="bookmarks-grid"', 'Bookmarks grid'),
    ('id="header-search-form"', 'Search form'),
    ('function renderCards', 'renderCards JS'),
    ('function renderBookmarks', 'renderBookmarks JS'),
    ('top-card fade-in', 'Top card class in JS'),
    ('top-card-label', 'Top card label in JS'),
    ('--primary: #0f2b5b', 'Updated CSS vars'),
    ('gradient-warm', 'Gradient present'),
    ('</html>', 'HTML closed'),
]

for needle, label in checks:
    count = c.count(needle)
    status = 'OK' if count > 0 else 'MISSING'
    print(f'  [{status}] {label} (x{count})')

print()
# Check for duplicates
dupes = [
    ('id="header-search-form"', 'Search form (should be 1)'),
    ('id="header-search-input"', 'Search input (should be 1)'),
    ('id="search-suggestions"', 'Suggestions (should be 1)'),
]
for needle, label in dupes:
    count = c.count(needle)
    status = 'OK' if count == 1 else f'WARN x{count}'
    print(f'  [{status}] {label}')

# Check for broken remnants
problems = [
    ('info-box fade-in', 'Old info-box class in JS'),
    ('info-box-number', 'Old info-box-number in JS'),
    ('appendBookmarkCards', 'Old appendBookmarkCards call'),
    (';">', 'Broken HTML artifact'),
]
print()
for needle, label in problems:
    count = c.count(needle)
    if count > 0:
        print(f'  [WARN] {label} still present (x{count})')
    else:
        print(f'  [CLEAN] {label}')
