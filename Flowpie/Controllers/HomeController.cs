using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;
using System.Web.Mvc;
using System.Web.Script.Serialization;
using Newtonsoft.Json;
using System.Net;
using System.IO;
using System.Net.Security;
using System.Security.Authentication;
using System.Security.Cryptography.X509Certificates;
using WxApiLib;
using WxApiLib.lib;

namespace Flowpie.Controllers
{
    public class HomeController : Controller
    {
        string app_id = "wx78b3b4daaed7f512";
        string app_secret = "5ba8c179baf309974fac686236591d15";
        string access_token = "";

        // 考试总题数
        int EXAMCOUNT = Int32.Parse(CommonLib.Common.ConfigReader.Read("Examcount"));
        int PASSSCORE = Int32.Parse(CommonLib.Common.ConfigReader.Read("Passscore"));

        #region 首页微信处理

        public ActionResult Index()
        {
            return View();
        }

        public ActionResult RegUser()
        {
            string code = this.HttpContext.Request.QueryString["code"];
            string tmp_web_url = this.HttpContext.Request.QueryString["web_url"];

            //string web_url = System.Web.HttpUtility.UrlDecode(tmp_web_url);

            //if (CommonLib.Common.Validate.IsNullString(web_url) != "")
            //{
            //    if (web_url == "http://wx.yune-jia.com/")
            //    {
            //        web_url = "/";
            //    }
            //    else
            //    {
            //        web_url = web_url.Replace("http://wx.yune-jia.com/", "/");
            //    }
            //}
            //else
            //{
            //    web_url = "/";
            //}
            string web_url = "/";
            string open_id = this.getOpenId(code);

            Models.Student stu = this.getUserInfo(open_id);

            string student_id = this.addStudent(stu);

            if (student_id == null)
                ViewData["data"] = "用户注册失败!";
            else
            {
                JxLib.SchoolController schoolController = new JxLib.SchoolController();
                JxLib.StudentController studentController = new JxLib.StudentController();
                JxLib.CouponController couponController = new JxLib.CouponController();

                List<System.Collections.Hashtable> list = schoolController.getAll();

                System.Collections.Hashtable item = studentController.load(student_id);

                if (item["SchoolID"].ToString() == "")
                {
                    foreach (System.Collections.Hashtable itm in list)
                    {
                        if (itm["IsCoupon"].ToString() == "0")
                            continue;

                        System.Collections.Hashtable coupon1 = new System.Collections.Hashtable();

                        coupon1.Add("CouponText", itm["CouponText"].ToString());
                        coupon1.Add("Amount", itm["CouponAmount"].ToString());
                        coupon1.Add("CouponRemark", itm["CouponRemark"].ToString());
                        coupon1.Add("Password", couponController.getPassword());
                        coupon1.Add("StudentID", student_id);
                        coupon1.Add("CreateAt", DateTime.Now.ToString("yyyy-MM-dd HH:mm:ss"));
                        coupon1.Add("ModifyAt", DateTime.Now.ToString("yyyy-MM-dd HH:mm:ss"));

                        couponController.add(coupon1);
                    } 
                }

                CacheLib.Cookie cookie = new CacheLib.Cookie();

                cookie.AddCookie("user_id", student_id);

                ViewData["data"] = student_id;
                ViewData["url"] = web_url;
            }    

            return View();
        }     

        private string addStudent(Models.Student stu)
        {
            JxLib.StudentController studentController = new JxLib.StudentController();
            SystemConfigureLib.SerialNumberController serialContrller = new SystemConfigureLib.SerialNumberController();

            System.Collections.Hashtable item = studentController.getUserByOpenId(stu.openid);

            if (item != null)
            {
                return item["StudentID"].ToString();
            }

            string student_id = serialContrller.getSerialNumberRand("stu", DateTime.Now.ToString("yyyy-MM-dd"));

            System.Collections.Hashtable data = new System.Collections.Hashtable();

            data.Add("NickName", CommonLib.Common.Validate.filterEmoji(stu.nickname));
            data.Add("OpenId", stu.openid);
            data.Add("HeadPic", stu.headimgurl);
            data.Add("Sex", stu.sex);
            data.Add("StudentID", student_id);

            string strText = studentController.add(data);

            if (studentController.Result)
                return student_id;
            else
                return null;
        }

