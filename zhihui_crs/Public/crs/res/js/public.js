//刷新页面
function f5(){
  location.reload();
}

function IsN(str){
  return (str == "" || str == undefined || str == null) ? true : false;
}

function isArray(obj) {
  return Object.prototype.toString.call(obj) === '[object Array]';
}

function BlockWindow_ExecOp(msg, w){
  var __widowWidth = $(window).width();
  var __popWidth = w; //Loading为要显示的div
  var __left = (__widowWidth - __popWidth)/2 + 'px';

  $.blockUI({
    message: msg,
    css: {
      width: w + 'px',
      fontSize:'14px',
      color:'#31708F',
      backgroundColor:'#D9EDF7',
      border: '1px solid #BCE8F1',
      padding: '10px',
      cursor:'default',
      left: __left
    },
    overlayCSS: {
      backgroundColor:'#000',
      cursor:'default'
    }
  });
}

function Un_BlockWindow_ExecOp(){
  $.unblockUI();
}

//region toastr
/*
 位置
 toast-top-right 右上角
 toast-botton-right 右下角
 toash-bottom-left 左下角
 toast-top-left 左上角
 toast-top-full-width 顶部通栏
 toast-bottom-full-width 底部通栏
 toast-top-center 顶部中间
 toast-bottom-center 底部中间
 */
var ToastrOptions = {
  closeButton: true,  //是否显示关闭按钮
  debug: false, //是否使用debug模式
  progressBar: true,  //结束进度条
  showDuration: 400,  //显示的动画时间
  hideDuration: 1000, //消失的动画时间
  timeOut: 3000,  //展现时间
  extendedTimeOut: 0,  //加长展示时间
  showEasing: "swing",  //显示时的动画缓冲方式
  hideEasing: "linear", //消失时的动画缓冲方式
  showMethod: "fadeIn", //显示时的动画方式
  hideMethod: "fadeOut",  //消失时的动画方式
  positionClass: "toast-top-center", //弹出窗的位置
  onclick: null //点击事件
};

function ToastrError(msg, title){
  toastr.options = ToastrOptions;

  if(IsN(title)){
    title = '错误';
  }
  
  var $toastError = toastr['error'](msg, title);
}

function ToastrWarning(msg, title){
  toastr.options = ToastrOptions;

  if(IsN(title)){
    title = '警告';
  }

  var $toast = toastr['warning'](msg, title);
}

function ToastrSuccess(msg, title){
  toastr.options = ToastrOptions;

  if(IsN(title)){
    title = '成功';
  }

  var $toast = toastr['success'](msg, title);
}

function ToastrInfo(msg, title){
  toastr.options = ToastrOptions;

  if(IsN(title)){
    title = '消息';
  }

  var $toast = toastr['info'](msg, title);
}
//endregion toastr

//region Alert
//警告消息提示框
function AlertWarning(title, btncolor){
  if(IsN(btncolor)){
    btncolor = '#F8AC59';
  }
  
  swal({
    title: "",
    text: title,
    type: "warning",
    confirmButtonColor: btncolor,
    confirmButtonText: "确定",
    closeOnConfirm: true
  });
}

//错误消息提示框
function AlertError(title, btncolor){
  if(IsN(btncolor)){
    btncolor = '#ED5565';
  }

  swal({
    title: "",
    text: title,
    type: "error",
    confirmButtonColor: btncolor,
    confirmButtonText: "确定",
    closeOnConfirm: true
  });
}

//成功消息提示框
function AlertSuccess(title, btncolor){
  if(IsN(btncolor)){
    btncolor = '#1C84C6';
  }

  swal({
    title: "",
    text: title,
    type: "success",
    html: true,
    confirmButtonColor: btncolor,
    confirmButtonText: "确定",
    closeOnConfirm: true
  });
}

//消息提示框
function AlertInfo(title, btncolor){
  if(IsN(btncolor)){
    btncolor = '#1AB394';
  }

  swal({
    title: "",
    text: title,
    type: "info",
    confirmButtonColor: btncolor,
    confirmButtonText: "确定",
    closeOnConfirm: true
  });
}
//endregion Alert

//点击列表主checkbox，设置子checkbox的选中状态
//主、子checkbox的name必须是固定的
function ListMainCheckBoxClick(obj){
  var MainCheckBoxStatus = $(obj).prop("checked");
  
  $('input[name="list_chk_sub"]').each(function(){
    $(this).prop("checked", MainCheckBoxStatus);
  });

  $('input[name="list_chk_all"]').each(function(){
    $(this).prop("checked", MainCheckBoxStatus);
  });
}

//点击列表子checkbox，设置主checkbox的选中状态
//主、子checkbox的name必须是固定的
function ListSubCheckBoxClick(obj){
  //获取当前点击的checkbox的选中状态
  var blSubCheckBoxStatus = $(obj).prop("checked");

  if(blSubCheckBoxStatus === false){
    //如果当前checkbox是取消选中状态，则设置上级全选checkbox为取消选中状态
    $('input[name="list_chk_all"]').each(function(){
      $(this).prop("checked", false);
    });
  }else{
    //初始化lv2级栏目的状态
    var blAllStatus = blSubCheckBoxStatus;

    $('input[name="list_chk_sub"]').each(function(){
      //如果有一个状态，与初始化的不匹配，则退出循环
      if($(this).prop("checked") != blAllStatus){
        blAllStatus = !blAllStatus;
        return false;
      }
    });

    //赋值lv1级栏目全选checkbox状态
    $('input[name="list_chk_all"]').each(function(){
      $(this).prop("checked", blAllStatus);
    });
  }
}

//获取列表中选中的checkbox的值，返回逗号分割的字符串
function GetListCheckBoxSelectValue(checkbox_name){
  var ValueList = [];
  var blSubCheckBoxStatus = false;
  
  if(IsN(checkbox_name)){
    checkbox_name = "list_chk_sub";
  }
  
  $('input[name="'+ checkbox_name +'"]').each(function(){
    blSubCheckBoxStatus = $(this).prop("checked");
    
    if(blSubCheckBoxStatus === true){
      ValueList.push($(this).val());
    }
  });
  
  return ValueList.join(",");
}