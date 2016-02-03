<?php

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

echo "begin work; \n"; // only for testing
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

echo "rollback; \n"; // only for testing
