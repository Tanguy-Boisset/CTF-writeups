import os
import string

chars = string.ascii_letters + string.digits + "_!:/?+=-@%$*,.;}{#"

flag = "404CTF{yJ7dhDm35pLoJcbQkUygIJ}"

for c in chars :
    print("Char : %s" % c)
    os.system('python3 hash.py ' + flag + c)
    #print("18f2048f7d4de5caabd2d0a3d23f4015af8033d46736a2e2d747b777a4d4d205\n")
    print("\n")