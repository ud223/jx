<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>Project 0/1</title>
    <link href="/Public/coach/css/global.css" rel="stylesheet">
<script type="text/javascript" src="/Public/coach/js/jquery-1.11.0.js"></script>
<script type="text/javascript" src="/Public/coach/js/jquery-mobile.js"></script>
<script type="text/javascript" src="/Public/coach/js/global.js"></script>
<script type="text/javascript" src="/Public/common/js/framework.js"></script>
<script type="text/javascript" src="/Public/common/js/global.js?v=1.000001" ></script>
<script src="/Public/common/js/plugin.js"></script>
<script type="text/javascript" src="/Public/coach/js/swipe.js"></script>
<script type="text/javascript" src="/Public/common/js/js/wx.js" ></script>
</head>

<body ontouchstart="">

    <style>
        .time.selected {
            background:#0cc;
            color:#FFF;
        }

        .time.rest {
            background:greenyellow;
            color:red;
        }

    </style>
    <div class="p16-xeet">
        点击日期，然后设置特定约教计划
    </div>

    <div class="p22-tx1">
            <?php
 for ($w = 1; $w < 3; $w++) { echo '<div class="row">'; for ($i = 1; $i < 8; $i++) { if ($w > 1) { $day = $i + 7; } else { $day = $i; } if ($day == 1) { echo '<div class="td day selected" val="'. date("Y-m-d",strtotime("+". $day. " day")) .'" num="'. date("w",strtotime("+". $day. " day")) .'"><div class="t1">'; } else { echo '<div class="td day" val="'. date("Y-m-d",strtotime("+". $day. " day")) .'" num="'. date("w",strtotime("+". $day. " day")) .'"><div class="t1">'; } echo date("D",strtotime("+". $day. " day")); echo '</div><div class="t2">'; echo date("d",strtotime("+". $day. " day")); echo '</div><div class="frm"></div></div>'; } echo '</div>'; } ?>
    </div>

    <div class="row">
        <div style="background:#FBFBFB;margin-bottom:5px;border:1px solid #EEE" class="time time_07">07:00-08:00</div>
        <div style="background:#FBFBFB;margin-bottom:5px;border:1px solid #EEE" class="time time_08">08:00-09:00</div>
        <div style="background:#FBFBFB;margin-bottom:5px;border:1px solid #EEE" class="time time_09">09:00-10:00</div>
        <div style="background:#FBFBFB;margin-bottom:5px;border:1px solid #EEE" class="time time_10">10:00-11:00</div>
        <div style="background:#FBFBFB;margin-bottom:5px;border:1px solid #EEE" class="time time_11">11:00-12:00</div>
        <div style="background:#FBFBFB;margin-bottom:5px;border:1px solid #EEE" class="time time_12">12:00-13:00</div>
        <div style="background:#FBFBFB;margin-bottom:5px;border:1px solid #EEE" class="time time_13">13:00-14:00</div>
        <div style="background:#FBFBFB;margin-bottom:5px;border:1px solid #EEE" class="time time_14">14:00-15:00</div>
        <div style="background:#FBFBFB;margin-bottom:5px;border:1px solid #EEE" class="time time_15">15:00-16:00</div>
        <div style="background:#FBFBFB;margin-bottom:5px;border:1px solid #EEE" class="time time_16">16:00-17:00</div>
        <div style="background:#FBFBFB;margin-bottom:5px;border:1px solid #EEE" class="time time_17">17:00-18:00</div>
        <div style="background:#FBFBFB;margin-bottom:5px;border:1px solid #EEE" class="time time_18">18:00-19:00</div>
        <div style="background:#FBFBFB;margin-bottom:5px;border:1px solid #EEE" class="time time_19">19:00-20:00</div>
        <div style="background:#FBFBFB;margin-bottom:5px;border:1px solid #EEE" class="time time_20">20:00-21:00</div>
        <div style="background:#FBFBFB;margin-bottom:5px;border:1px solid #EEE" class="time time_21">21:00-22:00</div>
        <div style="background:#FBFBFB;margin-bottom:5px;border:1px solid #EEE" class="time time_22">22:00-23:00</div>
    </div>

    <div class="p22-tx2">
        <div id="is_work" class="ssbt s1">
            设为 可预约
        </div>
        <div id="no_work" class="ssbt s2">
            设为 不可预约
        </div>
    </div>


    <div class="clear-15"></div>
    <div class="fullbtn normal cd_hottap" hottap="/home/coach/home">返回</div>
    <div class="bt-distance"></div>



    <script>
        $(function() {
            $('.cd_hottap').click(function() {
                location.href = $(this).attr('hottap');
            })

            $('.day').click(function() {
                $('.day').removeClass('selected');

                $(this).addClass('selected');

                $('.time').removeClass('selected');

                var run_date = $(this).attr('val');
                var week_num = $(this).attr('num');

                if (week_num == 0) {
                    week_num = 7;
                }

                loadMyTime(run_date, week_num);
            })

            $('.time').click(function() {
                if ($(this).hasClass('selected'))
                    $(this).removeClass('selected');
                else
                    $(this).addClass('selected');

                if ($(this).hasClass('rest')) {
                    $(this).removeClass('rest');
                }
            })

            var run_date = $('.day.selected').attr('val');
            var week_num = $('.day.selected').attr('num');

            if (week_num == 0) {
                week_num = 7;
            }


            $('#is_work').click(function() {
                saveItem(1, run_date, week_num);
            })

            $('#no_work').click(function() {
                saveItem(0, run_date, week_num)
            })

            loadMyTime(run_date, week_num);
        });

        function loadMyTime(run_date, week_num) {
            var coach_id = localStorage.getItem('user_id');

            var data = { 'coach_id': coach_id, 'run_date': run_date };//
//            alert(JSON.stringify(data));
            fit.ajax({
                url:'/api/getCoachTime',
                data:data,
                success:function(result){
//                    alert(JSON.stringify(result)); //return;
                    if (!result.data) {
                        $('.time').removeClass('rest');
                        $('.time').removeClass('selected');

                        loadTemplateTime(week_num);

                        return;
                    }

                    if (result.code == 200) {
                        var order_time = result.data[0].time;
                        var order_times = order_time.split(',');

                        if (typeof(result.data[0]) != 'undefined') {
                            if (result.data[0].is_work == 0) {
                                $('.time').removeClass('rest');
                                $('.time').removeClass('selected');

                                $.each(order_times, function () {
                                    var tmp_time = this;

                                    $.each($('.time'), function() {
                                        if ($(this).html() == tmp_time) {
                                            $(this).addClass('rest');
                                        }
                                    })
                                })

//                                $('.time.selected').addClass('rest');
//                                $('.time.selected').removeClass('selected');

//                                return;
                            }
                        }
                    }
                }
            });
        }

        function loadTemplateTime(week_num) {
            var coach_id = localStorage.getItem('user_id');

            var data = { 'coach_id': coach_id, 'week_num': week_num };//
//            alert(JSON.stringify(data));
            fit.ajax({
                url:'/api/getTempletConfig',
                data:data,
                success:function(result){
//                    alert(JSON.stringify(result)); //return;
                    if (!result.data) {
                        $('.time').removeClass('rest');
                        $('.time').removeClass('selected');

                        $.toastMsg("没有配置对应工作计划!");

                        return;
                    }

                    if (result.code == 200) {
                        var order_time = result.data[0].time;
                        var order_times = order_time.split(',');

                        if (typeof(result.data[0]) != 'undefined') {
                            if (result.data[0].is_work == 0) {
                                $('.time').removeClass('rest');
                                $('.time').removeClass('selected');

                                $.each(order_times, function () {
                                    var tmp_time = this;

                                    $.each($('.time'), function() {
                                        if ($(this).html() == tmp_time) {
                                            $(this).addClass('rest');
                                        }
                                    })
                                })

//                                $('.time.selected').addClass('rest');
//                                $('.time.selected').removeClass('selected');

//                                return;
                            }
                        }
                    }
                }
            });
        }

        function saveItem(type, run_date, week_num) {
            var coach_id = localStorage.getItem('user_id');
            var run_date = $('.day.selected').attr('val');
            var times = '';
            var el_times = $('.time.selected');
            var el_rest = $('.time.rest');

            $.each(el_rest, function() {
                if (times != '')
                    times = times + ',';

                times = times + $(this).html();
            })

            if (type == 0) {
                $.each(el_times, function() {
                    if (times != '')
                        times = times + ',';

                    times = times + $(this).html();
                })
            }

            if (times == '') {
                $.toastMsg('必须选择时间段!');

                return;
            }

            var data = { 'coach_id': coach_id, 'run_date': run_date, 'time': times, 'is_work': 0 };//
//            alert(JSON.stringify(data));
            fit.ajax({
                url:'/api/saveCoachTime',
                data:data,
                success:function(result){
//                    alert(JSON.stringify(result)); //return;
                    $.toastMsg(result.msg);

                    if (result.code == 200) {
                        loadMyTime(run_date, week_num);
                    }
                }
            });
        }
    </script>

</body>
<script type="text/javascript">
    userLogin();
</script>
</html>