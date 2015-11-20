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
    public class WeixinController : ApiController
    {
        string token = "jiaxiao";

        static string appId = "wx78b3b4daaed7f512";//公众号的appId
        static string appSecret = "5ba8c179baf309974fac686236591d15";//公众号的appSecret

        /*以下为公共接口调用URL*/
        static string appUrl = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential";
        static string postUrl = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=";
        static string postDelUrl = "https://api.weixin.qq.com/cgi-bin/menu/delete?access_token="; //删除菜单的URL

        [HttpPost]
        public void GetInfo()
        {
            NetLog.WriteTextLog("微信请求回复", "获取到请求", DateTime.Now);

            HttpContextBase context = (HttpContextBase)Request.Properties["MS_HttpContext"];

            //if (context.Request.HttpMethod.ToUpper() == "GET")
            //{
            //    // 微信加密签名    
            //    string signature = context.Request.QueryString["signature"];
            //    // 时间戳    
            //    string timestamp = context.Request.QueryString["timestamp"];
            //    // 随机数    
            //    string nonce = context.Request.QueryString["nonce"];
            //    // 随机字符串    
            //    string echostr = context.Request.QueryString["echostr"];

            //    if (CheckSignature(signature, timestamp, nonce))
            //    {
            //        context.Response.Write(echostr);
            //        context.Response.End();
            //    }
            //    else
            //    {
            //        context.Response.Write("请求失败!");
            //        context.Response.End();
            //    }
            //}
            //else
            //{
                string weixin = "";
                weixin = PostInput();//获取xml数据

                if (!string.IsNullOrEmpty(weixin))
                {
                    ResponseMsg(weixin);////调用消息适配器
                }
            //}
        }

        #region 获取post请求数据
        /// <summary>
        /// 获取post请求数据
        /// </summary>
        /// <returns></returns>
        private string PostInput()
        {
            Stream s = System.Web.HttpContext.Current.Request.InputStream;
            byte[] b = new byte[s.Length];
            s.Read(b, 0, (int)s.Length);
            return Encoding.UTF8.GetString(b);
        }
        #endregion

        private ExmlMsg GetExmlMsg(XmlElement root)
        {
            ExmlMsg xmlMsg = new ExmlMsg() {
                FromUserName = root.SelectSingleNode("FromUserName").InnerText,
                ToUserName = root.SelectSingleNode("ToUserName").InnerText,
                CreateTime = root.SelectSingleNode("CreateTime").InnerText,
                MsgType = root.SelectSingleNode("MsgType").InnerText,
             };
             if (xmlMsg.MsgType.Trim().ToLower() == "text")
             {
                 xmlMsg.Content = root.SelectSingleNode("Content").InnerText;
             }
             else if (xmlMsg.MsgType.Trim().ToLower() == "event")
             {
                 xmlMsg.EventName = root.SelectSingleNode("Event").InnerText;
             }
             return xmlMsg;
        }

        private class ExmlMsg
        {
            /// <summary>
            /// 本公众账号
            /// </summary>
            public string ToUserName { get; set; }
            /// <summary>
            /// 用户账号
            /// </summary>
            public string FromUserName { get; set; }
            /// <summary>
            /// 发送时间戳
            /// </summary>
            public string CreateTime { get; set; }
            /// <summary>
            /// 发送的文本内容
            /// </summary>
            public string Content { get; set; }
            /// <summary>
            /// 消息的类型
            /// </summary>
            public string MsgType { get; set; }
            /// <summary>
            /// 事件名称
            /// </summary>
            public string EventName { get; set; } 
        }

        #region 操作文本消息 + void textCase(XmlElement root)
        private void textCase(ExmlMsg xmlMsg)
        {
            HttpContextBase context = (HttpContextBase)Request.Properties["MS_HttpContext"];
            int nowtime = GetCreateTime();
            string msg = "终于等到你，还好我没放弃！哈哈欢迎关注云e驾！么么哒 U+1F60A \n";
            msg += "云e驾是汇聚正规专业驾校的第三方互联网学车平台 U+1F44F \n";
            msg += "让每一位学员能学的轻松快乐舒心，是我们最大的目标 U+1F61D \n";
            msg += "想学车的小伙伴们，现在学车只需要3800，学多少算多少  \n";
            msg += "一起来挑战云e驾学车，开启学车新模式-- -“计时收费”\n";
            msg += "关注云e驾立刻领取学车优惠卷和体验卷，参与活动赢学车名额噢！ U+1F339 \n";
            //msg = getText(xmlMsg);
            string resxml = "<xml><ToUserName><![CDATA[" + xmlMsg.FromUserName + "]]></ToUserName><FromUserName><![CDATA[" + xmlMsg.ToUserName + "]]></FromUserName><CreateTime>" + nowtime + "</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA[" + msg + "]]></Content></xml>";
            //string resxml = "<xml><ToUserName><![CDATA[" + xmlMsg.FromUserName + "]]></ToUserName><FromUserName><![CDATA[" + xmlMsg.ToUserName + "]]></FromUserName><CreateTime>" + nowtime + "</CreateTime><MsgType><![CDATA[news]]></MsgType><ArticleCount>1</ArticleCount><Articles><item><Title><![CDATA[云e驾学车咨询群获取更多一手资料，名额有限哦~]]></Title><Description><![CDATA[" + msg + "]]></Description><PicUrl><![CDATA[http://wx.yune-jia.com/jx/img/title.jpg]] ></PicUrl><Url><![CDATA[http://wx.yune-jia.com]]></Url></item></Articles ></xml>";

            context.Response.Write(resxml);

        }
        //
        #endregion

        private string getText(ExmlMsg xmlMsg)
        {
            string con = xmlMsg.Content.Trim();

            System.Text.StringBuilder retsb = new StringBuilder(200);
            retsb.Append("这里放你的业务逻辑");
            retsb.Append("接收到的消息：" + xmlMsg.Content);
            retsb.Append("用户的OPEANID：" + xmlMsg.FromUserName);

            return retsb.ToString();
        }
        //public static String emoji(int hexEmoji)
        //{
        //    return String.valueOf(Character.toChars(hexEmoji));
        //}


        #region 消息类型适配器
        private void ResponseMsg(string weixin)// 服务器响应微信请求
        {
            HttpContextBase context = (HttpContextBase)Request.Properties["MS_HttpContext"];
            XmlDocument doc = new XmlDocument();
            doc.LoadXml(weixin);//读取xml字符串
            XmlElement root = doc.DocumentElement;
            ExmlMsg xmlMsg = GetExmlMsg(root);
            //XmlNode MsgType = root.SelectSingleNode("MsgType");
            //string messageType = MsgType.InnerText;
            string messageType = xmlMsg.MsgType;//获取收到的消息类型。文本(text)，图片(image)，语音等。


            try
            {

                switch (messageType)
                {
                    //当消息为文本时
                    case "text":
                        //textCase(xmlMsg);
                        break;
                    case "event":
                        if (!string.IsNullOrEmpty(xmlMsg.EventName) && xmlMsg.EventName.Trim() == "subscribe")
                        {
                            //刚关注时的时间，用于欢迎词  
                            int nowtime = GetCreateTime();
                            string msg = "终于等到你，还好我没放弃！哈哈欢迎关注云e驾！么么哒 \ue105 \n";
                            msg += "云e驾是汇聚正规专业驾校的第三方互联网学车平台 \ue41f \n";
                            msg += "让每一位学员能学的轻松快乐舒心，是我们最大的目标  \ue130 \n";
                            msg += "想学车的小伙伴们，现在学车只需要3800，学多少算多少  \n";
                            msg += "一起来挑战云e驾学车，开启学车新模式-- -“计时收费 \ue01b”\n";
                            msg += "关注云e驾立刻领取学车优惠卷和体验卷，参与活动赢学车名额噢！ \ue302 \n";
                            string resxml = "<xml><ToUserName><![CDATA[" + xmlMsg.FromUserName + "]]></ToUserName><FromUserName><![CDATA[" + xmlMsg.ToUserName + "]]></FromUserName><CreateTime>" + nowtime + "</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA["+ msg + "]]></Content></xml>";

                            //string resxml = "<xml><ToUserName><![CDATA[" + xmlMsg.FromUserName + "]]></ToUserName><FromUserName><![CDATA[" + xmlMsg.ToUserName + "]]></FromUserName><CreateTime>" + nowtime + "</CreateTime><MsgType><![CDATA[news]]></MsgType><ArticleCount>1</ArticleCount><Articles><item><Title><![CDATA[云e驾学车咨询群获取更多一手资料，名额有限哦~]]></Title><Description><![CDATA[" + msg + "]]></Description><PicUrl><![CDATA[http://wx.yune-jia.com/jx/img/title.jpg]] ></PicUrl><Url><![CDATA[http://wx.yune-jia.com]]></Url></item></Articles ></xml>";

                            context.Response.Write(resxml);
                        }
                        break;
                    case "image":
                        break;
                    case "voice":
                        break;
                    case "vedio":
                        break;
                    case "location":
                        break;
                    case "link":
                        break;
                    default:
                        break;
                }
                context.Response.End();
            }
            catch (Exception ex)
            {
                NetLog.WriteTextLog("微信请求回复", ex.Message, DateTime.Now);
            }
        }
        #endregion

        /// <summary>  
        /// 验证签名  
        /// </summary>  
        /// <param name="signature"></param>  
        /// <param name="timestamp"></param>  
        /// <param name="nonce"></param>  
        /// <returns></returns>  
        private bool CheckSignature(String signature, String timestamp, String nonce)
        {
            String[] arr = new String[] { token, timestamp, nonce };
            // 将token、timestamp、nonce三个参数进行字典序排序    
            Array.Sort<String>(arr);

            StringBuilder content = new StringBuilder();
            for (int i = 0; i < arr.Length; i++)
            {
                content.Append(arr[i]);
            }

            String tmpStr = SHA1_Encrypt(content.ToString());


            // 将sha1加密后的字符串可与signature对比，标识该请求来源于微信    
            return tmpStr != null ? tmpStr.Equals(signature) : false;
        }


        /// <summary>  
        /// 使用缺省密钥给字符串加密  
        /// </summary>  
        /// <param name="Source_String"></param>  
        /// <returns></returns>  
        private string SHA1_Encrypt(string Source_String)
        {
            byte[] StrRes = Encoding.Default.GetBytes(Source_String);
            HashAlgorithm iSHA = new SHA1CryptoServiceProvider();
            StrRes = iSHA.ComputeHash(StrRes);
            StringBuilder EnText = new StringBuilder();
            foreach (byte iByte in StrRes)
            {
                EnText.AppendFormat("{0:x2}", iByte);
            }
            return EnText.ToString();
        }

        /// <summary>  
        /// 处理微信发来的请求   
        /// </summary>  
        /// <param name="xml"></param>  
        public void processRequest(String xml)
        {
            HttpContextBase context = (HttpContextBase)Request.Properties["MS_HttpContext"];

            try
            {
                // xml请求解析    
                Hashtable requestHT = ParseXml(xml);

                // 发送方帐号（open_id）    
                string fromUserName = (string)requestHT["FromUserName"];
                // 公众帐号    
                string toUserName = (string)requestHT["ToUserName"];
                // 消息类型    
                string msgType = (string)requestHT["MsgType"];

                NetLog.WriteTextLog("微信请求回复", "获取用户名"+ fromUserName, DateTime.Now);
                //
                string resxml =
                            "<xml>"
                            + "<ToUserName><![CDATA[" + toUserName + "]]></ToUserName>"
                            + "<FromUserName><![CDATA[" + fromUserName + "]]></FromUserName>"
                            + "<CreateTime>" + GetCreateTime() + "</CreateTime>"
                            + "<MsgType><![CDATA[text]]></MsgType>"
                            //+ "<Content><![CDATA[终于等到你，还好我没放弃！哈哈欢饮关注云e驾！么么哒云e驾是汇聚正规专业驾校的第三方互联网学车平台让每一位学员能学的轻松快乐舒心，是我们最大的目标想学车的小伙伴们，现在学车只需要3800，学多少算多少一起来挑战云e驾学车，开启学车新模式-- -“计时收费”关注云e驾立刻领取学车优惠卷和体验卷，参与活动赢学车名额噢！]]></Content>"
                            + "<FuncFlag>0</FuncFlag>"
                            + "</xml>";
                NetLog.WriteTextLog("微信请求回复", "准备发送回复消息", DateTime.Now);
                context.Response.Write(resxml);
                context.Response.End();
            }
            catch (Exception e)
            {
                NetLog.WriteTextLog("微信请求回复异常", e.Message, DateTime.Now);
                context.Response.Write(e.Message);
                context.Response.End();
            }
        }

        private int GetCreateTime()//创建时间戳
        {
            DateTime dateStart = new DateTime(1970, 1, 1, 8, 0, 0);//格林威治时间1970，1，1，0，0，0
            return (int)(DateTime.Now - dateStart).TotalSeconds;
        }

        /// <summary>
        /// 将xml文件转换成Hashtable
        /// </summary>
        /// <param name="xml"></param>
        /// <returns></returns>
        private static Hashtable ParseXml(String xml)
        {
            XmlDocument xmlDocument = new XmlDocument();
            xmlDocument.LoadXml(xml);
            XmlNode bodyNode = xmlDocument.ChildNodes[0];
            Hashtable ht = new Hashtable();
            if (bodyNode.ChildNodes.Count > 0)
            {
                foreach (XmlNode xn in bodyNode.ChildNodes)
                {
                    ht.Add(xn.Name, xn.InnerText);
                }
            }
            return ht;
        }

        [HttpGet]
        public void CreateWxMenu()
        {

            string weixin1 = GetWXMenuStr();

            PostMenuData(postUrl + GetAccessToken(), weixin1);
        }

        //获得ACCESS_TOKEN，通过appid和app_secect获得（订阅号）
        public static string GetAccessToken()
        {
            WebClient webClient = new WebClient();
            Byte[] bytes = webClient.DownloadData(string.Format("{0}&appid={1}&secret={2}", appUrl, appId, appSecret));
            string result = Encoding.GetEncoding("utf-8").GetString(bytes);

            //JObject jObj = JObject.Parse(result);      
            //JToken token = jObj["access_token"];     
            //return token.ToString().Substring(1, token.ToString().Length - 2);  

            string[] temp = result.Split(',');
            string[] tp = temp[0].Split(':');
            return tp[1].ToString().Replace('"', ' ').Trim().ToString();

        }

        //创建微信菜单JSON字符串
        public string GetWXMenuStr()
        {
            string weixin1 = "";
            weixin1 += "{\n";
            weixin1 += "\"button\":[\n";
            weixin1 += "{\n";
            // weixin1 += "\"type\":\"click\",\n";
            //第一个菜单
            weixin1 += "\"type\":\"view\",\n";
            weixin1 += "\"key\":\"rselfmenu_0_0\",\n";
            weixin1 += "\"name\":\"云e驾\",\n";
            weixin1 += "\"url\":\"http://wx.yune-jia.com\"\n";
            weixin1 += "},\n";
            //第二个菜单
            weixin1 += "{\n";
            weixin1 += "\"type\":\"view\",\n";
            weixin1 += "\"key\":\"rselfmenu_0_1\",\n";
            weixin1 += "\"name\":\"关于我们\",\n";
            weixin1 += "\"url\":\"http://wx.yune-jia.com/jx/html/pg05-aboutus.html\"\n";
            weixin1 += "}]\n";

            weixin1 += "}\n";
            return weixin1;
        }

        private void PostMenuData(string url, string postData)
        {
            Stream outstream = null;
            Stream instream = null;
            StreamReader sr = null;
            HttpWebResponse response = null;
            HttpWebRequest request = null;
            Encoding encoding = Encoding.UTF8;
            byte[] data = encoding.GetBytes(postData);
            // 准备请求...
            try
            {
                // 设置参数
                request = WebRequest.Create(url) as HttpWebRequest;
                CookieContainer cookieContainer = new CookieContainer();
                request.CookieContainer = cookieContainer;
                request.AllowAutoRedirect = true;
                request.Method = "POST";
                request.ContentType = "application/x-www-form-urlencoded";
                request.ContentLength = data.Length;
                outstream = request.GetRequestStream();
                outstream.Write(data, 0, data.Length);
                outstream.Close();
                //发送请求并获取相应回应数据
                response = request.GetResponse() as HttpWebResponse;
                //直到request.GetResponse()程序才开始向目标网页发送Post请求
                instream = response.GetResponseStream();
                sr = new StreamReader(instream, encoding);
                //返回结果网页（html）代码
                string content = sr.ReadToEnd();
                string err = string.Empty;
                // return content;
            }
            catch (Exception ex)
            {
                NetLog.WriteTextLog("微信创建菜单异常", ex.Message, DateTime.Now);
            }
        }
    }
}