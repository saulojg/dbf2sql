
PHP dbf2sql
-----------

Extracts data from DBF files and imports it into a SQL database. Only PostgreSQL is supported at the moment.

## Dependencies

* PHP >= 5.3
* Composer

## Installation

    composer install

## Usage

    php -q dbf2sql.php /path/to/file.dbf [encoding]
    
## Examples


    time php -q dbf2sql.php /path/to/file.dbf CP1251 > test.sql
    
or

    time php -q dbf2sql.php /path/to/file.dbf CP1251 | psql database username    
    
## Contact

Refer to http://www.orbital.com.ar

