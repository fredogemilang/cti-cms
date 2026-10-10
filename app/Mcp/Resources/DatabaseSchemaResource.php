<?php

namespace App\Mcp\Resources;

use App\Mcp\Guards\McpAbilityGuard;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\MimeType;
use Laravel\Mcp\Server\Attributes\Uri;
use Laravel\Mcp\Server\Resource;

#[Uri('cms://schema/database')]
#[MimeType('text/markdown')]
#[Description('Live database schema — all table names, columns, types, and defaults across MySQL and SQLite. Requires mcp.read ability.')]
class DatabaseSchemaResource extends Resource
{
    public function handle(Request $request): Response
    {
        McpAbilityGuard::authorize('mcp.read');

        $tables = Schema::getTableListing();
        $dbName = DB::getDatabaseName();

        $output = "# CTI CMS — Database Schema\n\n";
        $output .= "Database: `{$dbName}`\n\n";

        foreach ($tables as $table) {
            $tableName = is_string($table) ? $table : (string) (is_array($table) ? reset($table) : array_values((array) $table)[0]);

            $output .= "## `{$tableName}`\n\n";

            try {
                $columns = Schema::getColumns($tableName);
            } catch (\Throwable $e) {
                $output .= "_Could not inspect columns: {$e->getMessage()}_\n\n";

                continue;
            }

            $output .= "| Column | Type | Nullable | Default |\n";
            $output .= "|--------|------|----------|----------|\n";

            foreach ($columns as $column) {
                $nullable = ! empty($column['nullable']) ? 'YES' : 'NO';
                $default = $column['default'] ?? 'NULL';
                $output .= "| `{$column['name']}` | {$column['type']} | {$nullable} | {$default} |\n";
            }

            $output .= "\n";
        }

        return Response::text($output);
    }
}
