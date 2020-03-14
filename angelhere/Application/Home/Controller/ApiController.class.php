<?php
namespace Home\Controller;
use Think\Controller;

class ApiController {

    #region api调用

    public function test() {
        fit_api(true, 200, 'test', '');
    }

    #endregion


} 