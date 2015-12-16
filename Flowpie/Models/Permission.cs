using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;

namespace Flowpie.Models
{
    public class Permission
    {
        public string PermissionID { get; set; }
        /// <summary>
        /// 权限菜单的页面名称集合
        /// </summary>
        public string AccessFile { get; set; }
        public string MenuID { get; set; }
        public string UserTypeID { get; set; }
        public string IsAdd { get; set; }
        public string IsModify { get; set; }
        public string IsDelete { get; set; }
        public string SelectType { get; set; }
    }
}