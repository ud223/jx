using System;
using System.Collections.Generic;
using System.Linq;
using System.Net;
using System.Web;
using System.Net.Http;
using System.Web.Http;
using System.Data;
using System.Configuration;
using System.Collections;
using System.IO;
using System.Text;
using System.Web.Security;
using System.Web.UI;
using System.Web.UI.WebControls;
using System.Web.UI.WebControls.WebParts;
using System.Web.UI.HtmlControls;


namespace Flowpie.Controllers
{
    public class OrderController :ApiController
    {
        /// <summary>
        /// 获取当天的排课安排
        /// </summary>
        /// <returns></returns>
        [HttpGet]
        public string LoadOrderByDay()
        {
            JxLib.OrderController orderController = new JxLib.OrderController();
            Models.Result result = new Models.Result();
            DatabaseLib.Tools tools = new DatabaseLib.Tools();

            HttpContextBase context = (HttpContextBase)Request.Properties["MS_HttpContext"];

            Hashtable data = tools.paramToData(context.Request.Params);

            List<Hashtable> list = orderController.loadByDayNotMe(data["RunDate"].ToString(), data["SchoolID"].ToString(), data["StudentID"].ToString());

            if (orderController.Result)
            {
                result.code = "200";
                result.message = "获取成功!";

                System.Text.StringBuilder strData = new System.Text.StringBuilder();
                int index = 0;

                foreach (Hashtable item in list)
                {
                    Models.Teach teach = new Models.Teach();

                    teach.TeachID = item["TeachID"].ToString();
                    teach.Amount = item["Amount"].ToString();
                    teach.CoachID = item["CoachID"].ToString();
                    teach.CouponID = item["CouponID"].ToString();
                    teach.ModifyAt = item["ModifyAt"].ToString();
                    teach.CreateAt = item["CreateAt"].ToString();
                    teach.PayAmount = item["PayAmount"].ToString();
                    teach.RunDate = item["RunDate"].ToString();
                    teach.SchoolID = item["SchoolID"].ToString();
                    teach.Score = item["Score"].ToString();
                    teach.State = item["State"].ToString();
                    teach.StudentID = item["StudentID"].ToString();
                    teach.Time = item["Time"].ToString();
                    teach.WeekNum = item["WeekNum"].ToString();

                    string str_json = Newtonsoft.Json.JsonConvert.SerializeObject(teach);

                    if (index > 0)
                        strData.Append(",");

                    strData.Append(str_json);

                    index++;
                }

                result.count = list.Count.ToString();
                result.data = strData.ToString().Replace("[", "{").Replace("]", "}");
            }
            else
            {
                result.code = "0";
                result.message = "获取当天的训练安排失败!";
            }

            return Newtonsoft.Json.JsonConvert.SerializeObject(result).Replace("\"", "'");
        }

        /// <summary>
        /// 获取用户的当天安排
        /// </summary>
        /// <returns></returns>
        [HttpGet]
        public string LoadMyOrderByDay()
        {
            JxLib.OrderController orderController = new JxLib.OrderController();
            Models.Result result = new Models.Result();
            DatabaseLib.Tools tools = new DatabaseLib.Tools();

            HttpContextBase context = (HttpContextBase)Request.Properties["MS_HttpContext"];

            Hashtable data = tools.paramToData(context.Request.Form);

            List<Hashtable> list = orderController.loadByStudentID(data["RunDate"].ToString(), data["SchoolID"].ToString(), data["StudentID"].ToString());

            if (orderController.Result)
            {
                result.code = "200";
                result.message = "获取成功!";

                System.Text.StringBuilder strData = new System.Text.StringBuilder();
                int index = 0;

                foreach (Hashtable item in list)
                {
                    Models.Teach teach = new Models.Teach();

                    teach.TeachID = item["TeachID"].ToString();
                    teach.Amount = item["Amount"].ToString();
                    teach.CoachID = item["CoachID"].ToString();
                    teach.CouponID = item["CouponID"].ToString();
                    teach.ModifyAt = item["ModifyAt"].ToString();
                    teach.CreateAt = item["CreateAt"].ToString();
                    teach.PayAmount = item["PayAmount"].ToString();
                    teach.RunDate = item["RunDate"].ToString();
                    teach.SchoolID = item["SchoolID"].ToString();
                    teach.Score = item["Score"].ToString();
                    teach.State = item["State"].ToString();
                    teach.StudentID = item["StudentID"].ToString();
                    teach.Time = item["Time"].ToString();
                    teach.WeekNum = item["WeekNum"].ToString();

                    string str_json = Newtonsoft.Json.JsonConvert.SerializeObject(teach);

                    if (index > 0)
                        strData.Append(",");

                    strData.Append(str_json);

                    index++;
                }

                result.count = list.Count.ToString();
                result.data = strData.ToString().Replace("[", "{").Replace("]", "}");
            }
            else
            {
                result.code = "0";
                result.message = "获取用户的当天训练安排失败!";
            }

            return Newtonsoft.Json.JsonConvert.SerializeObject(result).Replace("\"", "'");
        }

