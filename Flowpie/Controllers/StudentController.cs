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
    }
}