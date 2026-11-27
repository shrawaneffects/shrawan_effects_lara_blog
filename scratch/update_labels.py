import re

# 1. Update resources/views/frontend/home.blade.php
with open('resources/views/frontend/home.blade.php', 'r', encoding='utf-8') as f:
    c = f.read()

# 1. Remove TRANSMISSION // FEATURED INTEL block
c = re.sub(
    r'\s*<div class="d-flex align-items-center gap-2 mb-3">\s*<span class="telemetry-tag"><i class="bi bi-broadcast"></i> TRANSMISSION // FEATURED INTEL</span>\s*</div>',
    '',
    c
)

# PRIORITY INTEL badge -> Featured
c = c.replace('PRIORITY INTEL', 'Featured')

# 2. TRENDING // TELEMETRY -> Trending Topics
c = c.replace(
    '<span>TRENDING // <span class="text-gradient">TELEMETRY</span></span>',
    '<span>Trending <span class="text-gradient">Topics</span></span>'
)
c = c.replace(
    'DISPATCH LOG <i class="bi bi-arrow-right ms-1"></i>',
    'View All <i class="bi bi-arrow-right ms-1"></i>'
)

# 3. SECTORS // TOPICS -> Categories & ALL SECTORS -> All Categories
c = c.replace(
    '<span>SECTORS // <span class="text-gradient">TOPICS</span></span>',
    '<span>Explore <span class="text-gradient">Categories</span></span>'
)
c = c.replace('ALL SECTORS', 'All Categories')
c = c.replace(
    '{{ $category->published_posts_count }} DOCS',
    '{{ $category->published_posts_count }} Articles'
)

# 4. DATASTREAM // LATEST -> Posts
c = c.replace(
    '<span>DATASTREAM // <span class="text-gradient">LATEST</span></span>',
    '<span>Latest <span class="text-gradient">Posts</span></span>'
)
c = c.replace('ALL DISPATCHES', 'All Posts')
c = c.replace(
    'Telemetry stream will update once new intel is published.',
    'No posts published yet.'
)

# 5. DATABASE QUERY -> Search Articles
c = c.replace(
    '<h5 class="fw-bold mb-3 font-cyber"><i class="bi bi-terminal me-2 text-success"></i> DATABASE QUERY</h5>',
    '<h5 class="fw-bold mb-3 font-cyber"><i class="bi bi-search me-2 text-success"></i> Search Articles</h5>'
)
c = c.replace('placeholder="Search keywords..."', 'placeholder="Search articles..."')
c = c.replace('INTEL TAGS', 'Popular Tags')

# 6. CIPHER TRANSMISSION -> Subscribe Newsletter
c = c.replace(
    '<h4 class="fw-bold font-cyber">CIPHER TRANSMISSION</h4>',
    '<h4 class="fw-bold font-cyber">Subscribe Newsletter</h4>'
)
c = c.replace(
    'Receive curated architectural blueprints, zero-day tutorials, and deep engineering intel.',
    'Receive curated web development tutorials, tech insights, and software updates.'
)
c = c.replace('INITIATE SUBSCRIPTION', 'Subscribe Now')

# 7. agent@shrawaneffects.com -> info@shrawaneffects.com
c = c.replace('agent@shrawaneffects.com', 'info@shrawaneffects.com')

with open('resources/views/frontend/home.blade.php', 'w', encoding='utf-8') as f:
    f.write(c)

print('home.blade.php updated successfully')

# 2. Update resources/views/layouts/app.blade.php
with open('resources/views/layouts/app.blade.php', 'r', encoding='utf-8') as f:
    app_c = f.read()

# 8. DIRECT PROTOCOLS -> Useful Links
app_c = app_c.replace(
    '<h6 class="fw-bold mb-3 text-gradient font-cyber">DIRECT PROTOCOLS</h6>',
    '<h6 class="fw-bold mb-3 text-gradient font-cyber">Useful Links</h6>'
)

with open('resources/views/layouts/app.blade.php', 'w', encoding='utf-8') as f:
    f.write(app_c)

print('app.blade.php updated successfully')
