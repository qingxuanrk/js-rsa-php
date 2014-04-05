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

require_once('./securityservice.php');
$security = new SecurityService();


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



