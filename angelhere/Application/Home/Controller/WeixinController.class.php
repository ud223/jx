<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/3
 * Time: 19:32
 */
namespace Home\Controller;
use Think\Controller;

class WeixinController {
    public function valid() {
        $echoStr = $_GET["echostr"];

        //valid signature , option
//        if($this->checkSignature()) {
//            echo $echoStr;
//            exit;
//        }
        $this->responseMsg();
    }

    private function checkSignature() {
        // you must define TOKEN by yourself
        if (!defined("TOKEN")) {
            throw new Exception('TOKEN is not defined!');
        }

        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        // use SORT_STRING rule
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }

    public function responseMsg()
    {
        //get post data, May be due to the different environments
        //$postStr = //$GLOBALS["HTTP_RAW_POST_DATA"];
        $postStr = file_get_contents('php://input');

        //extract post data
        if (!empty($postStr)){
            /* libxml_disable_entity_loader is to prevent XML eXternal Entity Injection,
               the best way is to check the validity of xml by yourself */
            libxml_disable_entity_loader(true);
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $fromUsername = $postObj->FromUserName;
            $toUsername = $postObj->ToUserName;
            $keyword = trim($postObj->Content);
            $ev = $postObj->Event;
            $time = time();
            $textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							<FuncFlag>0</FuncFlag>
							</xml>";
            if(!empty( $keyword ))
            {
//                $msgType = "text";
//                $contentStr = "欢迎来到PROJECT01！";
//                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
//                echo $resultStr;

                $msgType = "text";
                $contentStr = "Hi，终于等到你，欢迎来到PROJECT01！这里能为你提供专业的健身指导, 让你身体焕发激情!";

                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);

                echo $resultStr;
            }else{
                if ($ev == "subscribe"){
                    $itemTpl = "    <item>
                                    <Title><![CDATA[%s]]></Title>
                                    <Description><![CDATA[%s]]></Description>
                                    <PicUrl><![CDATA[%s]]></PicUrl>
                                    <Url><![CDATA[%s]]></Url>
                                </item>";

                    $item_str = sprintf($itemTpl, '深圳首届白领拳击赛', '12.17 深圳首届白领拳击赛 邀您参加', 'http://fit.webetter100.com/Public/upload/20161111002544_big.jpg', 'http://fit.webetter100.com/show/gt_ptdetail?id=1');

                    $textTpl_1 = "<xml>
                            <ToUserName><![CDATA[%s]]></ToUserName>
                            <FromUserName><![CDATA[%s]]></FromUserName>
                            <CreateTime>%s</CreateTime>
                            <MsgType><![CDATA[news]]></MsgType>
                            <ArticleCount>%s</ArticleCount>
                            <Articles>
                            $item_str</Articles>
                        </xml>";

//                    $msgType = "text";
//                    $contentStr = "Hi，终于等到你，欢迎来到PROJECT01！这里能为你提供专业的健身指导, 让你身体焕发激情!";
                    $resultStr = sprintf($textTpl_1, $fromUsername, $toUsername, $time, '1');
                    echo $resultStr;
                }

                //echo "Input something...";
            }

        }else {
            echo "test";
            exit;
        }
    }
} 