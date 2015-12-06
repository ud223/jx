/* POPUP (END) */
$.fn.tapA = function(fn) {
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
/* GJVALIDATOR (START) */
/*
 *  缺陷：
 *  无法适用于<input type="checkbox"/>以及<input type="radio"/>的场合
 *  若遇上述2中情况，可以用div模拟点击赋值到<input type="hidden"/>中去
 */
$.gjvalidator = function (options) {
    var style = $('<style id="gjvalidstyle">.gjvalidwarning{border:1px solid red;box-shadow:0 0 5px rgba(255,0,0,.5);}</style>');
    if (!$('#gjvalidstyle').length) {
        $('body').append(style);
    }
    $('.gjvalidwarning').removeClass('gjvalidwarning');

    var settings = {
        selector: '.gjvalid',
        attr: 'valid',
        msgattr : 'validmsg'
    };
    $.extend(settings, options);

    var validScope = {
        'require': new RegExp(/\S{1,}/),
        'email': new RegExp(/^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-])+/),
        'mobile': new RegExp(/^(0|86|17951)?(13[0-9]|15[012356789]|17[678]|18[0-9]|14[57])[0-9]{8}$/),
        'number': new RegExp(/^\+?[1-9][0-9]*$/),
        'id': new RegExp(/^(\d{15}$|^\d{18}$|^\d{17}(\d|X|x))$/)
    }

    var t = $(settings.selector);
    if (!t.length) {
        return;
    }

    var result = { result: true, msg: '' };
    $.each(t, function () {
        var $this = $(this);
        var validfor = $this.attr(settings.attr);
        // 指定容器和控件
        var tagn = $this.get(0).tagName;
        var container, ctrl;
        switch (tagn) {
            case 'INPUT':
                container = ctrl = $this;
                break;
            case 'SELECT':
                container = ctrl = $this;
                break;
            default:
                // 包容性
                container = $this;
                ctrl = $this.find('input');
                break;
        }
        var regx = validScope[validfor];
        if (regx.test($.trim(ctrl.val()))) {
            // 验证通过
            console.log(ctrl.attr('id'));
        } else {
            // 验证不通过
            result.result = false;
            result.msg = container.attr(settings.msgattr);
            container.addClass('gjvalidwarning');
            return false;
        }
        if (!result.result) {
            return false;
        }
    });
    return result;
}

$.gjvalidatorShow = function () {
    
};
$.gjvalidatorHide = function () {

};
/* GJVALIDATOR (END) */

