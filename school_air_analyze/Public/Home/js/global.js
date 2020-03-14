(function() {
	$.isEmpty = function(s) {
		return s == null || (typeof s == "string" && /^\s*$/.test(s));
	}
	$.postJSON = function(url, data, func) {
		$.post(url, data, func, "json");
	}

	$.getCookie = function(name) {
		var cookies = document.cookie.split("; ");
		for(var i = 0; i < cookies.length; ++i) {
			var pair = cookies[i].split("=");
			if(pair[0] == name) {
				return pair.length == 1 ? null : unescape(pair[1]);
			}
		}
		return null;
	}

	$.setCookie = function(name, value) {
		$.deleteCookie(name);
		if(value != null) {
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
		padding: '30px 20px',
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
		var clsbtn = $('<span>').addClass('P_closebtn').html("&times;");
		$($popupsettings.containerBoxSelector).append(popupFrame.append($(content).append(clsbtn)));
		var mt = "-" + $(content).outerHeight() / 2 + "px";
		$(content).css('margin-top', mt);

		if(!$popupsettings.modal) {
			popupFrame.children().click(function(e) {
				e.stopPropagation();
			});
			popupFrame.click(function(e) {
				$.popupclose();
			});
		}

		clsbtn.click(function() {
			$.popupclose();
		});

		if($popupsettings.closebtn) {
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
		if($popupsettings.msg) {
			msg = $popupsettings.msg;
		}
		var wp;
		if(typeof(msg) === 'object')
			wp = $('<div>').addClass('P_wp').append(msg);
		else
			wp = $('<div>').addClass('P_wp').html(msg);
		if($popupsettings.exitbtn) {
			var okdesubtn = $('<button>').addClass('P_okbtn').html($popupsettings.exitText);
			okdesubtn.click(function() {
				$.popupclose();
				if(typeof($popupsettings.exitCallback) === 'function') {
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
		if($popupsettings.header) {
			header = $popupsettings.header;
		}
		var msg = "";
		if($popupsettings.msg) {
			msg = $popupsettings.msg;
		}
		var wp;

		if(typeof(header) === 'object')
			wp = $('<div>').addClass('P_wp_header').css('padding', $popupsettings.padding).append(header);
		else
			wp = $('<div>').addClass('P_wp_header').css('padding', $popupsettings.padding).html(header);
		if(typeof(msg) === 'object')
			wp = $('<div>').addClass('P_wp_msg').css('padding', $popupsettings.padding).append(msg);
		else
			wp = $('<div>').addClass('P_wp_msg').css('padding', $popupsettings.padding).html(msg);

		var cancel = $('<button>').attr('class', 'P_confirm_btn').attr('action', 'cancel').attr('type', 'button').html($popupsettings.cancelText);
		cancel.click(function() {
			$.popupclose();
			if($popupsettings.cancelCallback) {
				$popupsettings.cancelCallback();
			}
		});
		var confirm = $('<button>').attr('class', 'P_confirm_btn').attr('action', 'confirm').attr('type', 'button').html($popupsettings.confirmText);
		confirm.click(function() {
			$.popupclose();
			if($popupsettings.confirmCallback) {
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

/* LOADING (END) */
$.toastMsg = function(msg, duration, direction) {
	if(!duration) {
		duration = 2000;
	}
	// direction的值只有"BOTTOM"、"TOP"、"MIDDLE"三种
	if(!direction) {
		// 默认为BOTTOM
		direction = "BOTTOM";
	}
	if(!msg) {
		msg = "操作成功";
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
	divwp.fadeIn(fadeDuration, function() {
		setTimeout(function() {
			divwp.fadeOut(fadeDuration);
		}, duration);
	});
};

function shareLayer() {
	if(!$('.shrcover').length) {
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

$(document).ready(function() {
	var switchMdfitro = function(obj) {
		var $this = $(obj).closest('.mdfitro');
		$this.siblings('.mdfitro').removeClass('selected');
		$this.addClass('selected');
	};
	$('.mdfitro').click(function() {
		switchMdfitro(this);
	});

	initCateMenu();
});

function adfunc(options) {
	var settings = {
		gallcls: '.gall-itm',
		height: 'ratio',
		ratio: 2.7
	};
	var settings = $.extend({}, settings, options);
	if($(settings.gallcls).length <= 0) {
		return;
	}
	var singleWidth = $(settings.gallcls).width();
	var gi_c = 0;
	$(settings.gallcls).each(function() {
		$(this).attr('index', gi_c);
		gi_c++;
	});
	gi_c = 0;
	$('#btc-lst .itm').each(function() {
		$(this).attr('index', gi_c);
		gi_c++;
	});

	var movfunc = function(obj) {
		var index = obj.attr('index');
		var targ = $(settings.gallcls + '[index="' + index + '"]');
		var x = singleWidth * index * -1;

		// 取消动画场景
		var otherTarg = targ.siblings(settings.gallcls);
		$.each(otherTarg, function() {
			var $t = $(this);
			$t.find('.slide_sheet').removeClass('slide_show');
		});

		setTimeout(function() {
			$('#gall-list').animate({
				left: x
			}, 500, function() {
				// 进入动画场景
				var sheets = targ.find('.slide_sheet');
				sheets.addClass('slide_show');
			});
		}, 300);

		$('#btc-lst .itm').removeClass('selected');
		$('#cst-space .itm[index=' + index + ']').addClass('selected');
	};

	// 一进去，第一个动画场景出现
	var targ = $(settings.gallcls + '[index="' + 0 + '"]');
	var sheets = targ.find('.slide_sheet');
	sheets.addClass('slide_show');

	$('#cst-space .itm').click(function() {
		movfunc($(this));

		clearInterval($('body').data('auto-scroll-gall'));
	});
	//				
	var getNI = function(obj) {
		var index = parseInt($('#btc-lst .itm.selected').attr('index'));
		var nIndex = index - 1;
		var mnd = $('#btc-lst .itm').length - 1;
		if(nIndex < 0) {
			nIndex = mnd;
		}
		if(obj.hasClass('rt')) {
			nIndex = index + 1;
			if(nIndex > mnd) {
				nIndex = 0;
			}
		}
		return nIndex;
	};
	$('.sid-bts').click(function() {
		var nIndex = getNI($(this));
		movfunc($('#btc-lst .itm[index=' + nIndex + ']'));
		clearInterval($('body').data('auto-scroll-gall'));
	});

	var intid = setInterval(function() {
		var nIndex = getNI($('.sid-bts.rt'));
		movfunc($('#btc-lst .itm[index=' + nIndex + ']'));
	}, 5000);
	$('body').data('auto-scroll-gall', intid);

	var resizeGitm = function() {
		$(settings.gallcls).width($(window).width());
		singleWidth = $(settings.gallcls).width();

		var nleft = $('#btc-lst .itm.selected').attr('index') * singleWidth * -1;
		$('#gall-list').css('left', nleft);

		// 调整高度
		if(settings.height === 'window_height') {
			$('#cst-space').height($(window).height());
		} else if(settings.height == 'ratio') {
			var cstw = $('#cst-space').width();
			var csth = cstw / settings.ratio + 'px';
			$('#cst-space').height(csth);
		} else {
			$('#cst-space').height(settings.height);
		}

	};

	resizeGitm();
	$(window).resize(function() {
		resizeGitm();
	});
}

function waiting(msg) {
	var wrapperId = "ft_waitinglayer";
	var str = '<div id="' + wrapperId + '" style="z-index:299;display:none;position:fixed;left:0;top:0;width:100%;height:100%;background:rgba(255,255,255,.75);"><div class="sk-circle">';
	for(var i = 1; i <= 12; i++) {
		str += '<div class="sk-circle' + i + ' sk-child"></div>';
	}
	str += "</div></div>";
	var obj = $(str);
	if(!$('#' + wrapperId).length) {
		$('body').append(obj);
	}
	$('#' + wrapperId).fadeIn(200);
	if(msg) {
		$('#' + wrapperId).append("<div style='width:100%;text-align:center;top:50%;margin-top:35px;'>" + msg + "</div>");
	}
}

function endWaiting() {
	var wrapperId = "ft_waitinglayer";
	$('#' + wrapperId).fadeOut(200);
}

$(document).ready(function() {
	// 顶部banner动画
	$(window).scroll(function() {
		if($(window).scrollTop() <= 150) {
			$('.nxtopbanner').removeClass('topped');
		} else {
			$('.nxtopbanner').addClass('topped');
		}

	});
	// 二级菜单悬浮
	$(window).scroll(function() {
		if($(window).scrollTop() <= $(window).height()) {
			$('.yositab').removeClass('topped');
		} else {
			$('.yositab').addClass('topped');
		}
	});
	// 顶部banner内部的hover事件
	$('.mdcontent .uln li').mouseenter(function() {
		var $this = $(this);
		$this.siblings('li').addClass('ignor');
		$this.removeClass('ignor');
	});
	$('.mdcontent .uln li').mouseleave(function() {
		$('.mdcontent .uln li').removeClass('ignor');
	});

	// 评论星级
	$('.staritmd').mouseenter(function() {
		var $this = $(this);
		var score = $this.attr('score');
		var lanp = $this.closest('.starslanp');
		var siblings = lanp.find('.staritmd');
		$.each(siblings, function() {
			var i = $(this);
			var s = i.attr('score');
			if(s > score) {
				i.removeClass('icon-iosstar').addClass('icon-iosstaroutline');
			} else {
				i.removeClass('icon-iosstaroutline').addClass('icon-iosstar');
			}
		});
	});
	$('.staritmd').click(function() {
		var $this = $(this);
		var s = $this.attr('score');
		var lanp = $this.closest('.starslanp');
		lanp.attr('score', s);
	});
	$('.starslanp').mouseleave(function() {
		var $this = $(this);
		var score = parseInt($this.attr('score'));
		if(score > 0) {
			var siblings = $this.find('.staritmd');
			$.each(siblings, function() {
				var i = $(this);
				var s = i.attr('score');
				if(s > score) {
					i.removeClass('icon-iosstar').addClass('icon-iosstaroutline');
				} else {
					i.removeClass('icon-iosstaroutline').addClass('icon-iosstar');
				}
			});
		} else {
			$this.find('.staritmd').removeClass('icon-iosstar').addClass('icon-iosstaroutline');
		}
	});

	$('*').click(function(event) {
		var $this = $(this);
		if($this.closest('.m_top_ddl').length || $this.closest('.nav_bar_menu_btn').length) {
			event.stopPropagation();
		} else {
			$('.m_top_ddl').slideUp();
			var sbtn = $('.nav_bar_menu_btn');
			sbtn.removeClass('highlight');
		}
	})
	$('.nav_bar_menu_btn, .nav_bar_menu_btn *').click(function(event) {
		var sbtn = $('.nav_bar_menu_btn');
		if(sbtn.hasClass('highlight')) {
			$('.m_top_ddl').slideUp();
			sbtn.removeClass('highlight');
		} else {
			$('.m_top_ddl').slideDown();
			sbtn.addClass('highlight');
		}
	});
});

var validateInput = function() {
	var result = true;
	$('.warningframe').removeClass('warningframe');
	$('*[req="yes"]').each(function() {
		var $this = $(this);
		var typeoftag = typeof($this);
		if(this.tagName.toLowerCase() === 'input') {
			console.log($this.val().length);
			if($this.val().length == 0) {
				$this.tooltip('show');
				$this.addClass('warningframe');
				result = false;
			} else {
				$this.tooltip('destroy');
			}
		} else if(this.tagName.toLowerCase() === 'select') {
			if(!$this.val() || $this.val() == '-1') {
				$this.tooltip('show');
				$this.addClass('warningframe');
				result = false;
			} else {
				$this.tooltip('destroy');
			}
		}
	});
	return result;
}

var scrollToObj = function(obj, duration, offset) {
	alert('hihi');
	if(!duration) {
		duration = 300;
	}
	var t = obj.offset().top;
	if(typeof(offset) != 'undefined') {
		t = t + offset;
	}
	$('html,body').animate({
		scrollTop: t
	}, duration);
}

function initCateMenu() {
	$('.cate-lav-itm').mouseenter(function() {
		$('.cate-lav-sdh').show();
	})
	$('.cate-lav-itm').mouseleave(function() {
		$('.cate-lav-sdh').hide();
	})
	$('.cate-lav-sdh').mouseenter(function() {
		$('.cate-lav-sdh').show();
	})
	$('.cate-lav-sdh').mouseleave(function() {
		$('.cate-lav-sdh').hide();
	})
}