using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Web;

namespace CacheLib
{
    public class Cookie
    {
        public string GetCookie(string key)
        {
            if (System.Web.HttpContext.Current.Request.Cookies[key] != null)
            {
                string _cstr = System.Web.HttpContext.Current.Request.Cookies[key].Value.ToString();

                Encoding stre = Encoding.GetEncoding("UTF-8");

                return System.Web.HttpUtility.UrlDecode(_cstr, stre);
            }

            return string.Empty;
        }

        public void AddCookie(string key, string value)
        {
            this.Delete(key);

            HttpCookie cookie = new HttpCookie(key);

            cookie.Expires = DateTime.Now.AddDays(1);
            cookie.Value = value;

            HttpContext.Current.Response.Cookies.Add(cookie);
        }

        public void Delete(string key)
        {
            if (HttpContext.Current.Request.Cookies[key] != null)
            {
                HttpContext.Current.Response.Cookies[key].Expires = DateTime.Now.AddDays(-1);
            }
        }
    }
}
