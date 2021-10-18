from string import ascii_lowercase, ascii_uppercase, digits, punctuation
from json import dumps


dump_me = {ascii_lowercase, ascii_uppercase, digits, punctuation}

for dump in dump_me:
    print(dumps(list(dump)))
