using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;
using System.Web.Mvc;

using System.Data;
using System.Collections;

namespace Flowpie.Controllers
{
    public class ManageController : Controller
    {
        [Flowpie.Models.MyAuth(Roles = "系统用户,驾校管理员")]//这里配置角色切忌不能有多余的空格
        public ActionResult Index()
        {
            this.init();

            return View();
        }
        //*********************************业务后台action*******************************************************

        #region 考试类型管理action
        [Flowpie.Models.MyAuth(Roles = "系统用户")]
        public ActionResult ExamTypeList(int page = 1)
        {
            this.init();

            JxLib.ExamTypeController examtypeController = new JxLib.ExamTypeController();

            List<System.Collections.Hashtable> list = examtypeController.getAll();

            ViewData["examtypes"] = list;

            ViewData["open_menu"] = "考试管理";

            return View();
        }

        [Flowpie.Models.MyAuth(Roles = "系统用户")]
        public ActionResult ExamTypeEdit(string id = null)
        {
            JxLib.ExamTypeController examtypeController = new JxLib.ExamTypeController();

            this.init();

            if (id == null)
            {
                ViewData["title"] = "新增考试类型";
            }
            else
            {
                System.Collections.Hashtable examtype = examtypeController.load(id);

                if (examtype != null)
                {
                    ViewData["examtype"] = examtype;
                }

                ViewData["title"] = "编辑考试类型";
            }

            ViewData["open_menu"] = "考试管理";

            return View();
        }

        [HttpPost]
        public ActionResult ExamTypeSave()
        {
            JxLib.ExamTypeController examtypeController = new JxLib.ExamTypeController();
            DatabaseLib.Tools tools = new DatabaseLib.Tools();

            string strParam = Request.Form.ToString();

            System.Collections.Hashtable data = tools.paramToData(strParam);

            string examtype_id = CommonLib.Common.Validate.IsNullString(Request.Params["ExamTypeID"]);

            if (examtype_id == "")
            {
                examtype_id = examtypeController.add(data);

                if (examtype_id == null)
                {
                    return RedirectToRoute("examtype-add");
                }
            }
            else
            {
                examtypeController.save(data);
            }

            return RedirectToRoute("examtype-add");
        }

        public ActionResult ExamTypeDelete(string id = null, int page = 1)
        {
            JxLib.ExamTypeController examtypeController = new JxLib.ExamTypeController();

            this.init();

            if (id == null)
            {
                return RedirectToRoute("examtype-list-home");
            }
            else
            {
                examtypeController.delete(id);

                return RedirectToRoute("examtype-list", new { page = page });
            }

        }

        #endregion;

        #region 学院路列表action
        [Flowpie.Models.MyAuth(Roles = "系统用户,驾校管理员")]
        public ActionResult StudentList(int page = 1)
        {
            this.init();

            JxLib.StudentController studentController = new JxLib.StudentController();

            List<System.Collections.Hashtable> list = studentController.getAll();

            ViewData["list"] = list;

            ViewData["open_menu"] = "驾校管理";

            return View();
        }
        [Flowpie.Models.MyAuth(Roles = "系统用户,驾校管理员")]
        public ActionResult StudentCoupon(string id)
        {
            this.init();

            JxLib.StudentController studentController = new JxLib.StudentController();

            System.Collections.Hashtable item = studentController.load(id);

            ViewData["item"] = item;

            ViewData["title"] = "发放优惠卷";
            ViewData["open_menu"] = "驾校管理";

            return View();
        }

        [HttpPost]
        public ActionResult StudentCouponSave()
        {
            JxLib.CouponController couponController = new JxLib.CouponController();
            DatabaseLib.Tools tools = new DatabaseLib.Tools();

            string strParam = Request.Form.ToString();

            System.Collections.Hashtable data = tools.paramToData(strParam);

            string password = couponController.getPassword();

            data.Add("Password", password);

            string coupon_id = couponController.add(data);

            //失败应该有一个错误页面
            if (coupon_id == "")
            {
                return RedirectToRoute("manage-student-list");
            }

            return RedirectToRoute("manage-student-list");
        }
        [Flowpie.Models.MyAuth(Roles = "系统用户,驾校管理员")]
        public ActionResult UseCoupon()
        {
            this.init();

            ViewData["title"] = "使用优惠卷";
            ViewData["open_menu"] = "驾校管理";

            return View();
        }

