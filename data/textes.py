# Connexion à la bd, si ne fonctionne pas, tout commenter et déterminer des valeurs manuellement.
from sql import connectSQL      # Aucune modification à ce fichier n'est autorisée.
db = "projet"           # Nom de votre bd
cnx, cur = connectSQL(db)

def setSens(val0, val1):
    """Détermine la variation entre deux valeurs"""
    if val0 > val1:
        return -1       # Décroissant
    elif val0 < val1:
        return 1        # Croissant
    else:
        return 0        # Stable

def ecart(val0, val1):
    if val0 == 0:
        return val1*100
    """Détermine le taux d'évolution entre une valeur de départ (val0) et une valeur d'arrivée (val1)"""
    return round(100*(val1 - val0)/val0, 2)

def getVal():
    """Récupère des valeurs depuis la BD. Fonction modifiable à souhaits."""
    data = cur.execute("SELECT arriveesTotal AS val FROM tourisme WHERE id_pays = 'FR' AND arriveesTotal IS NOT NULL").fetchall()
    return list(map(lambda x:x["val"], data))

def algo(vals):
    liste = []      # Liste des variations
    pics = []       # Liste des pics
    nb = 2          # Variable qui stocke le nombre de points d'affiliée avec la même variation (point 0 inclus)
    sens = setSens(vals[0], vals[1])        # On détermine la variation entre les deux premiers points

    if abs(ecart(vals[0], vals[1])) > 45: 
        pics.append(1)
        
    for i in range(2,len(vals)):            # On parcourt le reste des points
        if abs(ecart(vals[i-1], vals[i])) > 45:     # Pic si +45% entre deux points [???]
            pics.append(i)

        f = setSens(vals[i-1], vals[i])         # Sens actuel
        if f != sens:                           # Si changement de variation, ajout à la liste
            liste.append((nb-1, sens, vals[i-nb], vals[i-1], ecart(vals[i-nb], vals[i-1])))   # Longueur, sens, pt départ, pt arrivée, % evol
            nb = 1
            sens = f

        nb += 1
# Sortie de boucle, il pourrait rester une valeur sur le carreau
    liste.append((nb-1, sens, vals[i-nb+1], vals[-1], ecart(vals[i-nb+1], vals[-1])))
        
    return liste, pics

a = [60, 20, 15, 13, 14, 12, 1, 1, 1, 5, 8]
liste, pics = algo(a)
print(getVal())
print(liste)
print(pics)