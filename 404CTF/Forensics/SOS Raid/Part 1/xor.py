from pwn import *
with open("disk0.img", "rb") as f1:
    with open("disk1.img", "rb") as f2:
        with open("disk2.img", "wb") as f3:
            x = f1.read()
            y = f2.read()
            f3.write(xor(x,y))