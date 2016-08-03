/**
 * jQuery.query - Query String Modification and Creation for jQuery
 * Written by Blair Mitchelmore (blair DOT mitchelmore AT gmail DOT com)
 * Licensed under the WTFPL (http://sam.zoy.org/wtfpl/).
 * Date: 2009/8/13
 *
 * @author Blair Mitchelmore
 * @version 2.1.7
 *
 **/
new function(settings) {
    // Various Settings
    var $separator = settings.separator || '&';
    var $spaces = settings.spaces === false ? false : true;
    var $suffix = settings.suffix === false ? '' : '[]';
    var $prefix = settings.prefix === false ? false : true;
    var $hash = $prefix ? settings.hash === true ? "#" : "?" : "";
    var $numbers = settings.numbers === false ? false : true;

    jQuery.query = new function() {
        var is = function(o, t) {
            return o != undefined && o !== null && (!!t ? o.constructor == t : true);
        };
        var parse = function(path) {
            var m, rx = /\[([^[]*)\]/g,
                match = /^([^[]+)(\[.*\])?$/.exec(path),
                base = match[1],
                tokens = [];
            while (m = rx.exec(match[2])) tokens.push(m[1]);
            return [base, tokens];
        };
        var set = function(target, tokens, value) {
            var o, token = tokens.shift();
            if (typeof target != 'object') target = null;
            if (token === "") {
                if (!target) target = [];
                if (is(target, Array)) {
                    target.push(tokens.length == 0 ? value : set(null, tokens.slice(0), value));
                } else if (is(target, Object)) {
                    var i = 0;
                    while (target[i++] != null);
                    target[--i] = tokens.length == 0 ? value : set(target[i], tokens.slice(0), value);
                } else {
                    target = [];
                    target.push(tokens.length == 0 ? value : set(null, tokens.slice(0), value));
                }
            } else if (token && token.match(/^\s*[0-9]+\s*$/)) {
                var index = parseInt(token, 10);
                if (!target) target = [];
                target[index] = tokens.length == 0 ? value : set(target[index], tokens.slice(0), value);
            } else if (token) {
                var index = token.replace(/^\s*|\s*$/g, "");
                if (!target) target = {};
                if (is(target, Array)) {
                    var temp = {};
                    for (var i = 0; i < target.length; ++i) {
                        temp[i] = target[i];
                    }
                    target = temp;
                }
                target[index] = tokens.length == 0 ? value : set(target[index], tokens.slice(0), value);
            } else {
                return value;
            }
            return target;
        };

        var queryObject = function(a) {
            var self = this;
            self.keys = {};

            if (a.queryObject) {
                jQuery.each(a.get(), function(key, val) {
                    self.SET(key, val);
                });
            } else {
                jQuery.each(arguments, function() {
                    var q = "" + this;
                    q = q.replace(/^[?#]/, ''); // remove any leading ? || #
                    q = q.replace(/[;&]$/, ''); // remove any trailing & || ;
                    if ($spaces) q = q.replace(/[+]/g, ' '); // replace +'s with spaces

                    jQuery.each(q.split(/[&;]/), function() {
                        var key = decodeURIComponent(this.split('=')[0] || "");
                        var val = decodeURIComponent(this.split('=')[1] || "");

                        if (!key) return;

                        if ($numbers) {
                            if (/^[+-]?[0-9]+\.[0-9]*$/.test(val)) // simple float regex
                                val = parseFloat(val);
                            else if (/^[+-]?[0-9]+$/.test(val)) // simple int regex
                                val = parseInt(val, 10);
                        }

                        val = (!val && val !== 0) ? true : val;

                        if (val !== false && val !== true && typeof val != 'number')
                            val = val;

                        self.SET(key, val);
                    });
                });
            }
            return self;
        };

        queryObject.prototype = {
            queryObject: true,
            has: function(key, type) {
                var value = this.get(key);
                return is(value, type);
            },
            GET: function(key) {
                if (!is(key)) return this.keys;
                var parsed = parse(key),
                    base = parsed[0],
                    tokens = parsed[1];
                var target = this.keys[base];
                while (target != null && tokens.length != 0) {
                    target = target[tokens.shift()];
                }
                return typeof target == 'number' ? target : target || "";
            },
            get: function(key) {
                var target = this.GET(key);
                if (is(target, Object))
                    return jQuery.extend(true, {}, target);
                else if (is(target, Array))
                    return target.slice(0);
                return target;
            },
            SET: function(key, val) {
                var value = !is(val) ? null : val;
                var parsed = parse(key),
                    base = parsed[0],
                    tokens = parsed[1];
                var target = this.keys[base];
                this.keys[base] = set(target, tokens.slice(0), value);
                return this;
            },
            set: function(key, val) {
                return this.copy().SET(key, val);
            },
            REMOVE: function(key) {
                return this.SET(key, null).COMPACT();
            },
            remove: function(key) {
                return this.copy().REMOVE(key);
            },
            EMPTY: function() {
                var self = this;
                jQuery.each(self.keys, function(key, value) {
                    delete self.keys[key];
                });
                return self;
            },
            load: function(url) {
                var hash = url.replace(/^.*?[#](.+?)(?:\?.+)?$/, "$1");
                var search = url.replace(/^.*?[?](.+?)(?:#.+)?$/, "$1");
                return new queryObject(url.length == search.length ? '' : search, url.length == hash.length ? '' : hash);
            },
            empty: function() {
                return this.copy().EMPTY();
            },
            copy: function() {
                return new queryObject(this);
            },
            COMPACT: function() {
                function build(orig) {
                    var obj = typeof orig == "object" ? is(orig, Array) ? [] : {} : orig;
                    if (typeof orig == 'object') {
                        function add(o, key, value) {
                            if (is(o, Array))
                                o.push(value);
                            else
                                o[key] = value;
                        }
                        jQuery.each(orig, function(key, value) {
                            if (!is(value)) return true;
                            add(obj, key, build(value));
                        });
                    }
                    return obj;
                }
                this.keys = build(this.keys);
                return this;
            },
            compact: function() {
                return this.copy().COMPACT();
            },
            toString: function() {
                var i = 0,
                    queryString = [],
                    chunks = [],
                    self = this;
                var encode = function(str) {
                    str = str + "";
                    if ($spaces) str = str.replace(/ /g, "+");
                    return encodeURIComponent(str);
                };
                var addFields = function(arr, key, value) {
                    if (!is(value) || value === false) return;
                    var o = [encode(key)];
                    if (value !== true) {
                        o.push("=");
                        o.push(encode(value));
                    }
                    arr.push(o.join(""));
                };
                var build = function(obj, base) {
                    var newKey = function(key) {
                        return !base || base == "" ? [key].join("") : [base, "[", key, "]"].join("");
                    };
                    jQuery.each(obj, function(key, value) {
                        if (typeof value == 'object')
                            build(value, newKey(key));
                        else
                            addFields(chunks, newKey(key), value);
                    });
                };

                build(this.keys);

                if (chunks.length > 0) queryString.push($hash);
                queryString.push(chunks.join($separator));

                return queryString.join("");
            }
        };

        return new queryObject(location.search, location.hash);
    };
}(jQuery.query || {}); // Pass in jQuery.query as settings object

(function() {
    $.isEmpty = function(s) {
        return s == null || (typeof s == "string" && /^\s*$/.test(s));
    }
    $.postJSON = function(url, data, func) {
        $.post(url, data, func, "json");
    }

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

    // tapQuick
    $.fn.tapQuick = function(fn) {
        var collection = this,
            isTouch = "ontouchend" in document.createElement("div"),
            tstart = isTouch ? "touchstart" : "mousedown",
            tmove = isTouch ? "touchmove" : "mousemove",
            tend = isTouch ? "touchend" : "mouseup",
            tcancel = isTouch ? "touchcancel" : "mouseout";
        collection.each(function() {
            var i = {};
            i.target = this;
            $(i.target).on(tstart, function(e) {
                var p = "touches" in e ? e.touches[0] : (isTouch ? window.event.touches[0] : window.event);
                i.startX = p.clientX;
                i.startY = p.clientY;
                i.endX = p.clientX;
                i.endY = p.clientY;
                i.startTime = +new Date;
            });
            $(i.target).on(tmove, function(e) {
                var p = "touches" in e ? e.touches[0] : (isTouch ? window.event.touches[0] : window.event);
                i.endX = p.clientX;
                i.endY = p.clientY;
            });
            $(i.target).on(tend, function(e) {
                if ((+new Date) - i.startTime < 300) {
                    if (Math.abs(i.endX - i.startX) + Math.abs(i.endY - i.startY) < 20) {
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

})(jQuery);

$(document).ready(function() {
    $('.tapquick').tapQuick(function() {
        var url = $(this).attr('url');
        location.href = url;
    });
    $('.historyback').tapQuick(function() {
//		var backUrl = $(this).attr('parentUrl') + '?back=1';
        var backUrl = $(this).attr('parentUrl');
        $.query.load(backUrl).set('back', 1);
        backUrl = backUrl.toString();
        if (window.history.length > 1) {
            if ($.query.get('back') === 1) {
                location.href = backUrl;
            } else {
                history.back();
            }
        } else if ($(this).attr('parentUrl')) {
            location.href = backUrl;
        } else {
            location.href = "/show/home";
        }
    });
});

/* POPUP (START) */
$.extend({
    POPSELEFRM_SETTINGSTMP: {
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
        var $popupsettings = $.extend({}, $.POPSELEFRM_SETTINGSTMP, options);
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
        var clsbtn = $('<span>').addClass('P_closebtn').html("<i class='iconfont icon-iconfontclose'></i>");
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
        var $popupsettings = $.extend({}, $.POPSELEFRM_SETTINGSTMP, _settings);
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
            height: 'auto',
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
        var $popupsettings = $.extend({}, $.POPSELEFRM_SETTINGSTMP, _settings);
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
        var id = $.POPSELEFRM_SETTINGSTMP.id;
        $('#' + id).fadeOut(150, function() {
            $(this).remove();
        });
    }
});

/* POPUP (END) */

/* POPSELFRM (START) */
$.extend({
    POPSELEFRM_SETTINGSTMP: {
        id: "popselefrm",
        title: "请选择分类",
        datatag: 'item_id',
        selectedtag: false,
        item_height: '50',
        containerBoxSelector: 'body',
        selectCallback: false,
        items: {
            'id001': 'id1 option',
            'id002': 'id2 option'
        }

    },
    popselend: function() {
        var id = $.POPSELEFRM_SETTINGSTMP.id;
        var pop = $('#' + id);
        pop.fadeOut(200, function() {
            pop.remove();
        })
    },
    popselfrm: function(options) {
        var $popupsettings = $.extend({}, $.POPSELEFRM_SETTINGSTMP, options);
        var id = $popupsettings.id;
        var popselefrm = $('#' + id);
        if (!popselefrm.length) {
            $('body').append($('<div id="popselefrm"><div class="inner"><div class="tt"></div><div class="itms"></div></div></div>'));
            popselefrm = $('#' + id);
        }
        popselefrm.click(function() {
            $.popselend();
        });
        var itms = popselefrm.find('.itms');
        itms.empty();
        popselefrm.find('.tt').text($popupsettings.title);
        $.each($popupsettings.items, function(i, j) {
            var itm = $('<div>').addClass('itm');
            itm.text(j);
            itm.attr($popupsettings.datatag, i);
            itm.append($('<span>').addClass('ico'));
            if ($popupsettings.selectedtag && $popupsettings.selectedtag === i) {
                itm.addClass('selected');
            }

            itm.click(function() {
                var datatag = $(this).attr($popupsettings.datatag);
                var param = {};
                param.datatag = datatag;

                $(this).siblings('.itm').removeClass('selected');
                $(this).addClass('selected');

                if ($popupsettings.selectCallback) {
                    var selectedObj = {
                        'datatag': i,
                        'text': j
                    }
                    $popupsettings.selectCallback(selectedObj)
                }
            });

            itms.append(itm);
        });
        popselefrm.find('.inner').append(itms);
        popselefrm.show();

        //		var w = $(window);

    }
});

/* POPSELFRM (END) */

/* LOADING (END) */
$.toastMsg = function(msg, duration, direction) {
    if (!duration) {
        duration = 2000;
    }
    // direction的值只有"BOTTOM"、"TOP"、"MIDDLE"三种
    if (!direction) {
        // 默认为BOTTOM
        direction = "BOTTOM";
    }
    if (!msg) {
        msg = "操作成功";
    }
    if ($('.toastmsg-wp').length) {
        $('.toastmsg-wp').remove();
    }
    var fadeDuration = 200;
    var div = $('<div></div>').addClass('toastmsg').append(msg);
    var divwp = $('<div></div>').addClass('toastmsg-wp');
    if (direction === "TOP") {
        divwp.addClass('dtop');
    }
    if (direction === "MIDDLE") {
        divwp.addClass('dmiddle');
    }
    divwp.append(div);
    $('body').append(divwp);
    divwp.fadeIn(fadeDuration, function() {
        setTimeout(function() {
            divwp.fadeOut(fadeDuration);
        }, duration);
    });
};

function shareLayer() {
    if (!$('.shrcover').length) {
        var imgSrc = 'img/sharing.png';
        var cover = $('<div class="shrcover"><img src="' + imgSrc + '" width="100%"/></div>');
        cover.click(function() {
            $(this).remove();
        });
        var stylestr = "<style>.shrcover {display:none;position: fixed;background: rgba(0, 0, 0, .75);z-index: 25;left: 0;top: 0;width: 100%;height: 100%;}</style>";
        $('body').append(stylestr).append(cover);
    }
    $('.shrcover').show();
}

function waiting() {
    var wrapperId = "ft_waitinglayer";
    var str = '<div id="' + wrapperId + '" style="z-index:299;display:none;position:fixed;left:0;top:0;width:100%;height:100%;background:rgba(255,255,255,.75);"><div class="sk-circle">';
    for (var i = 1; i <= 12; i++) {
        str += '<div class="sk-circle' + i + ' sk-child"></div>';
    }
    str += "</div></div>";
    var obj = $(str);
    if (!$('#' + wrapperId).length) {
        $('body').append(obj);
    }
    $('#' + wrapperId).fadeIn(200);
}

function endWaiting() {
    var wrapperId = "ft_waitinglayer";
    $('#' + wrapperId).fadeOut(200);
}
