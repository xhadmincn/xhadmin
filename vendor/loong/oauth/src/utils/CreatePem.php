<?php

namespace loong\oauth\utils;

class CreatePem
{
    public static function create($config)
    {
        $data = Rsa::create();
        if (!is_dir($config['certs'])) {
            mkdir($config['certs'], 0777, true);
        }
        file_put_contents($config['rsa_privatekey'], $data['privatekey']);
        file_put_contents($config['rsa_publickey'], $data['publickey']);
    }
}
