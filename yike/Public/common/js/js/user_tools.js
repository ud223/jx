var User = {
    id: null,
    data: null,

    validate : function() {
        User.id  = localStorage.getItem('user_id');

        if (!User.id)  {
            //alert('请先微信授权!');
            //
            location.href = '/show/index';

            return false;
        }

        return true;
    },

    validateInfo: function(order_id) {
        //alert(JSON.stringify(User.data));
        //如果电话为空 请初始化用户信息
        if (!User.data.Tel || User.data.Tel == 'null') {
            User.toInit(order_id);

            return false;
        }

        return true;
    },
    //回调加载数据方法
    loadData: null,

    toInit: function(order_id) {
        if (location.href.indexOf('gt_register') > 0)
            return;

        //alert('请先填写手机信息!');

        location.href = '/show/gt_register?order_id='+ order_id;
    },

    toCoach: function() {
        if (location.href.indexOf('coach') > 0) {
            return;
        }

        location.href = '/show/coach_home';
    },

    getData: function(id) {
        //alert(id);
        var data = { 'id': id };

        fit.ajax({
            url:'/api/getUser',
            data:data,
            success:function(result){
                if (result.code == 200) {
                    User.data = result.data;

                    //alert(JSON.stringify(User.data));

                    //if (User.data.is_coach == '1') {
                    //    User.toCoach();

                    //    return;
                    //}

                    if (User.loadData) {
                        User.loadData(User.data);
                    }
                }
            }
        });
    },

    load : function() {
        if (!this.validate()) {
            return false;
        }

        this.getData(User.id);
    },
    ///保存成功后的操作
    updated: null,

    update: function (name, tel, nickname) {
        var data = { 'id': User.id, 'name': name, 'tel': tel, 'nickname': nickname };

        fit.ajax({
            url:'/api/updateUser',
            data:data,
            success:function(result){
                //alert(JSON.stringify(result));
                $.toastMsg(result.msg);

                if (User.updated) {
                    User.updated(result);
                }
            }
        });
    },

    setCode: null,

    getCode: function(tel) {
        var data = { 'phone': tel };

        fit.ajax({
            url:'/api/sms_valid',
            data:data,
            success:function(result){
                //alert(result.msg)

                if (User.setCode) {
                    User.setCode(result)
                };
            }
        });
    }
}