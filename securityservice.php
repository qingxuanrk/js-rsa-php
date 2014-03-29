<?php
/**
 * 加密解密
 *
 * PHP version 5
 *
 * @category  Bookmark All
 * @package   Security
 * @author    RenKai <ren_kai@live.com>
 * @copyright 2014 RenKai
 * @version   Release: 1.1
 */
class SecurityService
{
    /**
     * Init
     */
    public function __construct()
    {
        $this->g = is_array($this->input->get()) ?
             $this->input->get() : array();

        $this->p = is_array($this->input->post()) ?
             $this->input->post() : array();

        $this->s = $this->session->all_userdata();

        $this->token = isset($this->s['token']) ?
             $this->s['token'] : false;

        include_once APPPATH . './PasswordHash.php';
    }

    /**
     * 拼装返回数组结果
     * 如果mix是err 则视extra为出错的message
     * 否则 视为结果data
     *
     * @param  mix $mix   string 或者other
     * @param  array  $extra 额外的输出结果
     *
     * @return array        
     */
    public function rst($mix = null, $extra = null) {
        if ($mix === 'err') {
            $return = array(
                        'status' => 0,
                        'msg' => $extra,
            );
        } else {
            $return['status'] = 1;
            $return['data'] = $mix;
        }

        if (is_array($extra)) {
            $return = array_merge($return, $extra);
        }

        $time = time();
        //$return['token_data'] = array(md5($time), $time);
        
        echo json_encode($return);
    }
    /**
     * 生成密码
     * 
     * @param string $pass 密码
     * 
     * @return string 加密字串
     */
    public function createPassword($pass) 
    {
        $PasswordHash = new PasswordHash(8, true);

        $hash  = $PasswordHash->HashPassword($pass);
        $check = $PasswordHash->CheckPassword($pass, $hash);

        if (!$check) return false;

        return $hash;
    }

    /**
     * 验证密码
     * 
     * @param [string] $pass [密码]
     * @param [string] $hash [加密串]
     * 
     * @return [bool]  返回匹配正误
     */
    public function verify( $pass, $hash )
    {
        $PasswordHash = new PasswordHash(8, true);
        $check = $PasswordHash->CheckPassword($pass, $hash);

        return $check;
    }

    /**
     * 解密
     *
     * @param string $encryptedData  密文
     * @param string $privatekeyFile 私钥路径
     * @param string $passphrase     私钥密码
     *
     * @return string                 解密文本
     */
    public function rsaMakeDecrypt($encryptedData, $privatekeyFile = './key.pem', $passphrase = '' )
    {
        $encryptedData = hex2bin($encryptedData);
        $encryptedData = base64_encode($encryptedData);
        $privatekey    = openssl_pkey_get_private(file_get_contents($privatekeyFile), $passphrase);
        $sensitiveData = '';

        openssl_private_decrypt(base64_decode($encryptedData), $sensitiveData, $privatekey);

        return $sensitiveData; 
    }

    /**
     * 生成随机字符+数字
     * 
     * @param int $len 生成字符长度
     *
     * @return  string
     */
    public function randString($len = 5)
    {
        $str = 'ABCDEFGHJKMNPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890';
        $l   = strlen($str);
        $mix = '';

        for ($i=0; $i < $len; $i++) { 
            $rand = rand(0, $l-1);
            $mix .= $str[$rand];
        }

        $serial = date('YmdHis', time()) . $mix;

        return $serial;
    }

    /**
     * 获取公钥
     *
     * @param  string $path 公钥路径
     *
     * @return bool       PublicKeyString
     */
    public function getRsaKey($path = './pub.pem')
    {
        $publickey = openssl_pkey_get_public(file_get_contents($path));
        $cc        = openssl_pkey_get_details($publickey);
        $n         = bin2hex($cc['rsa']['n']);

        return $n;
    }

    /**
     * 验证
     *
     * @param  string $pass 密码
     * @param  string $true 原码
     *
     * @return bool       结果
     */
    public function verifyRsaKey($pass, $true)
    {
        $rst   = $this->rsaMakeDecrypt($pass);
        $pass  = new PasswordHash(8, true);
        $check = $pass->CheckPassword($rst, $true);

        return $check;
    }

}
