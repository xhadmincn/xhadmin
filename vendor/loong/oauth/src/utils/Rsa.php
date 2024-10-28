<?php

namespace loong\oauth\utils;

use loong\oauth\exception\DecryptException;
use loong\oauth\exception\EncryptException;

/**
 * RAS加解密
 */
class Rsa
{
    /**
     * 解密
     *
     * @param string 加密后的字符串
     * @param string 私钥，可以是文件路径
     * @return object
     */
    public static function decrypt($token, $rsa_privatekey)
    {
        if (file_exists($rsa_privatekey)) {
            $rsa_privatekey = file_get_contents($rsa_privatekey);
        }
        if (empty($rsa_privatekey)) {
            throw new DecryptException('私钥为空');
        }
        $split = str_split($token, 172 * 2); // 1024 bit  固定172
        $crypto = '';
        foreach ($split as $chunk) {
            $isOkay = openssl_private_decrypt(base64_decode($chunk), $decryptData, $rsa_privatekey); // base64在这里使用，因为172字节是一组，是encode来的
            if (!$isOkay) {
                throw new DecryptException("解密失败");
            }
            $crypto .= $decryptData;
        }
        if (!$crypto) {
            throw new DecryptException("解密失败");
        }
        return json_decode(base64_decode($crypto), true);
    }
    /**
     * 加密
     *
     * @param mixed 可被json序列化的数据
     * @param string 公钥，可以是文件路径
     * @return string
     */
    public static function encrypt(mixed $data, string $rsa_publickey)
    {
        if (file_exists($rsa_publickey)) {
            $rsa_publickey = file_get_contents($rsa_publickey);
        }
        if (empty($rsa_publickey)) {
            throw new EncryptException('公钥为空');
        }
        $data = base64_encode(json_encode($data, JSON_UNESCAPED_UNICODE));
        $split = str_split($data, 117 * 2); // 1024 bit && OPENSSL_PKCS1_PADDING  不大于117即可
        $crypto = '';
        foreach ($split as $chunk) {
            $isOkay = openssl_public_encrypt($chunk, $encryptData, $rsa_publickey);
            if (!$isOkay) {
                throw new EncryptException("加密失败");
            }
            $crypto .= base64_encode($encryptData);
        }
        return $crypto;
    }
    /**
     * 创建一对公钥私钥
     *
     * @return array
     */
    public static function create()
    {
        $res = openssl_pkey_new([
            "private_key_bits" => 2048,
            "private_key_type" => OPENSSL_KEYTYPE_RSA
        ]);
        openssl_pkey_export($res, $privKey);
        $pubKey = openssl_pkey_get_details($res);
        return [
            'privatekey' => $privKey,
            'publickey' => $pubKey["key"]
        ];
    }
}
