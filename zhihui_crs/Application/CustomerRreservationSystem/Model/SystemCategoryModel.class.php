<?php
namespace CustomerRreservationSystem\Model;
use Think\Model;

class SystemCategoryModel extends Model {
//  Protected $autoCheckFields = false;

  private $clsSystemCategory = null;
  
  function __construct() {
    $this->clsSystemCategory = new \Org\ZhiHui\SystemCategory();
  }

  /**
   * todo:系统栏目树
   */
  public function SystemCategoryTree(){
    
  }
}
