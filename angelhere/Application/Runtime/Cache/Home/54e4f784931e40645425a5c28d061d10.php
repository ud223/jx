<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>Project 0/1</title>
    
<link rel="stylesheet" href="/Public/common/js/css/base.css" />
<link rel="stylesheet" href="/Public/common/js/css/global.css" />
<script type="text/javascript" src="/Public/common/js/framework.js"></script>
<script type="text/javascript" src="/Public/common/js/global.js?v=1.000001" ></script>
<script src="/Public/common/js/plugin.js"></script>
<script type="text/javascript" src="/Public/common/js/js/user_tools.js" ></script>
<script type="text/javascript" src="/Public/common/js/js/order_tools.js" ></script>
<script type="text/javascript" src="/Public/common/js/js/wx.js" ></script>
</head>

<body ontouchstart="">


<div class="tpcbanner">
    <div class="tt1">预约 <?php echo ($data[0]["Name"]); ?></div>
    <div class="tt2">Book <?php echo ($data[0]["Name"]); ?></div>
    <span class="iconfont icon-angleleft historyback" parentUrl="gt_pt.html"></span>
</div>

<div class="coachdescbig" style="background-image: url(<?php echo UPLOAD_DIR; echo ($data[0]["show_pic_big"]); ?>);">
    <div class="desctt">
        <div class="t1"><?php echo ($data[0]["Name"]); ?></div>
        <div class="t2"><?php echo ($data[0]["title"]); ?></div>
        <div style="position:absolute;left:30px;top:125%;color:#5bcbbf">
            <span style="font-size:12px;">￥</span>
            <span id="priceperhour" style="font-size: 24px;"><?php echo ($data[0]["price"]); ?></span>
            <span style="font-size:12px;">/ 小时</span>
        </div>
    </div>
</div>
<style>
    .weektbs {
        background: #FFF;
        position: relative;
        left: auto;
        top: auto;
        box-shadow: 0 1px 10px rgba(0, 0, 0, .07);
    }

    .weektbs .td {
        width: 14%;
    }

    .weektbs .td:first-child,
    .weektbs .td:last-child {
        width: 15%;
    }

    .weektbs .td .day {
        /*color:#666;*/
        font-size: 14px;
    }

    .weektbs .td .date {
        /*color:#999;*/
        opacity: .6;
    }
    /*.weektbs .td.selected {
        background:#f9b126;
    }
    .weektbs .td.selected * {
        color:#FFF;
    }*/

    body * {
        -webkit-user-select: none;
    }

    body:after {
        background: #FFF;
    }

    body {
        padding-bottom: 80px;
    }

    .quantiankds {
        background: #FFF;
    }

    .quantiankds .kdswapper {
        background: #FBFBFB;
        width: 100%;
        height: 120px;
        position: relative;
    }

    .quantiankds .kds {
        position: absolute;
        left: 0;
        width: 100%;
        height: 100%;
        top: 0;
        border-bottom: 1px solid rgba(0, 0, 0, .07);
    }

    .quantiankds .kd {
        background: #FBFBFB;
        display: block;
        border: none;
        box-shadow: none;
        border-radius: 0;
        padding: 0;
        margin: 0;
        box-shadow: inset -1px 0 0 rgba(0, 0, 0, .05);
        line-height: 120px;
        font-size: 10px;
        color: rgba(0, 0, 0, .2);
        width: 6.25%;
        height: 100%;
        float: left;
        display: inline-block;
        text-align: center;
        position: relative;
    }

    .quantiankds .kd .crv {
        box-shadow: inset -1px 0 0 rgba(0, 0, 0, .05);
        position: absolute;
        left: 0;
        bottom: 0;
        width: 100%;
        height: 100%;
        z-index: 0;
    }

    .quantiankds .kd:last-child .crv {
        box-shadow: none;
    }

    .quantiankds .kd.full .crv {
        background: #e5e5e5;
    }

    .quantiankds .kd.selected .crv {
        background: #feffd2;
    }

    .quantiankds .kd.selected .time_txt {
        display: none;
    }

    .quantiankds .kd.selected .icon-check {
        display: block;
        color: #f9b126;
    }

    .quantiankds .kd .time_txt {
        z-index: 1;
        position: relative;
        font-weight: bold;
    }

    .quantiankds .kd.half .crv {
        height: 50%;
    }

    .quantiankds .kd:active {
        background: #EEE;
    }

    .quantiankds .kd .icon-check {
        display: none;
        width: 14px;
        height: 14px;
        text-align: center;
        line-height: 14px;
        left: 50%;
        margin-left: -7px;
        top: 50%;
        margin-top: -7px;
        position: absolute;
        z-index: 3;
        font-size: 14px;
    }
    .csts41 {
        padding-left:15px;
        border-left:4px solid #f9b126;
        color:#f9b126;
    }
    .csts42 {
        font-size: 12px;
        position:absolute;
        height:12px;
        line-height:12px;
        right:0;
        top:50%;
        margin-top:-6px;
    }
