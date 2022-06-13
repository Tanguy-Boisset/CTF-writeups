
cipher = "4/2@PAu<+ViNgg%^5NS`#J\u001fNK<XNW(_"
plain = ""

for i in range(len(cipher)):
    plain += chr(ord(cipher[i]) + i)

print(plain)