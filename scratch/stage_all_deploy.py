import os, shutil

dirs = [
    r'C:\Users\shraw\.gemini\antigravity\brain\959dac52-ec3c-4f63-9ec6-4fa31a11375d\browser_sandbox\uploads',
    r'C:\Users\shraw\.gemini\antigravity\brain\f2b8439d-9498-497d-a3b9-6369ef627256\browser_sandbox\uploads'
]

files_to_copy = {
    'app.blade.php': 'resources/views/layouts/app.blade.php',
    'admin.blade.php': 'resources/views/layouts/admin.blade.php',
    'convertcase.blade.php': 'resources/views/frontend/services/convertcase.blade.php',
    'services_index.blade.php': 'resources/views/frontend/services/index.blade.php',
    'show.blade.php': 'resources/views/frontend/blog/show.blade.php',
    'sidebar.blade.php': 'resources/views/frontend/blog/partials/sidebar.blade.php',
    'login.blade.php': 'resources/views/frontend/auth/login.blade.php',
    'trending-seo.css': 'public/css/trending-seo.css',
    'deploy.sh': 'deploy.sh',
    'public_htaccess': 'public/.htaccess',
    'storage_htaccess': 'public/storage/.htaccess',
    'Setting.php': 'app/Models/Setting.php',
    'Post.php': 'app/Models/Post.php',
    'PostSeo.php': 'app/Models/PostSeo.php',
    'QB0XzoGnGgVzyX0kEl5RxJnQARmh2vnw3GhnKgWB.png': 'storage/app/public/settings/QB0XzoGnGgVzyX0kEl5RxJnQARmh2vnw3GhnKgWB.png',
    'hg7FwgZwsNmVr7nH4XSpdH447S55YZXebkse97jt.png': 'storage/app/public/settings/hg7FwgZwsNmVr7nH4XSpdH447S55YZXebkse97jt.png',
    'FmPxHvRuuMFNwblYELBqVPuJIK61rsCHrWlSSSTE.png': 'storage/app/public/settings/FmPxHvRuuMFNwblYELBqVPuJIK61rsCHrWlSSSTE.png'
}

for d in dirs:
    os.makedirs(d, exist_ok=True)
    for target_name, src_path in files_to_copy.items():
        if os.path.exists(src_path):
            dest = os.path.join(d, target_name)
            shutil.copyfile(src_path, dest)
print(f"Staged {len(files_to_copy)} latest 5c0ec3e deploy files cleanly!")
