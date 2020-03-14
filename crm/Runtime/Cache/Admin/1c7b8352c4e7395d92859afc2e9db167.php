<?php if (!defined('THINK_PATH')) exit();?><div class="row">
    <div class="col-md-12">
        <h3 class="page-title"><?php echo ($page_title); ?></h3>
        <ul class="page-breadcrumb breadcrumb">
            <li>
                <i class="icon-home"></i>
                <a href="/btc/index.php/Admin/Index/index" class="unline">系统首页</a>
                <?php if(!empty($main_name)): ?><i class="icon-angle-right"></i><?php endif; ?>
            </li>
            <?php if(!empty($main_name)): ?><li><?php echo ($main_name); if(!empty($sub_name)): ?><i class="icon-angle-right"></i><?php endif; ?></li><?php endif; ?>
            <?php if(!empty($sub_name)): ?><li><?php echo ($sub_name); ?></li><?php endif; ?>
        </ul>
    </div>
</div>