        [HttpPost]
        public ActionResult UseCouponSave()
        {
            JxLib.CouponController couponController = new JxLib.CouponController();
            DatabaseLib.Tools tools = new DatabaseLib.Tools();

            string strParam = Request.Form.ToString();

            System.Collections.Hashtable data = tools.paramToData(strParam);

            System.Collections.Hashtable item = couponController.loadByPassword(data["Password"].ToString());

            if (item == null || item["IsUse"].ToString() != "0")
            {
                //报错跳转页面
                return Redirect("/manage/coupon/result");
            }

            return Redirect("/manage/coupon/result/"+ item["CouponID"].ToString());
        }
        [Flowpie.Models.MyAuth(Roles = "系统用户,驾校管理员")]
        public ActionResult CouponResult(string id)
        {
            JxLib.CouponController couponController = new JxLib.CouponController();

            this.init();

            if (id == null)
            {
                //报错跳转页面
                ViewData["data"] = -1;
            }
            else
            {
                System.Collections.Hashtable item = couponController.load(id);

                couponController.useCoupon(item);

                if (couponController.Result)
                {
                    //验证成功跳转
                    ViewData["data"] = 1;
                }
                else
                {
                    //报错跳转页面
                    ViewData["data"] = 0;
                }
            }

            ViewData["title"] = "优惠卷验证结果";
            ViewData["open_menu"] = "驾校管理";

            return View();
        }

        #endregion;


        //*********************************系统action*********************************************************

        #region 管理员action

        [Flowpie.Models.MyAuth(Roles = "系统用户")]
        public ActionResult UserList(int page = 1)
        {
            this.init();

            SystemConfigureLib.UserController userController = new SystemConfigureLib.UserController();
            SystemConfigureLib.UserTypeController userTypeController = new SystemConfigureLib.UserTypeController();

            List<System.Collections.Hashtable> list = userController.getAll();

            ViewData["users"] = list;

            List<System.Collections.Hashtable> userType = userTypeController.getAll();

            ViewData["user_type"] = userType;

            ViewData["open_menu"] = "系统管理";

            return View();
        }

        [Flowpie.Models.MyAuth(Roles = "系统用户")]
        public ActionResult UserEdit(string id = null)
        {
            SystemConfigureLib.UserController userController = new SystemConfigureLib.UserController();
            SystemConfigureLib.UserTypeController userTypeController = new SystemConfigureLib.UserTypeController();
            SystemConfigureLib.DeptController deptController = new SystemConfigureLib.DeptController();

            this.init();

            if (id == null)
            {
                ViewData["title"] = "新增管理员";
            }
            else
            {
                System.Collections.Hashtable user = userController.load(id);

                if (user != null)
                {
                    ViewData["user"] = user;
                }

                ViewData["title"] = "编辑管理员";
            }

            ViewData["open_menu"] = "系统管理";

            List<System.Collections.Hashtable> depts = deptController.getAll();

            ViewData["depts"] = depts;

            List<System.Collections.Hashtable> userType = userTypeController.getAll();

            ViewData["user_type"] = userType;

            return View();
        }

        [Flowpie.Models.MyAuth(Roles = "系统用户")]
        [HttpPost]
        public ActionResult UserSave()
        {
            SystemConfigureLib.UserController userController = new SystemConfigureLib.UserController();
            DatabaseLib.Tools tools = new DatabaseLib.Tools();

            string strParam = Request.Form.ToString();

            System.Collections.Hashtable data = tools.paramToData(strParam);

            string user_id = CommonLib.Common.Validate.IsNullString(Request.Params["UserID"]);

            if (user_id == "")
            {
                user_id = userController.add(data);

                if (user_id == null)
                {
                    return RedirectToRoute("user-add");
                }
            }
            else
            {
                userController.save(data);
            }

            return RedirectToRoute("user-add");
        }

        [Flowpie.Models.MyAuth(Roles = "系统用户")]
        public ActionResult UserDelete(string id = null, int page = 1)
        {
            SystemConfigureLib.UserController userController = new SystemConfigureLib.UserController();

            this.init();

            if (id == null)
            {
                return RedirectToRoute("user-list-home");
            }
            else
            {
                userController.delete(id);

                return RedirectToRoute("user-list", new { page = page });
            }

        }

        #endregion;

