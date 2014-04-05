function do_encrypt(text, n) {
    var rsa = new RSAKey();
    rsa.setPublic(n, "10001");
    var res = rsa.encrypt(text);
    if(res) {return res;}
    //return hex2b64(res);
}

var pk;
var token;
var _url = function(c, m) {return './verify.php?m=' + m + '&c=' + c ;}
$(function(){

    var fu_login = function login(data) {
        token = data.data;
        doLogin();
    }
    ajax(_url('getrsa'), {}, function (data){
        pk = data.data;
    });
    $('#login').on('click', function(){
        ajax(_url('gettoken'), {'time': timestamp},fu_login);
    });

    function doLogin() {
        var username  = $('#email').val();
        var password  = $('#password').val();
        var _json     = JSON.stringify({
            'u': username, 
            'p':password,
            't':hex_md5(token)
        });
        var logindata = {}

        logindata['i'] = do_encrypt(_json, pk);
        logindata['time']     = timestamp;
        logindata['md5']     = hex_md5(logindata['i']);

        ajaxGet(_url('verify'), logindata, function (data){
            if (data.data == 'ok') {location.reload(true);}
        });
    }
})

