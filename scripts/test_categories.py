import urllib.request, re

headers = {
    'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36',
    'Referer': 'https://shrawaneffects.com/'
}

req = urllib.request.Request('https://shrawaneffects.com/', headers=headers)
with urllib.request.urlopen(req) as resp:
    html = resp.read().decode('utf-8', errors='ignore')
    cats = set(re.findall(r'href=["\'](https://shrawaneffects\.com/category/[^"\']+)["\']', html))
    print('Category links found:', cats)
    for cat_url in list(cats)[:2]:
        req_cat = urllib.request.Request(cat_url, headers=headers)
        with urllib.request.urlopen(req_cat) as c_resp:
            c_html = c_resp.read().decode('utf-8', errors='ignore')
            c_preload = [l.strip() for l in c_html.splitlines() if 'rel="preload" as="image"' in l]
            print(f'{cat_url} -> Status {c_resp.status}, Preload: {c_preload}')

req_blog = urllib.request.Request('https://shrawaneffects.com/blog', headers=headers)
with urllib.request.urlopen(req_blog) as b_resp:
    b_html = b_resp.read().decode('utf-8', errors='ignore')
    b_preload = [l.strip() for l in b_html.splitlines() if 'rel="preload" as="image"' in l]
    print(f'https://shrawaneffects.com/blog -> Status {b_resp.status}, Preload: {b_preload}')
