import subprocess
import re

# 1. Get original 84cc363 app.blade.php
orig = subprocess.check_output(['git', 'show', '84cc363:resources/views/layouts/app.blade.php'], encoding='utf-8')

# 2. Extract CSS block from 84cc363
match = re.search(r'(    <style>.*?</style>)', orig, re.DOTALL)
if not match:
    raise ValueError("CSS block not found in 84cc363")
orig_css_block = match.group(1)

# 3. Read current app.blade.php
with open('resources/views/layouts/app.blade.php', 'r', encoding='utf-8') as f:
    curr = f.read()

# 4. Replace Bootstrap and CSS links in current with CDN and inline CSS
target_head_pattern = r'    <!-- Bootstrap 5\.3 CSS.*?    <style>.*?</style>'
replacement_head = '''    <!-- Bootstrap 5.3 CSS (CDN Guaranteed Zero 404) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons (Non-blocking Preload) -->
    <link rel="preload" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"></noscript>

''' + orig_css_block

new_content = re.sub(target_head_pattern, replacement_head, curr, flags=re.DOTALL)

with open('resources/views/layouts/app.blade.php', 'w', encoding='utf-8') as f:
    f.write(new_content)

print("Restored complete inline CSS and CDN Bootstrap into app.blade.php successfully!")