        #region 部门action
        [Flowpie.Models.MyAuth(Roles = "系统用户")]
        public ActionResult DeptList(int page = 1)
        {
            this.init();

            SystemConfigureLib.DeptController deptController = new SystemConfigureLib.DeptController();

            List<System.Collections.Hashtable> list = deptController.getAll();

            ViewData["depts"] = list;

            ViewData["open_menu"] = "系统管理";

            return View();
        }
        [Flowpie.Models.MyAuth(Roles = "系统用户")]
        public ActionResult DeptEdit(string id = null)
        {
            SystemConfigureLib.DeptController deptController = new SystemConfigureLib.DeptController();

            this.init();

            if (id == null)
            {
                ViewData["title"] = "新增部门";
            }
            else
            {
                System.Collections.Hashtable dept = deptController.load(id);

                if (dept != null)
                {
                    ViewData["dept"] = dept;
                }

                ViewData["title"] = "编辑部门";
            }

            ViewData["open_menu"] = "系统管理";

            return View();
        }

        [HttpPost]
        public ActionResult DeptSave()
        {
            SystemConfigureLib.DeptController deptController = new SystemConfigureLib.DeptController();
            DatabaseLib.Tools tools = new DatabaseLib.Tools();

            string strParam = Request.Form.ToString();

            System.Collections.Hashtable data = tools.paramToData(strParam);

            string dept_id = CommonLib.Common.Validate.IsNullString(Request.Params["DeptID"]);

            if (dept_id == "")
            {
                dept_id = deptController.add(data);

                if (dept_id == null)
                {
                    return RedirectToRoute("dept-add");
                }
            }
            else
            {
                deptController.save(data);
            }

            return RedirectToRoute("dept-add");
        }

        public ActionResult DeptDelete(string id = null, int page = 1)
        {
            SystemConfigureLib.DeptController deptController = new SystemConfigureLib.DeptController();

            this.init();

            if (id == null)
            {
                return RedirectToRoute("dept-list-home");
            }
            else
            {
                deptController.delete(id);

                return RedirectToRoute("dept-list", new { page = page });
            }

        }

        #endregion;

        #region 角色action
        [Flowpie.Models.MyAuth(Roles = "系统用户")]
        public ActionResult UserTypeList(int page = 1)
        {
            this.init();

            SystemConfigureLib.UserTypeController usertypeController = new SystemConfigureLib.UserTypeController();

            List<System.Collections.Hashtable> list = usertypeController.getAll();

            ViewData["usertypes"] = list;

            ViewData["open_menu"] = "系统管理";

            return View();
        }
        [Flowpie.Models.MyAuth(Roles = "系统用户")]
        public ActionResult UserTypeEdit(string id = null)
        {
            SystemConfigureLib.UserTypeController usertypeController = new SystemConfigureLib.UserTypeController();

            this.init();

            if (id == null)
            {
                ViewData["title"] = "新增角色";
            }
            else
            {
                System.Collections.Hashtable usertype = usertypeController.load(id);

                if (usertype != null)
                {
                    ViewData["usertype"] = usertype;
                }

                ViewData["title"] = "编辑角色";
            }

            ViewData["open_menu"] = "系统管理";

            return View();
        }

        [HttpPost]
        public ActionResult UserTypeSave()
        {
            SystemConfigureLib.UserTypeController usertypeController = new SystemConfigureLib.UserTypeController();
            DatabaseLib.Tools tools = new DatabaseLib.Tools();

            string strParam = Request.Form.ToString();

            System.Collections.Hashtable data = tools.paramToData(strParam);

            string uusertype_id = CommonLib.Common.Validate.IsNullString(Request.Params["UserTypeID"]);

            if (uusertype_id == "")
            {
                uusertype_id = usertypeController.add(data);

                if (uusertype_id == null)
                {
                    return RedirectToRoute("usertype-add");
                }
            }
            else
            {
                usertypeController.save(data);
            }

            return RedirectToRoute("usertype-add");
        }

        public ActionResult UserTypeDelete(string id = null, int page = 1)
        {
            SystemConfigureLib.UserTypeController usertypeController = new SystemConfigureLib.UserTypeController();

            this.init();

            if (id == null)
            {
                return RedirectToRoute("usertype-list-home");
            }
            else
            {
                usertypeController.delete(id);

                return RedirectToRoute("usertype-list", new { page = page });
            }

        }

        #endregion;

