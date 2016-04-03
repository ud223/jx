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
        private Models.User _userData;

        private Models.User UserData
        {
            get
            {
                if (this._userData == null)
                {
                    CacheLib.Cache cache = new CacheLib.Cache();
                    CacheLib.Cookie cookie = new CacheLib.Cookie();

                    string key = cookie.GetCookie("user_key");

                    this._userData = cache.Get<Models.User>(key);
                }

                return this._userData;
            }
        }

        private Models.Permission _userPermission;
        private string _sSelectTypeSql = " and app_schools.SchoolID = @SchoolID@";

        private string strParam = "";

 //       [Flowpie.Models.MyAuth(Roles = "系统用户,驾校管理员")]//这里配置角色切忌不能有多余的空格
        public ActionResult Index()
        {
            if (!this.init())
            {
                return Redirect("/account/login");
            }

            return View();
        }
        //*********************************业务后台action*******************************************************

        #region 考试类型管理action
   //     [Flowpie.Models.MyAuth(Roles = "系统用户")]
        public ActionResult ExamTypeList(int page = 1)
        {
            if (!this.init())
            {
                return Redirect("/manage/PermmissionForbidden");
            }

            JxLib.ExamTypeController examtypeController = new JxLib.ExamTypeController();

            List<System.Collections.Hashtable> list = examtypeController.getAll();

            ViewData["examtypes"] = list;

            ViewData["open_menu"] = "考试管理";

            return View();
        }

     //   [Flowpie.Models.MyAuth(Roles = "系统用户")]
        public ActionResult ExamTypeEdit(string id = null)
        {
            JxLib.ExamTypeController examtypeController = new JxLib.ExamTypeController();

            if (!this.init())
            {
                return Redirect("/manage/PermmissionForbidden");
            }

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

            if (!this.init())
            {
                return Redirect("/manage/PermmissionForbidden");
            }

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

        #region 学员action

        /// <summary>
        /// 学员申请列表
        /// </summary>
        /// <returns></returns>
        public ActionResult StudentApplication()
        {
            if (!this.init())
            {
                return Redirect("/manage/PermmissionForbidden");
            }

            JxLib.ApplicationController applicationController = new JxLib.ApplicationController();

            if (this._userPermission.SelectType != "1")
            {
                applicationController.SelectType = this._userPermission.SelectType;
                applicationController.SelectTypeSql = this._sSelectTypeSql.Replace("@SchoolID@", this.UserData.SchoolID);
            }

            List<Hashtable> list = applicationController.getAll();

            ViewData["list"] = list;
            ViewData["open_menu"] = "学员申请列表";
            return View();
        }

 //       [Flowpie.Models.MyAuth(Roles = "系统用户,驾校管理员")]
        public ActionResult StudentList(int page = 1)
        {
            if (!this.init())
            {
                return Redirect("/manage/PermmissionForbidden");
            }

            JxLib.StudentController studentController = new JxLib.StudentController();
            List<System.Collections.Hashtable> list = studentController.getBySchool();
            ViewData["list"] = list;
            ViewData["open_menu"] = "驾校管理";
            return View();
        }

        public ActionResult StudentDetail(string studentId)
        {
            if (!this.init())
            {
                return Redirect("/manage/PermmissionForbidden");
            }
            JxLib.GroundController groundController = new JxLib.GroundController();
            JxLib.StudentController studentController = new JxLib.StudentController();

            System.Collections.Hashtable item = studentController.getUserByStudentId(studentId);

            var list2 = groundController.getByType("2");
            var list3 = groundController.getByType("3");

            ViewData["list2"] = list2;
            ViewData["list3"] = list3;
            ViewData["item"] = item;
            ViewData["open_menu"] = "驾校管理";

            return View();
        }

        #endregion;

        #region 教练列表action

        public ActionResult CoachApplicationList()
        {
            if (!this.init())
            {
                return Redirect("/manage/PermmissionForbidden");
            }

            JxLib.CoachController coachController = new JxLib.CoachController();

            List<Hashtable> list = coachController.getApplicationBySchoolID(this.UserData.SchoolID);

            ViewData["list"] = list;

            ViewData["SchoolID"] = this.UserData.SchoolID;

            return View();
        }

        //        [Flowpie.Models.MyAuth(Roles = "系统用户,驾校管理员")]
        public ActionResult CoachList(int page = 1)
        {
            if (!this.init())
            {
                return Redirect("/manage/PermmissionForbidden");
            }

            JxLib.CoachController coachController = new JxLib.CoachController();

            List<Hashtable> list = coachController.getAllBySchoolByID(this.UserData.SchoolID);

            ViewData["list"] = list;

            return View();
        }
        #endregion;

        #region 教练详细action
  //      [Flowpie.Models.MyAuth(Roles = "系统用户,驾校管理员")]
        public ActionResult CoachDetail(string id)
        {
            this.strParam = id;

            if (id == null || id == "")
            {
                return Redirect("/manage/PermmissionForbidden");
            }

            if (!this.init())
            {
                return Redirect("/manage/PermmissionForbidden");
            }

            JxLib.StudentController studentController = new JxLib.StudentController();
            JxLib.OrderController orderController = new JxLib.OrderController();

            List<Hashtable> list = orderController.getRatingByCoachID(id);

            Hashtable item = studentController.load(id);

            ViewData["item"] = item;

            if (list.Count > 0)
                ViewData["list"] = list;
            else
                ViewData["list"] = null;

            return View();
        }
        #endregion;

        #region 教练申请action
 //       [Flowpie.Models.MyAuth(Roles = "系统用户,驾校管理员")]
        public ActionResult CoachApply(int page = 1)
        {
            if (!this.init())
            {
                return Redirect("/manage/PermmissionForbidden");
            }

            return View();
        }
        #endregion;

        #region 教练利用率action
  //      [Flowpie.Models.MyAuth(Roles = "系统用户,驾校管理员")]
        public ActionResult CoachUsage(int page = 1)
        {
            if (!this.init())
            {
                return Redirect("/manage/PermmissionForbidden");
            }

            return View();
        }
        #endregion;

        #region 学员详细action
  //      [Flowpie.Models.MyAuth(Roles = "系统用户,驾校管理员")]
        
        #endregion;

        #region 教练列表action
        //        [Flowpie.Models.MyAuth(Roles = "系统用户,驾校管理员")]
        public ActionResult PermissionForbidden(int page = 1)
        {
            if (!this.init())
            {
                return Redirect("/manage/PermmissionForbidden");
            }

            return View();
        }
        #endregion;

        #region 常用设置action
        //      [Flowpie.Models.MyAuth(Roles = "系统用户,驾校管理员")]
        public ActionResult SchoolSetting()
        {
            if (!this.init())
            {
                return Redirect("/manage/PermmissionForbidden");
            }

            JxLib.SchoolController schoolController = new JxLib.SchoolController();

            Hashtable item = schoolController.load(this.UserData.SchoolID);

            ViewData["item"] = item;

            return View();
        }

        #endregion;

        #region 训练场次设置action
  //      [Flowpie.Models.MyAuth(Roles = "系统用户,驾校管理员")]
        public ActionResult CourseEdit(string schoolId)
        {
            if (!this.init())
            {
                return Redirect("/manage/PermmissionForbidden");
            }

            return View();
        }
        #endregion;

        #region 分配教练action
  //      [Flowpie.Models.MyAuth(Roles = "系统用户,驾校管理员")]
        public ActionResult CoachDispatch()
        {
            if (!this.init())
            {
                return Redirect("/manage/PermmissionForbidden");
            }

            JxLib.OrderController orderController = new JxLib.OrderController();

            string schoolId = this.UserData.SchoolID;

            List<Hashtable> list = orderController.getConfigOrder(schoolId);

            List<string> date_list = new List<string>();
            List<Models.Value> data = new List<Models.Value>();

            foreach (Hashtable item in list)
            {
                if (date_list.Contains(item["RunDate"].ToString()))
                    continue;

                date_list.Add(item["RunDate"].ToString());

                Models.Value value = new Models.Value();

                value.Key = item["RunDate"].ToString();

                //循环判断当前教学计划都已安排
                foreach (Hashtable itm in list)
                {
                    if (itm["RunDate"].ToString() == value.Key)
                    {
                        if (itm["CoachID"].ToString() == "" || itm["CoachID"].ToString().ToLower() == "null")
                        {
                            value.Val = "0";

                            break;
                        }
                    }
                }

                //如果都已安排 设置值为1
                if (value.Val == "")
                    value.Val = "1";

                data.Add(value);
            }

            ViewData["list"] = data;
            //ViewData["date_list"] = date_list;

            return View();
        }
        #endregion;

        #region 分配教练至课程action
  //      [Flowpie.Models.MyAuth(Roles = "系统用户,驾校管理员")]
        public ActionResult CoachDispatchCourse(string id)
        {
            this.strParam = id;

            if (!this.init())
            {
                return Redirect("/manage/PermmissionForbidden");
            }


            JxLib.OrderController orderController = new JxLib.OrderController();
            JxLib.TrainingController trainingController = new JxLib.TrainingController();
            JxLib.CoachController coachController = new JxLib.CoachController();

            string schoolId = this.UserData.SchoolID;

            List<Hashtable> list = orderController.loadByDay(id, schoolId);

            DateTime day = Convert.ToDateTime(id);

            DayOfWeek weeknum = day.DayOfWeek;
            int weekday = Convert.ToInt32(weeknum);

            if (weekday == 0)
                weekday = 7;

            Hashtable config = trainingController.loadTraining(schoolId, weekday.ToString());

            List<Hashtable> coach_list = coachController.getAllBySchoolNotFreezeByID(schoolId);

            ViewData["day"] = config["Time"].ToString().Split(',');
            ViewData["list"] = list;
            ViewData["coach_list"] = coach_list;

            return View();
        }
        #endregion;

        #region 分配教练至集训action
 //       [Flowpie.Models.MyAuth(Roles = "系统用户,驾校管理员")]
        public ActionResult CoachDispatchTraining(string schoolId)
        {
            this.init();

            return View();
        }
        #endregion;

        #region 驾校信息编辑action
        public ActionResult SchoolList()
        {
            if (!this.init())
            {
                return Redirect("/manage/PermmissionForbidden");
            }

            JxLib.SchoolController schoolController = new JxLib.SchoolController();

            List<Hashtable> list = schoolController.getAll();

            ViewData["list"] = list;

            return View();
        }

  //      [Flowpie.Models.MyAuth(Roles = "系统用户,驾校管理员")]
        public ActionResult SchoolEdit(string schoolId)
        {
            if (!this.init())
            {
                return Redirect("/manage/PermmissionForbidden");
            }

            JxLib.SchoolController schoolController = new JxLib.SchoolController();

            if (schoolId == null || schoolId == "")
                schoolId = "0";

            if (this.UserData.SchoolID != "0")
                schoolId = this.UserData.SchoolID;

            Hashtable item = null;

            if (schoolId == "0")
                item = schoolController.loadStructure();
            else
                item = schoolController.load(schoolId);

            ViewData["item"] = item;

            return View();
        }

        [HttpPost]
        [ValidateInput(false)]
        public ActionResult SchoolSave()
        {
            JxLib.SchoolController schoolController = new JxLib.SchoolController();
            DatabaseLib.Tools tools = new DatabaseLib.Tools();

            string strParam = Request.Form.ToString();

            System.Collections.Hashtable data = tools.paramToData(strParam);

            string school_id = CommonLib.Common.Validate.IsNullString(Request.Params["SchoolID"]);

            if (school_id == "")
            {
                school_id = schoolController.add(data);

                return Redirect("/manage/schooledit");
            }
            else
            {
                schoolController.save(data);

                return Redirect("/manage/schooledit?id=" + school_id);
            }
        }

        #endregion;

        #region 登记学员action
   //     [Flowpie.Models.MyAuth(Roles = "系统用户,驾校管理员")]
        public ActionResult MarkStudent(string studentId)
        {
            if (!this.init())
            {
                return Redirect("/manage/PermmissionForbidden");
            }

            JxLib.StudentController studentController = new JxLib.StudentController();
            System.Collections.Hashtable item = studentController.getUserByStudentId(studentId);

            ViewData["item"] = item;
            ViewData["open_menu"] = "登记学员";

            return View();
        }
        #endregion;

        #region 场地管理

        public ActionResult GroundList()
        {
            if (!this.init())
            {
                return Redirect("/manage/PermmissionForbidden");
            }

            ViewData["title"] = "考试场地列表";

            JxLib.GroundController groundController = new JxLib.GroundController();

            var list = groundController.getAll();

            ViewData["list"] = list;

            return View();
        }

        public ActionResult GroundEdit(string id)
        {
            if (!this.init())
            {
                return Redirect("/manage/PermmissionForbidden");
            }

            ViewData["title"] = "考试场地编辑";

            JxLib.GroundController groundController = new JxLib.GroundController();

            var ground = groundController.load(id);

            ViewData["item"] = ground;

            return View();
        }

        [HttpPost]
        public ActionResult GroundSave()
        {
            JxLib.GroundController groundController = new JxLib.GroundController();
            DatabaseLib.Tools tools = new DatabaseLib.Tools();

            string strParam = Request.Form.ToString();

            System.Collections.Hashtable data = tools.paramToData(strParam);

            string id = CommonLib.Common.Validate.IsNullString(Request.Params["groundid"]);

            if (id == "")
            {
                if (!checkUser(1))
                {
                    return Redirect("/manage/groundlist");
                }

                id = groundController.add(data);

                if (id == null)
                {
                    return RedirectToRoute("/manage/groundedit");
                }
            }
            else
            {
                if (!checkUser(2))
                {
                    return Redirect("/manage/groundlist");
                }

                groundController.save(data);
            }

            return Redirect("/manage/groundedit");
        }

        #endregion

        #region 考试申请列表

        public ActionResult AssessmentList()
        {
            if (!this.init())
            {
                return Redirect("/manage/PermmissionForbidden");
            }

            ViewData["title"] = "考试申请列表";

            JxLib.AssessmentController assessmentController = new JxLib.AssessmentController();

            var list = assessmentController.getList();

            ViewData["list"] = list;

            return View();
        }

        #endregion

        #region 优惠卷

        //[Flowpie.Models.MyAuth(Roles = "系统用户,驾校管理员")]
        public ActionResult StudentCoupon(string id)
        {
            if (!this.init())
            {
                return Redirect("/manage/PermmissionForbidden");
            }
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
  //      [Flowpie.Models.MyAuth(Roles = "系统用户,驾校管理员")]
        public ActionResult UseCoupon()
        {
            if (!this.init())
            {
                return Redirect("/manage/PermmissionForbidden");
            }

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

        //[Flowpie.Models.MyAuth(Roles = "系统用户,驾校管理员")]
        public ActionResult CouponResult(string id)
        {
            JxLib.CouponController couponController = new JxLib.CouponController();

            if (!this.init())
            {
                return Redirect("/manage/PermmissionForbidden");
            }

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

    //    [Flowpie.Models.MyAuth(Roles = "系统用户")]
        public ActionResult UserList(int page = 1)
        {
            if (!this.init())
            {
                return Redirect("/manage/PermmissionForbidden");
            }

            SystemConfigureLib.UserController userController = new SystemConfigureLib.UserController();
            SystemConfigureLib.UserTypeController userTypeController = new SystemConfigureLib.UserTypeController();

            List<System.Collections.Hashtable> list = userController.getAll();

            ViewData["users"] = list;

            List<System.Collections.Hashtable> userType = userTypeController.getAll();

            ViewData["user_type"] = userType;

            ViewData["open_menu"] = "系统管理";

            return View();
        }

  //      [Flowpie.Models.MyAuth(Roles = "系统用户")]
        public ActionResult UserEdit(string id = null)
        {
            SystemConfigureLib.UserController userController = new SystemConfigureLib.UserController();
            SystemConfigureLib.UserTypeController userTypeController = new SystemConfigureLib.UserTypeController();
            JxLib.SchoolController schoolController = new JxLib.SchoolController();

            if (!this.init())
            {
                return Redirect("/manage/PermmissionForbidden");
            }

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

            List<System.Collections.Hashtable> schools = schoolController.getAll();

            ViewData["schools"] = schools;

            List<System.Collections.Hashtable> userType = userTypeController.getAll();

            ViewData["user_type"] = userType;

            return View();
        }

   //     [Flowpie.Models.MyAuth(Roles = "系统用户")]
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

 //       [Flowpie.Models.MyAuth(Roles = "系统用户")]
        public ActionResult UserDelete(string id = null, int page = 1)
        {
            SystemConfigureLib.UserController userController = new SystemConfigureLib.UserController();

            if (!this.init())
            {
                return Redirect("/manage/PermmissionForbidden");
            }

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
  //      [Flowpie.Models.MyAuth(Roles = "系统用户")]
        public ActionResult DeptList(int page = 1)
        {
            if (!this.init())
            {
                return Redirect("/manage/PermmissionForbidden");
            }

            SystemConfigureLib.DeptController deptController = new SystemConfigureLib.DeptController();

            List<System.Collections.Hashtable> list = deptController.getAll();

            ViewData["depts"] = list;

            ViewData["open_menu"] = "系统管理";

            return View();
        }
 //       [Flowpie.Models.MyAuth(Roles = "系统用户")]
        public ActionResult DeptEdit(string id = null)
        {
            SystemConfigureLib.DeptController deptController = new SystemConfigureLib.DeptController();

            if (!this.init())
            {
                return Redirect("/manage/PermmissionForbidden");
            }

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

            if (!this.init())
            {
                return Redirect("/manage/PermmissionForbidden");
            }

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
   //     [Flowpie.Models.MyAuth(Roles = "系统用户")]
        public ActionResult UserTypeList(int page = 1)
        {
            if (!this.init())
            {
                return Redirect("/manage/PermmissionForbidden");
            }

            SystemConfigureLib.UserTypeController usertypeController = new SystemConfigureLib.UserTypeController();

            List<System.Collections.Hashtable> list = usertypeController.getAll();

            ViewData["usertypes"] = list;

            ViewData["open_menu"] = "系统管理";

            return View();
        }
 //       [Flowpie.Models.MyAuth(Roles = "系统用户")]
        public ActionResult UserTypeEdit(string id = null)
        {
            SystemConfigureLib.UserTypeController usertypeController = new SystemConfigureLib.UserTypeController();

            if (!this.init())
            {
                return Redirect("/manage/PermmissionForbidden");
            }

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

            if (!this.init())
            {
                return Redirect("/manage/PermmissionForbidden");
            }

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
  //      [Flowpie.Models.MyAuth(Roles = "系统用户")]
        public ActionResult AccessTypeList(int page = 1)
        {
            if (!this.init())
            {
                return Redirect("/manage/PermmissionForbidden");
            }

            SystemConfigureLib.AccessTypeControllerr accessTypeControllerr = new SystemConfigureLib.AccessTypeControllerr();

            List<System.Collections.Hashtable> list = accessTypeControllerr.getAll();

            ViewData["accesstypes"] = list;

            ViewData["open_menu"] = "系统管理";

            return View();
        }
  //      [Flowpie.Models.MyAuth(Roles = "系统用户")]
        public ActionResult AccessTypeEdit(string id = null)
        {
            SystemConfigureLib.AccessTypeControllerr accessTypeControllerr = new SystemConfigureLib.AccessTypeControllerr();

            if (!this.init())
            {
                return Redirect("/manage/PermmissionForbidden");
            }

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

            if (!this.init())
            {
                return Redirect("/manage/PermmissionForbidden");
            }

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
    //    [Flowpie.Models.MyAuth(Roles = "系统用户")]
        public ActionResult MenuList(int page = 1)
        {
            if (!this.init())
            {
                return Redirect("/manage/PermmissionForbidden");
            }

            SystemConfigureLib.MenuController menuController = new SystemConfigureLib.MenuController();

            List<System.Collections.Hashtable> menus = menuController.getEnableMenus();

            ViewData["list"] = menus;
            ViewData["open_menu"] = "系统管理";

            return View();
        }
 //       [Flowpie.Models.MyAuth(Roles = "系统用户")]
        public ActionResult MenuEdit(string id = null)
        {
            SystemConfigureLib.MenuController menuController = new SystemConfigureLib.MenuController();

            if (!this.init())
            {
                return Redirect("/manage/PermmissionForbidden");
            }

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
                if (!checkUser(1))
                {
                    return Redirect("/manage/index");
                }

                menu_id = menuController.add(menu_data);

                if (menu_id == null)
                {
                    return RedirectToRoute("menu-add");
                }
            }
            else
            {
                if (!checkUser(2))
                {
                    return Redirect("/manage/index");
                }

                menuController.save(menu_data);
            }

            return RedirectToRoute("menu-add");
        }

        public ActionResult MenuDelete(string id = null, int page = 1)
        {
            SystemConfigureLib.MenuController menuController = new SystemConfigureLib.MenuController();

            if (!this.init())
            {
                return Redirect("/manage/PermmissionForbidden");
            }

            if (id == null)
            {
                return RedirectToRoute("menu-list-home");
            }
            else
            {
                if (!checkUser(3))
                {
                    return Redirect("/manage/index");
                }

                menuController.delete(id);

                return RedirectToRoute("menu-list", new { page = page });
            }
        }

        public ActionResult Permission(string menuid)
        {
            if (!this.init())
            {
                return Redirect("/manage/PermmissionForbidden");
            }

            SystemConfigureLib.MenuController menuController = new SystemConfigureLib.MenuController();
            SystemConfigureLib.PermissionController permissionController = new SystemConfigureLib.PermissionController();

            Hashtable menu = menuController.load(menuid);

            ViewData["item"] = menu;
            ViewData["list1"] = permissionController.getUserTypeNotInConfig(menuid);
            ViewData["list2"] = permissionController.getPermissionByMenuID(menuid);

            return View();
        }

        #endregion;

        #region 共用方法

        /// <summary>
        /// 初始化后台框架数据通用入口
        /// </summary>
        private bool init()
        {
            if (!checkUser(0))
            {
                return false;
            }

            initMenus();

            initUserData();

            return true;
        }

        private void initUserData()
        {
            ViewData["UserName"] = this.UserData.Name;
        }

        /// <summary>
        /// 后台管理框架获取菜单通用方法
        /// </summary>
        private void initMenus()
        {
            SystemConfigureLib.MenuController menuController = new SystemConfigureLib.MenuController();

            List<System.Collections.Hashtable> tmp_menu = menuController.getEnableMenus();
            List<System.Collections.Hashtable> menus = new List<Hashtable>();

            foreach (System.Collections.Hashtable item in tmp_menu)
            {
                if (item["ParentID"].ToString() == "0")
                    menus.Add(item);
                else
                {
                    foreach (Models.Permission permission in this.UserData.Permissions)
                    {
                        if (permission.MenuID == item["MenuID"].ToString())
                        {
                            menus.Add(item);
                        }
                    }
                }        
            }

            ViewData["menus"] = menus;
        }

        /// <summary>
        /// 用户权限是否能查看该页面
        /// </summary>
        private bool checkUser(int opt)
        {
            string fileName = HttpContext.Request.FilePath;
            System.IO.FileInfo file = new System.IO.FileInfo(fileName);

            if (this.UserData == null)
            {
                return false;
            }

            if (file.Name.ToLower() == "index")
                return true;

            string[] tmp_url = HttpContext.Request.FilePath.ToLower().Split('/');

            string pageName = "";

            if (tmp_url.Length == 4 || tmp_url.Length == 5 || tmp_url.Length == 6)
            {
                if (this.strParam.Length > 0)
                    pageName = tmp_url[2] + tmp_url[3].Replace(this.strParam, "");
                else
                    pageName = tmp_url[2] + tmp_url[3];
            }
            else if (tmp_url.Length == 3)
            {
                pageName = tmp_url[2];
            }
            else
            {
                return false;
            }

            if (pageName.IndexOf("save") > -1 || pageName.IndexOf("delete") > -1)
            {
                string tmp_page_name = pageName.Replace("save", "edit");

                foreach (Models.Permission permission in this.UserData.Permissions)
                {
                    if (permission.AccessFile.ToLower().IndexOf(tmp_page_name) > -1)
                    {
                        bool tmp_result = true;

                        this._userPermission = permission;

                        switch (opt)
                        {
                            case 1: {
                                    if (permission.IsAdd != "1")
                                    {
                                        tmp_result = false;
                                    }

                                    break;
                                }
                            case 2:
                                {
                                    if (permission.IsModify != "1")
                                    {
                                        tmp_result = false;
                                    }

                                    break;
                                }
                            case 3:
                                {
                                    if (permission.IsDelete != "1")
                                    {
                                        tmp_result = false;
                                    }

                                    break;
                                }
                        }

                        if (tmp_result)
                        {
                            return tmp_result;
                        }
                        else
                        {
                            return tmp_result;
                        }
                    }
                }
            }
            else
            {
                foreach (Models.Permission permission in this.UserData.Permissions)
                {
                    if (permission.AccessFile.ToLower().IndexOf(pageName) > -1)
                    {
                        ViewData["menuid"] = permission.MenuID;

                        this._userPermission = permission;

                        return true;
                    }
                }
            }
            
            return false;
        }

        #endregion;

        public ActionResult PermmissionForbidden()
        {
            return View();
        }
    }
}