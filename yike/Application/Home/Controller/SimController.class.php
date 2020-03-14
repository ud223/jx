<?php
namespace Home\Controller;
use Think\Controller;


class SimController extends Controller
{
    private $wsdl = "http://apicenter.huidesichuang.com/Service.asmx?wsdl";

    public function testapi(){
        $this->InsertLog();
        $client=new \SoapClient($this->wsdl);
        $param=array('sim'=>'13011548741');
        $ret = $client->SimRecharge($param);
        $array = object_array($ret);
        print_r( $array);
    }

    //记录日志
    public function InsertLog(){
        $log = M('sim_webservice_log');

        $data = array(
            'url' => 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'],
            'ip' => $_SERVER["REMOTE_ADDR"],
            'time' =>  date('Y-m-d H:i:s', time())
        );

        $log->add($data);
    }

    public function SimRecharge()
    {
        $token = I('token');
        $sim = I('sim');
        $password = I('password');

        if (!$token || !$sim || !$password) {
            echo fit_api(true, 1, '参数为空!', '');
            exit;
        }

        $client=new \SoapClient($this->wsdl);
        $param=array('token'=>$token,'sim'=>$sim,'password'=>$password);
        $ret = $client->SimRecharge($param);
        $array = object_array($ret);

        $this->InsertLog();
        echo fit_api(true, 0, 'api调用成功!', $array);
        exit;
    }

    public function SimRechargeLog()
    {
        $token = I('token');
        $sim = I('sim');

        if (!$token || !$sim) {
            echo fit_api(true, 1, '参数为空!', '');
            exit;
        }

        $client=new \SoapClient($this->wsdl);
        $param=array('token'=>$token,'sim'=>$sim);
        $ret = $client->SimRechargeLog($param);
        $array = object_array($ret);

        $this->InsertLog();
        echo fit_api(true, 0, 'api调用成功!', $array);
        exit;
    }

    public function GetRechargeCardState()
    {
        $token = I('token');
        $CardSN = I('cardsn');

        if (!$token || !$CardSN) {
            echo fit_api(true, 1, '参数为空!', '');
            exit;
        }

        $client=new \SoapClient($this->wsdl);
        $param=array('token'=>$token,'CardSN'=>$CardSN);
        $ret = $client->GetRechargeCardState($param);
        $array = object_array($ret);

        $this->InsertLog();
        echo fit_api(true, 0, 'api调用成功!', $array);
        exit;
    }

    public function GetSimOnline()
    {
        $ICCID = I('iccid');

        if (!$ICCID) {
            echo fit_api(true, 1, '参数为空!', '');
            exit;
        }

        $client=new \SoapClient($this->wsdl);
        $param=array('ICCID'=>$ICCID);
        $ret = $client->GetSimOnline($param);
        $array = object_array($ret);

        $this->InsertLog();
        echo fit_api(true, 0, 'api调用成功!', $array);
        exit;
    }

    public function GetSimDetailsRequest()
    {
        $token = I('token');
        $sim = I('sim');

        if (!$token || !$sim) {
            echo fit_api(true, 1, '参数为空!', '');
            exit;
        }

        $client=new \SoapClient($this->wsdl);
        $param=array('token'=>$token,'sim'=>$sim);
        $ret = $client->GetSimDetailsRequest($param);
        $array = object_array($ret);

        $this->InsertLog();
        echo fit_api(true, 0, 'api调用成功!', $array);
    }

    public function GetSimState()
    {
        $sim = I('sim');

        if (!$sim) {
            echo fit_api(true, 1, '参数为空!', '');
            exit;
        }

        $client=new \SoapClient($this->wsdl);
        $param=array('sim'=>$sim);
        $ret = $client->GetSimState($param);
        $array = object_array($ret);

        $this->InsertLog();
        echo fit_api(true, 0, 'api调用成功!', $array);
    }

    public function GetSimList()
    {
        $sim = I('sim');
        $Iccid  = I('iccid');
        $userName = I('username');
        $passWord = I('$password');

        if (!$sim || !$Iccid || !$userName || !$passWord) {
            echo fit_api(true, 1, '参数为空!', '');
            exit;
        }

        $client=new \SoapClient($this->wsdl);
        $param=array('SIM'=>$sim,'Iccid'=>$Iccid,'userName'=>$userName,'passWord'=>$passWord);
        $ret = $client->GetSimList($param);
        $array = object_array($ret);

        $this->InsertLog();
        echo fit_api(true, 0, 'api调用成功!', $array);
    }

