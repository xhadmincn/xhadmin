<?php

namespace loong\oauth;

use Exception;
use loong\oauth\exception\LockException;
use loong\oauth\exception\TokenExpireException;
use loong\oauth\exception\SingleException;
use loong\oauth\utils\CreatePem;
use loong\oauth\utils\Redis;
use loong\oauth\utils\Rsa;
use loong\oauth\utils\Str;

/**
 * Auth
 * @package loong\oauth
 * @method setPrefix(string $prefix)
 * @method setExpire(int $expire)
 * @method setSingle(bool $single)
 * @method setSingleKey(string $singleKey)
 * @method decrypt(string $token)
 * @method encrypt(mixed $data)
 * @method delete(string $token)
 * @method lock(string $token, mixed $password)
 * @method unlock(string $token, mixed $password)
 * @method hasLock(string $token)
 *
 * 
 * 静态调用需使用门面类
 * @method static void refreshRsa()
 * @method static Auth setPrefix(string $prefix)
 * @method static Auth setExpire(int $expire)
 * @method static Auth setSingle(bool $single)
 * @method static Auth setSingleKey(string $singleKey)
 * @method static string encrypt(mixed $data)
 * @method static mixed decrypt(string $token)
 * @method static bool delete(string $token)
 * @method static void lock(string $token, mixed $password)
 * @method static void unlock(string $token, mixed $password)
 * @method static bool hasLock(string $token)
 */
