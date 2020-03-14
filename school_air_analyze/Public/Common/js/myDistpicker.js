/*
myDistpicker    省市区三级联动
province_id     省份下拉框id
city_id         城市下拉框id
region_id       行政区下拉框id
*/
MyDistpicker = {
    data : null,
    province_id : null,    
    city_id : null,        
    region_id : null,
    url : null,
    success: null,
    error: null,
    
    init: function(province_id, city_id, region_id, data) {
        var that = this;
        this.province_id = province_id;
        this.city_id = city_id;
        this.region_id = region_id;
        this.data = data;

        $('#'+ this.province_id).change(function() {
            that.change(this, 0);
        });
    },  

    change: function(sel, level) {
        var that = this;
        var it = $(sel);

        var value = it.val();

        switch (level) {
            case 0:{
                

                break;
            }
            case 1:{

                break;
            }
        }
    },

    load: function() {
        var options = '';

        $.each(this.data, function() {
            options += "<option value='"+ this.name +"'>"+ this.name +"</option>";
        });

        $("#"+ this.province_id).append(options);
    },

    setVal: function(value, level) {
        var that = this;

        $('#'+ this.province_id).val(value);
    }, 

    province_load: function(value) {
        var that = this;

        $.each(that.data, function() {
            if (this.name == value) {
                var city_sel = $('#'+ that.city_id);

                city_sel.html('');

                var options = '';

                $.each(this.city, function() {
                    options += "<option value='"+ this.name +"'>"+ this.name +"</option>";
                });
        
                city_sel.append(options);
                
                return;
            }
        })
    },

    city_load: function(value) {

    },

    region_load: function(value) {

    }
}