        #endregion;

        #region 首页广场

        public ActionResult Clear()
        {
            return View();
        }

        public ActionResult Home()
        {
            JxLib.StudentController studentController = new JxLib.StudentController();
            CacheLib.Cookie cookie = new CacheLib.Cookie();

            string user_id =  cookie.GetCookie("user_id");

            System.Collections.Hashtable item = studentController.load(user_id);

            ViewData["schooid"] = item["SchoolID"];           
            ViewData["title"] = "云e驾";

            return View();
        }

        public ActionResult MyIndex()
        {
            JxLib.StudentController studentController = new JxLib.StudentController();
            CacheLib.Cookie cookie = new CacheLib.Cookie();

            string user_id = cookie.GetCookie("user_id");

            ViewData["data"] = studentController.load(user_id);
            ViewData["user_id"] = user_id;
            ViewData["title"] = "我的首页";

            return View();
        }

        public ActionResult SchoolList()
        {
            ViewData["title"] = "驾校广场";

            return View();
        }

        #endregion

        #region 驾校及报名action

        public ActionResult SchoolDetail(string id)
        {
            if (id == null)
            {
                Response.RedirectToRoute("school-list");
            }

            JxLib.StudentController studentController = new JxLib.StudentController();
            CacheLib.Cookie cookie = new CacheLib.Cookie();

            string user_id = cookie.GetCookie("user_id");

            System.Collections.Hashtable item = studentController.load(user_id);

            ViewData["data"] = item;
            ViewData["schoolid"] = id;
            ViewData["iscoach"] = item["IsCoach"].ToString();
            ViewData["lessonstate"] = item["LessonState"].ToString();
            ViewData["openid"] = item["OpenId"].ToString();

            ViewData["title"] = "驾校信息";

            return View();
        }

        /// <summary>
        /// 驾校报名
        /// </summary>
        /// <returns></returns>
        public ActionResult StudentEnter()
        {
            JxLib.StudentController studentController = new JxLib.StudentController();
            CacheLib.Cookie cookie = new CacheLib.Cookie();

            string schoolid = Request.QueryString["schoolid"];
            
            //如果驾校id等于空, 就直接返回首页
            if (CommonLib.Common.Validate.IsNullString(schoolid) == "")
            {
                return RedirectToRoute("home");
            }

            string user_id = cookie.GetCookie("user_id");

            System.Collections.Hashtable item = studentController.load(user_id);

            ViewData["schoolid"] = schoolid;
            ViewData["item"] = item;
            ViewData["title"] = "学员报名";

            return View();
        }

        [HttpPost]
        public ActionResult StudentEnterSave()
        {
            JxLib.StudentController studentController = new JxLib.StudentController();
            SystemConfigureLib.SerialNumberController serialNumberController = new SystemConfigureLib.SerialNumberController();
            JxLib.ApplicationController applicationController = new JxLib.ApplicationController();
            DatabaseLib.Tools tools = new DatabaseLib.Tools();
            CacheLib.Cookie cookie = new CacheLib.Cookie();

            string strParam = Request.Form.ToString();

            System.Collections.Hashtable data = tools.paramToData(strParam);

            string user_id = cookie.GetCookie("user_id");
            //深大驾校测试代码
            //data.Add("SchoolID", "1");
            //data.Add("StudentID", "00001");

            string application_id = serialNumberController.getSerialNumber("apy", DateTime.Now.ToString("yyyy-MM-dd"));

            data.Add("ApplicationID", application_id);
            data.Add("StudentID", user_id);
            data.Add("ApplicationTypeID", "1");
            data.Add("EnterDate", DateTime.Now.ToString("yyyy-MM-dd HH:mm:ss"));
            data.Add("Remark", "驾校报名");

            studentController.save(data);

            //studentController.saveEnter(data);

            applicationController.add(data);

            if (applicationController.Result)
            {
                return RedirectToRoute("enter-success");
            }
            else
            {
                return RedirectToRoute("home");
            }
        }

