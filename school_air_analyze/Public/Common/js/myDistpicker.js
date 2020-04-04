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

        $('#'+ this.city_id).change(function() {
            that.change(this, 1);
        });
    },  

    change: function(sel, level) {
        var that = this;
        var it = $(sel);

        var value = it.val();

        console.log(it.find("option:selected").attr('data_list'));

        var data_list = JSON.parse(it.find("option:selected").attr('data_list'));

        switch (level) {
            case 0:{
                this.city_load(false, data_list);

                break;
            }
            case 1:{
                console.log(data_list);

                this.region_load(false, data_list);

                break;
            }
        }
    },

    setVal: function(value, level) {
        var that = this;

        $('#'+ this.province_id).val(value);
    }, 

    province_load: function(value) {
        var that = this;

        this.provice_clear();

        var options = '';
        var sel_item = false;
        var provice_sel = $("#"+ this.province_id);

        sel_item = that.data[0];

        $.each(that.data, function() {
            options += "<option value='"+ this.name +"' data_list='"+ JSON.stringify(this.city) +"'>"+ this.name +"</option>";

            if (value && value == this.name) {
                sel_item = this;
            }
        })

        provice_sel.append(options);

        if (value) {
            provice_sel.val(value);
        }

        this.city_load(false, sel_item.city);
    },

    city_load: function(value, data_list) {
        this.city_clear();

        var options = '';
        var sel_item = false;
        var city_sel = $("#"+ this.city_id);

        sel_item = data_list[0];

        $.each(data_list, function() {
            options += "<option value='"+ this.name +"' data_list='"+ JSON.stringify(this.area) +"'>"+ this.name +"</option>";

            if (value && value == this.name) {
                sel_item = this;
            }
        });

        city_sel.append(options);

        if (value) {
            city_sel.val(value);
        }

        this.region_load(false, sel_item.area);
    },

    region_load: function(value, data_list) {
        this.region_clear();

        var options = '';
        var region_sel = $("#"+ this.region_id);

        $.each(data_list, function() {
            options += "<option value='"+ this +"'>"+ this +"</option>";
        });

        region_sel.append(options);

        if (value) {
            region_sel.val(value);
        }
    },

    provice_clear: function() {
        $('#'+ this.province_id).html('');
    },

    city_clear: function() {
        $('#'+ this.city_id).html('');
    },

    region_clear: function() {
        $('#'+ this.region_id).html('');
    }
}