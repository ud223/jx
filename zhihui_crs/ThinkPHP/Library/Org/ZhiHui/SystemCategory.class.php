<?php
/**
 * Created by PhpStorm.
 * User: 淼
 * Date: 2015/4/29
 * Time: 11:41
 */

namespace Org\ZhiHui;

class SystemCategory {
  private $_SystemCategoryList = array(
    array(
      'menu_name' => '管理员',
      'url' => 'javascript:void(0);',
      'ico' => 'fa-eye',
      'role' => array('admin'),
      'sub' => array(
        array(
          'menu_name' => '管理员管理',
          'url' => 'Admin/AdminUserList',
          'ico' => '',
          'role' => array('admin'),
          'sub' => array(),
        ),
      ),
      'permission' => array(
        'admin' => array(
          'Admin/AdminUserList',
          'Admin/AdminUserInfo',
          'Admin/AjaxAdminUserSave',
          'Admin/AjaxAdminUserDelete',
          'Admin/ResetAdminUserCache'
        ),
      ),
    ),
    array(
      'menu_name' => '用户',
      'url' => 'javascript:void(0);',
      'ico' => 'fa-user',
      'role' => array('admin', 'special', 'broker_company', 'captain'),
      'sub' => array(
        array(
          'menu_name' => '先锋账号管理',
          'url' => 'User/SpecialUserInfo',
          'ico' => '',
          'role' => array('admin', 'special'),
          'sub' => array(),
        ),
        array(
          'menu_name' => '唯润城账号管理',
          'url' => 'User/BrokerCompanyUserInfo',
          'ico' => '',
          'role' => array('admin', 'broker_company'),
          'sub' => array(),
        ),
        array(
          'menu_name' => '团队长管理',
          'url' => 'User/SellerCaptainList',
          'ico' => '',
          'role' => array('admin', 'broker_company'),
          'sub' => array(),
        ),
        array(
          'menu_name' => '客户经理管理',
          'url' => 'User/SellerMemberList',
          'ico' => '',
          'role' => array('admin', 'broker_company', 'captain'),
          'sub' => array(),
        ),
      ),
      'permission' => array(
        'admin' => array(
          'User/BrokerCompanyUserInfo',
          'User/AjaxBrokerCompanyUserSave',

          'User/SpecialUserInfo',
          'User/AjaxSpecialUserSave',

          'User/SellerCaptainList',
          'User/SellerCaptainInfo',
          'User/AjaxSellerCaptainSave',
          'User/ResetSellerCaptainCache',

          'User/SellerMemberList',
          'User/SellerMemberInfo',
          'User/AjaxSellerMemberSave',
          'User/ResetSellerMemberCache',

          'User/AjaxUserInfo',
          'User/AjaxCustomerList',
          'User/AjaxUserDelete',

          'User/CustomerPayList',
        ),
        'special' => array(
          'User/SpecialUserInfo',
          'User/AjaxSpecialUserSave',
        ),
        'broker_company' => array(
          'User/BrokerCompanyUserInfo',
          'User/AjaxBrokerCompanyUserSave',

          'User/SellerCaptainList',
          'User/SellerCaptainInfo',
          'User/AjaxSellerCaptainSave',
          'User/ResetSellerCaptainCache',

          'User/SellerMemberList',
          'User/SellerMemberInfo',
          'User/AjaxSellerMemberSave',
          'User/ResetSellerMemberCache',

          'User/AjaxUserInfo',
          'User/AjaxCustomerList',
          'User/AjaxUserDelete',

          'User/CustomerPayList',
        ),
        'captain' => array(
          'User/SellerMemberList',
          'User/SellerMemberInfo',
          'User/AjaxSellerMemberSave',
          'User/ResetSellerMemberCache',

          'User/AjaxUserInfo',
          'User/AjaxCustomerList',
          'User/AjaxUserDelete',

          'User/CustomerPayList',
        ),
      ),
    ),
    array(
      'menu_name' => '客户',
      'url' => 'javascript:void(0);',
      'ico' => 'fa-user-md',
      'role' => array('admin', 'broker_company', 'captain', 'member'),
      'sub' => array(
        array(
          'menu_name' => '客户管理',
          'url' => 'Customer/CustomerList',
          'ico' => '',
          'role' => array('admin', 'broker_company', 'captain', 'member'),
          'sub' => array(),
        ),
      ),
      'permission' => array(
        'admin' => array(
          'Customer/CustomerList',
          'Customer/CustomerInfo',
          'Customer/AjaxGetSellerMemberOption',
          'Customer/AjaxCustomerSave',
          'Customer/ResetCustomerCache',
          'Customer/AjaxCustomerDelete',
          'Customer/AjaxCustomerInfo',
          'Customer/AjaxCustomerPayList',
        ),
        'broker_company' => array(
          'Customer/CustomerList',
          'Customer/CustomerInfo',
          'Customer/AjaxGetSellerMemberOption',
          'Customer/AjaxCustomerSave',
          'Customer/ResetCustomerCache',
          'Customer/AjaxCustomerDelete',
          'Customer/AjaxCustomerInfo',
          'Customer/AjaxCustomerPayList',
        ),
        'captain' => array(
          'Customer/CustomerList',
          'Customer/CustomerInfo',
          'Customer/AjaxGetSellerMemberOption',
          'Customer/AjaxCustomerSave',
          'Customer/ResetCustomerCache',
          'Customer/AjaxCustomerInfo',
          'Customer/AjaxCustomerPayList',
        ),
        'member' => array(
          'Customer/CustomerList',
          'Customer/CustomerInfo',
          'Customer/AjaxGetSellerMemberOption',
          'Customer/AjaxCustomerSave',
          'Customer/ResetCustomerCache',
          'Customer/AjaxCustomerInfo',
          'Customer/AjaxCustomerPayList',
        ),
      ),
    ),
    array(
      'menu_name' => '产品',
      'url' => 'javascript:void(0);',
      'ico' => 'fa-heart-o',
      'role' => array('admin', 'special','broker_company', 'captain', 'member'),
      'sub' => array(
        array(
          'menu_name' => '类别管理',
          'url' => 'Product/ProductTypeList',
          'ico' => '',
          'role' => array('admin', 'broker_company'),
          'sub' => array(),
        ),
        array(
          'menu_name' => '产品管理',
          'url' => 'Product/ProductList',
          'ico' => '',
          'role' => array('admin', 'broker_company'),
          'sub' => array(),
        ),
        array(
          'menu_name' => '产品浏览',
          'url' => 'Product/ProductViewList',
          'ico' => '',
          'role' => array('admin', 'special', 'captain', 'member'),
          'sub' => array(),
        ),
      ),
      'permission' => array(
        'admin' => array(
          'Product/ProductTypeList',
          'Product/AjaxProductTypeSave',
          'Product/QuickModifyProductTypeField',
          'Product/ResetProductTypeCache',
          'Product/AjaxProductTypeDelete',
          'Product/ProductList',
          'Product/ProductInfo',
          'Product/AjaxProductSave',
          'Product/ResetProductCache',
          'Product/AjaxProductDelete',
          'Product/UploadProductContentImage',

          'Product/ProductViewList',
          'Product/AjaxProductViewList',
          'Product/ProductViewInfo',
          'Product/AjaxProductInfo',
        ),
        'special' => array(
          'Product/ProductViewList',
          'Product/AjaxProductViewList',
          'Product/ProductViewInfo',
          'Product/AjaxProductInfo',
        ),
        'broker_company' => array(
          'Product/ProductTypeList',
          'Product/AjaxProductTypeSave',
          'Product/QuickModifyProductTypeField',
          'Product/ResetProductTypeCache',
          'Product/AjaxProductTypeDelete',
          'Product/ProductList',
          'Product/AjaxProductViewList',
          'Product/ProductInfo',
          'Product/AjaxProductSave',
          'Product/ResetProductCache',
          'Product/AjaxProductDelete',
          'Product/UploadProductContentImage',
        ),
        'captain' => array(
          'Product/ProductViewList',
          'Product/AjaxProductViewList',
          'Product/ProductViewInfo',
          'Product/AjaxProductInfo',
        ),
        'member' => array(
          'Product/ProductViewList',
          'Product/AjaxProductViewList',
          'Product/ProductViewInfo',
          'Product/AjaxProductInfo',
        ),
      ),
    ),
    array(
      'menu_name' => '保险计划书',
      'url' => 'javascript:void(0);',
      'ico' => 'fa-file-text-o',
      'code' => 'Plan',
      'role' => array('admin', 'special', 'broker_company', 'captain', 'member'),
      'sub' => array(
        array(
          'menu_name' => '管理计划书',
          'url' => 'Order/PlanList',
          'ico' => '',
          'code' => 'PlanList',
          'role' => array('admin', 'broker_company', 'captain', 'member'),
          'sub' => array(),
        ),
        array(
          'menu_name' => '管理计划书',
          'url' => 'Order/AuditPlanList',
          'ico' => '',
          'code' => 'AuditPlanList',
          'role' => array('special'),
          'sub' => array(),
        ),
      ),
      'permission' => array(
        'admin' => array(
          'Order/PlanList',
          'Order/AjaxPlanInfo',
          'Order/PlanInfo',
        ),
        'special' => array(
          'Order/AuditPlanList',
          'Order/AjaxAuditPlanInfo',
          'Order/AuditPlanInfo',
          'Order/AuditSignatoryOrderInfoSave',
        ),
        'broker_company' => array(
          'Order/PlanList',
          'Order/AjaxPlanInfo',
          'Order/PlanInfo',
        ),
        'captain' => array(
          'Order/PlanList',
          'Order/AjaxPlanInfo',
          'Order/PlanInfo',
        ),
        'member' => array(
          'Order/PlanList',
          'Order/AjaxPlanInfo',
          'Order/PlanInfo',
        ),
      ),
    ),
    array(
      'menu_name' => '预约',
      'url' => 'javascript:void(0);',
      'ico' => 'fa-calendar-check-o',
      'code' => 'Order',
      'role' => array('admin', 'special', 'broker_company', 'captain', 'member'),
      'sub' => array(
        array(
          'menu_name' => '预约管理',
          'url' => 'Order/SubscribeOrderList',
          'ico' => '',
          'code' => 'SubscribeOrder',
          'role' => array('admin', 'broker_company', 'captain', 'member'),
          'sub' => array(),
        ),
        array(
          'menu_name' => '已完成预约',
          'url' => 'Order/CompletedOrderList',
          'ico' => '',
          'role' => array('admin', 'broker_company', 'captain', 'member'),
          'sub' => array(),
        ),
        
        array(
          'menu_name' => '审核预约预约',
          'url' => 'Order/UnauditSubscribeOrderList',
          'code' => 'UnauditSubscribeOrder',
          'ico' => '',
          'role' => array('special'),
          'sub' => array(),
        ),
        array(
          'menu_name' => '已成交预约',
          'url' => 'Order/BusinessOrderList',
          'ico' => '',
          'role' => array('special'),
          'sub' => array(),
        ),
      ),
      'permission' => array(
        'admin' => array(
          //region 预约预约
          'Order/SubscribeOrderList',
          'Order/SubscribeOrderInfoAdd',
          'Order/SubscribeOrderInfo',
          'Order/SubscribeOrderInfoSave',
          'Order/AjaxPlanList',
          'Order/AjaxPlanSave',
          'Order/ChangeReservationTime',
          //endregion 预约预约

          //region 消费预约
          'Order/PayOrderList',
          'Order/PayOrderInfo',
          'Order/PayOrderInfoSave',
          //endregion 消费预约

          //region 回访预约
          'Order/ReturnVisitOrderList',
          'Order/ReturnVisitOrderInfo',
          'Order/ReturnVisitOrderInfoSave',
          //endregion 回访预约

          //region 已完成预约
          'Order/CompletedOrderList',
          'Order/CompletedOrderInfo',
          //endregion 已完成预约

          //region 审核签约预约
          'Order/UnauditSignatoryOrderList',
          'Order/AuditSignatoryOrderInfo',
          'Order/AuditSignatoryOrderInfoSave',
          //endregion 审核签约预约

          //region 审核预约预约
          'Order/UnauditSubscribeOrderList',
          'Order/AuditSubscribeOrderInfo',
          'Order/AuditSubscribeOrderInfoSave',
          //endregion 审核预约预约

          'Order/AjaxSubscribeOrderList',
          'Order/AjaxOrderViewInfo',
          
          'Order/AjaxCustomerAboutData',
          'Order/AjaxCustomerSave',

          'Order/AjaxGetSellerMemberOption',
          'Order/AjaxGetCustomerOption',
          'Order/AjaxCustomerInfo',
          'Order/AjaxProductOptionList',
          'Order/AjaxProductInfo',
          'Order/AjaxOrderDelete',
          'Order/ResetOrderCache',

          'Download/OriginalNameFile',
          'Download/OriginalNameImage',
          //=======================

          'Order/OrderList',
        ),
        'special' => array(
          //region 审核预约预约
          'Order/UnauditSubscribeOrderList',
          'Order/AuditSubscribeOrderInfo',
          'Order/AuditSubscribeOrderInfoSave',
          'Order/AjaxPlanInfo',
          'Order/AjaxSubscribeOrderInfo',
          'Order/AjaxConfirmPaymentOrderInfo',
          'Order/AjaxProductOptionList',
          'Order/AuditUpdatesSubscribeOrderInfo',
          //endregion 审核预约预约

          //region 已成交预约
          'Order/BusinessOrderList',
          'Order/BusinessOrderInfo',
          //endregion 已成交预约
          
          'Order/ResetOrderCache',

          'Download/OriginalNameFile',
          'Download/OriginalNameImage',
        ),
        'broker_company' => array(
          //region 预约预约
          'Order/SubscribeOrderList',
          'Order/SubscribeOrderInfoAdd',
          'Order/SubscribeOrderInfo',
          'Order/SubscribeOrderInfoSave',
          'Order/AjaxPlanList',
          'Order/AjaxPlanSave',
          'Order/ChangeReservationTime',
          //endregion 预约预约

          //region 消费预约
          'Order/PayOrderList',
          'Order/PayOrderInfo',
          'Order/PayOrderInfoSave',
          //endregion 消费预约

          //region 回访预约
          'Order/ReturnVisitOrderList',
          'Order/ReturnVisitOrderInfo',
          'Order/ReturnVisitOrderInfoSave',
          //endregion 回访预约

          //region 已完成预约
          'Order/CompletedOrderList',
          'Order/CompletedOrderInfo',
          //endregion 已完成预约

          //region 审核签约预约
          'Order/UnauditSignatoryOrderList',
          'Order/AuditSignatoryOrderInfo',
          'Order/AuditSignatoryOrderInfoSave',
          //endregion 审核签约预约

          //region 审核预约预约
          'Order/UnauditSubscribeOrderList',
          'Order/AuditSubscribeOrderInfo',
          'Order/AuditSubscribeOrderInfoSave',
          //endregion 审核预约预约
  
          'Order/AjaxSubscribeOrderList',
          'Order/AjaxOrderViewInfo',
          
          'Order/AjaxCustomerAboutData',
          'Order/AjaxCustomerSave',

          'Order/AjaxGetSellerMemberOption',
          'Order/AjaxGetCustomerOption',
          'Order/AjaxCustomerInfo',
          'Order/AjaxProductOptionList',
          'Order/AjaxProductInfo',
          'Order/AjaxOrderDelete',
          'Order/ResetOrderCache',

          'Download/OriginalNameFile',
          'Download/OriginalNameImage',
        ),
        'captain' => array(
          //region 预约预约
          'Order/SubscribeOrderList',
          'Order/SubscribeOrderInfoAdd',
          'Order/SubscribeOrderInfo',
          'Order/SubscribeOrderInfoSave',
          'Order/AjaxPlanList',
          'Order/AjaxPlanSave',
          'Order/ChangeReservationTime',
          //endregion 预约预约

          //region 消费预约
          'Order/PayOrderList',
          'Order/PayOrderInfo',
          'Order/PayOrderInfoSave',
          //endregion 消费预约

          //region 回访预约
          'Order/ReturnVisitOrderList',
          'Order/ReturnVisitOrderInfo',
          'Order/ReturnVisitOrderInfoSave',
          //endregion 回访预约

          //region 已完成预约
          'Order/CompletedOrderList',
          'Order/CompletedOrderInfo',
          //endregion 已完成预约

          //region 审核签约预约
          'Order/UnauditSignatoryOrderList',
          'Order/AuditSignatoryOrderInfo',
          'Order/AuditSignatoryOrderInfoSave',
          //endregion 审核签约预约

          //region 审核预约预约
          'Order/UnauditSubscribeOrderList',
          'Order/AuditSubscribeOrderInfo',
          'Order/AuditSubscribeOrderInfoSave',
          //endregion 审核预约预约
  
          'Order/AjaxSubscribeOrderList',
          'Order/AjaxOrderViewInfo',
          
          'Order/AjaxCustomerAboutData',
          'Order/AjaxCustomerSave',

          'Order/AjaxGetSellerMemberOption',
          'Order/AjaxGetCustomerOption',
          'Order/AjaxCustomerInfo',
          'Order/AjaxProductOptionList',
          'Order/AjaxProductInfo',
          'Order/AjaxOrderDelete',
          'Order/ResetOrderCache',

          'Download/OriginalNameFile',
          'Download/OriginalNameImage',
        ),
        'member' => array(
          //region 预约预约
          'Order/SubscribeOrderList',
          'Order/SubscribeOrderInfoAdd',
          'Order/SubscribeOrderInfo',
          'Order/SubscribeOrderInfoSave',
          'Order/AjaxPlanList',
          'Order/AjaxPlanSave',
          'Order/ChangeReservationTime',
          //endregion 预约预约

          //region 消费预约
          'Order/PayOrderList',
          'Order/PayOrderInfo',
          'Order/PayOrderInfoSave',
          //endregion 消费预约

          //region 回访预约
          'Order/ReturnVisitOrderList',
          'Order/ReturnVisitOrderInfo',
          'Order/ReturnVisitOrderInfoSave',
          //endregion 回访预约

          //region 已完成预约
          'Order/CompletedOrderList',
          'Order/CompletedOrderInfo',
          //endregion 已完成预约

          //region 审核签约预约
          'Order/UnauditSignatoryOrderList',
          'Order/AuditSignatoryOrderInfo',
          'Order/AuditSignatoryOrderInfoSave',
          //endregion 审核签约预约

          //region 审核预约预约
          'Order/UnauditSubscribeOrderList',
          'Order/AuditSubscribeOrderInfo',
          'Order/AuditSubscribeOrderInfoSave',
          //endregion 审核预约预约
  
          'Order/AjaxSubscribeOrderList',
          'Order/AjaxOrderViewInfo',

          'Order/AjaxCustomerAboutData',
          'Order/AjaxCustomerSave',

          'Order/AjaxGetSellerMemberOption',
          'Order/AjaxGetCustomerOption',
          'Order/AjaxCustomerInfo',
          'Order/AjaxProductOptionList',
          'Order/AjaxProductInfo',
          'Order/AjaxOrderDelete',
          'Order/ResetOrderCache',

          'Download/OriginalNameFile',
          'Download/OriginalNameImage',
        ),
      ),
    ),
    /*
    array(
      'menu_name' => '佣金',
      'url' => 'javascript:void(0);',
      'ico' => 'fa-money',
      'role' => array('admin','broker_company', 'captain', 'member'),
      'sub' => array(
        array(
          'menu_name' => '佣金管理',
          'url' => 'Commission/CommissionDateList',
          'ico' => '',
          'role' => array('admin', 'broker_company'),
          'sub' => array(),
        ),
        array(
          'menu_name' => '历史佣金',
          'url' => 'Commission/UserHistoryCommissionList',
          'ico' => '',
          'role' => array('captain', 'member'),
          'sub' => array(),
        ),
      ),
      'permission' => array(
        'admin' => array(
          'Commission/CommissionDateList',
          'Commission/AjaxCalcLastMonthCommission',
          'Commission/AjaxSendCommission',


          'Commission/CommissionList',
          'Commission/CommissionInfo',
          'Commission/AjaxSellerManagerOption',
          'Commission/AjaxCommissionSave',
          'Commission/AjaxCommissionInfo',
          'Commission/ResetCommissionCache',
          'Commission/AjaxStandardAdd',
        ),
        'broker_company' => array(
          'Commission/CommissionDateList',
          'Commission/AjaxCalcLastMonthCommission',
          'Commission/AjaxSendCommission',
          'Commission/ResetCommissionCache',
          'Commission/AjaxCommissionOrderList',

          'Commission/AjaxCommissionOrderList',
        ),
        'captain' => array(
          'Commission/UserHistoryCommissionList',
          'Commission/AjaxCommissionOrderList',
          'Commission/AjaxUserCurrMonthCommissionInfo',
        ),
        'member' => array(
          'Commission/UserHistoryCommissionList',
          'Commission/AjaxCommissionOrderList',
          'Commission/AjaxUserCurrMonthCommissionInfo',
        ),
      )
    ),
    */
  );

