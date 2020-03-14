<?php
namespace Btten;

/**
 * 数据分页类
 */
class Paging {
    protected $config = array(
        'pagemodel' => "",  //页面模型
        'tablename' => "",  //表名
        'jointable' => "",   //join表
        'field' => "*",  //读取的字段
        'where' => "",  //查询条件
        'order' => "",  //排序字段
        'pageindex' => 1,  //当前页数
        'recordnum' =>  PAGE_LIST_ROWS,    //每页记录数
        'pagingindex' => PAGE_NUM_SHOWN, //分页页标数
        'url'=>'',  //分页链接
        'param'=>array(),   //附加参数
    );
    
    /**
     * 架构方法 设置参数
     * @access public     
     * @param  array $config 配置参数
     */    
    public function __construct($config=array()){
        if(!is_numeric($config["pageindex"])){
            $config["pageindex"] = 1;
        }
        
        if(!is_numeric($config["recordnum"])){
            $config["recordnum"] = PAGE_LIST_ROWS;
        }
        
        if(!is_numeric($config["pagingindex"])){
            $config["pagingindex"] = PAGE_NUM_SHOWN;
        }
        
        $this->config = array_merge($this->config, $config);
    }

    /**
     * 获取分页数据
     * @access private     
     * @param  array $config 配置参数
     */  
    public function GetPagingData(){
        $sTableName = C("DB_PREFIX") . $this->config['tablename'];
        $sSql = "select {$this->config['field']} from {$sTableName}";

        if(!empty($this->config['jointable'])){
            $sSql .= " " . $this->config['jointable'];
        }

        if(!empty($this->config['where'])){
            //$this->config['where'] = "1=1 " .  $this->config['where'];
            $sSql .= " where ". $this->config['where'];
            
        }

        if(!empty($this->config['order'])){
            $sSql .= " order by " .  $this->config['order'];
        }
        
        $nRecordNum = $this->GetDataTotal();
        $sSql .= " limit " . $this->DataLimit($nRecordNum);
//        exit($sSql);
        $model = new \Think\Model();
        $PagingData = $model->query($sSql);

        $arrResult["paging"]["totalnum"] = $nRecordNum;   //记录总数
        
        $nPageNum = ceil($nRecordNum / $this->config['recordnum']); //总页数
        
        if($this->config['pageindex'] > $nPageNum){
            $this->config['pageindex'] = $nPageNum;
        }
        
        $arrResult["paging"]["pageindex"] = $this->config['pageindex'];   //当前页数
        $arrResult["paging"]["recordnum"] = $this->config['recordnum'];   //每页记录数
        $arrResult["paging"]["pagingindex"] = $this->config['pagingindex'];   //分页页标数
        $arrResult["paging"]["html"] = $this->PagingHtml($this->config['pageindex'], $this->config['recordnum'], $nRecordNum, $this->config['pagingindex'], $this->config['url'], $this->config['param']);   //分页html代码
        $arrResult["data"] = $PagingData;   //查询到的数据库数据
        
        //增加一个固定数组元素，作为列表数据的操作id
        if(!empty($this->config["pagemodel"])){
            //读取字段配置
            $model = M("model_field");
            $FieldList = $model->field("`fieldname`, `listconfig`")->where("`mdlid` = (select `mdlid` from btten_model where `mdlname` = '{$this->config["pagemodel"]}') and `islist` = 'true' and `status` = 1")->order("`order`, `fieldid`")->select();

            $nCount = sizeof($FieldList);
            
            //查找是哪个字段，属于操作ID
            for($i=0; $i<$nCount; $i++){
                $arrConfig = json_decode($FieldList[$i]["listconfig"], true);
                
                if($arrConfig["isparam"] == "true"){
                    $sParamField = $FieldList[$i]["fieldname"];
                    break;
                }
            }
            
            //增加操作ID到数据集
            $nCount = sizeof($arrResult["data"]);
            
            for($i=0; $i<$nCount; $i++){
                $arrResult["data"][$i]["selectid"] = $arrResult["data"][$i][$sParamField];
            }
        }
        
        return $arrResult;
    }

    /**
     * 获取数据记录总数
     * @access private     
     * @param  array $config 配置参数
     */  
    private function GetDataTotal(){
        $model = M($this->config["tablename"]);
        $nTotal = $model->where($this->config["where"])->count();

        return $nTotal;
    }
    
    /**
     * @access protected 读取数据上下限
     * @param type $count 总记录数
     * @param type $pageindex 请求的分页数,即第几页
     * @param type $PageNum 每页显示的记录条数
     */
    protected function DataLimit($count) {
        $pageindex = $this->config['pageindex'];
        $PageNum = $this->config['recordnum'];
        $totalPages = ceil($count / $PageNum);     //总页数
        
        if($pageindex > $totalPages){
            $pageindex = $totalPages;
        }

        $pageindex = $pageindex ? $pageindex : 1;
        $from = ($pageindex - 1) * $PageNum;
        $limit = $from . ',' . $PageNum;
        
        return $limit;
    }
    
