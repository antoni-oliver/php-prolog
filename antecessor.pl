antecessor(X, Z) :- ascendent(X, Z).
antecessor(X, Z) :- ascendent(X, Y), antecessor(Y, Z). 
