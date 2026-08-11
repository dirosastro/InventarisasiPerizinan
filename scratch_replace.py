import os
import re

directory = '/Applications/XAMPP/xamppfiles/htdocs/InventarisasiPerizinan'
extensions = ['.php', '.html', '.js', '.json', '.conf', '.txt', '.ps1', '.md']

for root, dirs, files in os.walk(directory):
    if 'node_modules' in root or '.git' in root or 'vendor' in root:
        continue
    for file in files:
        if any(file.endswith(ext) for ext in extensions):
            filepath = os.path.join(root, file)
            with open(filepath, 'r', encoding='utf-8', errors='ignore') as f:
                content = f.read()
            
            if 'Siperjalan' in content or 'siperjalan' in content or 'SIPERJALAN' in content:
                new_content = content.replace('Siperjalan', 'Simpanan')
                new_content = new_content.replace('siperjalan', 'simpanan')
                new_content = new_content.replace('SIPERJALAN', 'SIMPANAN')
                
                with open(filepath, 'w', encoding='utf-8') as f:
                    f.write(new_content)
                print(f"Updated {filepath}")
