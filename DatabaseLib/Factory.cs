using System;
using System.Reflection;

namespace DatabaseLib
{
    /// <summary>
    /// 数据库工厂类
    /// </summary>
    public class DatabaseFactory : BaseLib.BaseClass
    {
        private string _sNameSpace = "DatabaseLib";
        private string _sClientPath = "Client";
        private string _sClassPath = "DatabaseLib.Client.";
        private string _sClassName = "";

        /// <summary>
        /// 初始化并验证数据库类型
        /// </summary>
        /// <returns></returns>
        public void GetDataType(BaseLib.SystemType type, string strDataType)
        {
            int nDataType = 0;

            if (strDataType == null || strDataType == "")
                strDataType = CommonLib.Common.ConfigReader.Read("DataType", type);

            if (!CommonLib.Common.Validate.IsNumber(strDataType))
            {
                this.Message = "数据库类型配置错误!";
                this.Result = false;
            }

            nDataType = Int32.Parse(strDataType);

            _sClassName = ((DataType)nDataType).ToString();

            if (CommonLib.Common.Validate.IsNumber(_sClassName))
            {
                _sClassName = "";

                this.Message = "数据库类型配置错误!";
                this.Result = false;
            }
        }

        /*!----------------------------------异常未处理-------------------------------------------*/
        public IDatabase CreateClient(BaseLib.SystemType tmpSystemType)
        {
            IDatabase dDatabase = null;

            try
            {
                //获取对应的数据库类型
                Type ClientType = Type.GetType(_sClassPath + ((DataType)(Int32.Parse(CommonLib.Common.ConfigReader.Read("DataType", tmpSystemType)))).ToString(), true);

                //创建对应的数据库操作实例并返回给接口使外层代码通过接口能找到数据库操作实现代码
                dDatabase = (IDatabase)Activator.CreateInstance(ClientType);

                dDatabase.SystemType = tmpSystemType;

                dDatabase.Server = CommonLib.Common.ConfigReader.Read("Server", tmpSystemType);
                dDatabase.Uid = CommonLib.Common.ConfigReader.Read("UID", tmpSystemType);
                dDatabase.Password = CommonLib.Common.ConfigReader.Read("PWD", tmpSystemType);
                dDatabase.DatabaseName = CommonLib.Common.ConfigReader.Read("Database", tmpSystemType);
                dDatabase.Port = CommonLib.Common.ConfigReader.Read("Port", tmpSystemType);
            }
            catch (Exception ex)
            {
                //获取操作异常的错误信息
                this.Message = ex.Message;
                this.Result = false;
            }

            //返回操作实例代码
            return dDatabase;
        }

        public IDatabase CreateClient(DatabaseLib.Entity.DatabaseEntity dbEntity, BaseLib.SystemType tmpSystemType)
        {
            IDatabase dDatabase = null;

            try
            {
                //获取对应的数据库类型
                Type ClientType = Type.GetType(_sClassPath + ((DataType)(Int32.Parse(dbEntity.Data_Type))).ToString(), true);

                //创建对应的数据库操作实例并返回给接口使外层代码通过接口能找到数据库操作实现代码
                dDatabase = (IDatabase)Activator.CreateInstance(ClientType);

                dDatabase.SystemType = tmpSystemType;

                dDatabase.Server = dbEntity.Server;
                dDatabase.Uid = dbEntity.Uid;
                dDatabase.Password = dbEntity.Pwd;
                dDatabase.DatabaseName = dbEntity.Name;
                dDatabase.Port = dbEntity.Port;
            }
            catch (Exception ex)
            {
                //获取操作异常的错误信息
                this.Message = ex.Message;
                this.Result = false;
            }

            //返回操作实例代码
            return dDatabase;
        }
    }
}
