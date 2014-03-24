var token = 'jYhrI*60up';
var n="bbaa123b5aed0dbs04ffbde58cec21a542ee5p9e6deb1b6315ae0f93de3295507b6udb1c3746ca0062f18754474433da2d3a38341ff5e10802c9400374da76502b397f70e1c5f736e9894b13d336c05a133907c4d61907dc55e09d4db6267e933668db1d10e3a69e3951f0b38f62d12f266395af93cfd16328a4e8097011124d";
function do_encrypt(text, n) {
  var rsa = new RSAKey();
  rsa.setPublic(n, "10001");
  var res = rsa.encrypt(text);
  if(res) {
    return res;
    //return hex2b64(res);
  }
}

function predo(){
    if (  $('#aname').val()==''){
        $('#message').text('帐号不完整');
        return false;
    }
    if (  $('#apass').val()=='' ){
        $('#message').text('密码不完整');
        return false;
    }
    $.ajax({
        type     : "POST",
        url      :  './verify.php',
        datatype : 'json',
        data     : {'type' : 'getkey'  },
        async    : false,  //同步请求
        //timeout  : 2000,
        success  : function(data) {
            var data = JSON.parse(data);
            n        = data.n;
            token    = data.token;
        },
        error : function(){
            $('#asubmit').removeAttr('disabled');
            $('#message').text('无法验证信息，稍后再试');
        },
    });
        return dologin();
};




function dologin(){
    $('#asubmit').text('正在验证');
    $('#asubmit').html('正在验证');
    $('#asubmit').attr('disabled','true');
    $.ajax({
    	type     : "POST",
        url      :  './verify.php',
    	datatype : 'json',
    	data     : {
                        'username' : $('#aname').val(), 
                        'psv' : do_encrypt($('#apass').val() + token, n), 
                        'token' : hex_md5(token), 
                        'type' : 'verify' 
                    },
        async    : false,  //同步请求
    	//timeout  : 2000,
    	success  : function(data) {
            if (data == 'ok') {
                //window.location='../';
                $('#message').text('验证正确！');
            } else {
                $('#message').text('帐号或密码缺少匹配');
                console.log(data);
				//$('#asubmit').removeAttr('disabled');
            };
        },
		error : function(){
			$('#asubmit').removeAttr('disabled');
			$('#message').text('无法验证信息，稍后再试');
		},
    });
    $('#asubmit').text('立即登录');
    return false;
};

$(function(){
    $('#apass,#aname').focus(function(){
    	$('#message').empty();
    	$('#asubmit').removeAttr('disabled');
		$('#asubmit').text('立即登录');
    });
});


