
## Get Start Running
### Requirements:
- Install Composer(https://getcomposer.org/doc/00-intro.md)
- Install PHP 7.2
- Install MySQL 5.7

```
cd /working/directory
composer install
``` 

How to run the script:
```
php bin/console batch-users -h

php bin/console batch-users -f /path/users.csv -c -u root -p root --db-host=localhost
```

