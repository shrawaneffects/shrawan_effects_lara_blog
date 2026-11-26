import os
import urllib.request
from PIL import Image

# 1. Download bootstrap.min.css locally
bs_url = 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css'
bs_path = 'public/css/bootstrap.min.css'
print('Downloading Bootstrap 5.3.3 to local css folder...')
req = urllib.request.Request(bs_url, headers={'User-Agent': 'Mozilla/5.0'})
with urllib.request.urlopen(req) as resp:
    data = resp.read()
    with open(bs_path, 'wb') as f:
        f.write(data)
print(f'Saved {bs_path}: {len(data)} bytes')

# 2. Re-compress all post and avatar images with max 1000px width, quality 75, method 6
folders = ['storage/app/public/posts', 'storage/app/public/avatars']
for folder in folders:
    if not os.path.isdir(folder):
        continue
    for fname in os.listdir(folder):
        if fname.endswith(('.png', '.jpg', '.jpeg')) and not fname.endswith(('.webp', '.webp.png', '.webp.jpg')):
            fpath = os.path.join(folder, fname)
            try:
                im = Image.open(fpath)
                if im.mode in ('RGBA', 'LA') or (im.mode == 'P' and 'transparency' in im.info):
                    im_rgb = im.convert('RGBA')
                else:
                    im_rgb = im.convert('RGB')
                
                # Resize if wider than 1000px
                max_w = 400 if 'avatars' in folder else 1000
                if im_rgb.width > max_w:
                    h = int(im_rgb.height * (max_w / im_rgb.width))
                    im_resized = im_rgb.resize((max_w, h), Image.Resampling.LANCZOS)
                else:
                    im_resized = im_rgb
                
                # Save both <fname>.webp and <base>.webp
                webp_path1 = fpath + '.webp'
                base, _ = os.path.splitext(fpath)
                webp_path2 = base + '.webp'
                
                im_resized.save(webp_path1, 'WEBP', quality=75, method=6)
                if webp_path1 != webp_path2:
                    im_resized.save(webp_path2, 'WEBP', quality=75, method=6)
                
                print(f'Optimized {fname}: {os.path.getsize(webp_path1)} bytes (was {os.path.getsize(fpath)} bytes)')
            except Exception as e:
                print(f'Error processing {fname}: {e}')

# Clean up temp test files if present
for t in ['test_q75.webp', 'test_q70.webp', 'test_1000_q75.webp', 'test_800_q75.webp']:
    if os.path.exists(t):
        os.remove(t)
