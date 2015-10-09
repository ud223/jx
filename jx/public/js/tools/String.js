function singleDateCheck(month) {
    if (month < 10) {
        return '0' + month;
    }

    return month;
}

function numDateCheck(month) {
    if (month.substr(0,1) == 0) {
        return month.substr(1, 1);
    }

    return month;
}

function dateToZhcn(date) {
    alert(JSON.stringify(data));
    return;
    date = date.replace("-", "年");
    date = date.replace("-", "月");

    return date + "日";
}

function dateToMonthAndDay(date) {
    if (!date) {
        return date;
    }

    tmp_date = date.split("-");

    return tmp_date[1] + "月" + tmp_date[2] + "日";
}

function limit_money_input() {
    $("input.money").bind("contextmenu", function(){
        return false;
    });

    $("input.money").css('ime-mode', 'disabled');

    $("input.money").bind("keydown", function(e) {
        var key = window.event ? e.keyCode : e.which;
        if (isFullStop(key)) {
            return $(this).val().indexOf('.') < 0;
        }
        return (isSpecialKey(key)) || ((isNumber(key) && !e.shiftKey));
    });
}

function isNumber(key) {
    return key >= 48 && key <= 57
}

function isSpecialKey(key) {
    //8:backspace; 46:delete; 37-40:arrows; 36:home; 35:end; 9:tab; 13:enter
    return key == 8 || key == 46 || (key >= 37 && key <= 40) || key == 35 || key == 36 || key == 9 || key == 13
}

function isFullStop(key) {
    return key == 190 || key == 110;
}