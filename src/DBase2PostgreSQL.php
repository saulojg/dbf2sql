<?php

namespace Orbital\Util;

use XBase\Table;
use XBase\Column;
use XBase\Record;

class DBase2PostgreSQL
{

    static function getCreateTableSentence(Table $table){
        $columns = $table->getColumns();
        $sql = "CREATE TABLE " . self::getTableName($table) . "(\n";
        $first = true;
        foreach($columns as $column){
            $sql .= "\t";
            if($first)
                $first = false;
            else
                $sql .= ", ";

            $sql .= self::getColumnDeclaration($column) . "\n";
        }
        return $sql . ");";
    }

    static private function getTableName(Table $table){
        return pathinfo($table->getName(), PATHINFO_FILENAME);
    }

    static private function getColumnDeclaration(Column $column){
        $sql = $column->getName() . " " . self::getSQLType($column);
        return $sql;
    }

    static private function getSQLType(Column $column){
        $length = max($column->getLength(), 1);

        switch($column->getType()){
            case Record::DBFFIELD_TYPE_CHAR:
                return "text";
            case Record::DBFFIELD_TYPE_MEMO:
                return "text";
            case Record::DBFFIELD_TYPE_DATE:
                return "timestamp without time zone";
            case Record::DBFFIELD_TYPE_DATETIME:
                return "timestamp without time zone";
            case Record::DBFFIELD_TYPE_DOUBLE:
                return "numeric ($length, " . $column->getDecimalCount() . ")";
            case Record::DBFFIELD_TYPE_FLOATING:
                return "numeric ($length, " . $column->getDecimalCount() . ")";
            case Record::DBFFIELD_TYPE_NUMERIC:
                return "numeric ($length, " . $column->getDecimalCount() . ")";
            case Record::DBFFIELD_TYPE_LOGICAL:
                return "boolean";
            default:
                throw new \Exception("Unsupported column type " . $column->getType() );
        }
    }

    static public function getInsertSentenceBegin(Table $table){
        $sql = "INSERT INTO " . self::getTableName($table) . " VALUES ";
        return $sql;
    }

    static public function getInsertSentenceValues(Record $record){
        $sql = "(";
        $first = true;
        foreach($record->getColumns() as $column){
            if($first)
                $first = false;
            else
                $sql .= ", ";
            $sql .= self::getValueFormatted($record, $column);
        }
        return $sql . ")";
    }

    private static function getValueFormatted(Record $record, Column $column){
        $value = $record->getObject($column);

        if(empty($value))
            return "NULL";

        switch($column->getType()){
            case Record::DBFFIELD_TYPE_CHAR:
                return self::getStringFormatted($value, $column);
            case Record::DBFFIELD_TYPE_MEMO:
                return self::getStringFormatted($value, $column);
            case Record::DBFFIELD_TYPE_DATE:
                return "to_timestamp($value)";
            case Record::DBFFIELD_TYPE_DATETIME:
                return "to_timestamp($value)";
            case Record::DBFFIELD_TYPE_DOUBLE:
                return $value;
            case Record::DBFFIELD_TYPE_FLOATING:
                return $value;
            case Record::DBFFIELD_TYPE_NUMERIC:
                return $value;
            case Record::DBFFIELD_TYPE_LOGICAL:
                return "'" . ((int) $value) . "'";
        }
    }

    private static function getStringFormatted($value, Column $column){
        $length = mb_strlen($value);
        if($length==0)
            return "''";
        elseif($length>$column->getLength()){
            // Monitoring strange cases (corrupt DBF?)
            $stderr = fopen('php://stderr', 'w+');
            fwrite($stderr, "Following value truncated to " . $column->getLength() . " chars: " . trim($value) . "\n");
            fclose($stderr);
            $value = mb_substr($value, $column->getLength());
        }
        // escape \
        $value = str_replace('\\','\\\\', $value);
        // quote and escape '
        return "E'" . str_replace("'", "\\'", $value) . "'";
    }

}
