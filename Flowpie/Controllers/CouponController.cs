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
    public class CouponController : ApiController
    {
        [HttpPost]
        public string AddTimeCoupon()
        {
            JxLib.CouponController couponController = new JxLib.CouponController();
            DatabaseLib.Tools tools = new DatabaseLib.Tools();
            Models.Result result = new Models.Result();

            HttpContextBase context = (HttpContextBase)Request.Properties["MS_HttpContext"];
            string strParam = context.Request.Form.ToString();

            System.Collections.Hashtable data = tools.paramToData(strParam);

            data.Add("CouponText", "学时券");
            data.Add("Amount", "0");
            data.Add("Password", "");
            data.Add("IsUse", "0");
            data.Add("CouponTypeID", "2");
            data.Add("Expire", DateTime.Now.AddMonths(3).ToString("yyyy-MM-dd"));
            data.Add("IsExpire", "0");

            string coupon_id = couponController.add(data);

            if (couponController.Result)
            {
                result.code = "200";
                result.message = "发送成功!";
            }
            else
            {
                result.code = "0";
                result.message = couponController.Message;
            }

            return Newtonsoft.Json.JsonConvert.SerializeObject(result).Replace("\"", "'");
        }

        [HttpGet]
        public string getCoupon()
        {
            JxLib.CouponController couponController = new JxLib.CouponController();
            Models.Result result = new Models.Result();
            DatabaseLib.Tools tools = new DatabaseLib.Tools();

            HttpContextBase context = (HttpContextBase)Request.Properties["MS_HttpContext"];

            Hashtable data = tools.paramToData(context.Request.Params);

            List<Hashtable> list = couponController.getByStuentId(data["StudentID"].ToString());

            if (couponController.Result)
            {
                result.code = "200";
                result.message = "获取成功!";

                System.Text.StringBuilder strData = new System.Text.StringBuilder();
                int index = 0;

                foreach (Hashtable item in list)
                {
                    if (item["Expire"].ToString() == "null")
                    {
                        continue;
                    }

                    Models.Coupon coupon = new Models.Coupon();

                    coupon.Amount = item["Amount"].ToString();
                    coupon.CouponID = item["CouponID"].ToString();
                    coupon.CouponText = item["CouponText"].ToString();
                    coupon.CouponTypeID = item["CouponTypeID"].ToString();

                    coupon.CreateAt = DateTime.Parse(item["CreateAt"].ToString()).ToString("yyyy年MM月dd日"); 

                    coupon.Expire = DateTime.Parse(item["Expire"].ToString()).ToString("yyyy年MM月dd日");
                    coupon.IsExpire = item["IsExpire"].ToString();

                    coupon.IsUse = item["IsUse"].ToString();
                    coupon.ModifyAt = item["ModifyAt"].ToString();
                    coupon.Password = item["Password"].ToString();
                    coupon.StudentID = item["StudentID"].ToString();

                    if (coupon.CouponTypeID == "2")
                    {
                        DateTime tmp_date = Convert.ToDateTime(coupon.Expire);
                    }
                   

                    string str_json = Newtonsoft.Json.JsonConvert.SerializeObject(coupon);

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
                result.message = "获取优惠券失败!";
            }

            return Newtonsoft.Json.JsonConvert.SerializeObject(result).Replace("\"", "'");
        }
    }
}