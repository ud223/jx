<?php
namespace Home\Controller;

use Think\Controller;

class PointController extends Controller
{
    private $website = "http://yike.ecomoter.com";

    //取出二唯数组中最小值
    private function GetMinArray($arr, $field)
    {
        $disArr = array();
        foreach ($arr as $value) {
            $disArr[] = $value[$field];
        }
        return min($disArr);
    }

    //对二唯数组排序
    private function GetSortArray($arr, $sort, $field)
    {
        $sort = array(
            'direction' => $sort, //排序顺序标志 SORT_DESC 降序；SORT_ASC 升序
            'field' => $field       //排序字段
        );
        $arrSort = array();
        foreach ($arr AS $uniqid => $row) {
            foreach ($row AS $key => $value) {
                $arrSort[$key][$uniqid] = $value;
            }
        }

        array_multisort($arrSort[$sort['field']], constant($sort['direction']), $arr);
        return $arr;
    }

    //得到最近的服务网点
    public function GetNearestPoint()
    {
        $access_token = I('access_token');
        $longitude = I('longitude');
        $latitude = I('latitude');

        if (!$access_token || !$longitude || !$latitude) {
            echo fit_api(true, 1, '参数为空!', '');
            exit;
        }

        $login = M('Login');
        $condition['key'] = $access_token;


        $data = $login->where($condition)->select();

        if ($data) {
            $userid = $data[0]["user_id"];

            //定义二维数组存储服务商网点id和距离
            $arr = array();

            $point = M('Point');
            $data = $point->select();

            if (count($data) == 0) {
                echo fit_api(true, 3, '无服务网点!', '');
                exit;
            }

            for ($i = 0; $i < count($data); $i++) {
                $data[$i]['distance'] = GetDistance($latitude, $longitude, $data[$i]["lat"], $data[$i]["long"]);
            }

            $data = $this->GetSortArray($data, 'SORT_ASC', 'distance');

            $user_point = M('user_point');
            $condition = array();
            $condition["user_id"] = $userid;
            $condition["point_id"] = $data[0]["id"];
            $num = $user_point->where($condition)->select();

            if ($num)
                $coll = 1;
            else
                $coll = 2;

            $result = array(
                'storeId' => $data[0]["id"],
                'storeName' => $data[0]["point_text"],
                'longitude' => $data[0]["long"],
                'latitude' => $data[0]["lat"],
                'phone' => $data[0]["phone"],
                'address' => $data[0]["address"],
                'isCollection ' => $coll
            );
            echo fit_api(true, 0, '成功!', $result);
        } else {
            echo fit_api(true, 99, 'access_token不正确!', '');
        }
    }

    //得到最近的服务网点列表
    public function GetNearestPointList()
    {
        $access_token = I('access_token');
        $longitude = I('longitude');
        $latitude = I('latitude');
        $currentPage = I("currentPage");
        $pageSize = I("pageSize", 20);

        if (!$access_token || !$longitude || !$latitude || !$currentPage) {
            echo fit_api(true, 1, '参数为空!', '');
            exit;
        }

        $login = M('Login');
        $condition['key'] = $access_token;
        $data = $login->where($condition)->select();

        if ($data) {
            $userid = $data[0]["user_id"];

            $point = M('Point');
            $data = $point->select();

            if (count($data) == 0) {
                echo fit_api(true, 3, '无服务网点!', '');
                exit;
            }

            for ($i = 0; $i < count($data); $i++) {
                $data[$i]['distance'] = GetDistance($latitude, $longitude, $data[$i]["lat"], $data[$i]["long"]);
            }

            $data = $this->GetSortArray($data, 'SORT_ASC', 'distance');

            //分页


            if ($currentPage < 1) {
                $currentPage = 1;
            }
            $result = array();
            for ($i = ($currentPage - 1) * $pageSize; $i < $currentPage * $pageSize; $i++) {
                if ($i >= count($data)) {
                    break;
                } else {
                    $user_point = M('user_point');
                    $condition = array(
                        "user_id" => $userid,
                        "point_id" => $data[$i]["id"]
                    );

                    $num = $user_point->where($condition)->select();

                    if ($num)
                        $coll = 1;
                    else
                        $coll = 2;

                    $result[] = array(
                        'storeId' => $data[$i]["id"],
                        'storeName' => $data[$i]["point_text"],
                        'longitude' => $data[$i]["long"],
                        'latitude' => $data[$i]["lat"],
                        'phone' => $data[$i]["phone"],
                        'address' => $data[$i]["address"],
                        'isCollection ' => $coll
                    );
                }
            }

            echo fit_api(true, 0, '成功!', array("storeList" => $result));
        } else {
            echo fit_api(true, 99, 'access_token不正确!', '');
        }
    }

