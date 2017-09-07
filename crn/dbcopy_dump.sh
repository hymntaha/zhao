# run this on production first

sudo mongodump -h 10.72.119.236:27017 -d bravo
sudo tar zcvf dump.tar.gz dump/
