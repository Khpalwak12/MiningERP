<?php

namespace App\Services;

use Illuminate\Database\Connection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use RuntimeException;

class MysqlPhpBackupService
{
    public function export(string $target): void
    {
        $connection = DB::connection();
        $database = $connection->getDatabaseName();
        $tableKey = 'Tables_in_'.$database;
        $tables = $connection->select('SHOW TABLES');

        $handle = fopen($target, 'w');

        if ($handle === false) {
            throw new RuntimeException(__('erp.backup.export_failed'));
        }

        fwrite($handle, "-- MiningERP backup\n");
        fwrite($handle, "SET NAMES utf8mb4;\n");
        fwrite($handle, "SET FOREIGN_KEY_CHECKS=0;\n");
        fwrite($handle, "SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';\n");

        foreach ($tables as $table) {
            $tableName = $table->{$tableKey};
            $escapedTable = $this->escapeIdentifier($tableName);
            $create = $connection->selectOne("SHOW CREATE TABLE {$escapedTable}");
            $createSql = $create->{'Create Table'} ?? $create->{'Create View'} ?? null;

            if ($createSql === null) {
                fclose($handle);

                throw new RuntimeException(__('erp.backup.export_failed'));
            }

            fwrite($handle, "\nDROP TABLE IF EXISTS {$escapedTable};\n");
            fwrite($handle, $createSql.";\n\n");

            foreach ($connection->cursor("SELECT * FROM {$escapedTable}") as $row) {
                $row = (array) $row;
                $columns = array_map(fn (string $column) => $this->escapeIdentifier($column), array_keys($row));
                $values = array_map(fn (mixed $value) => $this->quoteValue($value, $connection), array_values($row));

                fwrite(
                    $handle,
                    "INSERT INTO {$escapedTable} (".implode(', ', $columns).') VALUES ('.implode(', ', $values).");\n"
                );
            }
        }

        fwrite($handle, "\nSET FOREIGN_KEY_CHECKS=1;\n");
        fclose($handle);
    }

    public function import(string $source): void
    {
        if (! File::exists($source)) {
            throw new RuntimeException(__('erp.backup.invalid_archive'));
        }

        $connection = DB::connection();
        $connection->statement('SET FOREIGN_KEY_CHECKS=0');

        $handle = fopen($source, 'r');

        if ($handle === false) {
            throw new RuntimeException(__('erp.backup.import_failed'));
        }

        $statement = '';

        try {
            while (($line = fgets($handle)) !== false) {
                $trimmed = trim($line);

                if ($trimmed === '' || str_starts_with($trimmed, '--')) {
                    continue;
                }

                $statement .= $line;

                if (str_ends_with(rtrim($line), ';')) {
                    $connection->unprepared($statement);
                    $statement = '';
                }
            }

            if (trim($statement) !== '') {
                $connection->unprepared($statement);
            }
        } finally {
            fclose($handle);
            $connection->statement('SET FOREIGN_KEY_CHECKS=1');
        }
    }

    private function escapeIdentifier(string $identifier): string
    {
        return '`'.str_replace('`', '``', $identifier).'`';
    }

    private function quoteValue(mixed $value, Connection $connection): string
    {
        if ($value === null) {
            return 'NULL';
        }

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        if (is_int($value) || is_float($value)) {
            return (string) $value;
        }

        return $connection->getPdo()->quote((string) $value);
    }
}
