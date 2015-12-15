using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;

namespace Flowpie.Models
{
    public class Permission
    {
        public string PermissionID { get; set; }
        public string MenuID { get; set; }
        public string UserTypeID { get; set; }
        public string IsAdd { get; set; }
        public string IsModify { get; set; }
        public string IsDelete { get; set; }
        public string SelectType { get; set; }
    }
}