        public ActionResult EnterSuccess()
        {
            ViewData["title"] = "报名成功";

            return View();
        }

        public ActionResult MyMessage()
        {
            ViewData["title"] = "我的消息";

            return View();
        }

        public ActionResult MessageDetail()
        {
            ViewData["title"] = "消息明细";

            return View();
        }

        public ActionResult MyCoupon()
        {
            JxLib.CouponController couponController = new JxLib.CouponController();
            DatabaseLib.Tools tools = new DatabaseLib.Tools();
            CacheLib.Cookie cookie = new CacheLib.Cookie();

            string user_id = cookie.GetCookie("user_id");

            List<System.Collections.Hashtable> list = couponController.getByStuentId(user_id);

            ViewData["data"] = list;

            ViewData["title"] = "我的优惠卷";

            return View();
        }

        #endregion;

        #region 订单action

        public ActionResult OrderConfirmation(string id)
        {
            JxLib.OrderController orderController = new JxLib.OrderController();
            JxLib.CouponController couponController = new JxLib.CouponController();
            JxLib.StudentController studentController = new JxLib.StudentController();

            if (id == null || id == "")
            {
                return Redirect("/home");
            }

            System.Collections.Hashtable item = orderController.load(id);

            List<System.Collections.Hashtable> list = couponController.getUseByStuentId(item["StudentID"].ToString());

            System.Collections.Hashtable stu = studentController.load(item["StudentID"].ToString());

            if (item == null)
            {
                return Redirect("/home");
            }

            ViewData["orderid"] = item["TeachID"].ToString();
            ViewData["openid"] = stu["OpenId"].ToString();
            ViewData["item"] = item;
            ViewData["list"] = list;

            return View();
        }

        public ActionResult PaySuccess(string id)
        {
            JxLib.OrderController orderController = new JxLib.OrderController();

            System.Collections.Hashtable item = orderController.load(id);

            if (item == null)
            {
                return Redirect("/home");
            }

            if (item["State"].ToString() != "0")
            {
                return Redirect("/home");
            }

            ViewData["orderid"] = id;
            ViewData["state"] = item["State"].ToString();
            ViewData["item"] = item;

            return View();
        }

        public ActionResult OrderDetail(string id)
        {
            JxLib.OrderController orderController = new JxLib.OrderController();

            if (id == null || id == "")
            {
                return Redirect("/home");
            }

            System.Collections.Hashtable item = orderController.load(id);

            ViewData["item"] = item;
            ViewData["order_id"] = item["TeachID"].ToString();

            return View();
        }

        public ActionResult OrderRating(string id)
        {
            JxLib.OrderController orderController = new JxLib.OrderController();

            if (id == null || id == "")
            {
                return Redirect("/home");
            }

            System.Collections.Hashtable item = orderController.load(id);

            if (item["State"].ToString() != "3")
            {
                return Redirect("/home");
            }

            ViewData["item"] = item;
            ViewData["orderid"] = id;

            return View();
        }

        public ActionResult RatingSuccess()
        {
            return View();
        }

        #endregion;

        #region 约练考试

