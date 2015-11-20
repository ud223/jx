using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Runtime.InteropServices;
using System.Web;

namespace CommonLib.Web
{
    public class ClientInformations
    {
        [DllImport("Iphlpapi.dll")]
        private static extern int SendARP(Int32 dest, Int32 host, ref Int64 mac, ref Int32 length);
        [DllImport("Ws2_32.dll")]
        private static extern Int32 inet_addr(string ip);

        public string GetClientIP()
        {
            string result = String.Empty;
            result = HttpContext.Current.Request.ServerVariables["HTTP_X_FORWARDED_FOR"];
             
            //判断是否有代理或中转路由
            if (result != null && result != String.Empty)
             {
                 //可能有代理 
                 if (result.IndexOf(".") == -1)     //没有“.”肯定是非IPv4格式 
                     result = null;
                 else
                 {
                     if (result.IndexOf(",") != -1)
                     {
                         //有“,”，估计多个代理。取第一个不是内网的IP。 
                         result = result.Replace(" ", "").Replace("'", "");
                         string[] temparyip = result.Split(",;".ToCharArray());
                         for (int i = 0; i < temparyip.Length; i++)
                         {
                             if (temparyip[i].Substring(0, 3) != "10."
                                 && temparyip[i].Substring(0, 7) != "192.168"
                                 && temparyip[i].Substring(0, 7) != "172.16.")
                             {
                                 return temparyip[i];     //找到不是内网的地址 
                             }
                         }
                     }
                     else
                         return result;
                 }
             }

            string IpAddress = (HttpContext.Current.Request.ServerVariables["HTTP_X_FORWARDED_FOR"] != null && HttpContext.Current.Request.ServerVariables["HTTP_X_FORWARDED_FOR"] != String.Empty) ? HttpContext.Current.Request.ServerVariables["HTTP_X_FORWARDED_FOR"] : HttpContext.Current.Request.ServerVariables["REMOTE_ADDR"];

            if (null == result || result == String.Empty)
                result = HttpContext.Current.Request.ServerVariables["REMOTE_ADDR"];

            if (result == null || result == String.Empty)
                result = HttpContext.Current.Request.UserHostAddress;
            
            return result; 
        } 
    }
}
