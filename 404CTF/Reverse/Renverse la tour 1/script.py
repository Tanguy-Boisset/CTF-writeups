mdp = "¡P6¨sÉU1T0d¸VÊvçu©6RÈx¨4xFw5"

def rev_tour3(mdp):
    # placeholder
    password =['l', 'x', 'i', 'b', 'i', 'i', 'q', 'u', 'd', 'v', 'a', 'v', 'b', 'n', 'l', 'v', 'v', 'l', 'g', 'z', 'q', 'g', 'i', 'u', 'd', 'u', 'd', 'j', 'o', 'r', 'y', 'r', 'u', 'a']
    for i in range(len(mdp)):
        password[len(password) - i -1] = ord(mdp[i]) - i % 4
        password[i] = ord(mdp[len(password) - i -1 ]) - i % 4
    return password

def rev_tour2(new):
    password = []
    for i in range(len(new)):
        if i % 2 == 0:
            password.append(new[i])
    return password

def rev_tour1(rslt):
    s = ""
    for c in rslt[::-1]:
        s += chr(c)
    return s

flag = rev_tour1(rev_tour2(rev_tour3(mdp)))
print("404CTF{%s}" % flag)