        public ActionResult LessonStart(string id)
        {
            JxLib.OrderController orderController = new JxLib.OrderController();
            JxLib.StudentController studentController = new JxLib.StudentController();

            System.Collections.Hashtable item = orderController.load(id);
           

            if (item == null)
            {
                return Redirect("/home");
            }

            System.Collections.Hashtable stu = studentController.load(item["StudentID"].ToString());
            List<System.Collections.Hashtable> teachDetail = orderController.getDetailHistory(item["StudentID"].ToString());

            if (item["State"].ToString() != "1")
            {
                return Redirect("/home");
            }

            ViewData["orderid"] = id;
            ViewData["state"] = item["State"].ToString();
            ViewData["item"] = item;
            ViewData["stu"] = stu;

            if (stu["Sex"].ToString() == "2")
            {
                ViewData["Sex"] = "女";
            }
            else
            {
                ViewData["Sex"] = "男";
            }

            if (stu["Birthday"].ToString() == "")
                ViewData["age"] = "";
            else
            {
                DateTime birthday = DateTime.Parse(stu["Birthday"].ToString());

                int age = DateTime.Now.Year - birthday.Year;

                if (DateTime.Now.Month < birthday.Month || (DateTime.Now.Month == birthday.Month && DateTime.Now.Day < birthday.Day))
                    age--;

                ViewData["age"] = age.ToString();
            }
            
            foreach (System.Collections.Hashtable itm in teachDetail)
            {
                ViewData[itm["TeachTypeID"].ToString()] = itm["num"].ToString();
            }

            if (ViewData["1"] == null)
            {
                ViewData["1"] = "0";
            }

            if (ViewData["2"] == null)
            {
                ViewData["2"] = "0";
            }

            if (ViewData["3"] == null)
            {
                ViewData["3"] = "0";
            }

            string[] times = item["Time"].ToString().Split(',');

            int time = Convert.ToInt32(times[0]) - 1;

            ViewData["rundate"] = Convert.ToDateTime(item["RunDate"]).ToString("yyyy年MM月dd日") + " " + time.ToString() + ":00 - " + times[times.Length - 1] + ":00";

            return View();
        }

        public ActionResult Lessoning(string id)
        {
            JxLib.OrderController orderController = new JxLib.OrderController();
            JxLib.StudentController studentController = new JxLib.StudentController();

            System.Collections.Hashtable item = orderController.load(id);


            if (item == null)
            {
                return Redirect("/home");
            }

            System.Collections.Hashtable stu = studentController.load(item["StudentID"].ToString());
            List<System.Collections.Hashtable> teachDetail = orderController.getDetailHistory(item["StudentID"].ToString());

            if (item["State"].ToString() != "2")
            {
                return Redirect("/home");
            }

            ViewData["orderid"] = id;
            ViewData["state"] = item["State"].ToString();
            ViewData["item"] = item;
            ViewData["stu"] = stu;

            if (stu["Sex"].ToString() == "2")
            {
                ViewData["Sex"] = "女";
            }
            else
            {
                ViewData["Sex"] = "男";
            }

            if (stu["Birthday"].ToString() == "")
                ViewData["age"] = "";
            else
            {
                DateTime birthday = DateTime.Parse(stu["Birthday"].ToString());

                int age = DateTime.Now.Year - birthday.Year;

                if (DateTime.Now.Month < birthday.Month || (DateTime.Now.Month == birthday.Month && DateTime.Now.Day < birthday.Day))
                    age--;

                ViewData["age"] = age.ToString();
            }

            foreach (System.Collections.Hashtable itm in teachDetail)
            {
                ViewData[itm["TeachTypeID"].ToString()] = itm["num"].ToString();
            }

            if (ViewData["1"] == null)
            {
                ViewData["1"] = "0";
            }

            if (ViewData["2"] == null)
            {
                ViewData["2"] = "0";
            }

            if (ViewData["3"] == null)
            {
                ViewData["3"] = "0";
            }

            string[] times = item["Time"].ToString().Split(',');

            int time = Convert.ToInt32(times[0]) - 1;

            ViewData["rundate"] = Convert.ToDateTime(item["RunDate"]).ToString("yyyy年MM月dd日") + " " + time.ToString() + ":00 - " + times[times.Length - 1] + ":00";

            return View();
        }

        public ActionResult LessonEnd(string id)
        {
            JxLib.OrderController orderController = new JxLib.OrderController();
            JxLib.StudentController studentController = new JxLib.StudentController();

            System.Collections.Hashtable item = orderController.load(id);


            if (item == null)
            {
                return Redirect("/home");
            }

            System.Collections.Hashtable stu = studentController.load(item["StudentID"].ToString());
            List<System.Collections.Hashtable> teachDetail = orderController.getDetailHistory(item["StudentID"].ToString());

            if (item["State"].ToString() != "2")
            {
                return Redirect("/home");
            }

            ViewData["orderid"] = id;

            return View();
        }

