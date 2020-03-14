var appid = 'wxf044a5c986c9aa5a';

function validEnvironment() {
    var ua = window.navigator.userAgent.toLowerCase();

    //微信开发环境
    if (ua.match(/MicroMessenger/i) == 'micromessenger') {
        return 1;
    }
    else {
        return 0;
    }
}

function userLogin() {
    var user_id = localStorage.getItem('user_id');
    //alert(user_id);
    //var url = encodeURIComponent("http://www.yujiaqu.com/reg");
    var tmp_url = location.href;

    var url = encodeURI("http://mall.ecomoter.com/mobile/reg");

    if (!user_id) {
        var toUrl = "https://open.weixin.qq.com/connect/oauth2/authorize?appid="+ appid +"&redirect_uri="+ url +"&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect";

        //alert(toUrl);

        location.href = toUrl;
    }
    else {

    }
}