var arrData = [];   //提交的参数
var nErrorNum = 0;  //捕获的错误

//ajax参数配置
var ajaxForm_setting = {
    type:  "POST",
    url:  null,
    data : null,
    dataType: "json",
    timeout : 100000,
    cache: false,
    async: false,
    success : function(){},
    error : function(){}       
};

//正则验证规则表达式
var btten_rule = function(){
    return{
        intege:"^-?[1-9]\\d*$", //整数
        intege1:"^[1-9]\\d*$",  //正整数
        intege2:"^-[1-9]\\d*$", //负整数
        num:"^([+-]?)\\d*\\.?\\d+$",    //数字
        num1:"^[1-9]\\d*|0$",   //正数（正整数 + 0）
        num2:"^-[1-9]\\d*|0$",  //负数（负整数 + 0）
        decmal:"^([+-]?)\\d*\\.\\d+$",  //浮点数
        decmal1:"^[1-9]\\d*.\\d*|0.\\d*[1-9]\\d*$", //正浮点数
        decmal2:"^-([1-9]\\d*.\\d*|0.\\d*[1-9]\\d*)$",  //负浮点数
        decmal3:"^-?([1-9]\\d*.\\d*|0.\\d*[1-9]\\d*|0?.0+|0)$", //浮点数
        decmal4:"^[1-9]\\d*.\\d*|0.\\d*[1-9]\\d*|0?.0+|0$", //非负浮点数（正浮点数 + 0）
        decmal5:"^(-([1-9]\\d*.\\d*|0.\\d*[1-9]\\d*))|0?.0+|0$",    //非正浮点数（负浮点数 + 0）
        email:"^[\\w\\-\\.]+@[\\w\\-\\.]+(\\.\\w+)+$", //邮件
        color:"^[a-fA-F0-9]{6}$",   //颜色
        url:"^http[s]?:\\/\\/([\\w-]+\\.)+[\\w-]+([\\w-./?%&=]*)?$",    //url
        chinese:"^[\\u4E00-\\u9FA5\\uF900-\\uFA2D]+$",  //仅中文
        ascii:"^[\\x00-\\xFF]+$",   //仅ACSII字符
        zipcode:"^\\d{6}$", //邮编
        mobile:"^(1)[0-9]{10}$",    //手机
        ip4:"^(25[0-5]|2[0-4]\\d|[0-1]\\d{2}|[1-9]?\\d)\\.(25[0-5]|2[0-4]\\d|[0-1]\\d{2}|[1-9]?\\d)\\.(25[0-5]|2[0-4]\\d|[0-1]\\d{2}|[1-9]?\\d)\\.(25[0-5]|2[0-4]\\d|[0-1]\\d{2}|[1-9]?\\d)$",  //ip地址
        notempty:"^\\S+$",  //非空
        picture:"(.*)\\.(jpg|bmp|gif|ico|pcx|jpeg|tif|png|raw|tga)$",   //图片
        rar:"(.*)\\.(rar|zip|7zip|tgz)$",   //压缩文件
        date:"^\\d{4}(\\-|\\/|\.)\\d{1,2}\\1\\d{1,2}$", //日期
        qq:"^[1-9]*[1-9][0-9]*$",   //QQ号码
        tel:"^(([0\\+]\\d{2,3}-)?(0\\d{2,3})-)?(\\d{7,8})(-(\\d{3,}))?$",   //电话号码的函数(包括验证国内区号,国际区号,分机号)
        username:"^\\w+$",  //用来用户注册。匹配由数字、26个英文字母或者下划线组成的字符串
        letter:"^[A-Za-z]+$",   //字母
        letter_u:"^[A-Z]+$",    //大写字母
        letter_l:"^[a-z]+$",    //小写字母
        idcard:"^[1-9]([0-9]{14}|[0-9]{17})$"   //身份证
    };
}();

