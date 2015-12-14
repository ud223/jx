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
using System.Security.Cryptography;
using System.Xml;

namespace Flowpie.Controllers
{
    public class PermissionController : ApiController
    {
        public string PermissionAdd()
        {
            SystemConfigureLib.PermissionController permissionController = new SystemConfigureLib.PermissionController();
            Models.Result result = new Models.Result();
            DatabaseLib.Tools tools = new DatabaseLib.Tools();

            HttpContextBase context = (HttpContextBase)Request.Properties["MS_HttpContext"];

            System.Collections.Hashtable data = tools.paramToData(context.Request.Form);

            string permission_id = permissionController.add(data);

            if (permission_id == null || permission_id == "")
            {
                result.code = "200";
                result.message = "添加成功!";
            }
            else
            {
                result.code = "0";
                result.message = "该快递单已经添加!";
            }

            return Newtonsoft.Json.JsonConvert.SerializeObject(result).Replace("\"", "'");
        }
    }
}