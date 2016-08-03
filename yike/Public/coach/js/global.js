
/* POPUP (END) */

$.fn.tapA = function(fn){
    var collection = this,
        isTouch = "ontouchend" in document.createElement("div"),
        tstart = isTouch ? "touchstart" : "mousedown",
        tmove = isTouch ? "touchmove" : "mousemove",
        tend = isTouch ? "touchend" : "mouseup",
        tcancel = isTouch ? "touchcancel" : "mouseout";
    collection.each(function(){
        var i = {};
        i.target = this;
        $(i.target).on(tstart,function(e){
            var p = "touches" in e ? e.touches[0] : (isTouch ? window.event.touches[0] : window.event);
            i.startX = p.clientX;
            i.startY = p.clientY;
            i.endX = p.clientX;
            i.endY = p.clientY;
            i.startTime = + new Date;
        });
        $(i.target).on(tmove,function(e){
            var p = "touches" in e ? e.touches[0] : (isTouch ? window.event.touches[0] : window.event);
            i.endX = p.clientX;
            i.endY = p.clientY;
        });
        $(i.target).on(tend,function(e){
            if((+ new Date)-i.startTime<300){
                if(Math.abs(i.endX-i.startX)+Math.abs(i.endY-i.startY)<20){
                    var e = e || window.event;
                    e.preventDefault();
                    fn.call(i.target, e);
                }
            }
            i.startTime = undefined;
            i.startX = undefined;
            i.startY = undefined;
            i.endX = undefined;
            i.endY = undefined;
        });
    });
    return collection;
}


// 简便模式的waiting，采用svg图片达到动画效果
// css和html必须先写好，无法js一键加载
// 图片来源 https://github.com/jxnblk/loading
/* LOADING (START) */
$.waiting = function(msg) {
    $('.rotatedivwrapper').find('.msg').html(msg);
    $('.rotatedivwrapper').show();
};
$.endWaiting = function() {
    $('.rotatedivwrapper').hide();
};

/* LOADING (END) */
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



(function() {
    $('body').on('tap', '.cd_hottap', function() {
        var url = $(this).attr('hottap');
        location.href = url;
        $.waiting('加载中 ...');
    });

    $('.sort-bt').tap(function() {
        $(this).toggleClass('selected');
        $('.tcfilter-sort').slideToggle(150);
        // 收缩 位置筛选
        $('.loc-bt').removeClass('selected');
        $('.tcfilter-filter').slideUp(150);
    });
    $('.loc-bt').tap(function() {
        $(this).toggleClass('selected');
        $('.tcfilter-filter').slideToggle(150);
        // 收缩 分类筛选
        $('.sort-bt').removeClass('selected');
        $('.tcfilter-sort').slideUp(150);
    });

//    $('body').on('tap', '.cd-slidepopupback', function() {
//        var $this = $(this);
//        var container = $this.closest('.cd-slidepopup');
//        container.addClass('hide').removeClass('show');
//        $('html').removeClass('html-no-scroll');
//    });
    $('.cd-slidepopupback').tapA(function(){
        var $this = $(this);
        var container = $this.closest('.cd-slidepopup');
        container.addClass('hide').removeClass('show');
        $('html').removeClass('html-no-scroll');
    });
//    $('body').on('tap', '.cd-slideoutbtn', function() {
//        var $this = $(this).closest('.cd-slideoutbtn');
//        var target = $($this.attr('rel'));
//        if (target.length) {
//            target.addClass('show').removeClass('hide');
//            $('html').addClass('html-no-scroll');
//        }
//    });
    $('.cd-slideoutbtn').tapA(function(){
        var $this = $(this).closest('.cd-slideoutbtn');
        var target = $($this.attr('rel'));
        if (target.length) {
            target.addClass('show').removeClass('hide');
            $('html').addClass('html-no-scroll');
        }
    });
    $('body').on('tap', '.cd-coupon-itm', function(){
        var $this = $(this).closest('.cd-coupon-itm');
        if(!$this.hasClass('selected')) {
            var container = $this.closest('.cd-slidepopup');
            container.find('.cd-coupon-itm.selected').removeClass('selected');
            $this.addClass('selected');
        }

    });

    $('body').on('tap', '.rating-star', function(){
        var $this = $(this).closest('.rating-star');
        var val = parseInt($this.attr('val'));
        var container = $this.closest('.rating-stars');
        var stars = $this.removeClass('empty').siblings('.rating-star');
        $.each(stars, function(){
            if(parseInt($(this).attr('val')) <= val) {
                $(this).removeClass('empty');
            } else {
                $(this).addClass('empty');
            }
        });
        container.find('input[type="hidden"]').val(val);
    });


    $.getCookie = function(name) {
        var cookies = document.cookie.split("; ");
        for (var i = 0; i < cookies.length; ++i) {
            var pair = cookies[i].split("=");
            if (pair[0] == name) {
                return pair.length == 1 ? null : unescape(pair[1]);
            }
        }
        return null;
    }

    $.setCookie = function(name, value) {
        $.deleteCookie(name);
        if (value != null) {
            var date = new Date();
            date.setFullYear(date.getFullYear() + 1);
            document.cookie = name + "=" + escape(value) + ";path=/;expires=" + date.toGMTString();
        }
    }

    $.deleteCookie = function(name) {
        var date = new Date(0);
        document.cookie = name + "=; expires=" + date.toGMTString();
    }


})(jQuery);

