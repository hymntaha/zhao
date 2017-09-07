# run this on your dev machine after you run dbcopy_dump.sh on production

wget http://www.bravoyourcity.com/crn/dump.tar.gz
tar zxvf dump.tar.gz
mongo bravo --eval 'db.dropDatabase();'
mongorestore dump/
rm dump.tar.gz
rm -rf dump/
