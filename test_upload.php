<?php
require 'app/core/functions.php';
$file = [
    'name' => 'test.png',
    'type' => 'image/png',
    'tmp_name' => '/tmp/phpYzdZ5t',
    'error' => UPLOAD_ERR_OK,
    'size' => 1024
];
touch('/tmp/phpYzdZ5t');
handle_upload($file, 'ticket');
echo "Done\n";
