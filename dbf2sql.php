<?php

// Check that data *can* be added to database (rollback all changes)
define('DRY_RUN', false);

// Insert all or nothing
define('TRANSACTIONAL', false);

// Create schema only
define('DISABLE_INSERTS', false);

// Amount of rows to insert per transaction
define('CHUNK_SIZE', 1000);

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

if(!DISABLE_INSERTS) {
    $issueInsert = true;
    $insertRowCount = 0;
    while ($record = $table->nextRecord()) {
        if ($issueInsert) {
            echo DBase2PostgreSQL::getInsertSentenceBegin($table) . "\n";
            $issueInsert = false;
        } else {
            echo "\t, ";
        }

        echo DBase2PostgreSQL::getInsertSentenceValues($record) . "\n";

        $insertRowCount++;

        if(CHUNK_SIZE>0 && $insertRowCount >= CHUNK_SIZE){
            $issueInsert = true;
            $insertRowCount = 0;
            echo ";";
        }
    }

    if(!$issueInsert)
        echo ";";
}

if(TRANSACTIONAL)
    echo "commit\n";
elseif(DRY_RUN)
    echo "rollback; \n";
