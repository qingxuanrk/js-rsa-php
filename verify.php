<?php 
/**
 * RSA验证
 *
 * PHP version 5
 *
 * @category  RSA
 * @package   RSA
 * @author    RenKai <ren_kai@live.com>
 * @copyright 2013 RenKai
 * @license   http://www.qingxuan.info Licence
 * @link      http://www.qingxuan.info
 */
session_start();

require 'PasswordHash.php';

/**
 * 解密
 *
 * @param string $encryptedData  密文
 * @param string $privatekeyFile 私钥路径
 * @param string $passphrase     私钥密码
 *
 * @return string                 解密文本
 */
function rsaMakeDecrypt($encryptedData, $privatekeyFile = './priveteKey', $passphrase = '' )
{
    $encryptedData = base64_encode($encryptedData);
    $privatekey = openssl_pkey_get_private(file_get_contents($privatekeyFile), $passphrase);
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
function randString($len = 5)
{
    $str = 'ABCDEFGHJKMNPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890';
    $l = strlen($str);
    $mix = '';
    for ($i=0; $i < $len; $i++) { 
        $rand = rand(0, $l-1);
        $mix .= $str[$rand];
    }
    $serial = date('YmdHis', time()) . $mix;
    return $serial;
}


$p = $_POST;
switch ($p['type']) {
case 'getkey':
    $publickey = openssl_pkey_get_public(file_get_contents('./publicKey'));
    $cc = openssl_pkey_get_details($publickey);
    $n = bin2hex($cc['rsa']['n']);
    $token = randString(32);
    $_SESSION['token'] = $token;
    echo json_encode(array('n' => $n, 'token' => $token));
    break;
case 'verify':
    if (!$p['token'] === md5($_SESSION['token'])) {
        echo 'token error';
        die();
    }
    $hex =  $p['psv'];
    $rst = rsaMakeDecrypt(hex2bin($hex));
    $pass = new PasswordHash(8, true);
    $correct = 'cml' . $_SESSION['token'];
    $hash = $pass->HashPassword($correct);
    $check = $pass->CheckPassword($rst, $hash);
    echo  $check === true ? 'ok' : 'ERROR' ;
    break;
} //End Switch