</style>
<!--<div class="weektbs">-->
    <!--<div class="td selected">-->
        <!--<div class="day">Wed</div>-->
        <!--<div class="date">05.10</div>-->
    <!--</div>-->
    <!--<div class="td">-->
        <!--<div class="day">Thu</div>-->
        <!--<div class="date">05.11</div>-->
    <!--</div>-->
    <!--<div class="td">-->
        <!--<div class="day">Fri</div>-->
        <!--<div class="date">05.12</div>-->
    <!--</div>-->
    <!--<div class="td">-->
        <!--<div class="day">Sat</div>-->
        <!--<div class="date">05.13</div>-->
    <!--</div>-->
    <!--<div class="td">-->
        <!--<div class="day">Sun</div>-->
        <!--<div class="date">05.14</div>-->
    <!--</div>-->
    <!--<div class="td">-->
        <!--<div class="day">Mon</div>-->
        <!--<div class="date">05.15</div>-->
    <!--</div>-->
    <!--<div class="td">-->
        <!--<div class="day">Tue</div>-->
        <!--<div class="date">05.16</div>-->
    <!--</div>-->
<!--</div>-->
<div class="weektbs">
    <div class="td date selected" val="<?php echo date("Y-m-d",strtotime("+1 day")) ?>" num="<?php echo date("w",strtotime("+1 day")) ?>">
    <div class="day"><?php echo date("D",strtotime("+1 day")) ?></div>
    <div class="date"><?php echo date("m.d",strtotime("+1 day")) ?></div>
    </div>
    <div class="td date" val="<?php echo date("Y-m-d",strtotime("+2 day")) ?>" num="<?php echo date("w",strtotime("+2 day")) ?>">
    <div class="day"><?php echo date("D",strtotime("+2 day")) ?></div>
    <div class="date"><?php echo date("m.d",strtotime("+2 day")) ?></div>
    </div>
    <div class="td date" val="<?php echo date("Y-m-d",strtotime("+3 day")) ?>" num="<?php echo date("w",strtotime("+3 day")) ?>">
    <div class="day"><?php echo date("D",strtotime("+3 day")) ?></div>
    <div class="date"><?php echo date("m.d",strtotime("+3 day")) ?></div>
    </div>
    <div class="td date" val="<?php echo date("Y-m-d",strtotime("+4 day")) ?>" num="<?php echo date("w",strtotime("+4 day")) ?>">
    <div class="day"><?php echo date("D",strtotime("+4 day")) ?></div>
    <div class="date"><?php echo date("m.d",strtotime("+4 day")) ?></div>
    </div>

    <div class="td date" val="<?php echo date("Y-m-d",strtotime("+5 day")) ?>" num="<?php echo date("w",strtotime("+5 day")) ?>">
    <div class="day"><?php echo date("D",strtotime("+5 day")) ?></div>
    <div class="date"><?php echo date("m.d",strtotime("+5 day")) ?></div>
    </div>

    <div class="td date" val="<?php echo date("Y-m-d",strtotime("+6 day")) ?>" num="<?php echo date("w",strtotime("+6 day")) ?>">
    <div class="day"><?php echo date("D",strtotime("+6 day")) ?></div>
    <div class="date"><?php echo date("m.d",strtotime("+6 day")) ?></div>
    </div>

    <div class="td date" val="<?php echo date("Y-m-d",strtotime("+7 day")) ?>" num="<?php echo date("w",strtotime("+7 day")) ?>">
    <div class="day"><?php echo date("D",strtotime("+7 day")) ?></div>
    <div class="date"><?php echo date("m.d",strtotime("+7 day")) ?></div>
    </div>
