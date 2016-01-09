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
    public class TrainingController : ApiController
    {
        public string LoadConfigByWeekNum()
        {
            JxLib.TrainingController trainingController = new JxLib.TrainingController();
            Models.Result result = new Models.Result();
            DatabaseLib.Tools tools = new DatabaseLib.Tools();

            HttpContextBase context = (HttpContextBase)Request.Properties["MS_HttpContext"];

            System.Collections.Hashtable data = tools.paramToData(context.Request.Form);

            Hashtable item = trainingController.loadTraining(data["SchoolID"].ToString(), data["WeekNum"].ToString());

            if (item != null)
            {
                Models.TrainingConfig config = new Models.TrainingConfig();

                config.C1 = item["C1"].ToString();
                config.C2 = item["C2"].ToString();
                config.Num = item["Num"].ToString();
                config.SchoolID = item["SchoolID"].ToString();
                config.Time = item["Time"].ToString();
                config.WeekNum = item["WeekNum"].ToString();
                config.IsEnable = item["IsEnable"].ToString();

                string str_json = Newtonsoft.Json.JsonConvert.SerializeObject(config);

                result.code = "200";
                result.message = "获取成功!";
                result.data = Newtonsoft.Json.JsonConvert.SerializeObject(config);
            }
            else
            {
                result.code = "0";
                result.message = "获取训练计划失败!";
            }

            return Newtonsoft.Json.JsonConvert.SerializeObject(result).Replace("\"", "'");
        }

        public string LoadConfig()
        {
            JxLib.TrainingController trainingController = new JxLib.TrainingController();
            Models.Result result = new Models.Result();
            DatabaseLib.Tools tools = new DatabaseLib.Tools();

            HttpContextBase context = (HttpContextBase)Request.Properties["MS_HttpContext"];

            System.Collections.Hashtable data = tools.paramToData(context.Request.Form);

            List<Hashtable> list = trainingController.getAllBySchoolID(data["SchoolID"].ToString());

            StringBuilder strConfig = new StringBuilder();

            int index = 0;

            foreach (Hashtable item in list)
            {
                Models.TrainingConfig config = new Models.TrainingConfig();

                config.C1 = item["C1"].ToString();
                config.C2 = item["C2"].ToString();
                config.Num = item["Num"].ToString();
                config.SchoolID = item["SchoolID"].ToString();
                config.Time = item["Time"].ToString();
                config.WeekNum = item["WeekNum"].ToString();
                config.IsEnable = item["IsEnable"].ToString();

                string str_json = Newtonsoft.Json.JsonConvert.SerializeObject(config);

                if (index > 0)
                    strConfig.Append(",");

                strConfig.Append(str_json);

                index++;
            }

            if (list == null)
            {
                result.code = "0";
                result.message = "获取训练计划失败!";
            }
            else
            {
                result.code = "200";
                result.message = "获取成功!";
                result.count = list.Count.ToString();
                result.data = strConfig.ToString().Replace("[", "{").Replace("]", "}");
            }

            return Newtonsoft.Json.JsonConvert.SerializeObject(result).Replace("\"", "'");
        }

        public string Config()
        {
            JxLib.TrainingController trainingController = new JxLib.TrainingController();
            Models.Result result = new Models.Result();
            DatabaseLib.Tools tools = new DatabaseLib.Tools();

            HttpContextBase context = (HttpContextBase)Request.Properties["MS_HttpContext"];

            System.Collections.Hashtable data = tools.paramToData(context.Request.Form);

            Hashtable training = trainingController.loadTraining(data["SchoolID"].ToString(), data["WeekNum"].ToString());

            if (training != null)
            {
                trainingController.delete(training["TrainingConfigID"].ToString());
            }

            string training_id = trainingController.add(data);

            if (trainingController.Result)
            {
                result.code = "200";
                result.message = "保存成功!";
            }
            else
            {
                result.code = "0";
                result.message = trainingController.Message;
            }

            return Newtonsoft.Json.JsonConvert.SerializeObject(result).Replace("\"", "'");
        }
    }
}