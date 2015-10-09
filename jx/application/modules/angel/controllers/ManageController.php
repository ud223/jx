<?php

class Angel_ManageController extends Angel_Controller_Action {
    protected $login_not_required = array(
        'login',
        'register',
        'logout'
    );
    protected $SEPARATOR = ';';

    public function init() {
        parent::init();

        $this->_helper->layout->setLayout('manage');
    }

    public function indexAction() {
        
    }

    /*******************************************
     * 用户处理部分
     *
     * ****************************************/
    public function registerAction() {
        $this->userRegister('manage-login', "注册成为管理员", "admin");
        
        $this->view->ismanage = true;
    }

    public function logoutAction() {
        $this->userLogout('manage-login');
    }

    public function loginAction() {
        $this->userLogin('manage-index', "管理员登录");
    }

     /**********************************************************
     * 图片处理action部分
     *
     * ********************************************************/
    protected function decodePhoto($paramName = 'photo') {
        $paramPhoto = $this->request->getParam($paramName);
        if ($paramPhoto) {
            $paramPhoto = json_decode($paramPhoto);
            $photoModel = $this->getModel('photo');
            $photoArray = array();
            foreach ($paramPhoto as $name => $path) {
                $photoObj = $photoModel->getPhotoByName($name);
                if ($photoObj) {
                    $photoArray[] = $photoObj;
                }
            }
            return $photoArray;
        } else {
            return null;
        }
    }

    public function photoCreateAction() {
        $phototypeModel = $this->getModel('phototype');

        if ($this->request->isPost()) {
            $result = 0;
            // POST METHOD
            $tmp = $this->getParam('tmp');
            $title = $this->getParam('title');
            $description = $this->getParam('description');
            $phototypeId = $this->getParam('phototype');
            $thumbnail = $this->getParam('thumbnail') == "1" ? true : false;
            $scale = $this->getParam('scale');
//            $photoclass = $this->getParam('photoclass');

            $phototype = null;
            if ($phototypeId) {
                $phototype = $phototypeModel->getById($phototypeId);
                if (!$phototype) {
                    $this->_redirect($this->view->url(array(), 'manage-result') . '?error="notfound"');
                }
            }
            $owner = $this->me->getUser();
            $photoModel = $this->getModel('photo');
            try {
                $destination = $this->getTmpFile($tmp);
                $result = $photoModel->addPhoto($destination, $title, $description, $phototype, $thumbnail, $owner, $scale);//$photoclass
                if ($result) {
                    $result = 1;
                }
            } catch (Exception $e) {
                // image is not accepted
                $result = 2;
            }
            echo $result;
            exit;
        } else {
            // GET METHOD
            $fs = $this->getParam('fs');

            if ($fs) {
                $this->view->fileList = array();
                $f = explode("|", $fs);
                foreach ($f as $k => $v) {
                    $this->view->fileList[] = array('v' => $v, 'p' => $this->getTmpFile($v));
                }
            }
            $this->view->title = "确认保存图片";
            $this->view->phototype = $phototypeModel->getAll(false);
        }
    }

    public function photoUploadAction() {
        if ($this->request->isPost()) {
            // POST METHOD
            $result = 0;
            $upload = new Zend_File_Transfer();

            $upload->addValidator('Size', false, 5120000); //5M

            $uid = uniqid();
            $destination = $this->getTmpFile($uid);

            $upload->addFilter('Rename', $destination);

            if ($upload->isValid()) {
                if ($upload->receive()) {
                    $result = $uid;
                }
            }
            echo $result;
            exit;
        } else {
            // GET METHOD
            $this->view->title = "上传图片";
        }
    }

    public function photoClearcacheAction() {
        if ($this->request->isPost()) {
            // POST METHOD
            $result = 0;
            $utilService = $this->_container->get('util');
            $tmp = $utilService->getTmpDirectory();

            try {
                if ($od = opendir($tmp)) {
                    while ($file = readdir($od)) {
                        unlink($tmp . DIRECTORY_SEPARATOR . $file);
                    }
                }
                $result = 1;
            } catch (Exception $e) {
                $result = 0;
            }
            echo $result;
            exit;
        }
    }

    public function photoListAction() {
        $page = $this->request->getParam('page');
        $phototype = $this->request->getParam('phototype');
        if (!$page) {
            $page = 1;
        }
        $photoModel = $this->getModel('photo');

        $paginator = null;
        if (!$phototype) {
            $paginator = $photoModel->getAll();
        } else {
            $paginator = $photoModel->getPhotoByPhototype($phototype);
        }
        $paginator->setItemCountPerPage($this->bootstrap_options['default_page_size']);
        $paginator->setCurrentPageNumber($page);
        $resource = array();
        foreach ($paginator as $r) {
            $resource[] = array('path' => array('orig' => $this->view->photoImage($r->name . $r->type), 'main' => $this->view->photoImage($r->name . $r->type, 'main'), 'small' => $this->view->photoImage($r->name . $r->type, 'small'), 'large' => $this->view->photoImage($r->name . $r->type, 'large')),
                'name' => $r->name,
                'id' => $r->id,
                'type' => $r->type,
                'thumbnail' => $r->thumbnail,
                'owner' => $r->owner);
        }
        // JSON FORMAT
        if ($this->getParam('format') == 'json') {
            $this->_helper->json(array('data' => $resource,
                'code' => 200,
                'page' => $paginator->getCurrentPageNumber(),
                'count' => $paginator->count()));
        } else {
            $this->view->paginator = $paginator;
            $this->view->resource = $resource;
            $this->view->title = "图片列表";
        }
    }

