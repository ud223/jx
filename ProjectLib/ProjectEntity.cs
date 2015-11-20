//using System;
//using System.Collections.Generic;
//using System.Linq;
//using System.Text;

//namespace KernelLib.SystemEntity
//{
//    public class ProjectEntity
//    {
//        private string _sID;

//        public string ID
//        { get { return this._sID; } set { this._sID = value; } }

//        private string _sText;

//        public string Text
//        { get { return this._sText; } set { this._sText = value; } }

//        private string _sEnable;

//        public string Enable
//        { get { return this._sEnable; } set { this._sEnable = value; } }

//        private string _sDb_ID;

//        public string Db_ID
//        { get { return this._sDb_ID; } set { this._sDb_ID = value; } }

//        private string _sCreate_Date;

//        public string Create_Date
//        { get { return this._sCreate_Date; } set { this._sCreate_Date = value; } }

//        private string _sExpiry_Date;

//        public string Expiry_Date
//        { get { return this._sExpiry_Date; } set { this._sExpiry_Date = value; } }

//        private KernelLib.DatabaseEntity.DatabaseEntity _DbEntity;

//        public KernelLib.DatabaseEntity.DatabaseEntity DbEntity
//        {
//            get
//            {
//                if (this._DbEntity == null)
//                    this._DbEntity = new KernelLib.DatabaseEntity.DatabaseEntity();

//                return this._DbEntity;
//            }
//        }

//        private DatabaseLib.IDatabase _dataClient;

//        public DatabaseLib.IDatabase DataClient
//        { 
//            get
//            {
//                if (this._dataClient == null)
//                    throw new Exception("还没有初始化数据处理对象!");

//                return this._dataClient;
//            }
//            set
//            {
//                this._dataClient = value;
//            }
//        }

//        private List<ModuleEntity> _Modules;

//        public List<ModuleEntity> Modules
//        {
//            get

//            {
//                if (this._Modules == null)
//                    this._Modules = new List<ModuleEntity>();

//                return this._Modules;
//            }
//        }

//        private KernelLib.SystemEntity.LoginEntity _LoginEntity;

//        public KernelLib.SystemEntity.LoginEntity LoginEntity
//        { 
//            get 
//            {
//                if (this._LoginEntity == null)
//                    this._LoginEntity = new KernelLib.SystemEntity.LoginEntity();

//                return this._LoginEntity;
//            } 
//        }
//    }
//}
