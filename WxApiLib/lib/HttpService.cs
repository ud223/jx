using System;
using System.Collections.Generic;
using System.Web;
using System.Net;
using System.IO;
using System.Text;
using System.Net.Security;
using System.Security.Authentication;
using System.Security.Cryptography.X509Certificates;

namespace WxApiLib.lib
{
    /// <summary>
    /// http连接基础类，负责底层的http通信
    /// </summary>
    public class HttpService
    {

        public static bool CheckValidationResult(object sender, X509Certificate certificate, X509Chain chain, SslPolicyErrors errors)
        {
            //直接确认，否则打不开    
            return true;
        }

        public static string Post(string xml, string url, bool isUseCert, int timeout)
        {
            //System.GC.Collect();//垃圾回收，回收没有正常关闭的http连接

            //string result = "";//返回结果

            //HttpWebRequest request = null;
            //HttpWebResponse response = null;
            //Stream reqStream = null;

            //try
            //{
            //    //设置最大连接数
            //    ServicePointManager.DefaultConnectionLimit = 200;
            //    //设置https验证方式
            //    if (url.StartsWith("https", StringComparison.OrdinalIgnoreCase))
            //    {
            //        ServicePointManager.ServerCertificateValidationCallback =
            //                new RemoteCertificateValidationCallback(CheckValidationResult);
            //    }

            //    /***************************************************************
            //    * 下面设置HttpWebRequest的相关属性
            //    * ************************************************************/
            //    request = (HttpWebRequest)WebRequest.Create(url);

            //    Log.Debug("HttpService", "2.1:Pay url : " + url);

            //    request.Method = "POST";
            //    request.Timeout = timeout * 1000;

            //    //设置代理服务器
            //    //WebProxy proxy = new WebProxy();                          //定义一个网关对象
            //    //proxy.Address = new Uri(WxPayConfig.PROXY_URL);              //网关服务器端口:端口
            //    //request.Proxy = proxy;

            //    //设置POST的数据类型和长度
            //    request.ContentType = "application/x-www-form-urlencoded"; ;
            //    byte[] data = System.Text.Encoding.UTF8.GetBytes(xml);
            //    request.ContentLength = data.Length;
            //    //request.ServicePoint.Expect100Continue = false;
            //    isUseCert = false;
            //    //是否使用证书
            //    if (isUseCert)
            //    {
            //        string path = HttpContext.Current.Request.PhysicalApplicationPath;
            //        X509Certificate2 cert = new X509Certificate2(path + WxPayConfig.SSLCERT_PATH, WxPayConfig.SSLCERT_PASSWORD);
            //        request.ClientCertificates.Add(cert);
            //        Log.Debug("WxPayApi", "PostXml used cert");
            //    }

            //    //往服务器写入数据
            //    reqStream = request.GetRequestStream();
            //    reqStream.Write(data, 0, data.Length);
            //    reqStream.Close();

            //    //获取服务端返回
            //    response = (HttpWebResponse)request.GetResponse();

            //    //获取服务端返回数据
            //    StreamReader sr = new StreamReader(response.GetResponseStream(), Encoding.UTF8);
            //    result = sr.ReadToEnd().Trim();
            //    sr.Close();
            //}
            //catch (System.Threading.ThreadAbortException e)
            //{
            //    Log.Error("HttpService", "Thread - caught ThreadAbortException - resetting.");
            //    Log.Error("Exception message: {0}", e.Message);
            //    System.Threading.Thread.ResetAbort();
            //}
            //catch (WebException e)
            //{
            //    Log.Error("HttpService", e.ToString());
            //    if (e.Status == WebExceptionStatus.ProtocolError)
            //    {
            //        Log.Error("HttpService", "StatusCode : " + ((HttpWebResponse)e.Response).StatusCode);
            //        Log.Error("HttpService", "StatusDescription : " + ((HttpWebResponse)e.Response).StatusDescription);
            //    }
            //    throw new WxPayException(e.ToString());
            //}
            //catch (Exception e)
            //{
            //    Log.Error("HttpService", e.ToString());
            //    throw new WxPayException(e.ToString());
            //}
            //finally
            //{
            //    //关闭连接和流
            //    if (response != null)
            //    {
            //        response.Close();
            //    }
            //    if (request != null)
            //    {
            //        request.Abort();
            //    }
            //}
            //return result;
            //WX_SendNews news = new WX_SendNews(); 
            //posturl： news.Posturl;
            //postData：news.PostData;
            System.IO.Stream outstream = null;
            Stream instream = null;
            StreamReader sr = null;
            HttpWebResponse response = null;
            HttpWebRequest request = null;
            Encoding encoding = Encoding.UTF8;
            byte[] data = encoding.GetBytes(xml);
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
                return content;
            }
            catch (Exception ex)
            {
                string err = ex.Message;
                Log.Error("HttpService", err);
                throw new WxPayException(err);
                //return string.Empty;
            }

        }

        /// <summary>
        /// 处理http GET请求，返回数据
        /// </summary>
        /// <param name="url">请求的url地址</param>
        /// <returns>http GET成功后返回的数据，失败抛WebException异常</returns>
        public static string Get(string url)
        {
            System.GC.Collect();
            string result = "";

            HttpWebRequest request = null;
            HttpWebResponse response = null;

            //请求url以获取数据
            try
            {
                //设置最大连接数
                ServicePointManager.DefaultConnectionLimit = 200;
                //设置https验证方式
                if (url.StartsWith("https", StringComparison.OrdinalIgnoreCase))
                {
                    ServicePointManager.ServerCertificateValidationCallback =
                            new RemoteCertificateValidationCallback(CheckValidationResult);
                }

                /***************************************************************
                * 下面设置HttpWebRequest的相关属性
                * ************************************************************/
                request = (HttpWebRequest)WebRequest.Create(url);

                request.Method = "GET";

                //设置代理
                //WebProxy proxy = new WebProxy();
                //proxy.Address = new Uri(WxPayConfig.PROXY_URL);
                //request.Proxy = proxy;

                //获取服务器返回
                response = (HttpWebResponse)request.GetResponse();

                //获取HTTP返回数据
                StreamReader sr = new StreamReader(response.GetResponseStream(), Encoding.UTF8);
                result = sr.ReadToEnd().Trim();
                sr.Close();
            }
            catch (System.Threading.ThreadAbortException e)
            {
                Log.Error("HttpService", "Thread - caught ThreadAbortException - resetting.");
                Log.Error("Exception message: {0}", e.Message);
                System.Threading.Thread.ResetAbort();
            }
            catch (WebException e)
            {
                Log.Error("HttpService", e.ToString());
                if (e.Status == WebExceptionStatus.ProtocolError)
                {
                    Log.Error("HttpService", "StatusCode : " + ((HttpWebResponse)e.Response).StatusCode);
                    Log.Error("HttpService", "StatusDescription : " + ((HttpWebResponse)e.Response).StatusDescription);
                }
                throw new WxPayException(e.ToString());
            }
            catch (Exception e)
            {
                Log.Error("HttpService", e.ToString());
                throw new WxPayException(e.ToString());
            }
            finally
            {
                //关闭连接和流
                if (response != null)
                {
                    response.Close();
                }
                if (request != null)
                {
                    request.Abort();
                }
            }
            return result;
        }
    }
}