/* TOAST(START) */


/* TOAST(END) */


/* POPUP (START) */
$.extend({
    POPUPSETTINGSTMP: {
        id: "P_popup",
        content: "hello",
        modal: false,
        closebtn: true,
        exitbtn: false,
        //            relocate: true,
        msg: "hello",
        height: 'auto',
        width: 280,
        padding: 25,
        textAlign: 'left',
        containerBoxSelector: 'body'
    },
    popup: function(options) {
        var $popupsettings = $.extend({}, $.POPUPSETTINGSTMP, options);
        var id = $popupsettings.id;
        var w = $(window);
        $('#' + id).remove();
        var popupFrame = $('<div>').attr('id', id);
        var w = $popupsettings.width;
        var h = $popupsettings.height;
        var content = $popupsettings.content;
        content = (typeof(content) === 'string') ? $(content) : content;
        content.addClass('P_bg')
            .css('padding', $popupsettings.padding)
            .css('margin-left', "-" + w / 2 + "px")
            .css('width', w)
            .css('height', h)
            .css('text-align', $popupsettings.textAlign);

        popupFrame.show();
        var clsbtn = $('<span>').addClass('P_closebtn').html("&times;");
        $($popupsettings.containerBoxSelector).append(popupFrame.append($(content).append(clsbtn)));
        var mt = "-" + $(content).outerHeight() / 2 + "px";
        $(content).css('margin-top', mt);

        if (!$popupsettings.modal) {
            popupFrame.children().tap(function(e) {
                e.stopPropagation();
            });
            popupFrame.tap(function(e) {
                $.popupclose();
            });
        }

        clsbtn.tap(function() {
            $.popupclose();
        });

        if ($popupsettings.closebtn) {
            clsbtn.show();
        }
    },
    alertbox: function(options) {
        var _settings = {
            textAlign: 'center',
            exitbtn: true,
            exitCallback: false,
            exitText: '知道了'
        };
        $.extend(_settings, options);
        _settings.modal = true;
        var $popupsettings = $.extend({}, $.POPUPSETTINGSTMP, _settings);
        var id = "P_alertbox";
        var msg = "";
        if ($popupsettings.msg) {
            msg = $popupsettings.msg;
        }
        var wp;
        if (typeof(msg) === 'object')
            wp = $('<div>').addClass('P_wp').append(msg);
        else
            wp = $('<div>').addClass('P_wp').html(msg);
        if ($popupsettings.exitbtn) {
            var okdesubtn = $('<button>').addClass('P_okbtn').html($popupsettings.exitText);
            okdesubtn.tap(function() {
                $.popupclose();
                if (typeof($popupsettings.exitCallback) === 'function') {
                    $popupsettings.exitCallback();
                }
            });
            wp.append(okdesubtn);
        }

        var alertContent = $('<div>').attr('id', id).addClass('P_popupbg').append(wp);

        $popupsettings.content = alertContent;
        $.popup($popupsettings);
    },
    confirm: function(options) {
        var _settings = {
            width: 280,
            height: 120,
            textAlign: 'center',
            header: '',
            msg: '所否确定该操作？',
            confirmText: '是',
            cancelText: '否',
            confirmCallback: false,
            cancelCallback: false
        };
        $.extend(_settings, options);
        _settings.modal = true;
        var $popupsettings = $.extend({}, $.POPUPSETTINGSTMP, _settings);
        //            $popupsettings.closebtn = false;
        var id = "P_confirm";

        var header = "";
        if ($popupsettings.header) {
            header = $popupsettings.header;
        }
        var msg = "";
        if ($popupsettings.msg) {
            msg = $popupsettings.msg;
        }
        var wp;

        if (typeof(header) === 'object')
            wp = $('<div>').addClass('P_wp_header').css('padding', 15).append(header);
        else
            wp = $('<div>').addClass('P_wp_header').css('padding', 15).html(header);
        if (typeof(msg) === 'object')
            wp = $('<div>').addClass('P_wp_msg').css('padding', 15).append(msg);
        else
            wp = $('<div>').addClass('P_wp_msg').css('padding', 15).html(msg);

        var cancel = $('<button>').attr('class', 'P_confirm_btn').attr('action', 'cancel').attr('type', 'button').html($popupsettings.cancelText);
        cancel.tap(function() {
            $.popupclose();
            if ($popupsettings.cancelCallback) {
                $popupsettings.cancelCallback();
            }
        });
        var confirm = $('<button>').attr('class', 'P_confirm_btn').attr('action', 'confirm').attr('type', 'button').html($popupsettings.confirmText);
        confirm.tap(function() {
            $.popupclose();
            if ($popupsettings.confirmCallback) {
                $popupsettings.confirmCallback();
            }
        });
        var btns = $('<div>').attr('class', 'P_confirm_btns').append(confirm).append(cancel);
        wp.append(btns);

        var confirmContent = $('<div>').attr('id', id).addClass('P_popupbg').append(wp);
        $popupsettings.padding = 0;
        $popupsettings.content = confirmContent;
        $.popup($popupsettings);
    },
    popupclose: function() {
        var id = $.POPUPSETTINGSTMP.id;
        $('#' + id).fadeOut(150, function() {
            $(this).remove();
        });
    }
});

function closePopup(popup) {
    var $this = $(popup);
    var container = $this.closest('.cd-slidepopup');
    container.addClass('hide').removeClass('show');
    $('html').removeClass('html-no-scroll');
}