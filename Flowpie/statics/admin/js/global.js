$(document).ready(function () {
    // 重新整理menu的顺序和位置
    var level1s = $('.list-group-item-primary');
    $.each(level1s, function () {
        var $this = $(this);
        var id = $this.attr('id');
        var level2s = $('.list-group-item[parent="' + id + '"]');
        var newWrap = $('<div>').addClass('list-group-item-ddl').attr('rel', id);
        newWrap.append(level2s);
        $this.after(newWrap);
    });
    $('.bfwh').fadeIn(200);

	// 默认展开
	$('.list-group-item-primary.open').each(function(){
		$('.list-group-item-ddl[rel="' + $(this).attr('id') + '"]').show();
	});
	// 绑定展开按钮点击事件
	$('body').on('click', '.list-group-item-primary', function(){
		var tar = $(this).closest('.list-group-item-primary');
		tar.toggleClass('open');
		$('.list-group-item-ddl[rel="' + tar.attr('id') + '"]').toggle();
		// resize 是为了解决nanoscroll的问题
		$(window).resize();
	});
	// 默认展开
	$('.list-group-item.multi.open').each(function(){
		$('.list-group-item-subs[rel="' + $(this).attr('id') + '"]').show();
	});
	// 绑定展开子菜单按钮点击事件
	$('body').on('click', '.list-group-item.multi', function(){
		var tar = $(this).closest('.list-group-item');
		tar.toggleClass('open');
		$('.list-group-item-subs[rel="' + tar.attr('id') + '"]').toggle();
		$(window).resize();
	});
	
	// search 弹出
	$('.nav-itm.top-search').click(function(){
		$(this).toggleClass('selected');
		$('html').toggleClass('html-no-scroll');
		$('.dbmp-box').slideToggle(200);
	});
	$('.dbmp-search-clsbtn').click(function(){
		$('.nav-itm.top-search').click();
	});
	
	// 绑定侧栏隐藏/显示
	$('.side-hide').click(function(){
		$('.side-bar-fix').addClass('go');
		$('.main-body-relative').addClass('full');
		$('.side-hide-back').addClass('back');
		$('.side-bar-fix').removeClass('go-re');
		$('.main-body-relative').removeClass('full-re');
		$('.side-hide-back').removeClass('back-re');
	});
	$('.side-hide-back').click(function(){
		$('.side-bar-fix').addClass('go-re');
		$('.main-body-relative').addClass('full-re');
		$('.side-hide-back').addClass('back-re');
		$('.side-bar-fix').removeClass('go');
		$('.main-body-relative').removeClass('full');
		$('.side-hide-back').removeClass('back');
	});
    //下拉框控件初始化
	$('.dropdown-menu').find('.dropdown-item').click(function () {
	    $(this).parents('.input-group').find('.dropdown-text').val($(this).html());
	    $(this).parents('.input-group').find('.dropdown-value').val($(this).attr('val'));
	})



	$('.fb-switch').click(function () {
	    var $this = $(this).closest('.fb-switch');
	    var nowStatus = $this.attr('status');
	    if (nowStatus === "on") {
	        $this.attr('status', 'off');
	    } else {
	        $this.attr('status', 'on');
	    }
	});
});


/* FIX TABLE (START) */
$.readjustWidth = function() {
	$('.fix-tb').each(function() {
		var $thistb = $(this);
		var b1 = $thistb.find('.fix-tb-ct-b1');
		var b2 = $thistb.find('.fix-tb-ct-b2');
		var hcels = b1.find('.fix-tb-header-cell');
		var hrow1 = $(b2.find('.fix-tb-body-row').get(0));
		if (!hrow1.length) {
			return;
			//throw "develop error";
		}
		var hbds = hrow1.find('.fix-tb-body-cell');
		if (hcels.length != hbds.length) {
			return;
			throw "develop error";
		}
		for (var i = 0; i < hbds.length; i++) {
			// body cell
			var bc = $(hbds.get(i));
			// header cell
			var hc = $(hcels.get(i));
			var colwidth = bc.width();
			hc.width(colwidth);
		}
	});
};
/* FIX TABLE (END) */

/* MARK STUDENT (START) */
function markStudentSuccess(data) {
    if (data.code == "200") {
        alert("登记成功!");

        location.reload();
    }
}

function markStudentFailed(data) {
    alert(data.message);
}

function markStudent(id) {
    if (confirm('确认该学员已经报名缴费？')) {
        var params = {
            url: "/api/student/mark",
            data: { 'StudentID': id },
            successFun: markStudentSuccess,
            errorFun: markStudentFailed
        }
        ajaxTo(params);
    }
}
/* MARK STUDENT (END) */




function switchListBox() {
    var container = $('.switchlistgroup');
    var leftcontainer = container.find('.colleft');
    var rightcontainer = container.find('.colright');
    //var leftitm = container.find('.colleft .list-group-item');
    //var rightitm = container.find('.colright .list-group-item');
    var movetoleft = container.find('.movetoleft');
    var movetoright = container.find('.movetoright');

    var itm = container.find('.list-group-item');

    $('body').on('click', '.list-group-item', function () {
        $(this).toggleClass('active');
        container.find('.lighter').removeClass('lighter');
    });
    movetoleft.click(function () {
        var active = rightcontainer.find('.active');
        container.find('.active').removeClass('active');
        container.find('.lighter').removeClass('lighter');
        active.addClass('lighter');
        leftcontainer.find('.list-group').prepend(active);

        active.find(".config").hide();
    });

    movetoright.click(function () {
        var active = leftcontainer.find('.active');
        container.find('.active').removeClass('active');
        container.find('.lighter').removeClass('lighter');
        active.addClass('lighter');
        rightcontainer.find('.list-group').prepend(active);

        active.find(".config").show();
    });
}
