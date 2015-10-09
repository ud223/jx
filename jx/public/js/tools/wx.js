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
    //var url = encodeURIComponent("http://www.yujiaqu.com/reg");
    var tmp_url = location.href;

    var url = encodeURI("http://www.yujiaqu.com/reg?web_url=" + tmp_url);

    if (!user_id) {
        var toUrl = "https://open.weixin.qq.com/connect/oauth2/authorize?appid="+ appid +"&redirect_uri="+ url +"&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect";

        location.href = toUrl;
    }
    else {

    }
}