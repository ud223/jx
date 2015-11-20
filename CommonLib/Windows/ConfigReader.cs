using System;
using System.Configuration;

namespace CommonLib.Windows
{
    /// <summary>
    /// Config文件配置类(2.0)
    /// </summary>
    public class ConfigReader : IConfigReader
    {
        /// <summary>
        /// 构造操作实例
        /// </summary>
        public ConfigReader()
        {
            this.Init();
        }

        /// <summary>
        /// 初始化基本属性
        /// </summary>
        public override void Init()
        {
            this.objConfig = ConfigurationManager.OpenExeConfiguration(ConfigurationUserLevel.None);

            this.InitAppSettings();
        }
    }
}
