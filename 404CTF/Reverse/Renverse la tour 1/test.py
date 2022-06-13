a = "abcdefghi"

b = "1qs6d1q q?DKOkjd_ç qkfq)à"

def tour1(password):
    string = str("".join( "".join(password[::-1])[::-1])[::-1])
    return [ord(c) for c in string]

def simple1(password):
    s = password[::-1]
    return [ord(c) for c in s]

print(tour1(b)==simple1(b))

def tour2(password):
    new = []
    i = 0
    while password != []:
        new.append(password[password.index(password[i])])
        new.append(password[password.index(password[i])] + password[password.index(password[ i + 1 %len(password)])])
        password.pop(password.index(password[i]))
        i += int('qkdj', base=27) - int('QKDJ', base=31) + 267500
    return new

def simple2(password):
    new = []
    while password != []:
        new.append(password[0])
        new.append(password[0] + password[(1)%len(password)])
        password.pop(0)
    return new

print(tour2(tour1(b)) == simple2(simple1(b)))

def tour3(password):
    mdp =['l', 'x', 'i', 'b', 'i', 'i', 'q', 'u', 'd', 'v', 'a', 'v', 'b', 'n', 'l', 'v', 'v', 'l', 'g', 'z', 'q', 'g', 'i', 'u', 'd', 'u', 'd', 'j', 'o', 'r', 'y', 'r', 'u', 'a']
    for i in range(len(password)):
        mdp[i], mdp[len(password) - i -1 ] = chr(password[len(password) - i -1 ] + i % 4),  chr(password[i] + i % 4)
    return "".join(mdp)
    

def simple3(password):
    mdp =['l', 'x', 'i', 'b', 'i', 'i', 'q', 'u', 'd', 'v', 'a', 'v', 'b', 'n', 'l', 'v', 'v', 'l', 'g', 'z', 'q', 'g', 'i', 'u', 'd', 'u', 'd', 'j', 'o', 'r', 'y', 'r', 'u', 'a']
    for i in range(len(password)):
        mdp[i] = chr(password[len(password) - i -1 ] + i % 4)
        mdp[len(password) - i -1 ] = chr(password[i] + i % 4)
    return "".join(mdp)