    public function GetTerminalUsage()
    {
        $sim = I('sim');
        $Iccid  = I('iccid');
        $token = I('token');

        if (!$sim || !$Iccid || !$token) {
            echo fit_api(true, 1, '参数为空!', '');
            exit;
        }

        $client=new \SoapClient($this->wsdl);
        $param=array('SIM'=>$sim,'Iccid'=>$Iccid,'token'=>$token);
        $ret = $client->GetTerminalUsage($param);
        $array = object_array($ret);

        $this->InsertLog();
        echo fit_api(true, 0, 'api调用成功!', $array);
    }

    public function GetTerminalPackage()
    {
        $sim = I('sim');
        $Iccid  = I('iccid');
        $token = I('token');

        if (!$sim || !$Iccid || !$token) {
            echo fit_api(true, 1, '参数为空!', '');
            exit;
        }

        $client=new \SoapClient($this->wsdl);
        $param=array('SIM'=>$sim,'Iccid'=>$Iccid,'token'=>$token);
        $ret = $client->GetTerminalPackage($param);
        $array = object_array($ret);

        $this->InsertLog();
        echo fit_api(true, 0, 'api调用成功!', $array);
    }

    public function GetTerminalUsageDataDetailsRequest()
    {
        $sim = I('sim');
        $token = I('token');

        if (!$sim || !$token) {
            echo fit_api(true, 1, '参数为空!', '');
            exit;
        }

        $client=new \SoapClient($this->wsdl);
        $param=array('sim'=>$sim,'token'=>$token);
        $ret = $client->GetTerminalUsageDataDetailsRequest($param);
        $array = object_array($ret);

        $this->InsertLog();
        echo fit_api(true, 0, 'api调用成功!', $array);
    }

    public function DisableSim()
    {
        $sim = I('sim');
        $token = I('token');
        $userName = I('username');
        $passWord = I('password');

        if (!$sim || !$token || !$userName || !$passWord) {
            echo fit_api(true, 1, '参数为空!', '');
            exit;
        }

        $client=new \SoapClient($this->wsdl);
        $param=array('sim'=>$sim,'token'=>$token,'userName'=>$userName,'passWord'=>$passWord);
        $ret = $client->DisableSim($param);
        $array = object_array($ret);

        $this->InsertLog();
        echo fit_api(true, 0, 'api调用成功!', $array);
    }

    public function activateSimBySim()
    {
        $sim = I('sim');
        $token = I('token');
        $userName = I('username');
        $passWord = I('password');

        if (!$sim || !$token || !$userName || !$passWord) {
            echo fit_api(true, 1, '参数为空!', '');
            exit;
        }

        $client=new \SoapClient($this->wsdl);
        $param=array('sim'=>$sim,'token'=>$token,'userName'=>$userName,'passWord'=>$passWord);
        $ret = $client->activateSimBySim($param);
        $array = object_array($ret);

        $this->InsertLog();
        echo fit_api(true, 0, 'api调用成功!', $array);
    }

    public function ManufacturersRecharge()
    {
        $sim = I('sim');
        $token = I('token');
        $userName = I('username');
        $passWord = I('password');
        $flow = I('flow');

        if (!$sim || !$token || !$userName || !$passWord || !$flow) {
            echo fit_api(true, 1, '参数为空!', '');
            exit;
        }

        $client=new \SoapClient($this->wsdl);
        $param=array('sim'=>$sim,'token'=>$token,'userName'=>$userName,'passWord'=>$passWord,'flow'=>$flow);
        $ret = $client->ManufacturersRecharge($param);
        $array = object_array($ret);

        $this->InsertLog();
        echo fit_api(true, 0, 'api调用成功!', $array);
    }

    public function ACTIVATION_READY()
    {
        $sim = I('sim');
        $token = I('token');
        $userName = I('username');
        $passWord = I('password');

        if (!$sim || !$token || !$userName || !$passWord) {
            echo fit_api(true, 1, '参数为空!', '');
            exit;
        }

        $client=new \SoapClient($this->wsdl);
        $param=array('sim'=>$sim,'token'=>$token,'userName'=>$userName,'passWord'=>$passWord);
        $ret = $client->ACTIVATION_READY($param);
        $array = object_array($ret);

        $this->InsertLog();
        echo fit_api(true, 0, 'api调用成功!', $array);
    }
}