    //搜索服务网点
    public function SearchPoint()
    {
        $access_token = I('access_token');
        $longitude = I('longitude');
        $latitude = I('latitude');
        $keyword = I('keyword');
        $currentPage = I('currentPage');
        $pageSize = I('pageSize', 20);

        if (!$access_token || !$longitude || !$latitude || !$keyword || !$currentPage) {
            echo fit_api(true, 1, '参数为空!', '');
            exit;
        }

        $login = M('Login');
        $condition['key'] = $access_token;
        $data = $login->where($condition)->select();

        if ($data) {
            $userid = $data[0]["user_id"];

            //定义二维数组存储服务商网点id和距离
            $arr = array();

            $point = M('Point');
            $data["point_text"] = array('like', '%' . $keyword . '%');

            if ($currentPage < 1) {
                $currentPage = 1;
            }

            //$data = $point->where()->limit(($currentPage - 1) * $pageSize + 1 . ',' . $pageSize)->select();
            $data = $point->where("address like '%" . $keyword . "%' or point_text like '%" . $keyword . "%'")->page($currentPage)->limit(20)->select();
//            echo fit_api(true, 0, '成功!', $point->getLastSql()); exit;
            if (count($data) == 0) {
                echo fit_api(true, 3, '无最近服务网点!', '');
                exit;
            }

            for ($i = 0; $i < count($data); $i++) {
                $data[$i]['distance'] = GetDistance($latitude, $longitude, $data[$i]["lat"], $data[$i]["long"]);
            }

            $data = $this->GetSortArray($data, 'SORT_ASC', 'distance');

            $storeList = array();
            for ($i = 0; $i < count($data); $i++) {
//                if($i>=($currentPage - 1) * $pageSize && $i<$pageSize) {
                $user_point = M('user_point');
                $condition = array();
                $condition["user_id"] = $userid;
                $condition["point_id"] = $data[$i]["id"];
                $num = $user_point->where($condition)->select();

                if ($num)
                    $coll = 1;
                else
                    $coll = 2;

                $storeList[] = array(
                    'storeId' => $data[$i]["id"],
                    'storeName' => $data[$i]["point_text"],
                    'longitude' => $data[$i]["long"],
                    'latitude' => $data[$i]["lat"],
                    'phone' => $data[$i]["phone"],
                    'address' => $data[$i]["address"],
                    'isCollection ' => $coll
                );
//                }
            }
            echo fit_api(true, 0, '成功!', array("storeList" => $storeList));
        } else {
            echo fit_api(true, 99, 'access_token不正确!', '');
        }
    }

    //收藏或取消服务网点
    public function SetUserPoint()
    {
        $access_token = I('access_token');
        $storeId = I('storeId');
        $type = I('type');

        if (!$access_token || !$type || !$storeId) {
            echo fit_api(true, 1, '参数为空!', '');
            exit;
        }

        $login = M('Login');
        $condition['key'] = $access_token;
        $data = $login->where($condition)->select();

        if ($data) {
            $userid = $data[0]["user_id"];

            $user_point = M('user_point');

            $condition = array(
                "user_id" => $userid,
                "point_id" => $storeId
            );

            if ($type == "1")    //收藏
            {
                $num = $user_point->where($condition)->select();

                if ($num) {
                    echo fit_api(true, 3, '已收藏!', '');
                } else {
                    $user_point->user_id = $userid;
                    $user_point->point_id = $storeId;

                    $user_point->add();
                    echo fit_api(true, 0, '收藏成功!', '');
                }
            } else {               //取消
                $user_point->where($condition)->delete();
                echo fit_api(true, 0, '取消成功!', '');
            }
        } else {
            echo fit_api(true, 99, 'access_token不正确!', '');
        }
    }

    //在线报修
    public function SetRepair()
    {
        $access_token = I('access_token');
        $storeId = I('storeId');
        $car_id = I('car_id');
        $customerName = I('customerName');
        $info = I('info');
        $pic_array = I('picFileList');

        if (!$access_token || !$car_id || !$customerName || !$info) {
            echo fit_api(true, 1, '参数为空!', '');
            exit;
        }

        $login = M('Login');
        $condition['key'] = $access_token;
        $data = $login->where($condition)->select();

        if ($data) {
            $pic_file = "";
            $path = dirname(dirname(__FILE__));
            $path = substr($path, 0, strrpos($path, '/', 0));
            $path = substr($path, 0, strrpos($path, '/', 0)) . '/Public/uploads/';

            $arr = explode(',', $pic_array);

            foreach ($arr as $pic) {
                $file = $this->save_img($pic);
                if ($file) {
                    if ($pic_file != "") {
                        $pic_file = $pic_file . "," . $this->website . '/Public/uploads/' . $file;
                    } else {
                        $pic_file = $this->website . '/Public/uploads/' . $file;
                    }
                }
            }

            $repair = M("repair");

            $repair->car_id = $car_id;
            $repair->point_id = $storeId;
            $repair->name = $customerName;
            $repair->info = $info;
            $repair->pic_file = $pic_file;

            $result = $repair->add();

            if (false == $result) {
                echo fit_api(true, 2, '失败!', '');
            } else {
                echo fit_api(true, 0, '成功!', '');
            }
            //$newFile = fopen($imgDir.$filename,"w"); //打开文件准备写入
            //fwrite($newFile,$data); //写入二进制流到文件
            //fclose($newFile); //关闭文件
        } else {
            echo fit_api(true, 99, 'access_token不正确!', '');
        }
    }

    public function save_img($image)
    {
        //如果上传的文件没有图片base64加密前缀 就是原始图片路径 直接返回
        if (strpos($image, "data:image/jpeg;") < 0) {
            return $image;
        }

        $s = base64_decode(str_replace('data:image/jpeg;base64,', '', $image));

        $pic_name = date('YmdHis') . rand(1000000, 9999999) . '.jpg';

        $full_pic_name = SITE_DIR . UPLOAD_DIR . $pic_name;
//        echo fit_api(true, 0, '保存服务网点失败!'.$full_pic_name, '');exit;
        $file_count = file_put_contents($full_pic_name, $s);

        if (!$file_count) {
            return false;
        } else {
            return $pic_name;
        }
    }
}