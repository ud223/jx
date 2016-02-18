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

namespace Flowpie.tools
{
    public class Sms
    {
        public static string PostUrl = "http://106.ihuyi.cn/webservice/sms.php?method=Submit";
        [HttpPost]
        public bool SendSms(string mobile, string content)
        {
            string account = "cf_yunejia";
            string password = "yunejia123";//密码可以使用明文密码或使用32位MD5加密
            Random rad = new Random();
            int mobile_code = rad.Next(1000, 10000);

            string postStrTpl = "account={0}&password={1}&mobile={2}&content={3}";

            UTF8Encoding encoding = new UTF8Encoding();
            byte[] postData = encoding.GetBytes(string.Format(postStrTpl, account, password, mobile, content));

            HttpWebRequest myRequest = (HttpWebRequest)WebRequest.Create(PostUrl);
            myRequest.Method = "POST";
            myRequest.ContentType = "application/x-www-form-urlencoded";
            myRequest.ContentLength = postData.Length;

            Stream newStream = myRequest.GetRequestStream();
            // Send the data.
            newStream.Write(postData, 0, postData.Length);
            newStream.Flush();
            newStream.Close();

            HttpWebResponse myResponse = (HttpWebResponse)myRequest.GetResponse();
            Models.Result result = new Models.Result();

            if (myResponse.StatusCode == HttpStatusCode.OK)
            {
                StreamReader reader = new StreamReader(myResponse.GetResponseStream(), Encoding.UTF8);

                string res = reader.ReadToEnd();
                int len1 = res.IndexOf("</code>");
                int len2 = res.IndexOf("<code>");
                string code = res.Substring((len2 + 6), (len1 - len2 - 6));

                int len3 = res.IndexOf("</msg>");
                int len4 = res.IndexOf("<msg>");
                string msg = res.Substring((len4 + 5), (len3 - len4 - 5));

                return true;
            }
            else
            {
                return false;
            }
        }
    }
}