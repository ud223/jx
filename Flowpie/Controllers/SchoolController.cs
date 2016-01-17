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
    public class SchoolController : ApiController
    {
        public string OnCoupon()
        {
            JxLib.SchoolController schoolController = new JxLib.SchoolController();
            DatabaseLib.Tools tools = new DatabaseLib.Tools();
            Models.Result result = new Models.Result();

            HttpContextBase context = (HttpContextBase)Request.Properties["MS_HttpContext"];
            string strParam = context.Request.Form.ToString();

            System.Collections.Hashtable data = tools.paramToData(strParam);

            schoolController.onCoupon(data["SchoolID"].ToString(), data["CouponText"].ToString(), data["CouponAmount"].ToString(), data["CouponRemark"].ToString());

            if (schoolController.Result)
            {
                result.code = "200";
                result.message = "保存优惠卷发放配置成功!";
            }
            else
            {
                result.code = "0";
                result.message = schoolController.Message;
            }

            return Newtonsoft.Json.JsonConvert.SerializeObject(result).Replace("\"", "'");
        }

        public string OffCoupon()
        {
            JxLib.SchoolController schoolController = new JxLib.SchoolController();
            DatabaseLib.Tools tools = new DatabaseLib.Tools();
            Models.Result result = new Models.Result();

            HttpContextBase context = (HttpContextBase)Request.Properties["MS_HttpContext"];
            string strParam = context.Request.Form.ToString();

            System.Collections.Hashtable data = tools.paramToData(strParam);

            schoolController.offCoupon(data["SchoolID"].ToString());

            if (schoolController.Result)
            {
                result.code = "200";
                result.message = "关闭优惠卷发放成功!";
            }
            else
            {
                result.code = "0";
                result.message = schoolController.Message;
            }

            return Newtonsoft.Json.JsonConvert.SerializeObject(result).Replace("\"", "'");
        }
    }
}