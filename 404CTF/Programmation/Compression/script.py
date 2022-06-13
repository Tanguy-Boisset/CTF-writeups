import glob
import filetype
import os
import subprocess

cmd = {
    "zip": "unzip",
    "tar": "tar xvf",
    "gz": "gzip -d",
    "bz2": "bzip2 -d",
    "rar": "unrar x",
    "xz": "unxz",
    "7z": "7za x"
}

path = "/home/tanguy/Documents/404CTF/Programmation/Compression/"

max = 24

for i in range(max):
    print("\n" + path)
    while True:
        file = glob.glob(path + "flag" + str(max-i) + "*")[0]
        if len(glob.glob(path + "flag" + str(max-i-1) + "*"))>0:
            break
        try:
            kind = filetype.guess(file)
        except:
            break
        subprocess.Popen(cmd[kind.extension] + " " + file, shell=True, stdout=subprocess.PIPE).stdout.read()
    add_path = True
    for f in glob.glob(path + "flag*"):
        test = "flag" + str(max-i-1)
        if test in f:
            add_path = False
    if add_path:
        path += "flag" + str(max-i) + "/"