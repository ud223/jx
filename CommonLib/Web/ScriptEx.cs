using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;

namespace CommonLib.Web
{
    public class ScriptEx
    {
        public static void MessageBox(string msg, System.Web.UI.Page page)
        {
            page.ClientScript.RegisterStartupScript(page.GetType(), "message", "<script type=\"text/javascript\">alert('" + msg + "');</script>");
        }

        public static void MessageBoxAndURLRedirect(string msg, string url, System.Web.UI.Page page)
        {
            page.ClientScript.RegisterStartupScript(page.GetType(), "messageurlredirect", "<script type=\"text/javascript\">alert('" + msg + "');top.Main_iframe.location.href='" + url + "';</script>");
        }

        public static void MessageBoxAndParentURLRedirect(string msg, string url, System.Web.UI.Page page)
        {
            page.ClientScript.RegisterStartupScript(page.GetType(), "messageurlredirect", "<script type=\"text/javascript\">alert('" + msg + "');top.location.href='" + url + "';</script>");
        }

        public static void URLRedirect(string url, System.Web.UI.Page page)
        {
            page.ClientScript.RegisterStartupScript(page.GetType(), "urlredirect", "<script type=\"text/javascript\">top.Main_iframe.location.href='" + url + "';</script>");
        }

        public static void ParentURLRedirect(string url, System.Web.UI.Page page)
        {
            page.ClientScript.RegisterStartupScript(page.GetType(), "urlredirect", "<script type=\"text/javascript\">top.location.href='" + url + "';</script>");
        }

        public static void OpenFormClose(System.Web.UI.Page page)
        {
            page.ClientScript.RegisterStartupScript(page.GetType(), "parenturlrefresh", "<script type=\"text/javascript\">top.Main_iframe.document.getElementById('autoback').click();OpenFormRemove();</script>");
        }
    }
}