        public ActionResult LessonSuccess(string id)
        {
            JxLib.OrderController orderController = new JxLib.OrderController();
            JxLib.StudentController studentController = new JxLib.StudentController();

            System.Collections.Hashtable item = orderController.load(id);


            if (item == null)
            {
                return Redirect("/home");
            }

            System.Collections.Hashtable stu = studentController.load(item["StudentID"].ToString());
            List<System.Collections.Hashtable> teachDetail = orderController.getDetailHistory(item["StudentID"].ToString());

            if (item["State"].ToString() != "3")
            {
                return Redirect("/home");
            }

            return View();
        }

        #endregion;

        #region 我的首页

        public ActionResult MyOrder()
        {
            JxLib.OrderController orderController = new JxLib.OrderController();

            CacheLib.Cookie cookie = new CacheLib.Cookie();

            string user_id = cookie.GetCookie("user_id");

            if (user_id == null)
            {
                return Redirect("/home");
            }

            List<System.Collections.Hashtable> list = orderController.getMyOrder(user_id);

            ViewData["list"] = list;
            ViewData["count"] = list.Count;

            return View();
        }

        public ActionResult MyLesson()
        {
            JxLib.CoachController coachController = new JxLib.CoachController();

            CacheLib.Cookie cookie = new CacheLib.Cookie();

            string user_id = cookie.GetCookie("user_id");

            if (user_id == null)
            {
                return Redirect("/home");
            }

            List<System.Collections.Hashtable> list = coachController.getMyLesson(user_id);

            ViewData["list"] = list;
            ViewData["count"] = list.Count;

            return View();          
        }

        public ActionResult MyRecord()
        {
            JxLib.OrderController orderController = new JxLib.OrderController();

            CacheLib.Cookie cookie = new CacheLib.Cookie();

            string user_id = cookie.GetCookie("user_id");

            if (user_id == null)
            {
                return Redirect("/home");
            }

            List<System.Collections.Hashtable> list = orderController.getMyOrder(user_id);

            ViewData["list"] = list;
            ViewData["count"] = list.Count;

            return View();
        }

        #endregion;

        #region 考试action

        public ActionResult ExamMenu()
        {
            JxLib.UserExamController userExamController = new JxLib.UserExamController();
            JxLib.ExamController examController = new JxLib.ExamController();

            CacheLib.Cookie cookie = new CacheLib.Cookie();

            string user_id = cookie.GetCookie("user_id");

            System.Collections.Hashtable item = userExamController.getMySequenceExamLog(user_id);
            int count = examController.getAll().Count;

            ViewData["count"] = count;

            if (item == null)
                ViewData["exam_id"] = "1";
            else 
                ViewData["exam_id"] = item["ExamID"].ToString();

            ViewData["title"] = "科目一考试练习";

            return View();
        }
        
        public ActionResult SequenceExam()
        {
            JxLib.ExamController examController = new JxLib.ExamController();

            List<System.Collections.Hashtable> list = examController.getAll();

            CacheLib.Cookie cookie = new CacheLib.Cookie();
            JxLib.UserExamController userExamController = new JxLib.UserExamController();

            //实际运行代码
            string user_id = cookie.GetCookie("user_id");
            //开发测试代码
            //string user_id = "00001";

            //cookie.AddCookie("user_id", "00001");

            System.Collections.Hashtable sequenceExamLog = userExamController.getMySequenceExamLog(user_id);

            string exam_id = "1";

            if (sequenceExamLog != null)
            {
                exam_id = sequenceExamLog["ExamID"].ToString();
            }

            ViewData["index"] = exam_id;
            ViewData["count"] = list.Count;
            ViewData["title"] = "训练习题";

            return View();
        }

