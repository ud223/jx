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