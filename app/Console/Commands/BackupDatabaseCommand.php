<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class BackupDatabaseCommand extends Command
{
    protected $signature = 'backup:run-database';

    protected $description = 'Generate a MySQL database dump backup file';

    public function handle(): int
    {
        $backupDir = storage_path('app/backups');
        if (! file_exists($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $filename = 'db_backup_'.date('Y-m-d_H-i-s').'.sql';
        $filePath = $backupDir.'/'.$filename;

        $dbHost = config('database.connections.mysql.host', '127.0.0.1');
        $dbPort = config('database.connections.mysql.port', '3306');
        $dbName = config('database.connections.mysql.database');
        $dbUser = config('database.connections.mysql.username');
        $dbPass = config('database.connections.mysql.password');

        // Use mysqldump command line tool if available, else PHP export fallback
        $dumpCmd = "mysqldump --host={$dbHost} --port={$dbPort} --user={$dbUser}";
        if (! empty($dbPass)) {
            $dumpCmd .= " --password={$dbPass}";
        }
        $dumpCmd .= " {$dbName} > \"{$filePath}\"";

        exec($dumpCmd, $output, $returnCode);

        if ($returnCode !== 0 || ! file_exists($filePath) || filesize($filePath) === 0) {
            // PHP Fallback Export
            $tables = \DB::select('SHOW TABLES');
            $dbNameKey = 'Tables_in_'.$dbName;
            $sql = "-- CTI CMS Database Export\n-- Date: ".date('Y-m-d H:i:s')."\n\n";

            foreach ($tables as $t) {
                $tableName = $t->$dbNameKey ?? current((array) $t);
                $createSql = \DB::select("SHOW CREATE TABLE `{$tableName}`")[0]->{'Create Table'} ?? '';
                $sql .= "DROP TABLE IF EXISTS `{$tableName}`;\n{$createSql};\n\n";

                $rows = \DB::table($tableName)->get();
                foreach ($rows as $row) {
                    $rowArray = (array) $row;
                    $keys = array_keys($rowArray);
                    $values = array_map(function ($val) {
                        if ($val === null) {
                            return 'NULL';
                        }

                        return \DB::getPdo()->quote($val);
                    }, array_values($rowArray));

                    $sql .= "INSERT INTO `{$tableName}` (`".implode('`, `', $keys).'`) VALUES ('.implode(', ', $values).");\n";
                }
                $sql .= "\n";
            }

            file_put_contents($filePath, $sql);
        }

        $this->info("Database backup created successfully: {$filename} (".round(filesize($filePath) / 1024, 2).' KB)');

        return self::SUCCESS;
    }
}
