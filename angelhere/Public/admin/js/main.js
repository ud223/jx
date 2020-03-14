/**
 * Created by Administrator on 2016/3/16.
 */

//document.domain = location.href.match(/([^.]+[.][^./]+)\//)[1];
$(function(){



    //alert(1);
    var pathname = window.location.pathname.replace(/&.+$/,'');

    var paths = pathname.split('/');
    var active_class = paths[1]+'-'+paths[2];


    //alert(active_class);


    console.log($('.'+active_class));

    $('.'+active_class).addClass('active').closest('.treeview').addClass('active');


    if($('.'+active_class).length == 0){
        active_class = '.'+paths[1]+'-lists';
        //alert(active_class);
        $(active_class).addClass('active').closest('.treeview').addClass('active');
    }



    $('#cur_project_select').on('change',function(){
        var pj = $(this).val();
        var loc = window.location.href;
        //alert(1);

        loc = loc.replace(/\?.+?$/,'');
        var new_url = loc.replace(/&proj=[^&]+|&proj=[^&]+|[?]proj=[^&]*$/,'')+'?proj='+pj;

        window.location.href = new_url;
    });




    $('.action_btn').click(function(){
        var title = $(this).data('tip');

        console.log($(this).data('data'));
        var data = $(this).data('data');
        var url = $(this).data('url');

        $('#my-confirm').find('.title').text(title);

        $('#my-confirm').modal({
            relatedTarget: this,
            onConfirm: function(options) {
                chaomeng.ajax({
                    url:url,
                    data:data,
                    success:function(){

                    }
                });
            },
            // closeOnConfirm: false,
            onCancel: function() {
                //alert('算求，不弄了');
            }
        });
    });
});