var btten = function(){
    return{
        //检查变量是否为空
        IsN : function(str) {
            if ((!str) || (str == '') || (str == 'undefined') || (str == null)) {
                return true;
            } else {
                return false;
            }
        },
        
        //正则验证
        Regex : function(val, regx){
            if(btten.IsN(regx)){
                return true;
            }
            
            var objReg = new RegExp(regx);
            
            if(objReg.test(val)){
                return true;
            }else{
                return false;
            }
        },
        
        //检查字符串长度
        Len : function(str){
            var i, sum = 0;
            for (i = 0; i < str.length; i++) {
                if ((str.charCodeAt(i) >= 0) && (str.charCodeAt(i) <= 255)) {
                    sum = sum + 1;
                } else {
                    sum = sum + 2;
                }
            }
            return sum;
        },
        
        //获取变量的类型
        //return : string 、boolean 、number 、array 、object 、function 、date 、regexp 、math 、null
        GetType : function(obj){
            var _t; 
            return ((_t = typeof(obj)) == "object" ? obj==null && "null" || Object.prototype.toString.call(obj).slice(8,-1):_t).toLowerCase(); 
        },
        
        JsonToStr : function(o){
            var arr = [];
            var fmt =function(s) {
            if (typeof s =='object'&& s !=null) return json2str(s);
            return/^(string|number)$/.test(typeof s) ?"'"+ s +"'" : s;
            }
            for (var i in o) arr.push("'"+ i +"':"+ fmt(o[i]));
            return'{'+ arr.join(',') +'}';
        },
        
        //提示信息参数配置
        TipsOptions : {
            "closeButton": true,
            "debug": false,
            "positionClass": "toast-top-center",
            "onclick": null,
            "showDuration": "1000",
            "hideDuration": "1000",
            "timeOut": "3000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        },
        
        //成功提示信息
        Success : function(_msg, _title){
            toastr.options =this.TipsOptions;
            toastr['success'](_msg, _title); // Wire up an event handler to a button in the toast, if it exists
        },
        
        //提示信息
        Info : function(_msg, _title){
            toastr.options =this.TipsOptions;
            toastr['info'](_msg, _title); // Wire up an event handler to a button in the toast, if it exists
        },
        
        //警告提示信息
        Warning : function(_msg, _title){
            toastr.options =this.TipsOptions;
            toastr['warning'](_msg, _title); // Wire up an event handler to a button in the toast, if it exists
        },
        
        //错误提示信息
        Error : function(_msg, _title){            
            toastr.options =this.TipsOptions;
            toastr['error'](_msg, _title); // Wire up an event handler to a button in the toast, if it exists
        },
        
        Alert : function(_msg, _title){
            _title = _title ? _title : "消息提示";
            $('#btten_alert .modal-title').html(_title);
            $('#btten_alert .modal-body p').html(_msg);
            $('#btten_alert').modal({  
                keyboard: false,    //true：按esc键，弹出框消失。false：esc键不起作用  
                backdrop: true, //true:会有个背景颜色，点击背景颜色，弹出框消逝。false:没有背景颜色，点击弹出框以外区域，弹出框不消失  
                show: true, //true：点击激活按钮后显示弹出框。false：点击激活按钮后，该弹出框不显示  
                remote: ''  //通过jquery的load函数加载另外一个url的页面  
            });  
        },
        
        Confirm : function(_msg, _surefun, _cancelfun){
            $.dialog.confirm(
                _msg, 
                function(){
                    if(btten.IsN(_surefun)){
                        return;
                    }else{
                        _surefun;
                    }
                }, 
                function(){
                    if(btten.IsN(_cancelfun)){
                        return;
                    }else{
                        _cancelfun;
                    }
                }
            );
        },
        
        //锁屏
        Block : function(obj){
            App.blockUI(obj, true);
        },

        //解除锁屏
        Unblock : function(obj){
            jQuery(obj).unblock();
        },
        
        //刷新页面
        F5 : function(){
            location.reload();
        },
        
        //设置按钮为加载状态
        BtnLoading : function(obj){
            $(obj).button('loading');
        },
        
        //设置按钮为初始状态
        BtnReset : function(obj){
            $(obj).button('reset');
        },
        
        //刷新验证码
        FleshVerify : function(_imgid, _cntid){
            var src = $('#' + _imgid).attr("src");
            
            $('#' + _cntid).val('');
            $('#' + _imgid).attr("src", src + "/v/" + Math.random());
        },
        
        //查看大图
        BrowseImg : function(_path, _width, _height){
            $.dialog({
                id: 'browseimage',
                title: '浏览大图',
                lock: true,
                max: false,
                min: false,
                content: '<img src="'+ _path +'" width="'+ _width +'" height="'+ _height +'" />',
                padding: 0
            });
        },
        
        DialogPage : function(_dlgID, _title, _path, _width, _height){
            $.dialog({
                id: _dlgID,
                title: _title,
                width: _width,
                height: _height,
                lock: true,
                max: false,
                min: false,
                content: 'url:' + _path,
                padding: 0
            });
        },
        
        Logout : function(){
            var settings = ajaxForm_setting;
            
            $.ajax({
                type:  settings.type,
                url:  _MODULE_ + '/Login/logout',
                dataType: settings.dataType,
                timeout : settings.timeout,
                cache: settings.cache,
                async: settings.async,
                success: function(result){
                    if(btten.IsN(result)){
                        btten.Error("没有获取到服务器返回消息！");
                        return;
                    }

                    if(!result.status){
                        btten.Error(result.msg);
                    }else{
                        window.location.href = _MODULE_ + '/Login/index';
                    }
                },
                error: settings.error
            });
        },
        
        //验证对象
        Validator : function(controlOptions){
            controlOptions = controlOptions || {};

            var settings = {
                "nulltip":"",   //必填项为空的提示
                "error":"格式错误",     //验证规则错误提示
                "required":false,   //必填项
                "rule":null,    //规则
                "minlen":null,     //最小字符长度
                "maxlen":null,    //最大字符长度
                "notempty":false
            };
            
            $.extend(true, settings, controlOptions);
            
            var sVal = null;
            switch(settings.type){
                case "radio" : 
                    sVal = $('input[name="'+ settings.objid +'"]:checked').val();
                    break;
                    
                default :
                    sVal = $('#'+settings.objid).val();
                    break;
            }

            var nWordLen = btten.Len(sVal);
            var sTipsID = settings.objid + 'Tip';
            var sItemIfrID = settings.objid + '_item';
            
            $('#' + sTipsID).removeClass('has-error');
            $('#' + settings.objid + '_item .errortips').remove();

            //开始验证规则 Begin
  
            if (btten.IsN(sVal)){
                //验证必填
                if(settings.required){
                    $('#' + sTipsID).addClass('has-error');
                    $('#' + sItemIfrID).append('<span class="help-block errortips">必须填写</span>');

                    nErrorNum += 1;
                    return false;
                }
            }else{
                //验证规则
                if(!btten.Regex(sVal, settings.rule)){
                    $('#' + sTipsID).addClass('has-error');
                    $('#' + sItemIfrID).append('<span class="help-block errortips">'+ settings.error +'</span>');
                    nErrorNum += 1;
                    return false;
                }

                //验证最小字符长度
                if(!btten.IsN(settings.minlen)){
                    if(nWordLen < settings.minlen){
                        $('#' + sTipsID).addClass('has-error');
                        $('#' + sItemIfrID).append('<span class="help-block errortips">字符长度不能少于'+ settings.minlen +'个字符，中文计算成2个字符。</span>');
                        nErrorNum += 1;
                        return false;
                    }
                }

                //验证最大字符长度
                if(!btten.IsN(settings.maxlen)){
                    if(nWordLen > settings.maxlen){
                        $('#' + sTipsID).addClass('has-error');
                        $('#' + sItemIfrID).append('<span class="help-block errortips">字符长度不能超过'+ settings.maxlen +'个字符，中文计算成2个字符。</span>');
                        nErrorNum += 1;
                        return false;
                    }
                }
            }
            //开始验证规则 End
            
            arrData.push(settings.objid + "=" + encodeURIComponent(sVal));
        },
        
        //Ajax提交表单
        ValidatorSubmit : function(controlOptions){
            if(nErrorNum > 0){
                nErrorNum = 0;
                return;
            }else{
                nErrorNum = 0;
            }

            ajaxForm_setting.data = arrData.join("&");

            arrData = [];
            var settings = {};
            $.extend(true, settings, ajaxForm_setting, controlOptions);

            //alert(settings.type + "|" + settings.url + "|" + settings.data + "|" + settings.dataType + "|" + settings.timeout + "|" + settings.cache + "|" + settings.async + "|" + settings.success + "|" + settings.error);
            
            if(btten.IsN(settings.url)){
                alert("没有配置提交地址！");
                return;
            }
            
            $.ajax({
                type:  settings.type,
                url:  settings.url,
                data: settings.data,
                dataType: settings.dataType,
                timeout : settings.timeout,
                cache: settings.cache,
                async: settings.async,
                success: settings.success,
                error: settings.error
            });
        },
        
        //缩略图框架HTML
        ThumbHtml : function(_container, _multi, _sjson, _valueid){
            if(btten.IsN(_sjson)){
                btten.Warning("未获取到返回值！");
                return;
            };
            
            eval("var ojson = " + _sjson + ";");
            
            var sReturn = btten.AppendThumbHtml(ojson, _multi, _valueid);
            
            //检查是否允许上传多张图片
            if(_multi){
                //插入缩略图
                $('#' + _container).append(sReturn);

                //获取待提交的图片信息json
                var sVal = $('#' + _valueid).val();

                if(btten.IsN(sVal)){
                    //如果为空，表示是第一张图片，直接保存成json数组字符串
                    $('#' + _valueid).val('['+ _sjson +']');
                }else{
                    //将之前的图片json数组字符串执行成对象，方便操作。
                    eval("var arrJson = " + sVal + ";");

                    //将新添加的图片json字符串执行成对象，方便操作
                    //eval("var newJson = " + _sjson);
                    
                    var arrValue = [];
                    var nLen = arrJson.length;
                    var strJson = null;
                    var info = null;

                    //生成之前的图片json字符串，并插入到返回数组中
                    for(var i=0; i<nLen; i++){
                        info = arrJson[i];
                        strJson = '{"imageid": '+ info.imageid +', "image":"'+ info.image +'", "path":"'+ info.path +'", "width":'+ info.width +', "height":'+ info.height +', "thumb_image":"'+ info.thumb_image +'", "thumb_path":"'+ info.thumb_path +'", "thumb_width":'+ info.thumb_width +', "thumb_height":'+ info.thumb_height +', "thumb_type":"'+ info.thumb_type +'"}';
                        arrValue.push(strJson);
                    }
                    //将本次添加的图片json字符串加入到返回数组中
                    arrValue.push(_sjson);
                    
                    //赋值到待提交的隐藏文本框中
                    $('#' + _valueid).val('['+ arrValue.join(",") +']');
                }
            }else{
                $('#' + _container).html(sReturn);
                $('#' + _valueid).val('['+ _sjson +']');
            }
        },
        
        LoadDataImage : function(_container, _multi, _valueid){
            var sVal = $('#' + _valueid).val();
            
            if(!btten.IsN(sVal)){
                eval("var objJson = " + sVal + ";");
                var Dom = [];
                var nLen = objJson.length;
 
                for(var i=0; i<nLen; i++){
                    Dom.push(btten.AppendThumbHtml(objJson[i], _multi, _valueid));
                }

                if(_multi){
                    $('#' + _container).append(Dom.join(''));
                }else{
                    $('#' + _container).html(Dom.join(''));
                } 
            }
        },
        
        AppendThumbHtml : function(objJson, _multi, _valueid){
            var sHtml = [];
            var sBigImgPath = _PUBLIC_ + objJson.path + objJson.image;
            var sSmallImgPath = _PUBLIC_ + objJson.thumb_path + objJson.thumb_image;
            var sItemID = objJson.thumb_image.replace(".", "");
            
            sHtml.push('<div class="item" id="'+ sItemID +'" style="width:'+ objJson.thumb_width +'px;">');
            sHtml.push('<a href="javascript:void(0);" class="thumbnail" onclick="btten.BrowseImg(\''+ sBigImgPath +'\', '+ objJson.width +', '+ objJson.height +');">');
            sHtml.push('<img src="'+ sSmallImgPath +'" style=" width: '+ objJson.thumb_width +'px; height: '+ objJson.thumb_height +'px; display: block;">');
            sHtml.push('</a>');
            sHtml.push('<div class="btn-group btn-group btn-group-justified btn_ifr">');
            sHtml.push('<a href="javascript:void(0);" class="btn btn-info" onclick="btten.BrowseImg(\''+ sBigImgPath +'\', '+ objJson.width +', '+ objJson.height +');" ><i class="icon-picture"></i>');
            
            if(objJson.thumb_width >= 140){
                sHtml.push(' 大图');
            }
            
            sHtml.push('</a>');
            
            sHtml.push('<a href="javascript:void(0);" class="btn btn-danger" data-value="' + objJson.thumb_image + '" onclick="btten.RemoveThumbHtml(this, \''+ sItemID +'\', \''+ _valueid +'\', '+ _multi +');"><i class="icon-trash"></i>');
            
            if(objJson.thumb_width >= 140){
                sHtml.push(' 移除');
            }
            
            sHtml.push('</a>');
            
            sHtml.push('</div>');
            sHtml.push('</div>');
            
            return sHtml.join("");
        },
        
        //移除缩略图
        RemoveThumbHtml : function(_obj, _itemid, _valueid, _multi){
            if(_multi){
                var sJson = $('#' + _valueid).val();
                var DelImageName = $(_obj).attr('data-value');
                if(btten.IsN(sJson)){ return; }

                eval("var oJson = " + sJson + ";");
                
                var nLen = oJson.length;
                var info = null;
                var strJson = null;
                var arrReturn = [];
                
                for(var i=0; i<nLen; i++){
                    info = oJson[i];
                    
                    if(info.thumb_image != DelImageName){
                        strJson = '{"imageid": '+ info.imageid +', "image":"'+ info.image +'", "path":"'+ info.path +'", "width":'+ info.width +', "height":'+ info.height +', "thumb_image":"'+ info.thumb_image +'", "thumb_path":"'+ info.thumb_path +'", "thumb_width":'+ info.thumb_width +', "thumb_height":'+ info.thumb_height +', "thumb_type":"'+ info.thumb_type +'"}';
                        
                        arrReturn.push(strJson);
                    }
                }
                
                if((nLen-1) <= 0){
                    $('#' + _valueid).val('');
                }else{
                    $('#' + _valueid).val('['+ arrReturn.join(",") +']');
                }

                $('#' + _itemid).remove();
            }else{
                $('#' + _itemid).remove();
                $('#' + _valueid).val('');
            }
        },
        
        //文件框架HTML
        FileHtml : function(_container, _multi, _sjson, _valueid){
            if(btten.IsN(_sjson)){
                btten.Warning("未获取到返回值！");
                return;
            };
            
            eval("var ojson = " + _sjson + ";");

            var sReturn = btten.AppendFileHtml(_container, _multi, ojson, _valueid);
            
            if(_multi){
                $('#' + _container).append(sReturn);
                
                //获取待提交的文件信息json
                var sVal = $('#' + _valueid).val();
                
                if(btten.IsN(sVal)){
                    //如果为空，表示是第一个文件，直接保存成json数组字符串
                    $('#' + _valueid).val('['+ _sjson +']');
                }else{
                     //将之前的文件json数组字符串执行成对象，方便操作。
                    eval("var arrJson = " + sVal + ";");
                    
                    //将新添加的文件json字符串执行成对象，方便操作
                    //eval("var newJson = " + _sjson);
                    
                    var arrValue = [];
                    var nLen = arrJson.length;
                    var strJson = null;
                    var info = null;
                    
                    //生成之前的文件json字符串，并插入到返回数组中
                    for(var i=0; i<nLen; i++){
                        info = arrJson[i];
                        strJson = '{"fileid":'+ info.fileid +', "name":"'+ info.name +'", "size":'+ info.size +', "ext":"'+ info.ext +'", "savename":"'+ info.savename +'", "savepath":"'+ info.savepath +'"}';
                        arrValue.push(strJson);
                    }
                    //将本次添加的文件json字符串加入到返回数组中
                    arrValue.push(_sjson);

                    //赋值到待提交的隐藏文本框中
                    $('#' + _valueid).val('['+ arrValue.join(",") +']');
                }
            }else{
                $('#' + _container).html(sReturn);
                $('#' + _valueid).val('['+ _sjson +']');
            }
        },
        
        LoadDataFile : function(_container, _multi, _valueid){
            var sVal = $('#' + _valueid).val();

            if(!btten.IsN(sVal)){
                eval("var objJson = " + sVal + ";");

                var Dom = [];
                var nLen = objJson.length;
                
                for(var i=0; i<nLen; i++){
                    Dom.push(btten.AppendFileHtml(_container, _multi, objJson[i], _valueid));
                }
                
                $('#'+ _container).removeClass("btten_file_no_margin").addClass("btten_file_margin");

                if(_multi){
                    $('#' + _container).append(Dom.join(''));
                }else{
                    $('#' + _container).html(Dom.join(''));
                } 
            }
        },
        
        AppendFileHtml : function(f_container, f_multi, objJson, f_valueid){
            var sHtml = [];
            var sFilePath = _PUBLIC_ + objJson.savepath + objJson.savename;
            var sItemID = objJson.savename.replace(".", "");

            sHtml.push('<li class="list-group-item" id="'+ sItemID +'">');
            sHtml.push('<a href="javascript:void(0);" class="btn btn-xs btn-danger remove" data-value="' + objJson.savename + '" onclick="btten.RemoveFileHtml(this, \''+ sItemID +'\', \''+ f_valueid +'\', '+ f_multi +', \''+ f_container +'\');"><i class="icon-trash"></i> 移除</a>');
            sHtml.push('<div><a href="'+ sFilePath +'">'+ objJson.name +'</a></div>');
            sHtml.push('</li>');

            return sHtml.join("");
        },
        
        //移除文件
        RemoveFileHtml : function(_obj, _itemid, _valueid, _multi, _container){
            if(_multi){
                var sJson = $('#' + _valueid).val();
                var DelFileName = $(_obj).attr('data-value');
                if(btten.IsN(sJson)){ return; }
                
                eval("var oJson = " + sJson + ";");
                
                var nLen = oJson.length;
                var info = null;
                var strJson = null;
                var arrReturn = [];
                
                for(var i=0; i<nLen; i++){
                    info = oJson[i];
                    
                    if(info.savename != DelFileName){
                        strJson = '{"fileid":'+ info.fileid +', "name":"'+ info.name +'", "size":'+ info.size +', "ext":"'+ info.ext +'", "savename":"'+ info.savename +'", "savepath":"'+ info.savepath +'"}';
                        
                        arrReturn.push(strJson);
                    }
                }
                
                if((nLen-1) <= 0){
                    $('#'+ _container).removeClass("btten_file_margin").addClass("btten_file_no_margin");
                    $('#' + _valueid).val('');
                }else{
                    $('#' + _valueid).val('['+ arrReturn.join(",") +']');
                }
                
                $('#' + _itemid).remove();
            }else{
                $('#'+ _container).removeClass("btten_file_margin").addClass("btten_file_no_margin");
                $('#' + _itemid).remove();
                $('#' + _valueid).val('');
            }
        },
        
        //列表页复选框全选
        ListPageCheckBoxSelectAll : function(_ContainerID){
            jQuery('#'+ _ContainerID +' .group-checkable').change(function () {
                var set = jQuery(this).attr("data-set");
                var checked = jQuery(this).is(":checked");
                
                jQuery(set).each(function () {
                    if (checked) {
                        $(this).attr("checked", true);
                        $(this).parents('tr').addClass("active");
                    } else {
                        $(this).attr("checked", false);
                        $(this).parents('tr').removeClass("active");
                    }
                });
                
                jQuery.uniform.update(set);
            });
        },
        
        //列表页复选框点选
        ListPageCheckBoxSelect : function(_ContainerID){
            jQuery('#'+ _ContainerID +' tbody tr .checkboxes').change(function(){
                var checked = jQuery(this).is(":checked");

                if(checked){
                    $(this).parents('tr').addClass("active");
                }else{
                    $(this).parents('tr').removeClass("active");
                }
            });
        },
        
        //保存栏目
        SaveMenu : function(obj){
            $sPostUrl = $(obj).attr("data-url");
            alert($sPostUrl);
        },
        
        //删除指定数据
        DeleteData : function(_ids, _url){
            $.dialog.confirm('你确定要删除这条数据吗？', function(){
                var settings = {};
                $.extend(true, settings, ajaxForm_setting);
                var oJson = {"delid": _ids};

                $.ajax({
                    type: settings.type,
                    url:  _url,
                    data: oJson,
                    dataType: settings.dataType,
                    timeout : settings.timeout,
                    cache: settings.cache,
                    async: settings.async,
                    success: function(result){
                        if(btten.IsN(result)){
                            btten.Error("没有获取到服务器返回消息！");
                            return;
                        }

                        if(!result.status){
                            btten.Error(result.info);
                        }else{
                            btten.F5();
                        }
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        btten.Error("无法加载请求的内容！");
                    }
                });
            }, function(){
                return;
            });
        },
        
        //删除选中数据
        DeleteSelectData : function(_url){
            var sIds = btten.GetSelectIds();

            if(btten.IsN(sIds)){
                btten.Warning("请选择需要删除的数据！");
                return;
            }
            
            $('input[name="list_cb_ids"]').attr("checked", false);
            btten.DeleteData(sIds, _url);
        },
        
        //获取列表页选中的数据ID
        GetSelectIds : function(){
            var chk_value =[];//定义一个数组    
            $('input[name="list_cb_ids"]:checked').each(function(){//遍历每一个名字为interest的复选框，其中选中的执行函数    
                chk_value.push($(this).val());//将选中的值添加到数组chk_value中    
            });
            
            var sReturn = chk_value.join(",");

            return sReturn;
        }
    };
}();