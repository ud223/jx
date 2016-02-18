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
using System.Drawing;
using System.Drawing.Imaging;

namespace Flowpie.Controllers
{
    public class CoachController : ApiController
    {
        public string Application()
        {
            JxLib.CoachController coachController = new JxLib.CoachController();
            DatabaseLib.Tools tools = new DatabaseLib.Tools();
            Models.Result result = new Models.Result();

            HttpContextBase context = (HttpContextBase)Request.Properties["MS_HttpContext"];
            string strParam = context.Request.Form.ToString();

            System.Collections.Hashtable data = tools.paramToData(strParam);

            coachController.saveNameAndPhoen(data);

            if (coachController.Result)
            {
                string coachapplication_id = coachController.add(data);

                if (coachController.Result)
                {
                    result.code = "200";
                    result.message = "申请成功!";
                }
                else
                {
                    result.code = "0";
                    result.message = coachController.Message;
                }
            }
            else
            {
                result.code = "0";
                result.message = coachController.Message;
            }

            return Newtonsoft.Json.JsonConvert.SerializeObject(result).Replace("\"", "'");
        }

        public string Apply()
        {
            JxLib.CoachController coachController = new JxLib.CoachController();
            DatabaseLib.Tools tools = new DatabaseLib.Tools();
            Models.Result result = new Models.Result();

            HttpContextBase context = (HttpContextBase)Request.Properties["MS_HttpContext"];
            string strParam = context.Request.Form.ToString();

            System.Collections.Hashtable data = tools.paramToData(strParam);

            data.Add("State", "2");

            coachController.application(data);

            Hashtable item = coachController.load(data["CoachApplicationID"].ToString());

            coachController.toCoach(item["StudentID"].ToString(), item["SchoolID"].ToString());

            if (coachController.Result)
            {
                result.code = "200";
                result.message = "审核成功!";
            }
            else
            {
                result.code = "0";
                result.message = coachController.Message;
            }

            return Newtonsoft.Json.JsonConvert.SerializeObject(result).Replace("\"", "'");
        }

        public string Reject()
        {
            JxLib.CoachController coachController = new JxLib.CoachController();
            DatabaseLib.Tools tools = new DatabaseLib.Tools();
            Models.Result result = new Models.Result();

            HttpContextBase context = (HttpContextBase)Request.Properties["MS_HttpContext"];
            string strParam = context.Request.Form.ToString();

            System.Collections.Hashtable data = tools.paramToData(strParam);

            data.Add("State", "3");

            coachController.application(data);

            if (coachController.Result)
            {
                result.code = "200";
                result.message = "拒绝成功!";
            }
            else
            {
                result.code = "0";
                result.message = coachController.Message;
            }

            return Newtonsoft.Json.JsonConvert.SerializeObject(result).Replace("\"", "'");
        }

        public string Update()
        {
            JxLib.CoachController coachController = new JxLib.CoachController();
            DatabaseLib.Tools tools = new DatabaseLib.Tools();
            Models.Result result = new Models.Result();

            HttpContextBase context = (HttpContextBase)Request.Properties["MS_HttpContext"];
            string strParam = context.Request.Form.ToString();

            System.Collections.Hashtable data = tools.paramToData(strParam);

            string image = data["image"].ToString();

            if (image == "")
            {
                data.Add("HeadPic", data["headpic"].ToString());
            }
            else
            {
                image = image.IndexOf("data:image/jpeg;base64,") > -1 ? image.Replace("data:image/jpeg;base64,", "") : image;
                image = image.Replace("data:image/png;base64,", "");

                byte[] arr = Convert.FromBase64String(image);

                MemoryStream ms = new MemoryStream(arr);
                Bitmap bmp = new Bitmap(ms);


                Bitmap bmp2 = new Bitmap(bmp.Width, bmp.Height, PixelFormat.Format16bppRgb555);

                Graphics g = Graphics.FromImage(bmp2);

                g.DrawImage(bmp, new Rectangle(0, 0, bmp.Width, bmp.Height));

                g.Dispose();

                bmp.Dispose();

                ms.Close();

                string path = AppDomain.CurrentDomain.BaseDirectory + "photo/";
                string filename = DateTime.Now.ToString("yyyyMMddhhmmssfff") + ".jpg";

                string headpic = "/photo/" + filename;

                bmp2.Save(Path.Combine(path, filename));

                bmp2.Dispose();

                data.Add("HeadPic", headpic);
            }

            coachController.save(data);

            if (coachController.Result)
            {
                result.code = "200";
                result.message = "保存成功!";
            }
            else
            {
                result.code = "0";
                result.message = coachController.Message;
            }

            return Newtonsoft.Json.JsonConvert.SerializeObject(result).Replace("\"", "'");
        }

        public string Freeze()
        {
            JxLib.CoachController coachController = new JxLib.CoachController();
            DatabaseLib.Tools tools = new DatabaseLib.Tools();
            Models.Result result = new Models.Result();

            HttpContextBase context = (HttpContextBase)Request.Properties["MS_HttpContext"];
            string strParam = context.Request.Form.ToString();

            System.Collections.Hashtable data = tools.paramToData(strParam);

            coachController.freeze(data["CoachID"].ToString());

            if (coachController.Result)
            {
                result.code = "200";
                result.message = "冻结成功!";
            }
            else
            {
                result.code = "0";
                result.message = coachController.Message;
            }

            return Newtonsoft.Json.JsonConvert.SerializeObject(result).Replace("\"", "'");
        }

        public string UnFreeze()
        {
            JxLib.CoachController coachController = new JxLib.CoachController();
            DatabaseLib.Tools tools = new DatabaseLib.Tools();
            Models.Result result = new Models.Result();

            HttpContextBase context = (HttpContextBase)Request.Properties["MS_HttpContext"];
            string strParam = context.Request.Form.ToString();

            System.Collections.Hashtable data = tools.paramToData(strParam);

            coachController.unFreeze(data["CoachID"].ToString());

            if (coachController.Result)
            {
                result.code = "200";
                result.message = "解冻成功!";
            }
            else
            {
                result.code = "0";
                result.message = coachController.Message;
            }

            return Newtonsoft.Json.JsonConvert.SerializeObject(result).Replace("\"", "'");
        }
    }
}