/**
 * Created by lenovo on 2016/5/11.
 */

$.toastMsg = function(msg, duration, direction){
    if(!duration) {
        duration = 2000;
    }
    // direction的值只有"BOTTOM"、"TOP"、"MIDDLE"三种
    if(!direction) {
        // 默认为BOTTOM
        direction = "BOTTOM";
    }
    if(!msg) {
        msg="操作成功";
    }
    if($('.toastmsg-wp').length) {
        $('.toastmsg-wp').remove();
    }
    var fadeDuration = 200;
    var div = $('<div></div>').addClass('toastmsg').append(msg);
    var divwp = $('<div></div>').addClass('toastmsg-wp');
    if(direction === "TOP") {
        divwp.addClass('dtop');
    }
    if(direction === "MIDDLE") {
        divwp.addClass('dmiddle');
    }
    divwp.append(div);
    $('body').append(divwp);
    divwp.fadeIn(fadeDuration, function(){
        setTimeout(function(){
            divwp.fadeOut(fadeDuration);
        }, duration);
    });
};



var fit = {
    ajax:function(opts){
        var m_data = $.extend({

        },opts.data);
        var args = $.extend({
            dataType:'json',
            type:'post'
        },opts);

        args['data'] = m_data;


        $.extend(args,{
            success:function(data){

                switch (data.code){
                    case 302:
                        window.location.href = data.data;
                        break;
                    case 303:
                        setTimeout(function(){
                            window.location.href = data.data;
                        },1000);
                        break;
                }

                //data.msg && $.toastMsg(data.msg);

                opts.success(data);
            }
        });

        $.ajax(args);
    },
    formatTime:function(t, e, i) {
        var n = new Date(t)
            , o = e.replace("y", n.getUTCFullYear());
        o = o.replace("m", n.getUTCMonth() + 1),
            o = o.replace("d", n.getUTCDate());
        var a, s, r;
        return i ? (a = n.getUTCHours(),
            s = n.getUTCMinutes(),
            r = n.getUTCSeconds()) : (a = n.getHours(),
            s = n.getMinutes(),
            r = n.getSeconds()),
            o = o.replace("h", a > 9 ? a : "0" + a),
            o = o.replace("i", s > 9 ? s : "0" + s),
            o = o.replace("s", r > 9 ? r : "0" + r)
    }
};