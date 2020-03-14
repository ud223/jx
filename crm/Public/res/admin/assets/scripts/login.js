var Login = function () {
    var handleLogin = function() {
        var form = $('#loginform');
        
        $('#loginform').validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block', // default input error message class
            focusInvalid: false, // do not focus the last invalid input

            rules: {
                username: {
                    required: true
                },
                password: {
                    required: true
                },
                code: {
                    required: true
                }
            },

            messages: {
                username: {
                    required: "请输入账号"
                },
                password: {
                    required: "请输入密码"
                },
                code: {
                    required: "请输入验证码"
                }
            },

            //出现错误时调用的
            invalidHandler: function (event, validator) { //display error alert on form submit   
                btten.BtnReset($('#btnLogin'));
            },

            //高亮显示错误项
            highlight: function (element) { // hightlight error inputs
                $(element).closest('.form-group').addClass('has-error'); // set error class to the control group
            },

            success: function (label) {
                label.closest('.form-group').removeClass('has-error');
                label.remove();
            },

            errorPlacement: function (error, element) {
                error.insertAfter(element.closest('.input-icon'));
            },

            submitHandler: function (form) {
                form.submit();
            }
        });

        $('#loginform input').keypress(function (e) {
            if (e.which == 13) {
                if ($('#loginform').validate().form()) {
                    $('#loginform').submit();
                }
                
                return false;
            }
        });
    };
    
    return {
        //main function to initiate the module
        init: function () {
            handleLogin();
        }
    };

}();