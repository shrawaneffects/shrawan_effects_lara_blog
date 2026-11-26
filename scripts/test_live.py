import urllib.request

headers = {'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36'}

print("=== 1. Checking CSS ===")
try:
    req_css = urllib.request.Request('https://shrawaneffects.com/css/cyberpunk-hud.min.css', headers=headers)
    with urllib.request.urlopen(req_css) as resp:
        print('CSS Status:', resp.status)
        print('CSS Cache-Control:', resp.headers.get('Cache-Control'))
        print('CSS Content-Type:', resp.headers.get('Content-Type'))
        body = resp.read()
        print('CSS Length:', len(body))
except Exception as e:
    print('CSS Error:', e)

print("\n=== 2. Checking Storage Image ===")
try:
    headers_webp = dict(headers)
    headers_webp['Accept'] = 'image/webp,image/apng,image/*,*/*;q=0.8'
    req_img = urllib.request.Request('https://shrawaneffects.com/storage/posts/6hbUmiRMkW6U8vgjVpQd4mflhXIoBH9etx82TkM3.jpg', headers=headers_webp)
    with urllib.request.urlopen(req_img) as resp:
        print('IMG Status:', resp.status)
        print('IMG Content-Type:', resp.headers.get('Content-Type'))
        print('IMG Cache-Control:', resp.headers.get('Cache-Control'))
        body = resp.read()
        print('IMG Transferred Bytes:', len(body))
except Exception as e:
    print('IMG Error:', e)

print("\n=== 3. Checking Homepage HTML ===")
try:
    req_html = urllib.request.Request('https://shrawaneffects.com/', headers=headers)
    with urllib.request.urlopen(req_html) as resp:
        html = resp.read().decode('utf-8', errors='ignore')
        print('HTML Status:', resp.status)
        print('HTML Length:', len(html))
        print('Has cyberpunk-hud.min.css:', 'cyberpunk-hud.min.css' in html)
        print('Has LCP Preload:', 'rel="preload" as="image"' in html)
        print('Has Lazy GTM:', 'initGTM' in html)
        # Find preload tag if present
        for line in html.splitlines():
            if 'rel="preload" as="image"' in line:
                print('Preload tag:', line.strip())
except Exception as e:
    print('HTML Error:', e)

print("\n=== 4. Checking Post, Category, and Articles ===")
for path in ['/gta-6-on-ps5', '/category/ai', '/articles']:
    req = urllib.request.Request(f'https://shrawaneffects.com{path}', headers=headers)
    try:
        with urllib.request.urlopen(req) as resp:
            html = resp.read().decode('utf-8', errors='ignore')
            preload = [line.strip() for line in html.splitlines() if 'rel="preload" as="image"' in line]
            print(f'{path} -> Status {resp.status}, Preload: {preload}')
    except Exception as e:
        print(f'{path} -> Error: {e}')
