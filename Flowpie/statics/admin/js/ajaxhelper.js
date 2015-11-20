/// <summary>
/// 以ajax方式提交数据
/// </summary>
/// <param name="data">提交的数据</param>
/// <param name="type">提交方式 get or post, 默认是post</param>
/// <param name="successFun">返回成功后执行的方法</param>
/// <param name="errorFun">返回错误后执行的方法</param>
/// <returns></returns>
function ajaxTo(params) {
    var option = {
        url: false,
        data: false,
        type: 'post',
        dataType: 'json',
        successFun: false,
        errorFun: false
    }

    $.extend(option, params);

    if (!option.url) {
        alert('url没有设置!');

        return;
    }

    $.ajax({
        data: option.data,
        url: option.url,
        type: option.type,
        dataType: option.dataType,
        success: function (result) {
            var data = eval('(' + result + ')');
            //alert(JSON.stringify(data));
            if (data.code == '200') {
                if (option.successFun) {
                    option.successFun(data);
                }
            }
            else {
                if (option.errorFun) {
                    option.errorFun(data);
                }
            }
        },
        error: function () {
            alert("数据访问错误, 请重新尝试!");
        }
    });
}