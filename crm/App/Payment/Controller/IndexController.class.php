<?php
namespace Payment\Controller;
use Think\Controller;

class IndexController extends Controller {
    public function index(){
        $this->show('Thanks','utf-8');
    }
}