/* TOAST(START) */
$.toastMsg = function(msg, duration) {
	if (!duration) {
		duration = 2000;
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
	divwp.append(div);
	$('body').append(divwp);
	divwp.fadeIn(fadeDuration, function() {
		setTimeout(function() {
			divwp.fadeOut(fadeDuration);
		}, duration);
	});
};
/* TOAST(END) */



(function () {

    $(document).ready(function () {
        $('.cd_hottap').tapA(function () {
            var url = $(this).attr('hottap');
            location.href = url;
            $.waiting('加载中 ...');
        });

        $('.cd-slidepopupback').tapA(function () {
            var $this = $(this);
            var container = $this.closest('.cd-slidepopup');
            container.addClass('hide').removeClass('show');
            $('html').removeClass('html-no-scroll');
        });

        $('.cd-slideoutbtn').tapA(function () {
            var $this = $(this).closest('.cd-slideoutbtn');
            var target = $($this.attr('rel'));
            if (target.length) {
                target.addClass('show').removeClass('hide');
                $('html').addClass('html-no-scroll');
            }
        });

    });	

	//    $('body').on('tap', '.cd-slidepopupback', function() {
	//        var $this = $(this);
	//        var container = $this.closest('.cd-slidepopup');
	//        container.addClass('hide').removeClass('show');
	//        $('html').removeClass('html-no-scroll');
	//    });
	
	//    $('body').on('tap', '.cd-slideoutbtn', function() {
	//        var $this = $(this).closest('.cd-slideoutbtn');
	//        var target = $($this.attr('rel'));
	//        if (target.length) {
	//            target.addClass('show').removeClass('hide');
	//            $('html').addClass('html-no-scroll');
	//        }
    //    });

	

	//$('.rating-star').tapA(function() {
	//	var $this = $(this).closest('.rating-star');
	//	var val = parseInt($this.attr('val'));
	//	var container = $this.closest('.rating-stars');
	//	var stars = $this.removeClass('empty').siblings('.rating-star');
	//	$.each(stars, function() {
	//		if (parseInt($(this).attr('val')) <= val) {
	//			$(this).removeClass('empty');
	//		} else {
	//			$(this).addClass('empty');
	//		}
	//	});
	//	container.find('input[type="hidden"]').val(val);
	//});


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




/* 习题模块(START) */
function getExam(id) {
    var params = {
        url: "/api/Exam/GetExam",
        data: { "id": id },
        type: "get",
        successFun: loadExam
    }

    ajaxTo(params);
}

function loadExam(data) {
    var str_exam = JSON.stringify(data.data);

    str_exam = "[" + str_exam.substr(1, str_exam.length - 2) + "]";
    testing = eval('(' + str_exam + ')');

    var EXAMTYPE = 1;   // 顺序练习
    initTesting(EXAMTYPE);
}
var getNext = function (type) {
    if (!type) {
        type = 1;
    }
    var next = 0;
    if ($('body').data('current_testing') >= 0) {
        next = $('body').data('current_testing') + 1;
        if (next >= testing.length) {
            if (type === 1) {
                // 从头循环
                next = 0;
            } else {
                // 结束考试
                next = -1;
            }
            
        }
    }
    $('body').data('current_testing', next);
    return next;
};
// 初始化题目框架
var initTesting = function (type) {
	var blockId = 'cd-xiti';
	var block = $('#' + blockId);
	if (!block.length) {
		throw "no div for the testing";
		return;
	} else {
		var html = '<div class="km-kmxuhao" id="cd-kmxuhao"></div>';
		html += '<div class="km-txtct" id="cd-subjtitle"></div>';
		html += '<div class="km-imgct" id="cd-imgct"></div>';
		html += '<input type="hidden" id="cd-subjno" />';
		html += '<div class="km-datiqu" id="cd-daan"></div>';
		// cd-mnks-score保存的值为用户在一次答题过程中的得分，在做习题的时候这个值没什么用，在做模拟考试的时候可以视为考卷得分
		html += '<input type="hidden" class="km-score" id="cd-mnks-score" value="0" />';
		html = $(html);
		var nextbtn = $('<button>');
		nextbtn.addClass('km-nextsubject').attr('id', 'cd-xiayiti').html('下一题');
		nextbtn.tapA(function() {
			// 请求下一个题目内容并初始化题目（根据用户上一次退出的答题序号）
		    // ajax 获取数据
		    if (type == 1) {
		        var index = $('#cd-subjtitle').attr("exam_id");
		        getExam(index);
		    } else {
		        var n = getNext(type);
		        console.log('n is : ');
		        console.log(n);
		        if (n >= 0) {
		            var val = testing[n];
		            val.type = type;
		            refreshSubject(val);
		        } else {
		            // 结束考试跳转
		            var score = $('#cd-mnks-score').val();
		            location.href = '/exam/simulate/end?correct=' + score;
		        }
		    }
		    $(this).trigger('sendanswer');
		});
		block.empty().append(html).append(nextbtn);
	}


	// 请求第一道题目
	// ajax 获取数据
	// 做习题和模拟考试的取数据的方式可能不同，习题做一题取一题，即使退出下一次还是接着来；
	// 模拟考试每次不能退出，做一题从随机得到的试卷集合中取下一题
	// 此处做之前前后端要充分沟通
	var val = testing[getNext()];
	val.type = type;
	refreshSubject(val);
};
// 刷新一道题目
var refreshSubject = function (option) {
    var settings = {
        no: 1,
        exam_id: 1,
        content: '',
        total: 0,
        title: '驾考试题',
        img: false,
        video: false,
        answers: false,
        resourceUrl: '/statics/jx/resource/',
        type: 1 //1. 一般训练；2. 模拟考试
    };
    $.extend(settings, option);

    var no = settings.no;
    var exam_id = settings.exam_id;
    var content = settings.content;
	$('#cd-kmxuhao').text(no + " / " + settings.total);
	$('#cd-subjno').val(no);
	// 清空已有的HTML内容
	$('#cd-subjtitle').empty();
	$('#cd-imgct').empty();
	$('#cd-daan').empty();
    // 初始化HTML内容
	if (settings.exam_id) {
	    $('#cd-subjtitle').attr("exam_id", settings.exam_id);
	}

	if (settings.title) {
	    $('#cd-subjtitle').html(settings.title);
	}

	var resourceUrl = settings.resourceUrl;
	if (settings.img && settings.img != resourceUrl) {
	    $('#cd-imgct').append($("<img>").attr('src', settings.img));
	}

	if (settings.video && settings.video != resourceUrl) {
	    $('#cd-imgct').append($('<video>').attr('src', settings.video));
	}

	if (settings.answers) {
		$('#cd-xiayiti').hide();
		$.each(settings.answers, function (i, j) {
			var html = $("<div>").addClass('itm');
			if (this.correct) {
				html.addClass('correct');
			}
			html.append($('<span>').addClass('xuanxiang').text(i));
			html.append($('<span>').addClass('wenzi').text(this.title));
			var duicuo = $('<i>').addClass('duicuo fa');
			if (this.correct) {
				duicuo.addClass('fa-check-circle');
			} else {
				duicuo.addClass('fa-times-circle');
			}
			html.append(duicuo);
			html.tapA(function () {
			    if (settings.type === 1) {
			        // 一般顺序练习
			        if ($(this).siblings('.itm.selected').length > 0) {
			            // 本题若已经点选，则不再做任何操作
			            return;
			        }
			        // 正确答案加上show
			        $(this).siblings('.itm.correct').addClass('show');
			    } else if (settings.type === 2) {
			        // 模拟考试
			        $(this).siblings('.itm').removeClass('done').removeClass('show').removeClass('selected');
			        $(this).addClass('done');
			    } else {
			        throw "exam type is not correct";
			    }

			    $('#cd-xiayiti').show();
                $(this).addClass('selected').addClass('show');
				
				// 接下来记住用户最新的答题，答对还是答错并且回传服务器保存
				var correctornot = false;
				if ($(this).closest('.km-datiqu').find('.itm.correct.selected').length > 0) {
					// 答对了
					correctornot = true;
					// 保存一下本次答题总得分
					$('#cd-mnks-score').val(parseInt($('#cd-mnks-score').val()) + 1);
				}
				// ajax 发送数据 保存结果
				// 注意，做习题和做模拟考试的保存方法并不一样
				// 习题重在保存进度和保存记录错误的题目（多次错误只记录一次）
				// 而模拟考试则暂时只保存考完的结果（中间的做题对错对于服务器没有实际意义）：
			    // 得分（合格与否），考试日期（考试完成有2个标准：1. 所有试题完成；2. 时间到强制胶卷）；
			    // 顺序练习：点击选项就发送数据，且无法再选择
			    // 模拟考试：点击按钮发送数据，下一题
				var exam_id = $("#cd-subjtitle").attr("exam_id");
				if (settings.type === 1) {
				    // 一般顺序练习
				    if (correctornot) {
				        sequenceLog(exam_id);
				    }
				    else {
				        errorLog(exam_id);
				    }
				} else if (settings.type === 2) {
				    // 模拟考试
				    $('#cd-xiayiti').unbind('sendanswer');
				    $('#cd-xiayiti').bind('sendanswer', function () {
				        if (correctornot) {
				            sequenceLog(exam_id);
				        }
				        else {
				            errorLog(exam_id);
				        }
				    });

				    //$('#cd-xiayiti').tapA(function () {
				    //    if (correctornot) {
				    //        sequenceLog(exam_id);
				    //    }
				    //    else {
				    //        errorLog(exam_id);
				    //    }
				    //});
				} else {
				    throw "exam type is not correct";
				}
				


			});
			$('#cd-daan').append(html);
		});
	}
};
/* 习题模块(END) */

//根据QueryString参数名称获取值
function getQueryStringByName(name) {
	var result = location.search.match(new RegExp("[\?\&]" + name + "=([^\&]+)", "i"));
	if (result == null || result.length < 1) {
		return "";
	}
	return result[1];
}

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
			popupFrame.children().tapA(function(e) {
				e.stopPropagation();
			});
			popupFrame.tapA(function(e) {
				$.popupclose();
			});
		}

		clsbtn.tapA(function() {
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
			okdesubtn.tapA(function() {
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
		cancel.tapA(function() {
			$.popupclose();
			if ($popupsettings.cancelCallback) {
				$popupsettings.cancelCallback();
			}
		});
		var confirm = $('<button>').attr('class', 'P_confirm_btn').attr('action', 'confirm').attr('type', 'button').html($popupsettings.confirmText);
		confirm.tapA(function() {
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

//-----陆思浙-----------------------
var countdown = 60;
var is_count = false;

function countDown(val) {
    is_count = true;

    settime(val);
}

function settime(val) {
    if (!is_count)
        return;

    if (countdown == 0) {
        $(val).removeAttr("disabled");
        $(val).html("发送验证码");
        countdown = 60;
        is_count = false
    } else {
        $(val).attr("disabled", "true");
        $(val).html("重新发送(" + countdown + ")");
        countdown--;
    }
    setTimeout(function () { settime(val) }, 1000)
}

function closePopup(popup) {
    var $this = $(popup);
    var container = $this.closest('.cd-slidepopup');
    container.addClass('hide').removeClass('show');
    $('html').removeClass('html-no-scroll');
}

function getBirthByCode(code) {
    if (code) {
        var tmpStr = "";

        if (code.length == 15) {
            tmpStr = code.substring(6, 12);
            tmpStr = "19" + tmpStr;
            tmpStr = tmpStr.substring(0, 4) + "-" + tmpStr.substring(4, 6) + "-" + tmpStr.substring(6)

            birthday = tmpStr;
        }
        else {
            tmpStr = code.substring(6, 14);
            tmpStr = tmpStr.substring(0, 4) + "-" + tmpStr.substring(4, 6) + "-" + tmpStr.substring(6)


        }

        birthday = tmpStr;

        return birthday;
    }

    return '';
}
