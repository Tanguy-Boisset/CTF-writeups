import sys

# Read two files as byte arrays
file1_b = bytearray(open(sys.argv[1], 'rb').read())
file2_b = bytearray(open(sys.argv[2], 'rb').read())
file3_b = bytearray(open(sys.argv[3], 'rb').read())


recover_byte_array = bytearray(len(file1_b))

compteur=0
i=0
j=0
while i < len(recover_byte_array)-1:
    if compteur==0:
        recover_byte_array[i]=file1_b[j]
        recover_byte_array[i+1]=file2_b[j]
        j+=1
        compteur+=1
    elif compteur==1:
        recover_byte_array[i]=file1_b[j]
        recover_byte_array[i+1]=file3_b[j]
        j+=1
        compteur+=1
    elif compteur==2:
        recover_byte_array[i]=file2_b[j]
        recover_byte_array[i+1]=file3_b[j]
        j+=1
        compteur=0
    i+=2

open(sys.argv[4], 'wb').write(recover_byte_array)