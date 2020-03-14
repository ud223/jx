<?php if (!defined('THINK_PATH')) exit();?><ul class="page-sidebar-menu">
    <?php echo ($menu); ?>
    <?php if(!empty($AppMenu)): ?><li <?php if(!empty($navappmid)){ echo ' class="active"'; } ?>>
        <a href="javascript:;"><span class="title">内容管理</span><span class="arrow "></span></a>
        <ul class="sub-menu">
            <?php echo ($AppMenu); ?>
        </ul>
    </li><?php endif; ?>
</ul>