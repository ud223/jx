$(function() {

    var playVideo2 = $('#willesPlay2 video');//第二个
    var playVideo3 = $('#willesPlay3 video');//第三个
    // var playPause = $('.playPause'); 

    var playPause2 = $('.playPause2'); //播放和暂停(第二个)
    var playPause3 = $('.playPause3'); //播放和暂停(第三个)
    var currentTime = $('.timebar .currentTime'); //当前时间

    var duration2 = $('#willesPlay2 .timebar .duration'); //总时间
    var duration3 = $('#willesPlay3 .timebar .duration'); //总时间

    var progress2 = $('#willesPlay2 .timebar .progress-bar'); //进度条
    var progress3 = $('#willesPlay3 .timebar .progress-bar'); //进度条

    var volumebar2 = $('#willesPlay2 .volumeBar .volumewrap').find('.progress-bar');
    var volumebar3 = $('#willesPlay3 .volumeBar .volumewrap').find('.progress-bar');

    playVideo2[0].volume = 0.4; //初始化音量
    playVideo3[0].volume = 0.4; //初始化音量


    playPause2.on('click', function() {
        playControl(playPause2,2,playVideo2);
    });

    playPause3.on('click', function() {
        playControl(playPause3,3,playVideo3);
    });


    $('.playContent2').on('click', function() {
        playControl(playPause2,2,playVideo2);
    })
    $('.playContent3').on('click', function() {
        playControl(playPause3,3,playVideo3);
    })
    $(document).click(function() {
        $('.volumeBar').hide();
    });

    playVideo2.on('loadedmetadata', function() {
        duration2.text(formatSeconds(playVideo2[0].duration));
    });
    playVideo3.on('loadedmetadata', function() {
        duration3.text(formatSeconds(playVideo3[0].duration));
    });
    // *******************************************    

    playVideo2.on('timeupdate', function() {
        currentTime.text(formatSeconds(playVideo2[0].currentTime));
        progress2.css('width', 100 * playVideo2[0].currentTime / playVideo2[0].duration + '%');
    });
    playVideo3.on('timeupdate', function() {
        currentTime.text(formatSeconds(playVideo3[0].currentTime));
        progress3.css('width', 100 * playVideo3[0].currentTime / playVideo3[0].duration + '%');
    });
    // **************************************************************

     playVideo2.on('ended', function() {
        $('.playTip').removeClass('glyphicon-pause').addClass('glyphicon-play').fadeIn();
        playPause.toggleClass('playIcon');
    });
    playVideo3.on('ended', function() {
        $('.playTip').removeClass('glyphicon-pause').addClass('glyphicon-play').fadeIn();
        playPause.toggleClass('playIcon');
    });
    // ****************************************************************   
    $(window).keyup(function(event){
        event = event || window.event;
            if(event.keyCode == 32)playControl(playPause2,2,playVideo2);
            if(event.keyCode == 27){
            $('.fullScreen').removeClass('cancleScreen');
            $('#willesPlay .playControll').css({
                'bottom': -48
            }).removeClass('fullControll');
            };
        event.preventDefault();
    });
    
    
    //全屏

    $fullScreen2=$('#willesPlay2 .fullScreen');
    $fullScreen3=$('#willesPlay3 .fullScreen');



    $fullScreen2.on('click', function() {
        if ($(this).hasClass('cancleScreen')) {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            } else if (document.mozExitFullScreen) {
                document.mozExitFullScreen();
            } else if (document.webkitExitFullscreen) {
                document.webkitExitFullscreen();
            }
            $(this).removeClass('cancleScreen');
            $('#willesPlay2 .playControll').css({
                'bottom': -48
            }).removeClass('fullControll');
        } else {
                // *************************************
            if (playVideo2[0].requestFullscreen) {
                playVideo2[0].requestFullscreen();
            } else if (playVideo2[0].mozRequestFullScreen) {
                playVideo2[0].mozRequestFullScreen();
            } else if (playVideo2[0].webkitRequestFullscreen) {
                playVideo2[0].webkitRequestFullscreen();
            } else if (playVideo2[0].msRequestFullscreen) {
                playVideo2[0].msRequestFullscreen();
            }
            // *************************************
            $(this).addClass('cancleScreen');
            $('#willesPlay2 .playControll').css({
                'left': 0,
                'bottom': 0
            }).addClass('fullControll');
        }
        return false;
    });

    $fullScreen3.on('click', function() {
        if ($(this).hasClass('cancleScreen')) {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            } else if (document.mozExitFullScreen) {
                document.mozExitFullScreen();
            } else if (document.webkitExitFullscreen) {
                document.webkitExitFullscreen();
            }
            $(this).removeClass('cancleScreen');
            $('#willesPlay3 .playControll').css({
                'bottom': -48
            }).removeClass('fullControll');
        } else {
            // *************************************
            if (playVideo3[0].requestFullscreen) {
                playVideo3[0].requestFullscreen();
            } else if (playVideo3[0].mozRequestFullScreen) {
                playVideo3[0].mozRequestFullScreen();
            } else if (playVideo3[0].webkitRequestFullscreen) {
                playVideo3[0].webkitRequestFullscreen();
            } else if (playVideo3[0].msRequestFullscreen) {
                playVideo3[0].msRequestFullscreen();
            }
            // *************************************
            $(this).addClass('cancleScreen');
            $('#willesPlay3 .playControll').css({
                'left': 0,
                'bottom': 0
            }).addClass('fullControll');
        }
        return false;
    });
    //音量
    $('.volume').on('click', function(e) {
        e = e || window.event;
        $('.volumeBar').toggle();
        e.stopPropagation();
    });
    $('.volumeBar').on('click mousewheel DOMMouseScroll', function(e) {
        e = e || window.event;
        volumeControl(e);
        e.stopPropagation();
        return false;
    });
    $('.timebar .progress').mousedown(function(e) {
        e = e || window.event;
        updatebar(e.pageX);
    });
    //$('.playContent').on('mousewheel DOMMouseScroll',function(e){
    //  volumeControl(e);
    //});
    var updatebar = function(x) {

        var maxduration2 = playVideo2[0].duration; //Video 
        var maxduration3 = playVideo3[0].duration; //Video 

  
        var positions2 = x - progress2.offset().left; //Click pos
        var percentage2 = 100 * positions2 / $('#willesPlay2 .timebar .progress').width();

        var positions3 = x - progress3.offset().left; //Click pos
        var percentage3 = 100 * positions3 / $('#willesPlay3 .timebar .progress').width();

        //Check within range

 
        if (percentage2 > 100) {
            percentage2 = 100;
        }
        if (percentage2 < 0) {
            percentage2 = 0;
        }

        if (percentage3 > 100) {
            percentage3 = 100;
        }
        if (percentage3 < 0) {
            percentage3 = 0;
        }



        //Update progress bar and video currenttime

        progress2.css('width', percentage2 + '%');
        progress3.css('width', percentage3 + '%');

        playVideo2[0].currentTime = maxduration2 * percentage2 / 100;
        playVideo3[0].currentTime = maxduration3 * percentage3 / 100;
    };
    //音量控制
    function volumeControl(e) {
        e = e || window.event;
        var eventype = e.type;
        var delta = (e.originalEvent.wheelDelta && (e.originalEvent.wheelDelta > 0 ? 1 : -1)) || (e.originalEvent.detail && (e.originalEvent.detail > 0 ? -1 : 1));
        var positions = 0;
        var percentage = 0;
        if (eventype == "click") {
  

            positions2 = volumebar2.offset().top - e.pageY;
            percentage2 = 100 * (positions2 + volumebar2.height()) / $('#willesPlay2 .volumeBar .volumewrap').height();

            positions3 = volumebar3.offset().top - e.pageY;
            percentage3 = 100 * (positions3 + volumebar3.height()) / $('#willesPlay3 .volumeBar .volumewrap').height();    



        } else if (eventype == "mousewheel" || eventype == "DOMMouseScroll") {
 
            percentage2 = 100 * (volumebar2.height() + delta) / $('#willesPlay2 .volumeBar .volumewrap').height();
            percentage3 = 100 * (volumebar3.height() + delta) / $('#willesPlay3 .volumeBar .volumewrap').height();
        }


        if (percentage2 < 0) {
            percentage2 = 0;
            $('#willesPlay2 .otherControl .volume').attr('class', 'volume glyphicon glyphicon-volume-off');
        }

        if (percentage3 < 0) {
            percentage3 = 0;
            $('#willesPlay3 .otherControl .volume').attr('class', 'volume glyphicon glyphicon-volume-off');
        }


   
        if (percentage2 > 50) {
            $('#willesPlay2 .otherControl .volume').attr('class', 'volume glyphicon glyphicon-volume-up');
        }
        if (percentage3 > 50) {
            $('#willesPlay3 .otherControl .volume').attr('class', 'volume glyphicon glyphicon-volume-up');
        }        


  
        if (percentage2 > 0 && percentage2 <= 50) {
            $('#willesPlay2 .otherControl .volume').attr('class', 'volume glyphicon glyphicon-volume-down');
        }
        if (percentage3 > 0 && percentage3 <= 50) {
            $('#willesPlay3 .otherControl .volume').attr('class', 'volume glyphicon glyphicon-volume-down');
        }


  
        if (percentage2 >= 100) {
            percentage3 = 100;
        }

        if (percentage3 >= 100) {
            percentage3 = 100;
        }


        $('#willesPlay2 .volumewrap .progress-bar').css('height', percentage2 + '%');
        playVideo2[0].volume = percentage2 / 100;

        $('#willesPlay3 .volumewrap .progress-bar').css('height', percentage3 + '%');
        playVideo3[0].volume = percentage3 / 100;


        e.stopPropagation();
        e.preventDefault();
    }

    function playControl(playPause,num,playVideo) {
            playPause.toggleClass('playIcon');
            if (playVideo[0].paused) {
                playVideo[0].play();
                $('.playTip'+num).removeClass('glyphicon-play').addClass('glyphicon-pause').fadeOut();
            } else {
                playVideo[0].pause();
                $('.playTip'+num).removeClass('glyphicon-pause').addClass('glyphicon-play').fadeIn();
            }
        }
        //关灯
    $('.btnLight').click(function(e) {
        e = e || window.event;
        if ($(this).hasClass('on')) {
            $(this).removeClass('on');
            $('body').append('<div class="overlay"></div>');
            $('.overlay').css({
                'position': 'absolute',
                'width': 100 + '%',
                'height': $(document).height(),
                'background': '#000',
                'opacity': 1,
                'top': 0,
                'left': 0,
                'z-index': 999
            });
            $('.playContent').css({
                'z-index': 1000
            });
            $('.playControll').css({
                'bottom': -48,
                'z-index': 1000
            });

            $('.playContent').hover(function() {
                $('.playControll').stop().animate({
                    'height': 48,
                },500);
            }, function() {
                setTimeout(function() {
                    $('.playControll').stop().animate({
                        'height': 0,
                    }, 500);
                }, 2000)
            });
        } else {
            $(this).addClass('on');
            $('.overlay').remove();
            $('.playControll').css({
                'bottom': 0,
            });
        }
        e.stopPropagation();
        e.preventDefault();
    });
});

//秒转时间
function formatSeconds(value) {
    value = parseInt(value);
    var time;
    if (value > -1) {
        hour = Math.floor(value / 3600);
        min = Math.floor(value / 60) % 60;
        sec = value % 60;
        day = parseInt(hour / 24);
        if (day > 0) {
            hour = hour - 24 * day;
            time = day + "day " + hour + ":";
        } else time = hour + ":";
        if (min < 10) {
            time += "0";
        }
        time += min + ":";
        if (sec < 10) {
            time += "0";
        }
        time += sec;
    }
    return time;
}