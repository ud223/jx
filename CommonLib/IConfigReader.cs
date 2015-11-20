using System;
using System.Collections;
using System.Configuration;

namespace CommonLib
{
    /// <summary>
    /// 配置文件读取器抽象类
    /// </summary>
    public abstract class IConfigReader
    {
        /// <summary>
        /// 获取Web.Config文件实体
        /// </summary>
        protected Configuration objConfig;

        /// <summary>
        /// 获取Web.Config文件中appSettings节点
        /// </summary>
        protected AppSettingsSection objAppsettings;

        #region 抽象类虚方法(已实现)

        protected virtual void InitAppSettings()
        {
            this.objAppsettings = (AppSettingsSection)objConfig.GetSection("appSettings");
        }

        /// <summary>
        /// 读取一个节点的值
        /// </summary>
        /// <param name="strKey">节点的key</param>
        /// <returns></returns>
        public virtual string Read(string strKey)
        {
            if (objAppsettings != null)
            {
                return objAppsettings.Settings[strKey].Value;
            }

            return "";
        }

        /// <summary>
        /// 查询所有节点集合
        /// </summary>
        /// <returns></returns>
        public virtual Hashtable Query()
        {
            Hashtable Items = new Hashtable();

            if (objAppsettings != null)
            {
                foreach (KeyValueConfigurationElement node in objAppsettings.Settings)
                {
                    Items.Add(node.Key, node.Value);
                }
            }

            return Items;
        }

        /// <summary>
        /// 插入节点
        /// </summary>
        /// <param name="strKey">插入节点的key</param>
        /// <param name="strValue">插入节点的value</param>
        public virtual void Insert(string strKey, string strValue)
        {
            if (objAppsettings != null)
            {
                //判断插入节点是否已经存在
                if (IsExist(strKey))
                {
                    throw new Exception("节点已经存在!");
                }

                //插入节点
                objAppsettings.Settings.Add(strKey, strValue);

                //保存配置文件
                objConfig.Save(ConfigurationSaveMode.Modified);
            }
        }

        /// <summary>
        /// 批量插入配置节点
        /// </summary>
        /// <param name="hasItems">节点集合</param>
        public virtual void Insert(Hashtable hasItems)
        {
            if (objAppsettings != null)
            {
                //循环HashTable
                foreach (System.Collections.DictionaryEntry item in hasItems)
                {
                    if (!IsExist(item.Key.ToString()))
                        //给对应的节点赋值
                        objAppsettings.Settings.Add(item.Key.ToString(), item.Value.ToString());
                }

                //保存配置文件
                objConfig.Save(ConfigurationSaveMode.Modified);
            }
        }

        /// <summary>
        /// 移除配置文件节点
        /// </summary>
        /// <param name="strKey">节点的key</param>
        public virtual void Remove(string strKey)
        {
            if (objAppsettings.Settings != null)
            {
                //移除key对应的节点
                objAppsettings.Settings.Remove(strKey);

                //保存配置文件
                objConfig.Save(ConfigurationSaveMode.Modified);
            }
        }

        /// <summary>
        /// 批量插入配置节点
        /// </summary>
        /// <param name="hasItems">节点集合</param>
        public virtual void Remove(Hashtable hasItems)
        {
            if (objAppsettings != null)
            {
                //循环HashTable
                foreach (System.Collections.DictionaryEntry item in hasItems)
                {
                    if (IsExist(item.Key.ToString()))
                        //给对应的节点赋值
                        objAppsettings.Settings.Remove(item.Key.ToString());
                }

                //保存配置文件
                objConfig.Save(ConfigurationSaveMode.Modified);
            }
        }

        /// <summary>
        /// 更新节点
        /// </summary>
        /// <param name="strKey">要更新节点对应key</param>
        /// <param name="strValue">更新的值</param>
        public virtual void Update(string strKey, string strValue)
        {
            if (objAppsettings != null)
            {
                //给对应的节点赋值
                objAppsettings.Settings[strKey].Value = strValue;

                //保存配置文件
                objConfig.Save(ConfigurationSaveMode.Modified);
            }
        }

        /// <summary>
        /// 批量保存配置节点
        /// </summary>
        /// <param name="hasItems">节点集合</param>
        public virtual void Update(Hashtable hasItems)
        {
            if (objAppsettings != null)
            {
                //循环HashTable
                foreach (System.Collections.DictionaryEntry item in hasItems)
                {
                    //给对应的节点赋值
                    objAppsettings.Settings[item.Key.ToString()].Value = item.Value.ToString();
                }

                //保存配置文件
                objConfig.Save(ConfigurationSaveMode.Modified);
            }
        }

        /// <summary>
        /// 判断节点是否存在
        /// </summary>
        /// <param name="strKey">节点的key</param>
        /// <returns></returns>
        public virtual bool IsExist(string strKey)
        {
            if (objAppsettings != null)
            {
                if (objAppsettings.Settings[strKey] != null)
                    return true;
            }

            return false;
        }

        #endregion

        #region 抽象方法

        /// <summary>
        /// 实例操作对象
        /// </summary>
        public abstract void Init();

        /// <summary>
        /// 加密配置文件的配置节点
        /// </summary>
        public virtual void EncryptAppSettings()
        {
           if (!objAppsettings.SectionInformation.IsProtected)
            {
                //选接加密提供方式
                objAppsettings.SectionInformation.ProtectSection("RsaProtectedConfigurationProvider");
                objAppsettings.SectionInformation.ForceSave = true;

                //保存加密后的配置文件
                objConfig.Save(ConfigurationSaveMode.Modified);
            }
        }

        /// <summary>
        /// 解密配置文件
        /// </summary>
        public virtual void DecryptAppSettings()
        {
            //判断是否加密
            if (objAppsettings.SectionInformation.IsProtected)
            {
                //解密配置文件
                objAppsettings.SectionInformation.UnprotectSection();
                objAppsettings.SectionInformation.ForceSave = true;

                //保存配置文件
                objConfig.Save(ConfigurationSaveMode.Modified);
            }
        }

        #endregion
    }
}
