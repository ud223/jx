<?php
namespace CustomerRreservationSystem\Controller;
use Think\Controller;

class ErrorController extends Controller {
  public function NotPermission(){
    $this->display("not_permission");
  }
}