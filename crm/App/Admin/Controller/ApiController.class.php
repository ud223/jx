<?php
namespace Admin\Controller;
use Think\Controller;

class ApiController extends Controller {
    /*
     *商家列表
     * */
    public function test(){
        $test = '{"status":1,"info":"XXXX","adsItems":[{"pic":"","id":"","descripe":"","url":""},{"pic":"","id":"","descripe":"","url":""},{"pic":"","id":"","descripe":"","url":""}],"newsItems":[{"pic":"","title":"","descriple":"","id":"","date":"","answerNum":""}]}';
        $arr = json_decode($test,true);
        
        $arr = array(
            "adsItems"=>array(
                array(
                    "pic"=>"http://b.hiphotos.baidu.com/album/h%3D517%3Bcrop%3D173%2C0%2C480%2C517%3Bq%3D90/sign=07d3fe400d33874483c52f7d6634ba8b/9a504fc2d5628535142a723191ef76c6a6ef63dc.jpg",
                    "id"=>1,
                    "descripe"=>"test descripe",
                    "url"=>"www.163.com",
                ),
                
                array(
                    "pic"=>"http://a.hiphotos.baidu.com/album/h%3D252%3Bcrop%3D26%2C0%2C350%2C252%3Bq%3D90/sign=e41c42ea7acb0a469a228c3c59589556/00e93901213fb80e9303c1c737d12f2eb83894d4.jpg",
                    "id"=>2,
                    "descripe"=>"test descripe 2",
                    "url"=>"www.sina.com",
                ),
            ),
            "newsItems"=>array(
                array(
                    "pic"=>"http://c.hiphotos.baidu.com/album/h%3D252%3Bcrop%3D26%2C0%2C350%2C252%3Bq%3D90/sign=44025f5cd309b3def4bfe36dfe840ff3/0824ab18972bd40727b62bc979899e510fb30994.jpg",
                    "title"=>"test title",
                    "descriple"=>"test descriple 3",
                    "date"=>  time(),
                    "answerNum"=>10,
                ),
            ),
        );

        ApiSuccessReturn(array(), "XXXX", $arr);
    }
}