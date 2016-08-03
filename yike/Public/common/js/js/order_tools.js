Order = {
    id: null,
    date: null,

     validate: function() {
         Order.date = localStorage.getItem('order_date');

         if (!Order.date) {
             alert('请返回首页选择约练日期!')

             location.href = '/show/home';
         }
     },

    success: null,

    create : function(type, amount, pay_amount) {
        var user_id = localStorage.getItem('user_id');
        var lesson_id = $('#lesson_id').val();
        var run_date = $('#date').attr('val');

        if (!lesson_id) {
            lesson_id = 0;
        }

        var data = { 'user_id': user_id, 'lesson_id': lesson_id, 'run_date': run_date, 'amount': amount, 'pay_amount': pay_amount, 'type': type };
        //alert(JSON.stringify(data)); return;
        fit.ajax({
            url:'/api/createOrder',
            data:data,
            success:function(result){
                if (result.code == 200) {
                    Order.id = result.data;

                    if (Order.success) {
                        Order.success(Order.id);
                    }
                }
                else {
                    $.toastMsg(result.msg);
                }
            }
        });
    },

    create_pt : function(type, amount, pay_amount, coach_id, time, time_text) {
        var user_id = localStorage.getItem('user_id');
        var lesson_id = 0;

        if (!lesson_id) {
            lesson_id = 0;
        }

        var data = { 'user_id': user_id, 'lesson_id': lesson_id, 'run_date': Order.date, 'amount': amount, 'pay_amount': pay_amount, 'type': type, 'coach_id': coach_id, 'time': time, 'start_time': time_text };
        //alert(JSON.stringify(data));
        //return;
        fit.ajax({
            url:'/api/createPtOrder',
            data:data,
            success:function(result){
                //alert(JSON.stringify(result));

                if (result.code == 200) {
                    Order.id = result.data;

                    if (Order.success) {
                        Order.success(Order.id);
                    }
                }
                else {
                    $.toastMsg(result.msg);
                }
            }
        });
    },

    setCoach : function(order_id, coach_id) {
        var data = { 'order_id': order_id, 'coach_id': coach_id };

        fit.ajax({
            url:'/admin/manage/setCoach',
            data:data,
            success:function(result){
                alert(result.msg);
            }
        });
    },

    setAmount : function(order_id, amount, vip_amount) {
        var data = { 'order_id': order_id, 'amount': amount, 'vip_amount': vip_amount };
        //alert(JSON.stringify(data)); return;
        fit.ajax({
            url:'/api/updatePayAmount',
            data:data,
            success:function(result){
                //alert(JSON.stringify(result));

                if (result.code == 0) {
                    alert(result.msg);
                }
                else {
                    //如果用户电话没有填写, 会先跳转到修改用户信息页面
                    if (!User.validateInfo(order_id))
                        return;

                    if (amount == 0) {
                        Order.pay(order_id)
                    }
                    else {
                        Order.toPay(order_id);
                    }
                }
            }
        });
    },

    toPay : function(order_id) {
        var url =  '/show/order_pay?id='+ order_id;

        if (order_id == '10000') {
            url = '/show/me_profile';
        }

        location.href = url;
    },

    pay: function(order_id) {
        var data = { 'order_id': order_id };

        fit.ajax({
            url:'/api/vipPay',
            data:data,
            success:function(result){
                //alert(JSON.stringify(result));

                if (result.code == 0) {
                    alert(result.msg);
                }
                else {
                    location.href = '/show/me_order?id=' + order_id +'&back=1';
                }
            }
        });
    },

    start: function(order_id) {
        var data = { 'order_id': order_id };

        fit.ajax({
            url:'/api/lesson_start',
            data:data,
            success:function(result){
                //alert(JSON.stringify(result));

                if (result.code == 0) {
                    alert(result.msg);
                }
                else {
                    alert('开始上课!');

                    location.href = '/home/coach/my_lesson';
                }
            }
        });
    },

    end: function(order_id) {
        var data = { 'order_id': order_id };

        fit.ajax({
            url:'/api/lesson_end',
            data:data,
            success:function(result){
                //alert(JSON.stringify(result));

                if (result.code == 0) {
                    alert(result.msg);
                }
                else {
                    location.href = '/home/coach/my_lesson';
                }
            }
        });
    }
 }