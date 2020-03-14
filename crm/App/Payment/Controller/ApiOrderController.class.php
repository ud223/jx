<?php
namespace Payment\Controller;
use Think\Controller;

class ApiOrderController extends Controller {
    /*
     * 创建订单
     */
    public function CreateOrder(){
        $arrParam = $_GET;
        
        //测试数据 Begin
        $arrParam["userid"] = "1";
        $arrParam["type"] = "1";
        $arrParam["productid"] = "1";
        $arrParam["paytype"] = "Alipay";
        $arrParam["price"] = "99.8";
        $arrParam["day"] = "30";
        $arrParam["address"] = "1";
        //测试数据 End
        
        //检查订单数据
        $this->CheckOrderData($arrParam);
        
        $arrData = array(
            "ordernum"=>$this->OrderNum($arrParam["type"], $arrParam["userid"]),
            "uid"=>$arrParam["userid"],
            "type"=>$arrParam["type"],
            "table"=>$this->ProductTable($arrParam["type"]),
            "productid"=>$arrParam["productid"],
            "paytype"=>strtolower($arrParam["paytype"]),
            "price"=>$arrParam["price"],
            "serviceday"=>$arrParam["day"],
            "addressid"=>$arrParam["address"],
            "status"=>0,
            "addtime"=>time(),
        );
        
        $model = M("order");
        $addReturn = $model->add($arrData);
        
        if($addReturn){
            ApiSuccessReturn(array(), "订单创建成功", array("orderid"=>$arrData["ordernum"]));
        }else{
            ApiErrorRetrun("订单创建失败");
        }
    }
    
    /*
     * 检查订单参数
     */
    private function CheckOrderData($_param=array()){
        if(empty($_param)){
            ApiErrorRetrun("缺少订单参数");
        }
        
        $nUid = $_param["userid"];
        $nOrderType = $_param["type"];
        $nProductID = $_param["productid"];
        $sPayType = strtolower($_param["paytype"]);
        $sPrice = $_param["price"];
        $nServiceDay = $_param["day"];
        
        if(IsNum($nUid, false)){
            ApiErrorRetrun("缺少用户ID参数或用户ID参数非法");
        }
        
        if(IsNum($nOrderType, true)){
            ApiErrorRetrun("缺少订单类型参数或订单类型参数非法");
        }
        
        if(IsNum($nProductID, false)){
            ApiErrorRetrun("缺少商品ID参数或商品ID参数非法");
        }
        
        if(empty($sPayType)){
            ApiErrorRetrun("缺少支付类型参数");
        }else{
            $sSurePayType = $this->CheckPayType($sPayType);
            
            if(empty($sSurePayType)){
                ApiErrorRetrun("支付类型参数非法");
            }
        }
        
        if(IsNum($sPrice, false)){
            ApiErrorRetrun("缺少支付金额参数或支付金额参数非法");
        }
        
        if(IsNum($nServiceDay, false)){
            ApiErrorRetrun("缺少服务天数参数或服务天数参数非法");
        }
    }

    /*
     * 创建订单号
     * 组成方式：前缀+时间戳+5位随机数+用户ID
     * 返回值：string
     */
    private function OrderNum($_ordertype,$_uid){
        $OrderPrefix = "";
        $sReturn = "";
        
        switch ($_ordertype){
            case 0 :
                $OrderPrefix = "S";
                break;
            
            case 1 :
                $OrderPrefix = "P";
                break;
        }
        
        if(!empty($OrderPrefix)){
            $RandNum = CreatRandArray(10000, 99999, 1);
            $sReturn = $OrderPrefix . time() . $RandNum[0] . $_uid;
        }

        //检查订单号是否存在 Begin
        $model = M("order");
        $OrderInfo = $model->where("ordernum='{$sReturn}'")->find();
        
        if(!empty($OrderInfo)){
            $this->OrderNum($_ordertype,$_uid);
        }
        //检查订单号是否存在 End
        
        return $sReturn;
    }
    
    /*
     * 获取订单商品所在的数据库表名
     */
    private function ProductTable($_ordertype){
        $sReturn = "";
        
        switch ($_ordertype){
            case 0 :
                $sReturn = "service";
                break;
            
            case 1 :
                $sReturn = "product";
                break;
        }
        
        return $sReturn;
    }
    
    /*
     * 检查支付类型，在config文件中是否存在
     */
    private function CheckPayType($_paytype){
        $PaymentList = C("payment");
        $sReturn = "";

        foreach($PaymentList as $key=>$val){
            if($_paytype == $key){
                $sReturn = $key;

                break;
            }
        }
        
        return $sReturn;
    }
}