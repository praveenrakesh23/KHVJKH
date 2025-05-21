<?php
$db = new SQLite3('database.db');
$db->exec("CREATE TABLE IF NOT EXISTS uploads (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    filename TEXT,
    filetype TEXT,
    uploaded_on TEXT
)");
?>