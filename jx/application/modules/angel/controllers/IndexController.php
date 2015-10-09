<?php

class Angel_IndexController extends Angel_Controller_Action {
    private $app_id = 'wx4f812fb76adfa3ff';
    private $app_secret = '4450180e6fd9b8a88b79a2261a1da6ae';
    private $access_token = '';

    protected $login_not_required = array(
        'index',
        'clear'
    );

    public function init() {
        $this->_helper->layout->setLayout('main');
        parent::init();
    }

    public function aboutAction() {

    }

    public function clearAction() {

    }

    public function indexAction() {
//        exit("xixi");
        $regionModel = $this->getModel('region');

        $region = $regionModel->getAll(false);

        $this->view->region = $region;
    }

    /***************************************************************
     * 用户处理
     *
     * *************************************************************/
    public function upgradeAction() {
        $this->_helper->layout->setLayout('upgrade');
    }
    
    public function subscribeAction() {
        if ($this->request->isXmlHttpRequest() && $this->request->isPost()) {
            try {
                $email = $this->request->getParam("email");
                $subscribeModel = $this->getModel('subscribe');
                $subscribeModel->addSubscribe($email);
                echo 1;
                exit;
            } catch (Angel_Exception_User $e) {
                echo 0;
                exit;
            }
        } else {
            
        }
    }

    /**
     * 登录
     */
    public function loginAction() {
        if ($this->request->isPost()) {
            $this->userLogin('show-play', "登录瑜伽去");
        }
        else {
            //第一次请求先判断是否移动端浏览器,如果是移动端浏览器就跳转到移动端注册页面
            if ($this->isMobile()) {
                $loginPath = $this->view->url(array(), 'phone-login') ;

                $this->_redirect($loginPath);
            }
        }
    }

    /**
     * 注册
     */
    public function registerAction() {
        if ($this->request->isPost()) {
            $this->userRegister('login', "注册瑜伽去", "user");
        }
        else {
            //第一次请求先判断是否移动端浏览器,如果是移动端浏览器就跳转到移动端注册页面
            if ($this->isMobile()) {
                $registerPath = $this->view->url(array(), 'phone-register') ;

                $this->_redirect($registerPath);
            }
        }
    }

    public function isEmailCanBeUsedAction() {
        if ($this->request->isXmlHttpRequest() && $this->request->isPost()) {

            $email = $this->request->getParam('email');
            $result = false;
            try {
                $userModel = $this->getModel('user');
                $result = $userModel->isEmailExist($email);
            } catch (Angel_Exception_User $e) {
                $this->_helper->json(0);
            }
            // email已经存在返回false，不存在返回true
            $this->_helper->json($result ? false : true);
        }
    }

    public function forgotPasswordAction() {
        if ($this->request->isPost()) {
            $email = $this->request->getParam('email');
            $result = false;
            try {
                $userModel = $this->getModel('user');
                $result = $userModel->forgotPassword($email);
            } catch (Angel_Exception_User $e) {
                $this->view->error = $e->getDetail();
                $this->view->re_email = $email;
                $result = false;
            }
            if ($result) {
                $this->view->send = "success";
            }
        }
        $this->view->title = "找回密码";
    }

    public function logoutAction() {
        $this->userLogout('login');
    }

    /**************************************************************
     * action
     *
     * ***********************************************************/


    /***********************************************
     * action
     *
     * *********************************************/


    /************************************************
     * action
     *
     * *********************************************/


    /************************************************
     * action
     *
     * **********************************************/


    /*******************************************************
     * 其他action
     *
     * *****************************************************/

    /*******************************************************
     * tools方法
     *
     * *****************************************************/
}