</div>

<div class="idit007">
    <div class="t1">
        <span class="csts41">几点训练</span>
        <div class="csts42">
            <!--<span style="display:inline-block;height:10px;width:10px;border:1px solid rgba(0,0,0,.075);float:left;background:#FBFBFB;margin-right:5px;border-radius:2px;"></span>-->
            <!--<span style="float:left;">可预约</span>-->
        </div>
    </div>
</div>

<div class="quantiankds">
    <div class="kdswapper">
        <div class="kds">
            <script>
                for (var i = 7; i <= 22; i++) {
                    var cls = 'kd';
//                    if (i === 12 || i === 15 || i === 19 || i === 20) {
//                        cls = 'kd full';
//                    }
                    var index = i;

                    if (index < 10) {
                        index = '0' + index;
                    }

                    var str = "<div class='" + cls + " full time_"+ index +"' time='" + index + "'><span class='iconfont icon-check'></span><span class='time_txt'>" + i + "</span><div class='crv'></div></div>";
                    document.write(str);
                }
            </script>
        </div>
    </div>
</div>

<div class="zhushi">
    单次点击选择训练时间点，最多可以连续预约3小时<br/>再次点击选中项即取消
</div>


<div class="idit007">
    <div class="t1">
        <span class="csts41">关于：<?php echo ($data[0]["Name"]); ?></span>
        <div class="csts42">
        </div>
    </div>
</div>
<div style="padding:15px;">
    <div style="margin-bottom:15px;">
        <?php echo ($data[0]["remark"]); ?>
    </div>
    <div></div>
</div>


<style>
    .zhushi {
        margin: 20px;
        padding: 8px 20px;
        border-radius: 100px;
        font-size: 12px;
        text-align: left;
        background: rgba(0, 0, 0, .05);
        color: #aaa
    }

    .ycedtm {
        position: fixed;
        top: 100%;
        box-sizing: border-box;
        left: 0;
        width: 100%;
        background: #FFF;
        box-shadow: 0 0 5px rgba(0, 0, 0, .1);
        z-index: 9;
    }

    .ycedtm .wp {
        padding: 15px 120px 15px 15px;
    }

    .ycedtm .cmitie .itm {
        display: inline-block;
        padding: 2px 5px;
        border-radius: 3px;
        border: 1px solid #EEE;
        font-size: 12px;
        margin-bottom: 5px;
        margin-right:5px;
        float:left;
    }
    .ycedtm .cmitie:after {
        display:block;
        content: ' ';
        clear:both;
    }

    .ycedtm .paybtn {
        height: 100%;
        width: 120px;
        display: inline-block;
        padding: 0;
        border: none;
        right: 0;
        top: 0;
        position: absolute;
        background: #f9b126;
        color: #FFF;
        z-index:3;
    }

    .ycedtm .t1 {
        color: #f9b126;
        margin-bottom: 5px;
    }
</style>
<div class="ycedtm">
    <button class="paybtn" url="/show/gt_orderdetail">
        <div style="font-weight:bold;"><span class="hour"></span>小时，￥<span class="money"></span>元</div>
        <div id="submit">现在预约</div>
    </button>
    <div class="wp">
        <div class="t1">您选中的训练时间</div>
        <div class="cmitie">
            <!--<span class="itm">15:00 - 16:00</span>
            <span class="itm">15:00 - 16:00</span>-->

        </div>
    </div>
