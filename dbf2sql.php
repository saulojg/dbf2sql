<?php

// Check that data *can* be added to database (rollback all changes)
define('DRY_RUN', false);

// Insert all or nothing
define('TRANSACTIONAL', false);

require __DIR__ . '/vendor/autoload.php';

use XBase\Table;
use Orbital\Util\DBase2PostgreSQL;

if($argc < 2){
    echo "Usage " . $argv[0] . " <file.dbf> [encoding] \n";
    die;
}

$file = $argv[1];
$encoding = isset($argv[2]) ? $argv[2] : null;

$table = new Table($file, null, $encoding);

if(TRANSACTIONAL || DRY_RUN)
    echo "begin work; \n";

echo DBase2PostgreSQL::getCreateTableSentence($table) . "\n";

$first = true;
while ($record = $table->nextRecord()) {
    if($first) {
        echo DBase2PostgreSQL::getInsertSentenceBegin($table) . "\n";
        $first = false;
    }else{
        echo "\t, ";
    }

    echo DBase2PostgreSQL::getInsertSentenceValues($record) . "\n";
}

echo ";";

if(TRANSACTIONAL)
    echo "commit\n";
elseif(DRY_RUN)
    echo "rollback; \n";