  /**
   * todo:获取所有系统栏目
   *
   * @return array
   */
  public function SystemCategoryListAll(){
    foreach($this->_SystemCategoryList as $key=>$val){
      $this->_SystemCategoryList[$key]["child"] = sizeof($val["sub"]);
    }

    return $this->_SystemCategoryList;
  }
  
  /**
   * todo: 获取系统栏目树
   */
  public function GetSystemCategoryTree(){
    $SystemCategoryList = array();
    $sAccountType = GetLoginAccountTypeCookeis();
    
    foreach($this->_SystemCategoryList as $Lv1Key=>$Lv1Val){
      if(!in_array($sAccountType, $Lv1Val["role"])){
        continue;
      }
      
      $Lv1Info = $Lv1Val;
      $Lv1Info["child"] = 0;
      $Lv1Info["sub"] = array();

      foreach($Lv1Val["sub"] as $Lv2Key=>$Lv2Val){
        if(!in_array($sAccountType, $Lv2Val["role"])){
          continue;
        }
        
        $Lv2Info = $Lv2Val;
        $Lv2Info["child"] = sizeof($Lv2Val["sub"]);
        
        array_push($Lv1Info["sub"], $Lv2Info);
      }

      $Lv1Info["child"] = sizeof($Lv1Info["sub"]);
      
      array_push($SystemCategoryList, $Lv1Info);
    }
    
    return $SystemCategoryList;
  }
}