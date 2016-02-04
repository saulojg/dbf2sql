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
                return "character varying ($length)";
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
                if(strlen($value)==0)
                    return "''";
                return '$$' . str_replace('$','\$', $value).'$$';
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

}