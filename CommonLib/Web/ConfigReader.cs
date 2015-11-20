using System;
using System.Configuration;
using System.Web.Configuration;
using System.Collections;


namespace CommonLib.Web
{
    /// <summary>
    /// ASP.Net(2.0-3.5)Web.Config读取器
    /// </summary>
    public class ConfigReader : IConfigReader
    {
        public ConfigReader()
        {
            this.Init();
        }

        /// <summary>
        /// 实例化操作对象
        /// </summary>
        public override void Init()
        {
            try
            {
                this.objConfig = WebConfigurationManager.OpenWebConfiguration("~");

                this.InitAppSettings();
            }
            catch (Exception ex)
            { 
            
            }
        }

        /// <summary>
        /// 加密配置文件的配置节点
        /// </summary>
        public override void EncryptAppSettings()
        {
            //获取配置文件的物理路径
            Configuration objEncryConfig = WebConfigurationManager.OpenWebConfiguration(System.Web.HttpContext.Current.Request.ApplicationPath);

            //获取文件中的appSettings节点
            AppSettingsSection objEncryAppsettings = (AppSettingsSection)objEncryConfig.GetSection("appSettings");

            if (!objEncryAppsettings.SectionInformation.IsProtected)
            {
                //选接加密提供方式
                objEncryAppsettings.SectionInformation.ProtectSection("RsaProtectedConfigurationProvider");
                objEncryAppsettings.SectionInformation.ForceSave = true;

                //保存加密后的配置文件
                objEncryConfig.Save(ConfigurationSaveMode.Modified);
            }
        }

        /// <summary>
        /// 解密配置文件
        /// </summary>
        public override void DecryptAppSettings()
        {
            //获取配置文件的物理路径
            Configuration objDecConfig = WebConfigurationManager.OpenWebConfiguration(System.Web.HttpContext.Current.Request.ApplicationPath);

            //获取文件中的appSettings节点
            AppSettingsSection objDecAppsettings = (AppSettingsSection)objDecConfig.GetSection("appSettings");

            //判断是否加密
            if (objDecAppsettings.SectionInformation.IsProtected)
            {
                //解密配置文件
                objDecAppsettings.SectionInformation.UnprotectSection();
                objDecAppsettings.SectionInformation.ForceSave = true;

                //保存配置文件
                objDecConfig.Save(ConfigurationSaveMode.Modified);
            }
        }
    }
}
