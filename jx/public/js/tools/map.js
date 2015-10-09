function getAddressByCoordinate(lng, lat, fun) {
    var url = "http://api.map.baidu.com/geocoder/v2/?ak=8TN0gC5Rqo6cec2jroKOkNpE&callback=renderReverse&location="+ lat +","+ lng +"&output=json&pois=1";

    $.ajax({
        url: url,
        dataType: 'JSONP',
        method: 'post',
        success: function (response) {
            //alert(JSON.stringify(response)); return;
            if (response.status == 0) {
                if (fun) {
                    fun(response);
                }
            }
            else {
                $.toastMsg("获取失败", 3000);
            }
        },
        error: function () {

        }
    });
}

function getAddressByAddressName(address_name, fun) {
   var url = "http://api.map.baidu.com/geocoder/v2/?ak=8TN0gC5Rqo6cec2jroKOkNpE&callback=renderOption&output=json&address="+ address_name +"&city=深圳市";

    $.ajax({
        url: url,
        dataType: 'JSONP',
        method: 'post',
        success: function (response) {
            //alert(JSON.stringify(response)); return;
            if (response.status == 0) {
                if (fun) {
                    getAddressByCoordinate(response.result.location.lng, response.result.location.lat, fun)
                }
            }
            else {
                $.toastMsg("没有找到对应信息,请输入完整地名!", 3000);
            }
        },
        error: function () {

        }
    });
}