        #region 访问类型action
        [Flowpie.Models.MyAuth(Roles = "系统用户")]
        public ActionResult AccessTypeList(int page = 1)
        {
            this.init();

            SystemConfigureLib.AccessTypeControllerr accessTypeControllerr = new SystemConfigureLib.AccessTypeControllerr();

            List<System.Collections.Hashtable> list = accessTypeControllerr.getAll();

            ViewData["accesstypes"] = list;

            ViewData["open_menu"] = "系统管理";

            return View();
        }
        [Flowpie.Models.MyAuth(Roles = "系统用户")]
        public ActionResult AccessTypeEdit(string id = null)
        {
            SystemConfigureLib.AccessTypeControllerr accessTypeControllerr = new SystemConfigureLib.AccessTypeControllerr();

            this.init();

            if (id == null)
            {
                ViewData["title"] = "新增角色";
            }
            else
            {
                System.Collections.Hashtable accesstype = accessTypeControllerr.load(id);

                if (accesstype != null)
                {
                    ViewData["accesstype"] = accesstype;
                }

                ViewData["title"] = "编辑角色";
            }

            ViewData["open_menu"] = "系统管理";

            return View();
        }

        [HttpPost]
        public ActionResult AccessTypeSave()
        {
            SystemConfigureLib.AccessTypeControllerr accessTypeControllerr = new SystemConfigureLib.AccessTypeControllerr();
            DatabaseLib.Tools tools = new DatabaseLib.Tools();

            string strParam = Request.Form.ToString();

            System.Collections.Hashtable data = tools.paramToData(strParam);

            string accesstype_id = CommonLib.Common.Validate.IsNullString(Request.Params["AccessTypeID"]);

            if (accesstype_id == "")
            {
                accesstype_id = accessTypeControllerr.add(data);

                if (accesstype_id == null)
                {
                    return RedirectToRoute("accesstype-add");
                }
            }
            else
            {
                accessTypeControllerr.save(data);
            }

            return RedirectToRoute("accesstype-add");
        }

        public ActionResult AccessTypeDelete(string id = null, int page = 1)
        {
            SystemConfigureLib.AccessTypeControllerr accessTypeControllerr = new SystemConfigureLib.AccessTypeControllerr();

            this.init();

            if (id == null)
            {
                return RedirectToRoute("accesstype-list-home");
            }
            else
            {
                accessTypeControllerr.delete(id);

                return RedirectToRoute("accesstype-list", new { page = page });
            }

        }

        #endregion;

        #region 菜单action
        [Flowpie.Models.MyAuth(Roles = "系统用户")]
        public ActionResult MenuList(int page = 1)
        {
            this.init();

            ViewData["open_menu"] = "系统管理";

            return View();
        }
        [Flowpie.Models.MyAuth(Roles = "系统用户")]
        public ActionResult MenuEdit(string id = null)
        {
            SystemConfigureLib.MenuController menuController = new SystemConfigureLib.MenuController();

            this.init();

            if (id == null)
            {
                ViewData["title"] = "新增菜单";
            }
            else
            {
                System.Collections.Hashtable menu = menuController.load(id);

                if (menu != null)
                {
                    ViewData["menu"] = menu;
                }

                ViewData["title"] = "编辑菜单";
            }

            ViewData["open_menu"] = "系统管理";

            

            List<System.Collections.Hashtable> menus = menuController.getTopMenu();

            ViewData["top_menus"] = menus;

            return View();
        }

        [HttpPost]
        public ActionResult MenuSave()
        {
            SystemConfigureLib.MenuController menuController = new SystemConfigureLib.MenuController();
            DatabaseLib.Tools tools = new DatabaseLib.Tools();

            string strParam = Request.Form.ToString();

            System.Collections.Hashtable menu_data = tools.paramToData(strParam);

            string menu_id = CommonLib.Common.Validate.IsNullString(Request.Params["MenuID"]);

            if (menu_id == "")
            {
                menu_id = menuController.add(menu_data);

                if (menu_id == null)
                {
                    return RedirectToRoute("menu-add");
                }
            }
            else
            {
                menuController.save(menu_data);
            }

            return RedirectToRoute("menu-add");
        }

        public ActionResult MenuDelete(string id = null, int page = 1)
        {
            SystemConfigureLib.MenuController menuController = new SystemConfigureLib.MenuController();

            this.init();

            if (id == null)
            {
                return RedirectToRoute("menu-list-home");
            }
            else
            {
                menuController.delete(id);

                return RedirectToRoute("menu-list", new { page = page });
            }

        }

        #endregion;

        #region 共用方法

        /// <summary>
        /// 初始化后台框架数据通用入口
        /// </summary>
        private void init()
        {
            initMenus();
        }

        /// <summary>
        /// 后台管理框架获取菜单通用方法
        /// </summary>
        private void initMenus()
        {
            SystemConfigureLib.MenuController menuController = new SystemConfigureLib.MenuController();

            List<System.Collections.Hashtable> menu = menuController.getAll();

            ViewData["menus"] = menu;
        }

        #endregion;
    }
}