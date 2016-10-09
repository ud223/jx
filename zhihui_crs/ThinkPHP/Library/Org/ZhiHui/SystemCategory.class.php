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
      'menu_name' => '经纪公司',
      'url' => 'javascript:void(0);',
      'ico' => 'fa-registered',
      'role' => array('admin','broker_company'),
      'sub' => array(
        array(
          'menu_name' => '经纪公司管理',
          'url' => 'BrokerCompany/BrokerCompanyList',
          'ico' => '',
          'role' => array('admin'),
          'sub' => array(),
        ),
        array(
          'menu_name' => '经济公司帐号管理',
          'url' => 'BrokerCompany/BrokerCompanyAccountList',
          'ico' => '',
          'role' => array('admin','broker_company'),
          'sub' => array(),
        ),
        array(
          'menu_name' => '经济公司门店管理',
          'url' => 'BrokerCompany/BrokerCompanyStoreList',
          'ico' => '',
          'role' => array('admin','broker_company'),
          'sub' => array(),
        ),
      ),
      'permission' => array(
        'admin' => array(
          'BrokerCompany/BrokerCompanyList',
          'BrokerCompany/BrokerCompanyInfo',
          'BrokerCompany/AjaxBrokerCompanySave',
          'BrokerCompany/QuickModifyBrokerCompanyField',
          'BrokerCompany/ResetBrokerCompanyCache',
          'BrokerCompany/AjaxBrokerCompanyDelete',
          'BrokerCompany/BrokerCompanyAccountList',
          'BrokerCompany/BrokerCompanyAccountInfo',
          'BrokerCompany/AjaxBrokerCompanyAccountSave',
          'BrokerCompany/AjaxBrokerCompanyAccountDelete',
          'BrokerCompany/ResetBrokerCompanyAccountCache',
          'BrokerCompany/BrokerCompanyStoreList',
          'BrokerCompany/BrokerCompanyStoreInfo',
          'BrokerCompany/AjaxBrokerCompanyStoreSave',
          'BrokerCompany/ResetBrokerCompanyStoreCache',
          'BrokerCompany/AjaxBrokerCompanyStoreDelete',
        ),
        'broker_company' => array(
          'BrokerCompany/BrokerCompanyInfo',
          'BrokerCompany/AjaxBrokerCompanySave',
          'BrokerCompany/BrokerCompanyAccountList',
          'BrokerCompany/BrokerCompanyAccountInfo',
          'BrokerCompany/AjaxBrokerCompanyAccountSave',
          'BrokerCompany/AjaxBrokerCompanyAccountDelete',
          'BrokerCompany/ResetBrokerCompanyAccountCache',
          'BrokerCompany/BrokerCompanyStoreList',
          'BrokerCompany/BrokerCompanyStoreInfo',
          'BrokerCompany/AjaxBrokerCompanyStoreSave',
          'BrokerCompany/ResetBrokerCompanyStoreCache',
          'BrokerCompany/AjaxBrokerCompanyStoreDelete',
        ),
      ),
    ),
    array(
      'menu_name' => '销售经理',
      'url' => 'javascript:void(0);',
      'ico' => 'fa-user',
      'role' => array('admin','broker_company'),
      'sub' => array(
        array(
          'menu_name' => '销售经理管理',
          'url' => 'SellerManager/SellerManagerList',
          'ico' => '',
          'role' => array('admin','broker_company'),
          'sub' => array(),
        ),
      ),
      'permission' => array(
        'admin' => array(
          'SellerManager/SellerManagerList',
          'SellerManager/SellerManagerInfo',
          'SellerManager/AjaxSellerManagerSave',
          'SellerManager/AjaxSellerManagerDelete',
          'SellerManager/ResetSellerManagerCache',
        ),
        'broker_company' => array(
          'SellerManager/SellerManagerList',
          'SellerManager/SellerManagerInfo',
          'SellerManager/AjaxSellerManagerSave',
          'SellerManager/AjaxSellerManagerDelete',
          'SellerManager/ResetSellerManagerCache',
        ),
      ),
    ),
    array(
      'menu_name' => '客户',
      'url' => 'javascript:void(0);',
      'ico' => 'fa-user-md',
      'role' => array('admin','broker_company','seller_manager'),
      'sub' => array(
        array(
          'menu_name' => '客户管理',
          'url' => 'Customer/CustomerList',
          'ico' => '',
          'role' => array('admin','broker_company','seller_manager'),
          'sub' => array(),
        ),
      ),
      'permission' => array(
        'admin' => array(
          'Customer/CustomerList',
          'Customer/CustomerInfo',
          'Customer/AjaxCustomerSave',
          'Customer/ResetCustomerCache',
          'Customer/AjaxCustomerDelete',
        ),
        'broker_company' => array(
          'Customer/CustomerList',
          'Customer/CustomerInfo',
          'Customer/AjaxCustomerSave',
          'Customer/ResetCustomerCache',
          'Customer/AjaxCustomerDelete',
        ),
        'seller_manager' => array(
          'Customer/CustomerList',
          'Customer/CustomerInfo',
          'Customer/AjaxCustomerSave',
          'Customer/ResetCustomerCache',
        ),
      ),
    ),
    array(
      'menu_name' => '服务商',
      'url' => 'javascript:void(0);',
      'ico' => 'fa-globe',
      'role' => array('admin','broker_company'),
      'sub' => array(
        array(
          'menu_name' => '服务商管理',
          'url' => 'ServiceProviders/ServiceProvidersList',
          'ico' => '',
          'role' => array('admin','broker_company'),
          'sub' => array(),
        ),
      ),
      'permission' => array(
        'admin' => array(
          'ServiceProviders/ServiceProvidersList',
          'ServiceProviders/AjaxServiceProvidersSave',
          'ServiceProviders/QuickModifyServiceProvidersField',
          'ServiceProviders/ResetServiceProvidersCache',
          'ServiceProviders/AjaxServiceProvidersDelete',
        ),
        'broker_company' => array(
          'ServiceProviders/ServiceProvidersList',
          'ServiceProviders/AjaxServiceProvidersSave',
          'ServiceProviders/QuickModifyServiceProvidersField',
          'ServiceProviders/ResetServiceProvidersCache',
          'ServiceProviders/AjaxServiceProvidersDelete',
        ),
      ),
    ),
    array(
      'menu_name' => '产品',
      'url' => 'javascript:void(0);',
      'ico' => 'fa-heart',
      'role' => array('admin','broker_company'),
      'sub' => array(
        array(
          'menu_name' => '类别管理',
          'url' => 'Product/ProductTypeList',
          'ico' => '',
          'role' => array('admin','broker_company'),
          'sub' => array(),
        ),
        array(
          'menu_name' => '产品管理',
          'url' => 'Product/ProductList',
          'ico' => '',
          'role' => array('admin','broker_company'),
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
        ),
        'broker_company' => array(
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
        ),
      ),
    ),
    array(
      'menu_name' => '工单',
      'url' => 'javascript:void(0);',
      'ico' => 'fa-file',
      'role' => array('admin','broker_company','seller_manager'),
      'sub' => array(
        array(
          'menu_name' => '工单管理',
          'url' => 'Order/OrderList',
          'ico' => '',
          'role' => array('admin', 'broker_company', 'seller_manager'),
          'sub' => array(),
        ),
      ),
      'permission' => array(
        'admin' => array(
          'Order/OrderList',
          'Order/OrderInfo',
          'Order/OrderAddInfo',
          'Order/AjaxCustomerInfo',
          'Order/AjaxOptionList',
          'Order/AjaxProductOptionList',
          'Order/AjaxProductInfo',
          'Order/AjaxOrderAdd',
          'Order/OrderInfoEditReview',
          'Order/OrderInfoReviewSave',
          'Order/ResetOrderCache',
          'Order/AjaxOrderDelete',
          'Order/OrderInfoReview',
          'Download/OriginalNameFile',
          'Download/OriginalNameImage',
        ),
        'broker_company' => array(
          'Order/OrderList',
          'Order/OrderInfo',
          'Order/OrderAddInfo',
          'Order/AjaxCustomerInfo',
          'Order/AjaxOptionList',
          'Order/AjaxProductOptionList',
          'Order/AjaxProductInfo',
          'Order/AjaxOrderAdd',
          'Order/ResetOrderCache',
          'Order/AjaxOrderDelete',
          'Order/OrderInfoReview',
          'Order/OrderInfoReviewSave',
          'Download/OriginalNameFile',
          'Download/OriginalNameImage',
        ),
        'seller_manager' => array(
          'Order/OrderList',
          'Order/OrderInfo',
          'Order/OrderAddInfo',
          'Order/OrderInfoEdit',
          'Order/AjaxOrderEditSave',
          'Order/AjaxCustomerInfo',
          'Order/AjaxProductOptionList',
          'Order/AjaxProductInfo',
          'Order/AjaxOrderAdd',
          'Order/ResetOrderCache',
          'Download/OriginalNameFile',
          'Download/OriginalNameImage',
        ),
      ),
    ),
    array(
      'menu_name' => '佣金',
      'url' => 'javascript:void(0);',
      'ico' => 'fa-money',
      'role' => array('admin','broker_company'),
      'sub' => array(
        array(
          'menu_name' => '佣金管理',
          'url' => 'Commission/CommissionList',
          'ico' => '',
          'role' => array('admin', 'broker_company'),
          'sub' => array(),
        ),
      ),
      'permission' => array(
        'admin' => array(
          'Commission/CommissionList',
          'Commission/CommissionInfo',
          'Commission/AjaxSellerManagerOption',
          'Commission/AjaxCommissionSave',
          'Commission/AjaxCommissionInfo',
          'Commission/ResetCommissionCache',
          'Commission/AjaxStandardAdd',
        ),
        'broker_company' => array(
          'Commission/CommissionList',
          'Commission/CommissionInfo',
          'Commission/AjaxSellerManagerOption',
          'Commission/AjaxCommissionSave',
          'Commission/AjaxCommissionInfo',
          'Commission/ResetCommissionCache',
          'Commission/AjaxStandardAdd',
        ),
      )
    ),
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