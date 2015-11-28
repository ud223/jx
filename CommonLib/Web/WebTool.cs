using System;
using System.Configuration;
using System.Web.Configuration;
using System.Collections;


namespace CommonLib.Web
{
    /// <summary>
    /// ASP.Net(2.0-3.5)Web工具类
    /// </summary>
    public class WebTool
    {
        /// <summary>
        /// 获取静态文件的CDN路径（如果开发模式则不使用CDN）
        /// </summary>
        /// <param name="path">文件网站路径</param>
        /// <returns>包装了CDN头以后的路径</returns>
        public static string CDN(string path)
        {
            string result = path;
            if (CommonLib.Common.ConfigReader.Read("CurrentEnv") == "prod")
            {
                string cdnDomain = CommonLib.Common.ConfigReader.Read("CdnDomain");
                // 生产环境，出现cdn域名
                result = cdnDomain + path;
            }
            return result;
        }
    }
}
