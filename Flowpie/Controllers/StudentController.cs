using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;
using System.Net.Http;
using System.Web.Http;

namespace Flowpie.Controllers
{
    public class StudentController : ApiController
    {
        [HttpPost]
        public string Mark()
        {
            JxLib.StudentController studentController = new JxLib.StudentController();
            DatabaseLib.Tools tools = new DatabaseLib.Tools();

            HttpContextBase context = (HttpContextBase)Request.Properties["MS_HttpContext"];
            string strParam = context.Request.Form.ToString();

            System.Collections.Hashtable data = tools.paramToData(strParam);

            data.Add("MarkDate", DateTime.Now.ToString("yyyy-MM-dd HH:mm:ss"));

            studentController.mark(data);

            Models.Result result = new Models.Result();

            if (studentController.Result)
            {
                result.code = "200";
            }
            else
            {
                result.code = "0";
            }

            result.message = studentController.Message;

            return Newtonsoft.Json.JsonConvert.SerializeObject(result).Replace("\"", "'");
        }

        [HttpPost]
        public string Sign()
        {
            JxLib.StudentController studentController = new JxLib.StudentController();
            JxLib.ApplicationController applicationController = new JxLib.ApplicationController();
            DatabaseLib.Tools tools = new DatabaseLib.Tools();
            Models.Result result = new Models.Result();

            HttpContextBase context = (HttpContextBase)Request.Properties["MS_HttpContext"];
            string strParam = context.Request.Form.ToString();

            System.Collections.Hashtable data = tools.paramToData(strParam);

            data.Add("MarkDate", DateTime.Now.ToString("yyyy-MM-dd HH:mm:ss"));

            System.Collections.Hashtable application = applicationController.load(data["ApplicationID"].ToString());

            applicationController.apply(data);

            if (applicationController.Result)
            {
                System.Collections.Hashtable student = studentController.load(application["StudentID"].ToString());

                data.Add("SchoolID", application["SchoolID"].ToString());
                data.Add("StudentID", application["StudentID"].ToString());

                studentController.saveEnter(data);

                if (studentController.Result)
                {
                    result.code = "200";
                    result.message = "审批成功!";
                }
                else
                {
                    result.code = "0";
                    result.message = studentController.Message;
                }
            }
            else
            {
                result.code = "0";
                result.message = applicationController.Message;
            }

            return Newtonsoft.Json.JsonConvert.SerializeObject(result).Replace("\"", "'");
        }

        [HttpPost]
        public string setScore()
        {
            JxLib.OrderController orderController = new JxLib.OrderController();
            JxLib.CoachController coachController = new JxLib.CoachController();
            JxLib.StudentController studentController = new JxLib.StudentController();
            SystemConfigureLib.SerialNumberController serialController = new SystemConfigureLib.SerialNumberController();
            DatabaseLib.Tools tools = new DatabaseLib.Tools();
            Models.Result result = new Models.Result();

            HttpContextBase context = (HttpContextBase)Request.Properties["MS_HttpContext"];
            string strParam = context.Request.Form.ToString();

            System.Collections.Hashtable data = tools.paramToData(strParam);

            System.Collections.Hashtable item = orderController.load(data["TeachID"].ToString());

            if (item == null)
            {
                result.code = "0";
                result.message = "没有找到对应订单";
            }
            else
            {
                string orderRatingID = serialController.getSerialNumber("rat", DateTime.Now.ToString("yyyy-MM-dd"));

                data.Add("OrderRatingID", orderRatingID);

                orderController.setOrderScore(data);

                if (orderController.Result)
                {
                    string coach_id = item["CoachID"].ToString();

                    System.Collections.Hashtable coach = studentController.load(coach_id);

                    if (studentController.Result)
                    {
                        decimal onTimeScore = Convert.ToDecimal(data["OnTimeScore"].ToString());
                        decimal ContentScore = Convert.ToDecimal(data["ContentScore"].ToString());
                        decimal WayScore = Convert.ToDecimal(data["WayScore"].ToString());
                        decimal loadScore = Convert.ToDecimal(coach["Score"].ToString());

                        decimal score = (onTimeScore + ContentScore + WayScore + loadScore) / 4;

                        coachController.updateScore(coach_id, score.ToString());

                        if (coachController.Result)
                        {
                            orderController.nextState(data["TeachID"].ToString());

                            if (orderController.Result)
                            {
                                result.code = "200";
                                result.message = "评论成功!:" + orderController.Message;
                            }
                            else
                            {
                                result.code = "200";
                                result.message = "状态更新失败!:" + orderController.Message;
                            }
                        }
                        else
                        {
                            result.code = "0";
                            result.message = "更新教练分数失败!:" + orderController.Message;
                        }
                    }
                    else
                    {
                        result.code = "0";
                        result.message = "没有找到教练信息!:" + orderController.Message;
                    }
                }
                else
                {
                    result.code = "0";
                    result.message = "评分保存失败!:"+ orderController.Message;
                }
            }

            return Newtonsoft.Json.JsonConvert.SerializeObject(result).Replace("\"", "'");
        }
    }
}