    /**
     * 输出分页HTML代码
     * @ $_currPage 当前分页数
     * @ $_pageRows 每页记录数
     * @ $_totalNum 记录总数
     * @ $_pagingBtnNum 分页索引按钮数
     * @ $_url 分页链接
     * @ $_param 额外参数
     */
    private function PagingHtml($_currPage, $_pageRows, $_totalNum, $_pagingBtnNum, $_url, $_param=array()){
        if(empty($_totalNum)){
            return "";
        }
        
        //组合分页链接的额外参数
        $sParam = "";
        
        if(!IsN($_param)){
            $sParam = "act/search/";
            foreach($_param as $key=>$val) {
                if(!IsN($val)){
                    $sParam .=   "{$key}/". $val .'/';
                }
            }
        }
        
        $arrHtml = array();
        $CurrFirst = ($_currPage - 1) * $_pageRows + 1; //当前页的第一条记录，是总数中的第几条
        $CurrLast = $_currPage * $_pageRows;    //当前页的最后一条记录，是总数中的第几条
        
        if($CurrLast > $_totalNum){
            $CurrLast = $_totalNum;
        }
        
        $PageIndexBtnNum = ceil($_totalNum / $_pageRows);   //共有多少页数据
        $BeginIndexBtnNum = $this->GetFirstNum($_currPage, $_pagingBtnNum);  //设置起始分页按钮数
        $EndIndexBtnNum = $BeginIndexBtnNum - 1 + 10;   //设置结束分页按钮数

        //如果分页索引按钮值小于1，则赋值为第1页
        if($BeginIndexBtnNum < 1){
            $BeginIndexBtnNum = 1;
        }
        
        array_push($arrHtml, '<div class="row">');
        array_push($arrHtml, '<div class="col-md-12">');
        array_push($arrHtml, '<ul class="pagination">');
        
        //设置上一页按钮的样式与链接
        if($_currPage == 1){
            $sClass = "disabled";
            $sPaginUrl = "javascript:void(0);";
        }else{
            $sClass = "";
            $sPaginUrl = $_url .'/pageNum/'. ($_currPage - 1) . '/' . $sParam;
        }
        
        array_push($arrHtml, '<li class="'. $sClass .'"><a href="'. $sPaginUrl .'"><i class="icon-step-backward"></i></a></li>');
        
        //创建分页按钮索引，开始按钮数，循环到结束按钮数，
        for($i=$BeginIndexBtnNum; $i<=$EndIndexBtnNum; $i++){
            //如果循环数大于分页索引按钮的总数，则退出循环
            if($i > $PageIndexBtnNum){
                break;
            }
            
            if($i == $_currPage){
                $sClass = "disabled";
                $sPaginUrl = "javascript:void(0);";
            }else{
                $sClass = "";
                $sPaginUrl = $_url .'/pageNum/'. $i . '/' . $sParam;
            }
            
            array_push($arrHtml, '<li class="'. $sClass .'"><a href="'. $sPaginUrl .'">'. $i .'</a></li>');
        }
        
        if($_currPage == $PageIndexBtnNum){
            $sClass = "disabled";
            $sPaginUrl = "javascript:void(0);";
        }else{
            $sClass = "";
            $sPaginUrl = $_url .'/pageNum/'. ($_currPage + 1) . '/' . $sParam;
        }

        array_push($arrHtml, '<li class="'. $sClass .'"><a href="'. $sPaginUrl .'"><i class="icon-step-forward"></i></a></li>');
        array_push($arrHtml, '</ul>');
        array_push($arrHtml, '</div>');
        array_push($arrHtml, '<div class="col-md-12">');
        array_push($arrHtml, '<label class="label label-info" style="margin-right:5px;">共 '. $_totalNum .' 条记录</label>');
        array_push($arrHtml, '<label class="label label-info" style="margin-right:5px;">当前 '. $CurrFirst .' - '. $CurrLast .' 条记录</label>');
        array_push($arrHtml, '<label class="label label-info" style="margin-right:5px;">'. $_currPage .' / '. $PageIndexBtnNum .' 页</label>');
        array_push($arrHtml, '</div>');
        array_push($arrHtml, '</div>');
        
        return join("",$arrHtml);
    }
    
    //获取第一个分页索引数
    private function GetFirstNum($_pageNum, $_paginTotalNum){        
        if($_pageNum <= $_paginTotalNum){
            $nStartNum = 1;
        }else{
            $nGroup = ((int)(($_pageNum-1) / $_paginTotalNum)) * $_paginTotalNum;
            $nStartNum = $nGroup + 1;
        }

        return $nStartNum;
    }
}