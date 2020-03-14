<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title><?php echo (C("sitename")); ?></title>
        <meta name="keywords" content="<?php echo (C("keywords")); ?>" /><!--网站关键字-->
        <meta name="description" content="<?php echo (C("description")); ?>" /><!--网站描述--> 

        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="/Public/Home/css/bootstrap.min.css" rel="stylesheet">
        <link href="/Public/Home/css/navbar.css" rel="stylesheet">
        <link href="/Public/Home/css/style.css" rel="stylesheet">
        <link href="/Public/Home/css/font-awesome.min.css" rel="stylesheet">        
        <script type="text/javascript" src="/Public/Home/js/jquery.js"></script>
        <script type="text/javascript" src="/Public/Home/js//bootstrap.min.js"></script>
        <script src="/Public/Home/js/popup.js"></script>
        <script type="text/javascript" src="/Public/Home/js/bootstrap-collapse.js"></script>
        <script type="text/javascript" src="http://api.map.baidu.com/api?key=&v=1.1&services=true"></script>
        <script type="text/javascript" src="/Public/Home/js/flexible-bootstrap-carousel.js"></script>
    </head>
    <body>
    <header id="top" class="navbar navbar-default nav-common" role="banner">
    <a class="center-block logo" href="<?php echo U('Index/index');?>"><img src="<?php echo ($logo["img"]); ?>"></a>
    <div class="container">
        <form class="order" action="<?php echo U('Index/search');?>" id="form" method="post" enctype="multipart/form-data">
        <div class="input-group col-md-2 pull-right sosuo">
            <input type="text" class="form-control" name="pian" placeholder="请输入片名" required="required" />  
                <span class="input-group-btn">  
                <button type="submit" class="btn btn-info btn-search"><span class="glyphicon glyphicon glyphicon-search" aria-hidden="true"></span></button>  
            </span>  
        </div>  
        </form>
    </div>

    <nav id="navbar" role="navigation">
        <div class="navwidth">
            <ul class="nav visible-lg "><!--pc端导航-->
                <li class="ienav"><a href="<?php echo U('Index/index');?>"><?php echo ($indexinfo["name"]); ?></a></li>
                <li><a href="<?php echo U('Mogul/index');?>"><?php echo ($indexinfo1["name"]); ?></a></li>
                <li><a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseOne" ><?php echo ($indexinfo2["name"]); ?></a></li>
                <li><a href="<?php echo U('News/index');?>"><?php echo ($indexinfo3["name"]); ?></a></li>
                <li><a href="<?php echo U('About/index');?>"><?php echo ($indexinfo4["name"]); ?></a></li>
                <li><a href="<?php echo U('Feeds/index');?>"><?php echo ($indexinfo5["name"]); ?></a></li>
                <li><a href="<?php echo U('House/index');?>">房子管理</a></li>
            </ul>
            <ul class="nav visible-xs visible-sm"><!--移动端导航-->
                <li class="ienav"><a href="<?php echo U('Index/index');?>"><?php echo ($indexinfo["name"]); ?></a></li>
                <li><a href="<?php echo U('Mogul/index');?>"><?php echo ($indexinfo1["name"]); ?></a></li>
                <li><a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseOne" ><?php echo ($indexinfo2["name"]); ?></a></li>
                <li><a href="<?php echo U('News/index');?>"><?php echo ($indexinfo3["name"]); ?></a></li>
                <li><a href="<?php echo U('About/index');?>"><?php echo ($indexinfo4["name"]); ?></a></li>
            </ul>
            <div id="collapseOne" class="collapse" style="height: 0px;">
                <div class="container">
                    <div class="row">
                        <div class="col-xs-3 zl"><a href="<?php echo U('Work/index?zid=10');?>"><img src="<?php echo ($filmcate); ?>"></a></div>
                        <div class="col-xs-3 zl"><a href="<?php echo U('Work/index?zid=11');?>"><img src="<?php echo ($wangcate); ?>"></a></div>
                        <div class="col-xs-3 zl"><a href="<?php echo U('Work/index?zid=12');?>"><img src="<?php echo ($adcate); ?>"></a></div>
                        <div class="col-xs-3 zl"><a href="<?php echo U('Work/index?zid=13');?>"><img src="<?php echo ($yugaocate); ?>"></a></div>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</header>
    

