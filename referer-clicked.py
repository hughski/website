#!/usr/bin/python

from time import strptime

f = open("/home/hughsie/Referer_Clicked.mbox", 'r')

results = {}

for l in f:
    if not l.startswith("Date: "):
        continue
    d = l[11:13]
    m = l[14:17]
    m = strptime(m,'%b').tm_mon
    y = l[18:22]
    rev = "%s-%02i-%s" % (y, m, d)

    if rev in results:
        results[rev] = results[rev] + 1
    else:
        results[rev] = 1
f.close()

for e in results:
    print e + ',' + str(results[e])
