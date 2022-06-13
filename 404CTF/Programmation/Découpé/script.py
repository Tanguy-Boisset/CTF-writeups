import PIL.Image
import numpy as np

myPNG = PIL.Image.new('L', (33*24,33*24), 255)
myData = myPNG.load()

for t in range(24):
    print("t : %d" % t)
    for i in range(33):
        for m in range(24):
            f = PIL.Image.open('output/%d.png' % (t*24 + 1 + m))
            pixels = f.load()
            for j in range(33):
                myData[m*33 + i, j + t*33] = pixels[i,j]
            f.close()

myPNG.save('qrcode.png')



