<?php

namespace App\Http\Controllers;

use Auth;
use DB;
use Exception;

trait TraitAuditTrail
{
    protected function ensureAuditTrailTable()
    {
        if (DB::getSchemaBuilder()->hasTable('audit_trail')) {
            $this->ensureAuditTrailColumns();
            return;
        }

        DB::statement("CREATE TABLE IF NOT EXISTS audit_trail (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id INT NULL,
            user_name VARCHAR(191) NULL,
            module VARCHAR(100) NOT NULL,
            action VARCHAR(50) NOT NULL,
            entity_type VARCHAR(100) NULL,
            entity_id VARCHAR(100) NULL,
            details TEXT NULL,
            old_values TEXT NULL,
            new_values TEXT NULL,
            ip_address VARCHAR(45) NULL,
            user_agent VARCHAR(255) NULL,
            created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    }

    protected function ensureAuditTrailColumns()
    {
        $columns = [
            'user_id' => 'ALTER TABLE audit_trail ADD COLUMN user_id INT NULL',
            'user_name' => 'ALTER TABLE audit_trail ADD COLUMN user_name VARCHAR(191) NULL',
            'module' => 'ALTER TABLE audit_trail ADD COLUMN module VARCHAR(100) NOT NULL DEFAULT ""',
            'action' => 'ALTER TABLE audit_trail ADD COLUMN action VARCHAR(50) NOT NULL DEFAULT ""',
            'entity_type' => 'ALTER TABLE audit_trail ADD COLUMN entity_type VARCHAR(100) NULL',
            'entity_id' => 'ALTER TABLE audit_trail ADD COLUMN entity_id VARCHAR(100) NULL',
            'details' => 'ALTER TABLE audit_trail ADD COLUMN details TEXT NULL',
            'old_values' => 'ALTER TABLE audit_trail ADD COLUMN old_values TEXT NULL',
            'new_values' => 'ALTER TABLE audit_trail ADD COLUMN new_values TEXT NULL',
            'ip_address' => 'ALTER TABLE audit_trail ADD COLUMN ip_address VARCHAR(45) NULL',
            'user_agent' => 'ALTER TABLE audit_trail ADD COLUMN user_agent VARCHAR(255) NULL',
            'created_at' => 'ALTER TABLE audit_trail ADD COLUMN created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP',
            'updated_at' => 'ALTER TABLE audit_trail ADD COLUMN updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP'
        ];

        foreach ($columns as $column => $statement) {
            try {
                if (!DB::getSchemaBuilder()->hasColumn('audit_trail', $column)) {
                    DB::statement($statement);
                }
            } catch (Exception $e) {
                // Keep audit setup best-effort for older databases.
            }
        }
    }

    protected function auditTrailHasColumn($column)
    {
        try {
            return DB::getSchemaBuilder()->hasColumn('audit_trail', $column);
        } catch (Exception $e) {
            return false;
        }
    }

    protected function auditTrail($module, $action, $details = null, $entityType = null, $entityId = null, $oldValues = null, $newValues = null)
    {
        try {
            $this->ensureAuditTrailTable();

            $user = Auth::user();
            if (!$user) {
                return;
            }

            $request = request();

            $candidateData = [
                'user_id' => $user->id,
                'user_name' => $user->fullname,
                'module' => $module,
                'action' => $action,
                'details' => is_array($details) ? json_encode($details) : $details,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $optionalData = [
                'entity_type' => $entityType ?: $module,
                'entity_id' => $entityId,
                'old_values' => is_array($oldValues) ? json_encode($oldValues) : $oldValues,
                'new_values' => is_array($newValues) ? json_encode($newValues) : $newValues,
                'ip_address' => $request ? $request->ip() : null,
                'user_agent' => $request ? substr((string) $request->header('User-Agent'), 0, 255) : null,
            ];

            $data = [];
            foreach ($candidateData as $column => $value) {
                if ($this->auditTrailHasColumn($column)) {
                    $data[$column] = $value;
                }
            }

            foreach ($optionalData as $column => $value) {
                if ($this->auditTrailHasColumn($column)) {
                    $data[$column] = $value;
                }
            }

            if (!empty($data)) {
                DB::table('audit_trail')->insert($data);
            }
        } catch (Exception $e) {
            // Audit logging must never block the main system action.
        }
    }

    protected function auditCalculateDiff($oldData, $newData, $labelMap = [])
    {
        $oldArray = is_object($oldData) ? (array) $oldData : (is_array($oldData) ? $oldData : []);
        $newArray = is_object($newData) ? (array) $newData : (is_array($newData) ? $newData : []);

        $changedOld = [];
        $changedNew = [];
        $detailLines = [];

        foreach ($newArray as $key => $newValue) {
            if (!array_key_exists($key, $oldArray)) {
                continue;
            }
            $oldValue = $oldArray[$key];

            $strOld = ($oldValue === null) ? '' : (string) $oldValue;
            $strNew = ($newValue === null) ? '' : (string) $newValue;

            if ($strOld !== $strNew) {
                $label = isset($labelMap[$key]) ? $labelMap[$key] : ucfirst(str_replace('_', ' ', $key));
                $changedOld[$label] = ($oldValue === null || $oldValue === '') ? '-' : $oldValue;
                $changedNew[$label] = ($newValue === null || $newValue === '') ? '-' : $newValue;
                $detailLines[] = "- {$label} changed from '" . (($oldValue === null || $oldValue === '') ? '-' : $oldValue) . "' to '" . (($newValue === null || $newValue === '') ? '-' : $newValue) . "'";
            }
        }

        return [
            'old' => $changedOld,
            'new' => $changedNew,
            'details' => count($detailLines) ? implode("\n", $detailLines) : "- No tracked field changes."
        ];
    }

    protected function parseDetailsToValues($details)
    {
        $oldParsed = [];
        $newParsed = [];

        if (empty($details)) {
            return ['old' => $oldParsed, 'new' => $newParsed];
        }

        $lines = explode("\n", $details);
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }

            if (preg_match('/^(?:-\s*)?(.+?)\s+changed\s+from\s+[\'"]?(.*?)[\'"]?\s+to\s+[\'"]?(.*?)[\'"]?$/i', $line, $m)) {
                $field = trim($m[1]);
                $oldVal = trim($m[2]);
                $newVal = trim($m[3]);
                $oldParsed[$field] = $oldVal !== '' ? $oldVal : '-';
                $newParsed[$field] = $newVal !== '' ? $newVal : '-';
            } elseif (preg_match('/^(?:-\s*)?(.+?):\s*[\'"]?(.*?)[\'"]?\s*(?:->|to)\s*[\'"]?(.*?)[\'"]?$/i', $line, $m)) {
                $field = trim($m[1]);
                $oldVal = trim($m[2]);
                $newVal = trim($m[3]);
                $oldParsed[$field] = $oldVal !== '' ? $oldVal : '-';
                $newParsed[$field] = $newVal !== '' ? $newVal : '-';
            }
        }

        return ['old' => $oldParsed, 'new' => $newParsed];
    }

    protected function backfillAuditTrailValues()
    {
        try {
            if (!$this->auditTrailHasColumn('old_values') || !$this->auditTrailHasColumn('details')) {
                return;
            }

            $emptyRecords = DB::table('audit_trail')
                ->where(function($q) {
                    $q->whereNull('old_values')
                      ->orWhere('old_values', '')
                      ->orWhere('old_values', 'null')
                      ->orWhere('old_values', '{}');
                })
                ->whereNotNull('details')
                ->where('details', '!=', '')
                ->get();

            foreach ($emptyRecords as $record) {
                $parsed = $this->parseDetailsToValues($record->details);
                if (!empty($parsed['old']) || !empty($parsed['new'])) {
                    DB::table('audit_trail')
                        ->where('id', $record->id)
                        ->update([
                            'old_values' => json_encode($parsed['old']),
                            'new_values' => json_encode($parsed['new'])
                        ]);
                }
            }
        } catch (Exception $e) {
            // best effort
        }
    }
}
