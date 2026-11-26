import os

path = 'resources/views/layouts/app.blade.php'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

start_marker = '    <!-- Fonts: Geist & Geist Mono via Google Fonts (swap display to prevent FOIT) -->'
end_marker = '    </style>\n    @stack(\'styles\')'

replacement = '''    <!-- Fonts: Geist & Geist Mono via Google Fonts (non-blocking preload with swap display) -->
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Geist+Mono:wght@400;500;600;700&family=Geist:wght@400;500;600;700&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Geist+Mono:wght@400;500;600;700&family=Geist:wght@400;500;600;700&display=swap"></noscript>

    <!-- Bootstrap 5.3 CSS -->
    <link rel="preload" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" as="style">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons (Non-blocking Preload) -->
    <link rel="preload" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"></noscript>

    <!-- Cyberpunk HUD Theme Stylesheet (Cached 1 Year) -->
    <link rel="stylesheet" href="{{ asset('css/cyberpunk-hud.min.css') }}">
    @yield('lcp_preload')

    <style>
        :root{--font-main:'Geist',-apple-system,BlinkMacSystemFont,sans-serif;--neon-green:#00ff66;--cyber-dark:#050804}
        body{font-family:var(--font-main);min-height:100vh;background-color:#050804;color:#e4f5e7}
    </style>'''

pos_start = content.find(start_marker)
pos_end = content.find(end_marker)

if pos_start != -1 and pos_end != -1:
    pos_end_tag = pos_end + len('    </style>')
    new_content = content[:pos_start] + replacement + content[pos_end_tag:]
    
    # Also update logo image dimensions to prevent CLS
    new_content = new_content.replace(
        '<img src="{{ $siteLogo }}" alt="{{ $siteName }}" width="160" height="40" style="max-height: 40px; width: auto; object-fit: contain;">',
        '<img src="{{ $siteLogo }}" alt="{{ $siteName }}" width="160" height="40" class="me-2" style="max-height: 40px; width: 160px; object-fit: contain; aspect-ratio: 160 / 40;" fetchpriority="high">'
    )
    new_content = new_content.replace(
        '<img src="{{ $siteLogo }}" alt="{{ $siteName }}" width="160" height="38" style="max-height: 38px; width: auto; object-fit: contain;">',
        '<img src="{{ $siteLogo }}" alt="{{ $siteName }}" width="160" height="38" class="me-2" style="max-height: 38px; width: 160px; object-fit: contain; aspect-ratio: 160 / 38;">'
    )
    
    with open(path, 'w', encoding='utf-8') as f:
        f.write(new_content)
    print(f'Successfully updated layout! Reduced from {len(content)} to {len(new_content)} bytes.')
else:
    print(f'Markers not found! pos_start: {pos_start}, pos_end: {pos_end}')