        public ActionResult SimulateExamStart()
        {
            JxLib.ExamController examController = new JxLib.ExamController();
            JxLib.UserExamController userExamController = new JxLib.UserExamController();
            CacheLib.Cookie cookie = new CacheLib.Cookie();

            System.Collections.Hashtable data = new System.Collections.Hashtable();
            string user_id = cookie.GetCookie("user_id");
            //深大驾校测试代码
            //data.Add("SchoolID", "1");
            //data.Add("StudentID", "00001");
            data.Add("StudentID", user_id);
            //插入数据,考试结束
            string strExamID = userExamController.ExamStart(data);

            List<System.Collections.Hashtable> list = examController.getTestExam();

            System.Text.StringBuilder strExams = new System.Text.StringBuilder();

            int index = 1;

            foreach (System.Collections.Hashtable item in list)
            {
                Models.ExamA exam_a = null;
                Models.ExamB exam_b = null;

                if (CommonLib.Common.Validate.IsNullString(item["OptionC"]) == "")
                {
                    Models.Answer answerA = null;

                    if (item["Answer"].ToString().IndexOf("A") > -1)
                    {
                        answerA = new Models.Answer
                        {
                            title = item["OptionA"].ToString(),
                            correct = true
                        };
                    }
                    else
                    {
                        answerA = new Models.Answer
                        {
                            title = item["OptionA"].ToString(),
                            correct = false
                        };

                    }

                    string strAnswerA = Newtonsoft.Json.JsonConvert.SerializeObject(answerA);

                    Models.Answer answerB = null;

                    if (item["Answer"].ToString().IndexOf("B") > -1)
                    {
                        answerB = new Models.Answer
                        {
                            title = item["OptionB"].ToString(),
                            correct = true
                        };
                    }
                    else
                    {
                        answerB = new Models.Answer
                        {
                            title = item["OptionB"].ToString(),
                            correct = false
                        };

                    }

                    Models.AnswersA answers = new Models.AnswersA
                    {
                        A = answerA,
                        B = answerB
                    };

                    exam_a = new Models.ExamA
                    {
                        no = index.ToString(),
                        exam_id = item["ExamID"].ToString(),
                        total = this.EXAMCOUNT.ToString(),
                        title = item["ExamText"].ToString(),
                        img = CommonLib.Common.ConfigReader.Read("ResourceUrl") + CommonLib.Common.Validate.IsNullString(item["ImgUrl"]),
                        video = CommonLib.Common.ConfigReader.Read("ResourceUrl") + CommonLib.Common.Validate.IsNullString(item["VideoUrl"]),
                        answers = answers
                    };

                    if (index > 1)
                        strExams.Append(",");

                    strExams.Append(Newtonsoft.Json.JsonConvert.SerializeObject(exam_a));
                }
                else
                {
                    Models.Answer answerA = null;

                    if (item["Answer"].ToString().IndexOf("A") > -1)
                    {
                        answerA = new Models.Answer
                        {
                            title = item["OptionA"].ToString(),
                            correct = true
                        };
                    }
                    else
                    {
                        answerA = new Models.Answer
                        {
                            title = item["OptionA"].ToString(),
                            correct = false
                        };

                    }

                    Models.Answer answerB = null;

                    if (item["Answer"].ToString().IndexOf("B") > -1)
                    {
                        answerB = new Models.Answer
                        {
                            title = item["OptionB"].ToString(),
                            correct = true
                        };
                    }
                    else
                    {
                        answerB = new Models.Answer
                        {
                            title = item["OptionB"].ToString(),
                            correct = false
                        };

                    }

                    Models.Answer answerC = null;

                    if (item["Answer"].ToString().IndexOf("C") > -1)
                    {
                        answerC = new Models.Answer
                        {
                            title = item["OptionC"].ToString(),
                            correct = true
                        };
                    }
                    else
                    {
                        answerC = new Models.Answer
                        {
                            title = item["OptionC"].ToString(),
                            correct = false
                        };

                    }

                    Models.Answer answerD = null;

                    if (item["Answer"].ToString().IndexOf("D") > -1)
                    {
                        answerD = new Models.Answer
                        {
                            title = item["OptionD"].ToString(),
                            correct = true
                        };
                    }
                    else
                    {
                        answerD = new Models.Answer
                        {
                            title = item["OptionD"].ToString(),
                            correct = false
                        };

                    }

                    Models.AnswersB answers = new Models.AnswersB
                    {
                        A = answerA,
                        B = answerB,
                        C = answerC,
                        D = answerD
                    };


                    exam_b = new Models.ExamB
                    {
                        no = index.ToString(),
                        exam_id = item["ExamID"].ToString(),
                        total = this.EXAMCOUNT.ToString(),
                        title = item["ExamText"].ToString(),
                        img = CommonLib.Common.ConfigReader.Read("ResourceUrl") + CommonLib.Common.Validate.IsNullString(item["ImgUrl"]),
                        video = CommonLib.Common.ConfigReader.Read("ResourceUrl") + CommonLib.Common.Validate.IsNullString(item["VideoUrl"]),
                        answers = answers
                    };

                    if (index > 1)
                        strExams.Append(",");

                    strExams.Append(Newtonsoft.Json.JsonConvert.SerializeObject(exam_b));
                }

                index++;
            }


            string tmp = strExams.ToString().Replace("[", "{").Replace("]", "}");

            ViewData["exams"] = tmp;
            ViewData["exam_id"] = strExamID;
            ViewData["title"] = "开始考试";

            return View();
        }

