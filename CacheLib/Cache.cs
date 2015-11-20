using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Web;
using System.Web.Caching;

namespace CacheLib
{
    public class Cache
    {
        /// <summary>
        /// 获取对像
        /// </summary>
        /// <param name="key"></param>
        /// <returns>返回一个对象</returns>
        public T Get<T>(string key)
        {
            return (T)HttpContext.Current.Cache[key];
        }

        /// <summary>
        /// 缓存一个对象
        /// </summary>
        /// <param name="keyPix">保存key前缀</param>
        /// <param name="obj">保存对象</param>
        /// <returns>缓存标识key</returns>
        public string Add<T>(string keyPix, T obj)
        {
            string key = keyPix + DateTime.Now.ToString("yyyyMMddHHmmssfff");
            string[] keys = { key };

            HttpContext.Current.Cache.Insert(key, obj, null, DateTime.Now.AddHours(8), TimeSpan.Zero);

            return key;
        }

        /// <summary>
        /// 清除cache缓存
        /// </summary>
        /// <param name="key"></param>
        public void Remove(string key)
        {
            HttpContext.Current.Cache.Remove(key);
        }
    }
}
