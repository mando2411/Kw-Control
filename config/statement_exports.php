<?php

return [
    'download_link_ttl_minutes' => (int) env('STATEMENT_EXPORT_LINK_TTL_MINUTES', 20),
    'file_ttl_hours' => (int) env('STATEMENT_EXPORT_FILE_TTL_HOURS', 24),
];