        [HttpPost]
        public string OrderAdd()
        {
            JxLib.OrderController orderController = new JxLib.OrderController();
            JxLib.TrainingController trainingController = new JxLib.TrainingController();
            JxLib.StudentController studentController = new JxLib.StudentController();

            SystemConfigureLib.SerialNumberController serialController = new SystemConfigureLib.SerialNumberController();
            Models.Result result = new Models.Result();
            DatabaseLib.Tools tools = new DatabaseLib.Tools();

            HttpContextBase context = (HttpContextBase)Request.Properties["MS_HttpContext"];

            Hashtable data = tools.paramToData(context.Request.Form);

            Hashtable config = trainingController.loadTraining(data["SchoolID"].ToString(), data["WeekNum"].ToString());
            Hashtable student = studentController.load(data["StudentID"].ToString());

            string[] times = data["Time"].ToString().Split(',');

            decimal amount = 0;

            if (student["LicenseTypeID"].ToString() == "1")
            {
                amount = times.Length * Convert.ToDecimal(config["C1"].ToString());
            }
            else
            {
                amount = times.Length * Convert.ToDecimal(config["C2"].ToString());
            }

            data.Add("Amount", amount.ToString());
            data.Add("PayAmount", amount.ToString());

            string serial_id = serialController.getSerialNumber("ord", DateTime.Now.ToString("yyyy-MM-dd"));

            data.Add("TeachID", serial_id);

            orderController.add(data);

            if (orderController.Result)
            {
                result.code = "200";
                result.message = "订单成功!";
                result.data = serial_id;
            }
            else
            {
                result.code = "0";
                result.message = orderController.Message;
            }

            return Newtonsoft.Json.JsonConvert.SerializeObject(result).Replace("\"", "'");
        }

        [HttpPost]
        public string UpdateAmount()
        {
            JxLib.OrderController orderController = new JxLib.OrderController();
            JxLib.CouponController couponController = new JxLib.CouponController();
          
            Models.Result result = new Models.Result();
            DatabaseLib.Tools tools = new DatabaseLib.Tools();

            HttpContextBase context = (HttpContextBase)Request.Properties["MS_HttpContext"];

            Hashtable data = tools.paramToData(context.Request.Form);

            orderController.updatePayAmount(data["orderid"].ToString(), data["couponid"].ToString(), data["payamount"].ToString());

            if (orderController.Result)
            {
                result.code = "200";
                result.message = "订单成功!";

                Hashtable param = new Hashtable();

                param.Add("CouponID", data["couponid"].ToString());

                couponController.useCoupon(param);

                if (!couponController.Result)
                {
                    result.code = "0";
                    result.message = "优惠卷扣除失败!:"+couponController.Message;
                }
            }
            else
            {
                result.code = "0";
                result.message = orderController.Message;
            }

            return Newtonsoft.Json.JsonConvert.SerializeObject(result).Replace("\"", "'");
        }

        [HttpPost]
        public string addOrderDetail()
        {
            JxLib.OrderController orderController = new JxLib.OrderController();

            SystemConfigureLib.SerialNumberController serialController = new SystemConfigureLib.SerialNumberController();
            Models.Result result = new Models.Result();
            DatabaseLib.Tools tools = new DatabaseLib.Tools();

            HttpContextBase context = (HttpContextBase)Request.Properties["MS_HttpContext"];

            Hashtable data = tools.paramToData(context.Request.Form);

            string[] TeachTypeIDs = data["TeachTypeID"].ToString().Split(',');

            foreach (string str in TeachTypeIDs)
            {
                Hashtable info = new Hashtable();

                string serial_id = serialController.getSerialNumber("odl", DateTime.Now.ToString("yyyy-MM-dd"));

                info.Add("TeachDetailID", serial_id);
                info.Add("TeachID", data["orderid"].ToString());
                info.Add("TeachTypeID", str);

                orderController.addDetail(info);

                if (!orderController.Result)
                    break;
            }

            if (orderController.Result)
            {
                result.code = "200";
                result.message = "练习明细添加成功!";
            }
            else
            {
                result.code = "0";
                result.message = orderController.Message;
            }

            return Newtonsoft.Json.JsonConvert.SerializeObject(result).Replace("\"", "'");
        }

        [HttpPost]
        public string setCoach()
        {
            JxLib.OrderController orderController = new JxLib.OrderController();

            SystemConfigureLib.SerialNumberController serialController = new SystemConfigureLib.SerialNumberController();
            Models.Result result = new Models.Result();
            DatabaseLib.Tools tools = new DatabaseLib.Tools();

            HttpContextBase context = (HttpContextBase)Request.Properties["MS_HttpContext"];

            Hashtable data = tools.paramToData(context.Request.Form);

            string[] teach_ids = data["teach_ids"].ToString().Split(',');
            string[] coach_ids = data["coach_ids"].ToString().Split(',');

            for (int i = 0; i < teach_ids.Length; i++)
            {
                orderController.saveCoach(teach_ids[i], coach_ids[i]);

                if (!orderController.Result)
                    break;
            }

            if (orderController.Result)
            {
                result.code = "200";
                result.message = "操作成功!";
            }
            else
            {
                result.code = "0";
                result.message = orderController.Message;
            }

            return Newtonsoft.Json.JsonConvert.SerializeObject(result).Replace("\"", "'");
        }

        [HttpPost]
        public string orderNext()
        {
            JxLib.OrderController orderController = new JxLib.OrderController();

            SystemConfigureLib.SerialNumberController serialController = new SystemConfigureLib.SerialNumberController();
            Models.Result result = new Models.Result();
            DatabaseLib.Tools tools = new DatabaseLib.Tools();

            HttpContextBase context = (HttpContextBase)Request.Properties["MS_HttpContext"];

            Hashtable data = tools.paramToData(context.Request.Form);

             orderController.nextState(data["orderid"].ToString());

            if (orderController.Result)
            {
                result.code = "200";
                result.message = "操作成功!";
            }
            else
            {
                result.code = "0";
                result.message = orderController.Message;
            }

            return Newtonsoft.Json.JsonConvert.SerializeObject(result).Replace("\"", "'");
        }
    }
}