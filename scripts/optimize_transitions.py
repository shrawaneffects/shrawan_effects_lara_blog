with open('public/css/cyberpunk-hud.min.css', 'r', encoding='utf-8') as f:
    css = f.read()

# Replace non-composited 'transition:all ...' with GPU-composited transitions
replacements = [
    ('transition:all 0.3s ease;', 'transition:transform 0.25s ease,opacity 0.25s ease,box-shadow 0.25s ease;'),
    ('transition:all 0.35s ease;', 'transition:transform 0.25s ease,opacity 0.25s ease,box-shadow 0.25s ease;'),
    ('transition:all 0.4s ease;', 'transition:transform 0.25s ease,opacity 0.25s ease,box-shadow 0.25s ease;'),
    ('transition:all 0.3s cubic-bezier(0.175,0.885,0.32,1.275);', 'transition:transform 0.25s cubic-bezier(0.175,0.885,0.32,1.275),opacity 0.25s ease;'),
    ('transition:all 0.35s cubic-bezier(0.165,0.84,0.44,1);', 'transition:transform 0.25s cubic-bezier(0.165,0.84,0.44,1),opacity 0.25s ease;')
]

for old, new in replacements:
    css = css.replace(old, new)

with open('public/css/cyberpunk-hud.min.css', 'w', encoding='utf-8') as f:
    f.write(css)

print('Updated transitions to GPU-composited properties!')