        public ActionResult SimulateExamEnd(string id)
        {
            JxLib.UserExamController userExamController = new JxLib.UserExamController();
            JxLib.ExamController examController = new JxLib.ExamController();

            CacheLib.Cookie cookie = new CacheLib.Cookie();

            string user_id = cookie.GetCookie("user_id");

            System.Collections.Hashtable item = userExamController.getMySequenceExamLog(user_id);
            int count = examController.getAll().Count;

            ViewData["count"] = count;

            if (item == null)
                ViewData["exam_id"] = "1";
            else
                ViewData["exam_id"] = item["ExamID"].ToString();

            ViewData["title"] = "考试结束";

            return View();
        }

        public ActionResult SimulateExam()
        {
            ViewData["title"] = "模拟考试";

            return View();
        }

        public ActionResult MyExam()
        {
            JxLib.UserExamController userExamController = new JxLib.UserExamController();
            CacheLib.Cookie cookie = new CacheLib.Cookie();

            string user_id = cookie.GetCookie("user_id");

            List<System.Collections.Hashtable> list = userExamController.getMyExam(user_id);

            ViewData["data"] = list;

            int ok_count = 0;
            
            foreach (System.Collections.Hashtable item in list)
            {
                string score = CommonLib.Common.Validate.IsNullString(item["Score"]);

                if (score == "")
                    continue;

                if (Int32.Parse(score) > this.PASSSCORE)
                {
                    ok_count++;
                } 
            }

            ViewData["need_count"] = 3 - ok_count;
            ViewData["ok_count"] = ok_count;
            ViewData["title"] = "我的成绩";

            return View();
        }

        #endregion;

        #region 微信访问代码

        /// <summary>
        /// Will return the string contents of a
        /// regular file or the contents of a
        /// response from a URL
        /// </summary>
        /// <param name="fileName">The filename or URL</param>
        /// <returns></returns>
        protected string file_get_contents(string fileName)
        {
            HttpWebRequest req = (HttpWebRequest)HttpWebRequest.Create(fileName);///cgi-bin/loginpage?t=wxm2-login&lang=zh_CN 
            //req.CookieContainer = cookie;
            req.Method = "GET";
            req.ProtocolVersion = HttpVersion.Version10;
            ServicePointManager.SecurityProtocol = SecurityProtocolType.Tls;
            HttpWebResponse res = (HttpWebResponse)req.GetResponse();
            StreamReader rd = new StreamReader(res.GetResponseStream());
            string theContent = rd.ReadToEnd();

            return theContent;
        }

        public static bool ValidateServerCertificate(object sender, X509Certificate certificate, X509Chain chain, SslPolicyErrors errors)
        {
            return true;
        }

