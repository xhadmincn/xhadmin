<?php

$data = [];
foreach (glob(config_path().'settings/*.php') as $path) {
    $content = file_get_contents($path);
    if (empty($content)) {
        continue;
    }
    $group = basename($path, '.php');
    $data[$group] = require $path;
}
return $data;
