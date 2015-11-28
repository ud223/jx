using System;
using System.Configuration;
using System.Web.Configuration;
using System.Collections;


namespace CommonLib.Web
{
    /// <summary>
    /// ASP.Net(2.0-3.5)Web������
    /// </summary>
    public class WebTool
    {
        /// <summary>
        /// ��ȡ��̬�ļ���CDN·�����������ģʽ��ʹ��CDN��
        /// </summary>
        /// <param name="path">�ļ���վ·��</param>
        /// <returns>��װ��CDNͷ�Ժ��·��</returns>
        public static string CDN(string path)
        {
            string result = path;
            if (CommonLib.Common.ConfigReader.Read("CurrentEnv") == "prod")
            {
                string cdnDomain = CommonLib.Common.ConfigReader.Read("CdnDomain");
                // ��������������cdn����
                result = cdnDomain + path;
            }
            return result;
        }
    }
}
