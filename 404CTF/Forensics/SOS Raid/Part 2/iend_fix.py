
img = open('copie_corr.png', 'rb').read()

data = img + b'\x00\x00\x00\x00\x49\x45\x4e\x44\xae\x42\x60\x82'

restored = open('restored.png', 'wb')
restored.write(data)