    public function photoSaveAction() {
        $notFoundMsg = '未找到目标图片';
        $photoModel = $this->getModel('photo');
        $phototypeModel = $this->getModel('phototype');
        $id = $this->request->getParam('id');

        if ($this->request->isPost()) {
            $result = 0;
            // POST METHOD
            $title = $this->request->getParam('title');
            $description = $this->request->getParam('description');
            $phototypeId = $this->request->getParam('phototype');
//            $photoclass = $this->getParam('photoclass');
            $phototype = null;
            if ($phototypeId) {
                $phototype = $phototypeModel->getById($phototypeId);
                if (!$phototype) {
                    $this->_redirect($this->view->url(array(), 'manage-result') . '?error="notfound"');
                }
            }
            try {
                $result = $photoModel->savePhoto($id, $title, $description, $phototype);//, $photoclass
            } catch (Angel_Exception_Photo $e) {
                $error = $e->getDetail();
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
            if ($result) {
                $this->_redirect($this->view->url(array(), 'manage-result') . '?redirectUrl=' . $this->view->url(array(), 'manage-photo-list-home'));
            } else {
                $this->_redirect($this->view->url(array(), 'manage-result') . '?error=' . $error);
            }
        } else {
            // GET METHOD
            $this->view->title = "编辑图片";

            if ($id) {
                $target = $photoModel->getById($id);
                $phototype = $phototypeModel->getAll(false);
                if (!$target) {
                    $this->_redirect($this->view->url(array(), 'manage-result') . '?error=' . $notFoundMsg);
                }
                $this->view->model = $target;
                $this->view->phototype = $phototype;
            } else {
                $this->_redirect($this->view->url(array(), 'manage-result') . '?error=' . $notFoundMsg);
            }
        }
    }

    public function photoRemoveAction() {
        if ($this->request->isPost()) {
            $result = 0;
            // POST METHOD
            $id = $this->getParam('id');
            if ($id) {
                $photoModel = $this->getModel('photo');
                $result = $photoModel->removePhoto($id);
            }
            echo $result;
            exit;
        }
    }

    /***************************************************
     * 图片类型acton部分
     *
     * ************************************************/
    public function phototypeListAction() {
        $page = $this->request->getParam('page');
        if (!$page) {
            $page = 1;
        }
        $phototypeModel = $this->getModel('phototype');
        $photoModel = $this->getModel('photo');
        $paginator = $phototypeModel->getAll();
        $paginator->setItemCountPerPage($this->bootstrap_options['default_page_size']);
        $paginator->setCurrentPageNumber($page);
        $resource = array();
        foreach ($paginator as $r) {
            $resource[] = array('id' => $r->id,
                'name' => $r->name,
                'description' => $r->description,
                'owner' => $r->owner);
        }
        // JSON FORMAT
        if ($this->getParam('format') == 'json') {
            $this->_helper->json(array('data' => $resource,
                'code' => 200,
                'page' => $paginator->getCurrentPageNumber(),
                'count' => $paginator->count()));
        } else {
            $this->view->paginator = $paginator;
            $this->view->resource = $resource;
            $this->view->title = "图片分类列表";
            $this->view->photoModel = $photoModel;
        }
    }

    public function phototypeCreateAction() {
        if ($this->request->isPost()) {
            $result = 0;
            // POST METHOD
            $name = $this->request->getParam('name');
            $description = $this->request->getParam('description');
            $owner = $this->me->getUser();
            $phototypeModel = $this->getModel('phototype');
            try {
                $result = $phototypeModel->addPhototype($name, $description, $owner);
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
            if ($result) {
                $this->_redirect($this->view->url(array(), 'manage-result') . '?redirectUrl=' . $this->view->url(array(), 'manage-phototype-list-home'));
            } else {
                $this->_redirect($this->view->url(array(), 'manage-result') . '?error=' . $error);
            }
        } else {
            // GET METHOD
            $this->view->title = "创建图片分类";
        }
    }

    public function phototypeSaveAction() {
        $notFoundMsg = '未找到目标图片分类';

        if ($this->request->isPost()) {
            $result = 0;
            // POST METHOD
            $id = $this->request->getParam('id');
            $name = $this->request->getParam('name');
            $description = $this->request->getParam('description');
            $phototypeModel = $this->getModel('phototype');
            try {
                $result = $phototypeModel->savePhototype($id, $name, $description);
            } catch (Angel_Exception_Phototype $e) {
                $error = $e->getDetail();
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
            if ($result) {
                $this->_redirect($this->view->url(array(), 'manage-result') . '?redirectUrl=' . $this->view->url(array(), 'manage-phototype-list-home'));
            } else {
                $this->_redirect($this->view->url(array(), 'manage-result') . '?error=' . $error);
            }
        } else {
            // GET METHOD
            $this->view->title = "编辑图片分类";

            $id = $this->request->getParam("id");
            if ($id) {
                $phototypeModel = $this->getModel('phototype');
                $target = $phototypeModel->getById($id);
                if (!$target) {
                    $this->_redirect($this->view->url(array(), 'manage-result') . '?error=' . $notFoundMsg);
                }
                $this->view->model = $target;
            } else {
                $this->_redirect($this->view->url(array(), 'manage-result') . '?error=' . $notFoundMsg);
            }
        }
    }

    public function phototypeRemoveAction() {
        if ($this->request->isPost()) {
            $result = 0;
            // POST METHOD
            $id = $this->getParam('id');
            if ($id) {
                $phototypeModel = $this->getModel('phototype');
                $result = $phototypeModel->remove($id);
            }
            echo $result;
            exit;
        }
    }


    /****************************************************
     * 分类action部分
     *
     * *************************************************/
    public function categoryCreateAction() {
        $categoryModel = $this->getModel('category');

        if ($this->request->isPost()) {
            $result = 0;
            // POST METHOD
            $name = $this->request->getParam('name');
            $description = $this->request->getParam('description');

            try {
                $result = $categoryModel->addCategory($name, $description);
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
            if ($result) {
                $this->_redirect($this->view->url(array(), 'manage-result') . '?redirectUrl=' . $this->view->url(array(), 'manage-category-list-home'));
            } else {
                $this->_redirect($this->view->url(array(), 'manage-result') . '?error=' . $error);
            }
        } else {
            // GET METHOD
            $this->view->title = "创建瑜伽分类";
        }
    }

    public function categoryListAction() {
        $categoryModel = $this->getModel('category');

        $page = $this->request->getParam('page');

        if (!$page) {
            $page = 1;
        }

        $paginator = $categoryModel->getAll();
        $paginator->setItemCountPerPage($this->bootstrap_options['default_page_size']);
        $paginator->setCurrentPageNumber($page);

        $this->view->title = "瑜伽分类列表";
        $this->view->paginator = $paginator;
    }

    public function categoryRemoveAction() {
        if ($this->request->isPost()) {
            $result = 0;
            // POST METHOD
            $id = $this->getParam('id');

            if ($id) {
                $categoryModel = $this->getModel('category');
                $result = $categoryModel->remove($id);
            }
            echo $result;
            exit;
        }
    }

    public function categorySaveAction() {
        $notFoundMsg = '未找到目标分类';
        $categoryModel = $this->getModel('category');

        if ($this->request->isPost()) {
            $result = 0;
            // POST METHOD
            $id = $this->request->getParam('id');
            $name = $this->request->getParam('name');
            $description = $this->request->getParam('description');
            
            try {
                $result = $categoryModel->saveCategory($id, $name, $description);
            } catch (Angel_Exception_Category $e) {
                $error = $e->getDetail();
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
            if ($result) {
                $this->_redirect($this->view->url(array(), 'manage-result') . '?redirectUrl=' . $this->view->url(array(), 'manage-category-list-home'));
            } else {
                $this->_redirect($this->view->url(array(), 'manage-result') . '?error=' . $error);
            }
        } else {
            // GET METHOD
            $this->view->title = "编辑分类";

            $id = $this->request->getParam("id");

            if ($id) {
                $target = $categoryModel->getById($id);

                if (!$target) {
                    $this->_redirect($this->view->url(array(), 'manage-result') . '?error=' . $notFoundMsg);
                }
                $this->view->model = $target;
            } else {
                $this->_redirect($this->view->url(array(), 'manage-result') . '?error=' . $notFoundMsg);
            }
        }
    }

    public function getCategoryByIds($ids) {
        $categoryModel = $this->getModel('category');

        $result = $categoryModel->getByIds($ids);

        return $result;
    }

    /********************************************************
     * 其他代码action部分
     *
     * *******************************************************/
    public function resultAction() {
        $this->view->error = $this->request->getParam('error');
        $this->view->redirectUrl = $this->request->getParam('redirectUrl');
    }

    protected function getTmpFile($uid) {
        $utilService = $this->_container->get('util');
        $result = $utilService->getTmpDirectory() . '/' . $uid;
        return $result;
    }

    /**************************************
     * 测试 api action
     *
     * ************************************/
    public function apiTestAction() {

    }
}
