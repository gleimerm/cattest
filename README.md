
## Get Start Running
PHP script, that is executed from the command line, which accepts a CSV file as input
(see command line directives below) and processes the CSV file. The parsed file data is to be
inserted into a MySQL database.

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

Usage:
  batch-users [options]

Options:
  -f, --file=FILE                    name(path) of the CSV to be parsed
  -c, --create-table[=CREATE-TABLE]  MySQL users table to be built
  -d, --dry-run[=DRY-RUN]            used with the --file directive in case we want to run the
                                     script but not insert into the DB [default: false]
  -u, --db-user[=DB-USER]            MySQL username
  -p, --db-password[=DB-PASSWORD]    MySQL password
      --db-host[=DB-HOST]            MySQL host


php bin/console batch-users -f /path/users.csv -c -u root -p root --db-host=localhost
```

