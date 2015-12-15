using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;

namespace Flowpie.Models
{
    public class User
    {
        /// <summary>
        /// 用户ID
        /// </summary>
        public string UserID { get; set; }
        /// <summary>
        /// 用户名
        /// </summary>
        public string Name { get; set; }
        /// <summary>
        /// 用户头像
        /// </summary>
        public string HeadPic { get; set; }
        /// <summary>
        /// 用户电子邮箱
        /// </summary>
        public string Email { get; set; }
        /// <summary>
        /// 用户联系手机
        /// </summary>
        public string Phone { get; set; }
        /// <summary>
        /// 用户角色类型ID
        /// </summary>
        public string UserTypeID { get; set; }
        /// <summary>
        /// 用户角色类型
        /// </summary>
        public string UserTypeText { get; set; }
        /// <summary>
        /// 所属驾校ID
        /// </summary>
        public string SchoolID { get; set; }
        /// <summary>
        /// 所属驾校
        /// </summary>
        public string SchoolText { get; set; }
        /// <summary>
        /// 权限列表
        /// </summary>
        public List<Permission> Permissions { get; set; }
    }
}