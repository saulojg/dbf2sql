
PHP dbf2sql
-----------

Extracts data from DBF files and imports it into a SQL database. Currently PostgreSQL is supported.

## Installation

    composer install

## Usage

    dbf2pg.php /path/to/file.dbf [encoding]
    
## Examples


    time php -q dbf2pg.php /path/to/file.dbf CP1251 > test.sql
    
or

    time php -q dbf2pg.php /path/to/file.dbf CP1251 | psql database username    
    
## Contact

Refer to http://www.orbital.com.ar