<div class="container baner">
    <div class="row">
        <div class="col-md-12">
            <div id="cst-space" style="padding-bottom: 40px;border-bottom: 1px solid #c9a16d;">
                <div id="myCarousel" class="carousel slide">
                    <!-- 轮播（Carousel）指标 -->
                    <ol class="carousel-indicators">
                        <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
                        <li data-target="#myCarousel" data-slide-to="1"></li>
                        <li data-target="#myCarousel" data-slide-to="2"></li>
                    </ol>   
                    <!-- 轮播（Carousel）项目 -->
                    <div class="carousel-inner">
                        <?php if(is_array($flash)): $k = 0; $__LIST__ = $flash;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($k % 2 );++$k; if($k == 1 ): ?><div class="item active">
                            <?php else: ?>
                              <div class="item"><?php endif; ?>                                                
                                <a  href="<?php echo ($vo["link"]); ?>"><img src="<?php echo ($vo["pic"]); ?>" alt="First slide"></a>
                             </div><?php endforeach; endif; else: echo "" ;endif; ?>                        
                    </div>
                    <!-- 轮播（Carousel）导航 -->
                    <a class="carousel-control left" href="#myCarousel" data-slide="prev"><img id="place1" src="/Public/Home/img/left.png"></a>
                    <a class="carousel-control right" href="#myCarousel" data-slide="next"><img id="place2" src="/Public/Home/img/right.png"></a> 
                    <script type="text/javascript">
                         $("#place1").mouseover(function(){
                          $(this).attr("src","/Public/Home/img/left2.jpg")
                        })

                         $("#place1").mouseout(function(){
                          $(this).attr("src","/Public/Home/img/left.png")
                        }) 

                         $("#place2").mouseover(function(){
                          $(this).attr("src","/Public/Home/img/right2.jpg")
                        })

                         $("#place2").mouseout(function(){
                          $(this).attr("src","/Public/Home/img/right.png")
                        })                      
                    </script>
                </div> 
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="sp">
        <h3 class="center-block"><img src="/Public/Home/img/guangpu_15.jpg"></h3>
        <div class="shipin" style="position:relative;">
            <a href="<?php echo ($mogulbigpic["url"]); ?>">
            <img class="img-responsive" src="<?php echo ($mogulbigpic["logo"]); ?>" >
            <div class="icovido" style="position:absolute;top:45%;left:48%;z-index:1;"><img src="/Public/Home/img/ico.png"></div>
            </a>
        </div>      
        <div class="row" style="padding-top:30px;padding-bottom:20px;">
            <?php if(is_array($mogulall)): $k = 0; $__LIST__ = array_slice($mogulall,0,4,true);if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($k % 2 );++$k;?><div class="col-xs-6 col-sm-3 sppic1">
                <a href="<?php echo U('Mogul/detail?id='.$vo['aid']);?>" class="sp-a">
                    <div class="preview" style="background:url('<?php echo ($vo["thumbnail"]); ?>') no-repeat; background-size:cover;background-position:center center;"></div>
                    <div class="icovido"><img src="/Public/Home/img/ico.png"></div>
                </a>
                <a href="<?php echo U('Mogul/detail?id='.$vo['aid']);?>" >
                    <h4 style=" font-size: 16px;line-height: 28px;color: #333333;text-align: center"><?php echo ($vo["title"]); ?></h4>
                </a>
                <!-- <span><?php echo ($vo["description"]); ?></span> -->
            </div><?php endforeach; endif; else: echo "" ;endif; ?>
        </div>
    </div>
</div>

<div class="container">
    <div class="sp" style=" padding-top:25px;">
        <h3 class="center-block">作品</h3>
        <div class="shipin" style="position:relative;">
            <a href="<?php echo ($zuopinbigpic["url"]); ?>">
            <img class="img-responsive" src="<?php echo ($zuopinbigpic["logo"]); ?>" >
            <div class="icovido" style="position:absolute;top:45%;left:48%;z-index:1;"><img src="/Public/Home/img/ico.png"></div>
            </a>
        </div>
        <div class="row" style="padding-top:30px;padding-bottom:35px;">
            <?php if(is_array($zuopzilei)): $k = 0; $__LIST__ = $zuopzilei;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($k % 2 );++$k;?><div class="col-xs-6 col-sm-3 sppic">
                <a href="<?php echo U('Work/index?zid='.$vo['id']);?>" class="sp-a">
                    <div class="preview" style="background:url('<?php echo ($vo["pic"]); ?>') no-repeat; background-size:cover;background-position:center center;"></div>
                    <div class="icovido"><img src="/Public/Home/img/ico.png"></div>
                </a>
                <p><?php echo ($vo["name"]); ?></p>
                <span><!-- <?php echo ($vo["seotitle"]); ?> --><?php echo ($vo["title"]); ?></span>
            </div><?php endforeach; endif; else: echo "" ;endif; ?>
        </div>
    </div>
</div>

<div class="container">
    <div class="row">
        <div class="partner">
            <h3 class="center-block">合作资源</h3>
            <ul>
                <?php if(is_array($hezuoziyuan)): $k = 0; $__LIST__ = $hezuoziyuan;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($k % 2 );++$k;?><li class="col-lg-2 col-xs-3"><a href="#"><img src="<?php echo ($vo["thumbnail"]); ?>"></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
            </ul>
        </div> 
    </div>
</div>

    <!--<div class="container">
    <div class="center-block logo"><a href="#" style="margin-top:60px;"><img src="/Public/Home/img/guangpu_03.jpg"></a></div>
</div>-->
<div class="btmwidth db">
    <ul>
        <ul>
            <li><a href="#"><img src="/Public/Home/img/guangpu_71.png"></a></li>
            <li><a href="#collapseOne" class="theme-login"><img src="/Public/Home/img/guangpu_73.png"></a></li>
            <li><a target= _blank href="https://weibo.com/u/6468344436?refer_flag=1001030101_&is_all=1"><img src="/Public/Home/img/guangpu_75.png"></a></li>
        </ul>
    </ul>
</div>

<div class="theme-popover">
    <div class="theme-poptit">
        <a href="javascript:;" title="关闭" class="close">×</a>
        <h3>扫一扫  关注我们吧</h3>
    </div>
    <div class="theme-popbod dform"><center><img src="/Public/Home/img/weixin.jpg"></center></div>
</div>
<div class="theme-popover-mask"></div>

<div class="btmwidth">
    <ul class="nav"><!-- 去掉 nav-pills 可以在手机界面纵向排列-->
        <li><a href="<?php echo U('About/index');?>">关于我们</a></li>
        <li><a href="<?php echo U('About/index');?>" >联系我们</a></li>
        <li><a href="<?php echo U('Feeds/index');?>">成为供稿人</a></li>
    </ul>
</div>
<div class="container bottomwz">
    @版权所有 深圳市梦想光谱传媒文化发展有限公司版权所有 粤ICP备17140347号-1
</div>

</body>
</html>