</div>
    <input type="hidden" id="coach_config_id" value="<?php echo ($data[0]["config_id"]); ?>">
    <input type="hidden" id="coach_id" value="<?php echo ($data[0]["id"]); ?>">



    <script>
        $(document).ready(function() {
            User.load();

            var sel_date = '<?php echo date("Y-m-d",strtotime("+1 day")) ?>';
            var sel_week_num = '<?php echo date("w",strtotime("+1 day")) ?>';

            if (sel_week_num == 0)
                sel_week_num = 7;

            localStorage.setItem('order_date', sel_date);

            loadTimeConfig(sel_date, sel_week_num);

            $('.td.date').click(function() {
                $('.td.date').removeClass('selected');

                $(this).addClass('selected');

                var date = $('.td.date.selected').attr('val');
                var week_num = $('.td.date.selected').attr('num');

                localStorage.setItem('order_date', date);

                $('.kd').addClass('full');

                loadTimeConfig(date, week_num);
            })

//            $('.td.date').click(function() {
//
//            })

            $('.quantiankds .kd').tapQuick(function() {
                var $this = $(this);
                if (!$this.hasClass('full')) {
                    //						$this.toggleClass('selected');
                    var thistime = parseInt($this.attr('time'));

                    if (!$this.hasClass('selected')) {
                        var selected = $('.quantiankds .kd.selected');
                        if (selected.length >= 3) {
                            selected.removeClass('selected');
                            $.toastMsg('一天约练时间不宜超过3小时');
                        }
                        $this.addClass('selected');

                    } else {
                        $this.removeClass('selected');
                    }

                    $('.ycedtm .cmitie').empty();
                    $.each($('.quantiankds .kd.selected'), function(){
                        var h = "<span class='itm time'>" + getTimeTxt($(this).attr('time')) + "</span>";
                        $('.ycedtm .cmitie').append(h);
                    });

                    var timespanCount = $('.quantiankds .kd.selected').length;
                    if (timespanCount) {
                        $('.ycedtm').animate({
                            top: $(window).height() - $('.ycedtm').outerHeight()
                        }, 200);
                    } else {
                        $('.ycedtm').animate({
                            top: '100%'
                        }, 200);
                    }

                    $('.paybtn .hour').text(timespanCount);
                    $('.paybtn .money').text($('#priceperhour').text() * timespanCount);
                }
            });

            $('#submit').click(function() {
                Order.success = function(id) {
                    location.href = '/show/gt_orderdetail?id=' + id;
                }

                Order.validate();

                var time_text = '';

                var time_list = $('.ycedtm .cmitie').find('.time');

                $.each(time_list, function() {
                    if (time_text != '')
                        time_text = time_text + ',';

                    time_text = time_text + $(this).html();
                })

                var coach_config_id = $('#coach_config_id').val();
                var coach_id = $('#coach_id').val();
                var amount = $('.paybtn .money').text();
                var time = $('.paybtn .hour').text();

                Order.create_pt(3, amount, amount, coach_id, time, time_text, coach_config_id);

                return false;
            });
        });

        function loadTimeConfig(date, week_num) {
            var coach_id = $('#coach_id').val();

            var data = { 'coach_id': coach_id, 'date': date, 'week_num':week_num };

            fit.ajax({
                url:'/api/getCoachTimeConfig',
                data:data,
                success:function(result){
//                    alert(JSON.stringify(result)); //return;

                    if (result.code == 200) {
                        var order_time = result.data.order_time;
                        var order_times = order_time.split(',');
//                        alert(result.data[0]);
//                        alert(result.data[0].time);
                        $('.kd').removeClass('selected');

                        if (typeof(result.data[0]) != 'undefined') {
                            var str_time = result.data[0].time;
                            var times = str_time.split(',');

                            $.each(times, function() {
                                for (var i = 0; i <  order_times.length; i++) {
                                    if (order_times[i] == this.split(':')[0])
                                        return;
                                }

                                $('.time_'+ this.split(':')[0]).removeClass('full');
                            })
                        }
                        else {
                            $('.kd').removeClass('full');

                            for (var i = 0; i <  order_times.length; i++) {
                                $('.time_'+ order_times[i]).addClass('full');
                            }

                            $('.time_12').addClass('full');
                            $('.time_13').addClass('full');
                            $('.time_18').addClass('full');
                        }
                    }
                }
            });
        }

        var getTimeTxt = function(t) {
            var t1 = parseInt(t) + 1;
            return t + ":00 - " + t1 + ":00";
        }
    </script>

</body>
<script type="text/javascript">
    userLogin();
</script>
</html>