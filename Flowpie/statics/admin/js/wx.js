var appid = 'wx78b3b4daaed7f512';

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

    var tmp_url = location.href;

    var url = encodeURI("http://wx.yune-jia.com/reg/user?web_url=" + tmp_url);
    //如果是测试id也进行登录
    if (!user_id || user_id == "00001") {

        var toUrl = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' + appid + "&redirect_uri=" + url + "&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect";

        location.href = toUrl;
    }
    else {

    }
}