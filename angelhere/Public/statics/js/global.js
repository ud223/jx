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

$(document).ready(function() {
	$('.popselfrm_trigger').click(function() {
		var $this = $(this);
		$.popselfrm({
			selectedtag: $this.attr('datatag'),
			items: {
				'mingpian': '名片',
				'xuqiu': '需求',
				'huiyi': '会议'
			},
			selectCallback: function(obj) {
				$this.find('.n').text(obj.text);
				$this.attr('datatag', obj.datatag);
			}
		});
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
			wp = $('<div>').addClass('P_wp_header').css('padding', 15).append(header);
		else
			wp = $('<div>').addClass('P_wp_header').css('padding', 15).html(header);
		if(typeof(msg) === 'object')
			wp = $('<div>').addClass('P_wp_msg').css('padding', 15).append(msg);
		else
			wp = $('<div>').addClass('P_wp_msg').css('padding', 15).html(msg);

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
		if(!popselefrm.length) {
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
			if($popupsettings.selectedtag && $popupsettings.selectedtag === i) {
				itm.addClass('selected');
			}

			itm.click(function() {
				var datatag = $(this).attr($popupsettings.datatag);
				var param = {};
				param.datatag = datatag;

				$(this).siblings('.itm').removeClass('selected');
				$(this).addClass('selected');

				if($popupsettings.selectCallback) {
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
});

function adfunc(options) {
	var settings = {
		gallcls: '.gall-itm',
		height: 'window_height'
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
		} else {
			$('#cst-space').height(settings.height);
		}
		
	};

	resizeGitm();
	$(window).resize(function() {
		resizeGitm();
	});
}