        private string getOpenId(string code)
        {
            string url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" + this.app_id + "&secret=" + this.app_secret + "&code=" + code + "&grant_type=authorization_code";


            string weixin = this.file_get_contents(url);

            System.Web.Script.Serialization.JavaScriptSerializer j = new System.Web.Script.Serialization.JavaScriptSerializer();

            Models.OpenId openid_info = new Models.OpenId();

            openid_info = j.Deserialize<Models.OpenId>(weixin);

            this.access_token = openid_info.access_token;

            return openid_info.openid;
        }

        private Models.Student getUserInfo(string open_id)
        {
            string url = "https://api.weixin.qq.com/sns/userinfo?access_token=" + this.access_token + "&openid=" + open_id + "&lang=zh_CN";

            string weixin = this.file_get_contents(url);

            System.Web.Script.Serialization.JavaScriptSerializer j = new System.Web.Script.Serialization.JavaScriptSerializer();

            Models.Student stu = new Models.Student();

            stu = j.Deserialize<Models.Student>(weixin);

            return stu;
        }

        #endregion

        #region 微信支付操作

        public ActionResult OrderPay()
        {
            string wxJsApiParam = "";
            //string editAddress = "";
            JxLib.OrderController orderController = new JxLib.OrderController();
            WxApiLib.lib.Log.Info(this.GetType().ToString(), "1. page load");

            string orderid = Request.QueryString["orderid"];
            string openid = Request.QueryString["openid"];
            string total_fee = Request.QueryString["total_fee"];

            System.Collections.Hashtable item = orderController.load(orderid);
            //如果当前传过来的订单id得到的状态不是支付状态 直接返回首页
            if (item["State"].ToString() != "0")
            {
                return Redirect("/home");
            }

            //检测是否给当前页面传递了相关参数
            if (string.IsNullOrEmpty(openid) || string.IsNullOrEmpty(total_fee) || total_fee == "0")
            {
                Response.Write("<span style='color:#FF0000;font-size:20px'>" + "页面传参出错,请返回重试" + "</span>");
                WxApiLib.lib.Log.Error(this.GetType().ToString(), "This page have not get params, cannot be inited, exit...");

                return View();
            }

            decimal amount = Convert.ToDecimal(total_fee);// * 100;

            //若传递了相关参数，则调统一下单接口，获得后续相关接口的入口参数
            JsApiPay jsApiPay = new JsApiPay(Request, Response);
            jsApiPay.openid = openid;
            jsApiPay.total_fee = Convert.ToInt32(amount);

            //JSAPI支付预处理
            try
            {
                WxApiLib.lib.WxPayData unifiedOrderResult = jsApiPay.GetUnifiedOrderResult();

                wxJsApiParam = jsApiPay.GetJsApiParameters();//获取H5调起JS API参数      
                                                             //editAddress = jsApiPay.GetEditAddressParameters();          

                WxApiLib.lib.Log.Debug(this.GetType().ToString(), "wxJsApiParam : " + wxJsApiParam);

                JavaScriptSerializer js = new JavaScriptSerializer();
                Models.PayInfo payInfo = js.Deserialize<Models.PayInfo>(wxJsApiParam);

                ViewData["appId"] = payInfo.appId;
                ViewData["nonceStr"] = payInfo.nonceStr;
                ViewData["package"] = payInfo.package;
                ViewData["paySign"] = payInfo.paySign;
                ViewData["signType"] = payInfo.signType;
                ViewData["timeStamp"] = payInfo.timeStamp;

                ViewData["orderid"] = Request.QueryString["orderid"];
            }
            catch (Exception ex)
            {
                Response.Write("<span style='color:#FF0000;font-size:20px'>" + "下单失败，请返回重试:" + ex.Message + "</span>");
            }

            return View();
        }

        public ActionResult ResultNotify()
        {
            ResultNotify resultNotify = new ResultNotify(Request, Response);
            resultNotify.ProcessNotify();

            return View();
        }

        public ActionResult PayCancel()
        {
            return View();
        }

        #endregion

        public ActionResult Test()
        {
            return View();
        }
    }
}