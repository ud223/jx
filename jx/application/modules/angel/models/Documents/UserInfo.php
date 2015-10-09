<?php
/**
 *  @author powerdream5
 *  用户document，包含创业者，投资人和担保人
 */

namespace Documents;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/** @ODM\Document */
class UserInfo extends AbstractDocument {
    /** @ODM\String */
    protected $openid;

    /** @ODM\Int */
    protected $subscribe;

    /** @ODM\String */
    protected $nickname;

    /** @ODM\Int */
    protected $sex;

    /** @ODM\String */
    protected $language;

    /** @ODM\String */
    protected $city;

    /** @ODM\String */
    protected $province;

    /** @ODM\String */
    protected $country;

    /** @ODM\String */
    protected $headimgurl;

    /** @ODM\String */
    protected $subscribe_time;

    /** @ODM\Int */
    protected $last_time;

    /** @ODM\String */
    protected $access_token;

    //用户类型  1： 普通用户  2： 瑜家老师
    /** @ODM\String */
    protected $usertype = 1;

    /** @ODM\String */
    protected $name;

    /** @ODM\String */
    protected $birthday;

    /** @ODM\String */
    protected $place;

    /** @ODM\String */
    protected $educational;

//    /** @ODM\ReferenceMany(targetDocument="\Documents\Photo") */
//    protected $certificate = array();

    /** @ODM\String */
    protected $certificate;

    /** @ODM\String */
    protected $phone;

    /** @ODM\String */
    protected $code;

    /** @ODM\String */
    protected $email;

    /** @ODM\String */
    protected $qq;

    //练习多少年
    /** @ODM\Int */
    protected $years = 0;

    /** @ODM\String */
    protected $wechat;
    //所在城市
    /** @ODM\String */
    protected $location;
    //授课区域
    /** @ODM\ReferenceMany(targetDocument="\Documents\Region") */
    protected $region;

    /** @ODM\ReferenceMany(targetDocument="\Documents\Category") */
    protected $category;

    /** @ODM\ReferenceMany(targetDocument="\Documents\Lesson") */
    protected $lesson;

    /** @ODM\String */
    protected $bank;

    /** @ODM\String */
    protected $bank_code;

    /** @ODM\String */
    protected $description;

    /** @ODM\String */
    protected $experience;

    /** @ODM\String */
    protected $price;

    /** @ODM\Int */
    protected $teacher_count = 0;

    /** @ODM\String */
    protected $amount = 0;

    /** @ODM\String */
    protected $use_amount = 0;

    /** @ODM\ReferenceMany(targetDocument="\Documents\Skill") */
    protected $skill = array();
    //账号是否冻结 0为没有冻结 1为冻结
    /** @ODM\Int */
    protected $frozen = 0;
    //是否删除 0为没有删除 1为已删除
    /** @ODM\Int */
    protected $delete = 0;

    /** @ODM\ReferenceMany(targetDocument="\Documents\Photo") */
    protected $photo = array();

    /** @ODM\String */
    protected $user_score = 5;

    /** @ODM\String */
    protected $teacher_score = 5;
    //是否正式注册
    /** @ODM\Int */
    protected $is_reg = 0;
}
