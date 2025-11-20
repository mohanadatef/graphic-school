<?php

return [
    'name' => 'ExportImport',
    'version' => '1.0.0',
    'description' => 'Excel, PDF, and CSV export/import module',
    'enabled' => true,
    'settings' => [
        'export_types' => ['excel', 'pdf', 'csv'],
        'max_rows' => 100000,
    ],
];

