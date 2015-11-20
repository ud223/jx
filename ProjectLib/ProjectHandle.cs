//using System;
//using System.Collections.Generic;
//using System.Linq;
//using System.Text;
//using System.Data;

//namespace ProjectLib
//{
//    public class ProjectHandle : BaseLib.BaseClass
//    {
//        /// <summary>
//        /// 初始化项目配置基本信息
//        /// </summary>
//        /// <param name="project"></param>
//        public void Init(KernelLib.SystemEntity.ProjectEntity project)
//        {
//            DatabaseLib.DatabaseFactory dataFactory = new DatabaseLib.DatabaseFactory();
//            DatabaseLib.IDatabase dataClient = dataFactory.CreateClient(BaseLib.SystemType.Web);

//            string strSql = "SELECT * FROM s_projects WHERE ID = '@ID@'; SELECT s_project_modules.ID, s_project_modules.Project_ID, s_project_modules.Index, s_project_modules.Enable, s_modules.Text, s_modules.Entity_ID FROM s_project_modules JOIN s_modules ON s_project_modules.Module_ID = s_modules.ID WHERE s_project_modules.Project_ID = '@ID@' AND s_project_modules.Enable = 1; SELECT s_databases.* FROM s_databases JOIN s_projects ON s_databases.ID = s_projects.DB_ID WHERE s_projects.ID = '@ID@'; SELECT * FROM s_project_login_config WHERE Project_ID = '@ID@'";

//            strSql = strSql.Replace("@ID@", project.ID);

//            dataClient.SqlText = strSql;

//            DataSet ds = dataClient.Query();

//            this.Message = dataClient.Message;
//            this.Result = dataClient.Result;


//            if (this.Result)
//            {
//                //初始化项目基本信息
//                project.Text = ds.Tables[0].Rows[0]["Text"].ToString();
//                project.Db_ID = ds.Tables[0].Rows[0]["DB_ID"].ToString();
//                project.Create_Date = ds.Tables[0].Rows[0]["Create_Date"].ToString();
//                project.Enable = ds.Tables[0].Rows[0]["Enable"].ToString();
//                project.Expiry_Date = ds.Tables[0].Rows[0]["Expiry_Date"].ToString();
//                //初始化项目数据库配置信息
//                project.DbEntity.ID = ds.Tables[2].Rows[0]["ID"].ToString();
//                project.DbEntity.Name = ds.Tables[2].Rows[0]["Name"].ToString();
//                project.DbEntity.Text = ds.Tables[2].Rows[0]["Text"].ToString();
//                project.DbEntity.Server = ds.Tables[2].Rows[0]["Server"].ToString();
//                project.DbEntity.Uid = ds.Tables[2].Rows[0]["Uid"].ToString();
//                project.DbEntity.Pwd = ds.Tables[2].Rows[0]["Pwd"].ToString();
//                project.DbEntity.Port = ds.Tables[2].Rows[0]["Port"].ToString();
//                project.DbEntity.Data_Type = ds.Tables[2].Rows[0]["Data_Type"].ToString();
//                project.DbEntity.Create_Date = ds.Tables[2].Rows[0]["Create_Date"].ToString();
//                project.DbEntity.IsCreate = ds.Tables[2].Rows[0]["IsCreate"].ToString();
//                //初始化项目用户登录配置信息
//                project.LoginEntity.ID = ds.Tables[3].Rows[0]["ID"].ToString();
//                project.LoginEntity.LoginName = ds.Tables[3].Rows[0]["LoginName"].ToString();
//                project.LoginEntity.PwdName = ds.Tables[3].Rows[0]["PwdName"].ToString();
//                project.LoginEntity.Table_ID = ds.Tables[3].Rows[0]["Table_ID"].ToString();
//                project.LoginEntity.Project_ID = ds.Tables[3].Rows[0]["Project_ID"].ToString();

//                //循环绑定项目所包含模块实体数据
//                foreach (DataRow row in ds.Tables[1].Rows)
//                {
//                    KernelLib.SystemEntity.ModuleEntity moduleEntity = new KernelLib.SystemEntity.ModuleEntity();

//                    moduleEntity.ID = row["ID"].ToString();
//                    moduleEntity.Text = row["Text"].ToString();
//                    moduleEntity.Project_ID = row["Project_ID"].ToString();
//                    moduleEntity.Index = row["Index"].ToString();
//                    moduleEntity.Entity.ID = row["Entity_ID"].ToString();
//                    moduleEntity.Enable = row["Enable"].ToString();

//                    project.Modules.Add(moduleEntity);
//                }
//                //实例化数据库类库的配置信息，避免循环引用
//                DatabaseLib.Entity.DatabaseEntity databaseEntity = new DatabaseLib.Entity.DatabaseEntity();

//                databaseEntity.ID = project.DbEntity.ID;
//                databaseEntity.Name = project.DbEntity.Name;
//                databaseEntity.Text = project.DbEntity.Text;
//                databaseEntity.Uid = project.DbEntity.Uid;
//                databaseEntity.Pwd = project.DbEntity.Pwd;
//                databaseEntity.Port = project.DbEntity.Port;
//                databaseEntity.Data_Type = project.DbEntity.Data_Type;
//                databaseEntity.Create_Date = project.DbEntity.Create_Date;
//                databaseEntity.IsCreate = project.DbEntity.IsCreate;
//                //获取项目数据访问对象
//                project.DataClient = dataFactory.CreateClient(databaseEntity, BaseLib.SystemType.Web);
//            }
//        }
//        /// <summary>
//        /// 初始化并加载项目中所包含的模块
//        /// </summary>
//        /// <param name="project"></param>
//        public void LoadModule(KernelLib.SystemEntity.ProjectEntity project)
//        {
//            ModuleHandle modelHandle = new ModuleHandle();

//            foreach (KernelLib.SystemEntity.ModuleEntity moduleEntity in project.Modules)
//            {
//                modelHandle.Init(moduleEntity);
//            }
//        }

//        /// <summary>
//        /// 缓存项目实体信息
//        /// </summary>
//        /// <param name="project"></param>
//        public void Save(KernelLib.SystemEntity.ProjectEntity project)
//        {
//            CacheLib.Cache cache = new CacheLib.Cache();
//            CacheLib.Cookie cookie = new CacheLib.Cookie();

//            string key = cache.Add<KernelLib.SystemEntity.ProjectEntity>("pj", project);

//            cookie.AddCookie("project", key);
//        }

//        /// <summary>
//        /// 加载项目实体信息
//        /// </summary>
//        /// <returns></returns>
//        public KernelLib.SystemEntity.ProjectEntity Load()
//        {
//            CacheLib.Cache cache = new CacheLib.Cache();
//            CacheLib.Cookie cookie = new CacheLib.Cookie();

//            string key = cookie.GetCookie("project");

//            KernelLib.SystemEntity.ProjectEntity project = cache.Get<KernelLib.SystemEntity.ProjectEntity>(key);

//            return project;
//        }
//    }
//}
