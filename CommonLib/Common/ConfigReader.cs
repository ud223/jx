using System;
using BaseLib;
using System.Collections;

namespace CommonLib.Common
{
    /// <summary>
    /// 通用配置文件读取器
    /// </summary>
    public class ConfigReader
    {
        private static Windows.ConfigReader _oWindowsConfigReader = null;

        private static Web.ConfigReader _oWebConfigReader = null;

        private static IConfigReader _oConfigReader = null;

        /// <summary>
        /// 创建合适的配置文件读取器
        /// </summary>
        /// <param name="type">系统类型</param>
        private static IConfigReader CreateReader(SystemType type)
        {
            switch (type)
            {
                case SystemType.Windows:
                    {
                        if (_oWindowsConfigReader == null)
                            _oWindowsConfigReader = new Windows.ConfigReader();

                        _oWindowsConfigReader.Init();

                        return _oWindowsConfigReader;
                    }
                case SystemType.Web:
                    {
                        if (_oWebConfigReader == null)
                            _oWebConfigReader = new Web.ConfigReader();

                        _oWebConfigReader.Init();

                        return _oWebConfigReader;
                    }
                default:
                    {
                        throw new Exception("不明系统类型");
                    }
            }
        }

        /// <summary>
        /// 读取一个Web.config节点的值
        /// </summary>
        /// <param name="strKey">节点的key</param>
        /// <returns></returns>
        public static string Read(string strKey, SystemType type)
        {
            _oConfigReader = CreateReader(type);

            return _oConfigReader.Read(strKey);
        }

        /// <summary>
        /// 读取一个Web.config节点的值（默认用BaseLib.SystemType.Web）
        /// </summary>
        /// <param name="strKey">节点的key</param>
        /// <returns></returns>
        public static string Read(string strKey)
        {
            return Read(strKey, BaseLib.SystemType.Web);
        }

        /// <summary>
        /// 查询所有节点集合
        /// </summary>
        /// <returns></returns>
        public static Hashtable Query(SystemType type)
        {
            _oConfigReader = CreateReader(type);

            return _oConfigReader.Query();
        }

        /// <summary>
        /// 插入节点
        /// </summary>
        /// <param name="strKey">插入节点的key</param>
        /// <param name="strValue">插入节点的value</param>
        public static void Insert(string strKey, string strValue, SystemType type)
        {
            _oConfigReader = CreateReader(type);

            _oConfigReader.Insert(strKey, strValue);
        }

        /// <summary>
        /// 批量插入配置节点
        /// </summary>
        /// <param name="hasItems">节点集合</param>
        public static void Insert(Hashtable hasItems, SystemType type)
        {
            _oConfigReader = CreateReader(type);

            _oConfigReader.Insert(hasItems);
        }

        /// <summary>
        /// 移除配置文件节点
        /// </summary>
        /// <param name="strKey">节点的key</param>
        public static void Remove(string strKey, SystemType type)
        {
            _oConfigReader = CreateReader(type);

            _oConfigReader.Remove(strKey);
        }

        /// <summary>
        /// 批量移除配置节点
        /// </summary>
        /// <param name="hasItems">节点集合</param>
        public static void Remove(Hashtable hasItems, SystemType type)
        {
            _oConfigReader = CreateReader(type);

            _oConfigReader.Remove(hasItems);
        }

        /// <summary>
        /// 更新节点
        /// </summary>
        /// <param name="strKey">要更新节点对应key</param>
        /// <param name="strValue">更新的值</param>
        public static void Update(string strKey, string strValue, SystemType type)
        {
            _oConfigReader = CreateReader(type);

            _oConfigReader.Update(strKey, strValue);
        }

        /// <summary>
        /// 批量保存配置节点
        /// </summary>
        /// <param name="hasItems">节点结合</param>
        public static void Update(Hashtable hasItems, SystemType type)
        {
            _oConfigReader = CreateReader(type);

            _oConfigReader.Update(hasItems);
        }

        /// <summary>
        /// 判断节点是否存在
        /// </summary>
        /// <param name="strKey">节点的key</param>
        /// <returns></returns>
        public static bool IsExist(string strKey, SystemType type)
        {
            _oConfigReader = CreateReader(type);

            return _oConfigReader.IsExist(strKey);
        }

        /// <summary>
        /// 加密配置文件的配置节点
        /// </summary>
        public static void EncryptAppSettings(SystemType type)
        {
            _oConfigReader = CreateReader(type);

            _oConfigReader.EncryptAppSettings();
        }

        /// <summary>
        /// 解密配置文件
        /// </summary>
        public static void DecryptAppSettings(SystemType type)
        {
            _oConfigReader = CreateReader(type);

            _oConfigReader.DecryptAppSettings();
        }
    }
}
