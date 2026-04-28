<?php
$file = file_get_contents('.env');
$file = str_replace('DB_CONNECTION=sqlite', 'DB_CONNECTION=mysql', $file);
$file = preg_replace('/DB_DATABASE=.*/', 'DB_DATABASE=weather_app_db', $file);
$file = preg_replace('/DB_USERNAME=.*/', 'DB_USERNAME=root', $file);
$file = preg_replace('/DB_PASSWORD=.*/', 'DB_PASSWORD=', $file);
file_put_contents('.env', $file);
echo ".env updated for MySQL\n";