class Auth
{
    # 私钥或私钥路径
    private $rsa_privatekey;
    # 公钥或公钥路径
    private $rsa_publickey;
    # 前缀
    private $prefix;
    # 过期时间
    private $expire;
    # 是否单点登录
    private $single;
    # 单点登录的key
    private $singleKey;
    /**
     * 构造函数
     * @param array 配置
     */
    public function __construct(array $config = [])
    {
        $_config = [
            'certs' => config_path('certs'),
            'rsa_privatekey' => config_path('certs') . '/rsa_private.pem',
            'rsa_publickey' => config_path('certs') . '/rsa_public.pem',
            'prefix' => 'oauth',
            'expire' => 7200,
            'single' => false,
        ];
        $oauthFile = config_path() . '/oauth.php';
        $fileConfig = [];
        if (file_exists($oauthFile)) {
            $fileConfig = include $oauthFile;
        }
        $config = array_merge($_config, $fileConfig, $config);
        $this->rsa_privatekey = $config['rsa_privatekey'];
        # 判断私钥是否存在，不存在则生成
        if (!file_exists($this->rsa_privatekey)) {
            CreatePem::create($config);
        }
        $this->rsa_publickey = $config['rsa_publickey'];
        $this->prefix = $config['prefix'];
        $this->expire = $config['expire'];
        $this->single = $config['single'];
    }
    /**
     * 刷新密钥
     * 用于生成密钥对或把所有用户踢下线
     * @return void
     */
    public function refreshRsa()
    {
        $_config = [
            'certs' => config_path('certs'),
            'rsa_privatekey' => config_path('certs') . '/rsa_private.pem',
            'rsa_publickey' => config_path('certs') . '/rsa_public.pem'
        ];
        $oauthFile = config_path() . '/oauth.php';
        $fileConfig = [];
        if (file_exists($oauthFile)) {
            $fileConfig = include $oauthFile;
        }
        $config = array_merge($_config, $fileConfig, $config);
        CreatePem::create($config);
    }
    /**
     * 设置前缀
     * 用于区分多应用同用户的情况
     * @param string 前缀
     * @return Auth
     */
    public function setPrefix(string $prefix)
    {
        $this->prefix = $prefix;
        return $this;
    }
    /**
     * 设置过期时间
     * @param int 过期时间
     * @return Auth
     */
    public function setExpire(int $expire)
    {
        $this->expire = $expire;
        return $this;
    }
    /**
     * 设置是否单点登录
     * @param bool 是否单点登录
     * @return Auth
     */
    public function setSingle(bool $single)
    {
        $this->single = $single;
        return $this;
    }
    /**
     * 设置单点登录的key
     * @param string 单点登录的key
     * @return Auth
     */
    public function setSingleKey(string $singleKey)
    {
        $this->singleKey = $singleKey;
        return $this;
    }
    /**
     * 解密
     * 如果是静态链式调用必须最后调用
     * @param string token
     * @return mixed
     */
    public function decrypt(string $token)
    {
        $decryptData = Rsa::decrypt($token, $this->rsa_privatekey);
        if (!Redis::get($decryptData['key'])) {
            throw new TokenExpireException('token已过期');
        }
        if ($this->hasLock($token)) {
            throw new LockException('token已锁定');
        }
        if ($this->single) {
            $singleKey = Redis::get('OAUTH::' . $this->prefix . '::' . $decryptData['data'][$this->singleKey]);
            if ($singleKey != $decryptData['key']) {
                throw new SingleException('已在其他地方登录');
            }
        }
        if (isset($decryptData['expire']) && $decryptData['expire'] > 0) {
            # 刷新过期时间
            Redis::expire($decryptData['key'], $decryptData['expire']);
        }
        return $decryptData['data'];
    }
    /**
     * 加密
     * 如果是静态链式调用必须最后调用
     * @param mixed data
     * @return string token
     */
    public function encrypt(mixed $data)
    {
        $time = time();
        $encryptData = [
            'key' => 'OAUTH::' . md5($this->prefix . Str::random(32)) . '::' . sha1($time),
            'data' => $data,
            'expire' => $this->expire,
        ];
        $expire = $this->expire;
        if ($this->expire <= 0) {
            $expire = 60;
        }
        Redis::setex($encryptData['key'], $expire, $time);
        if ($this->single) {
            Redis::set('OAUTH::' . $this->prefix . '::' . $data[$this->singleKey], $encryptData['key']);
        }
        if ($this->expire === 0) {
            Redis::persist($encryptData['key']);
        }
        return Rsa::encrypt($encryptData, $this->rsa_publickey);
    }
    /**
     * 删除指定token
     *
     * @param string $token
     * @return bool
     */
    public function delete(string $token)
    {
        $decryptData = Rsa::decrypt($token, $this->rsa_privatekey);
        if (!Redis::get($decryptData['key'])) {
            return true;
        }
        return Redis::del($decryptData['key']);
    }
    /**
     * 锁定token
     *
     * @param string $token
     * @param mixed $password 解锁密码
     * @return void
     */
    public function lock(string $token, mixed $password)
    {
        $decryptData = Rsa::decrypt($token, $this->rsa_privatekey);
        if (!Redis::get($decryptData['key'])) {
            throw new TokenExpireException('token已过期');
        }
        $expire = Redis::ttl($decryptData['key']);
        $lockKey = 'OAUTH::LOCK::' . $decryptData['key'];
        Redis::setex($lockKey, $expire, $password);
    }
    /**
     * 解锁token
     *
     * @param string $token
     * @param mixed $password
     * @return void
     */
    public function unlock(string $token, mixed $password)
    {
        $decryptData = Rsa::decrypt($token, $this->rsa_privatekey);
        if (!Redis::get($decryptData['key'])) {
            throw new TokenExpireException('token已过期');
        }
        $lockKey = 'OAUTH::LOCK::' . $decryptData['key'];
        $lockPassword = Redis::get($lockKey);
        if ($lockPassword != $password) {
            throw new Exception('PIN码错误');
        }
        Redis::del($lockKey);
    }
    public function hasLock(string $token)
    {
        $decryptData = Rsa::decrypt($token, $this->rsa_privatekey);
        if (!Redis::get($decryptData['key'])) {
            throw new TokenExpireException('token已过期');
        }
        $lockKey = 'OAUTH::LOCK::' . $decryptData['key'];
        return Redis::exists($lockKey);
    }
}
