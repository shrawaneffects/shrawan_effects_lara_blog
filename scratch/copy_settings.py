import os, shutil

src = 'storage/app/public/settings/FmPxHvRuuMFNwblYELBqVPuJIK61rsCHrWlSSSTE.png'
targets = [
    'nS0WZEtEZ8c1ivBACBfE2uHexzYBllzdrzIQS8MV.png',
    'QB0XzoGnGgVzyX0kEl5RxJnQARmh2vnw3GhnKgWB.png',
    'hg7FwgZwsNmVr7nH4XSpdH447S55YZXebkse97jt.png'
]

for t in targets:
    p1 = os.path.join('storage/app/public/settings', t)
    p2 = os.path.join('public/storage/settings', t)
    shutil.copyfile(src, p1)
    if os.path.exists('public/storage/settings'):
        shutil.copyfile(src, p2)
    print(f"Copied to {p1} and {p2}")

print("Done! Files in storage/app/public/settings:")
for f in os.listdir('storage/app/public/settings'):
    print(" -", f)
