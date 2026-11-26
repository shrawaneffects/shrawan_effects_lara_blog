import os
from PIL import Image

# 1. Optimize Hero Image (RlCBX7zYm...) to max 750px width, quality 72 for ultra-fast mobile loading
hero_orig = 'storage/app/public/posts/RlCBX7zYm1g1OfC6Z2nPQEt6I3ZdBNrgXbL93P4m.png'
if os.path.exists(hero_orig):
    im = Image.open(hero_orig).convert('RGBA')
    w, h = im.size
    max_w = 750
    h_new = int(h * (max_w / w))
    im_resized = im.resize((max_w, h_new), Image.Resampling.LANCZOS)
    im_resized.save(hero_orig + '.webp', 'WEBP', quality=72, method=6)
    im_resized.save('storage/app/public/posts/RlCBX7zYm1g1OfC6Z2nPQEt6I3ZdBNrgXbL93P4m.webp', 'WEBP', quality=72, method=6)
    print(f'Hero WebP optimized: {os.path.getsize(hero_orig + ".webp")} bytes (was 86 KB)')

# 2. Optimize all other post images to max 750px width, quality 72
posts_dir = 'storage/app/public/posts'
for fname in os.listdir(posts_dir):
    if fname.endswith(('.png', '.jpg', '.jpeg')) and not fname.endswith('.webp'):
        fpath = os.path.join(posts_dir, fname)
        try:
            im = Image.open(fpath)
            im_rgb = im.convert('RGBA') if im.mode in ('RGBA', 'LA', 'P') else im.convert('RGB')
            if im_rgb.width > 750:
                h_new = int(im_rgb.height * (750 / im_rgb.width))
                im_rgb = im_rgb.resize((750, h_new), Image.Resampling.LANCZOS)
            w_path1 = fpath + '.webp'
            base, _ = os.path.splitext(fpath)
            w_path2 = base + '.webp'
            im_rgb.save(w_path1, 'WEBP', quality=72, method=6)
            if w_path1 != w_path2:
                im_rgb.save(w_path2, 'WEBP', quality=72, method=6)
            print(f'{fname} WebP: {os.path.getsize(w_path1)} bytes')
        except Exception as e:
            print(f'Error {fname}: {e}')

# 3. Optimize Avatars to max 120px width, quality 75
avatars_dir = 'storage/app/public/avatars'
for fname in os.listdir(avatars_dir):
    if fname.endswith(('.png', '.jpg', '.jpeg')) and not fname.endswith('.webp'):
        fpath = os.path.join(avatars_dir, fname)
        try:
            im = Image.open(fpath).convert('RGBA')
            if im.width > 120:
                h_new = int(im.height * (120 / im.width))
                im = im.resize((120, h_new), Image.Resampling.LANCZOS)
            w_path1 = fpath + '.webp'
            base, _ = os.path.splitext(fpath)
            w_path2 = base + '.webp'
            im.save(w_path1, 'WEBP', quality=75, method=6)
            if w_path1 != w_path2:
                im.save(w_path2, 'WEBP', quality=75, method=6)
            print(f'{fname} Avatar WebP: {os.path.getsize(w_path1)} bytes')
        except Exception as e:
            print(f'Error avatar {fname}: {e}')
