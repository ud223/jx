using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;
using System.Web.Mvc;
using System.Web.Routing;

namespace Flowpie
{
    public class RouteConfig
    {
        public static void RegisterRoutes(RouteCollection routes)
        {
            routes.IgnoreRoute("{resource}.axd/{*pathInfo}");

            routes.MapRoute(
                name: "index",
                url: "",
                defaults: new { controller = "Home", action = "Index", id = UrlParameter.Optional }
            );

            #region 驾校action

            routes.MapRoute(
               name: "home",
               url: "home",
               defaults: new { controller = "Home", action = "Home", id = UrlParameter.Optional }
            );
            
            routes.MapRoute(
               name: "reg-user",
               url: "reg/user",
               defaults: new { controller = "Home", action = "RegUser", id = UrlParameter.Optional }
            );

            routes.MapRoute(
               name: "my-index",
               url: "my/index",
               defaults: new { controller = "Home", action = "MyIndex", id = UrlParameter.Optional }
            );

            routes.MapRoute(
               name: "school-list",
               url: "school/list",
               defaults: new { controller = "Home", action = "SchoolList", id = UrlParameter.Optional }
            );

            routes.MapRoute(
               name: "school-detail",
               url: "school/detail/{id}",
               defaults: new { controller = "Home", action = "SchoolDetail", id = UrlParameter.Optional }
            );

            routes.MapRoute(
               name: "student-enter",
               url: "student/enter",
               defaults: new { controller = "Home", action = "StudentEnter", id = UrlParameter.Optional }
            );
            
            routes.MapRoute(
               name: "student-enter-save",
               url: "student/enter/save",
               defaults: new { controller = "Home", action = "StudentEnterSave", id = UrlParameter.Optional }
            );

            routes.MapRoute(
               name: "enter-success",
               url: "enter/success",
               defaults: new { controller = "Home", action = "EnterSuccess", id = UrlParameter.Optional }
            );

            routes.MapRoute(
               name: "my-message",
               url: "my/message",
               defaults: new { controller = "Home", action = "MyMessage", id = UrlParameter.Optional }
            );

            routes.MapRoute(
               name: "message-detail",
               url: "message/detail",
               defaults: new { controller = "Home", action = "MessageDetail", id = UrlParameter.Optional }
            );

            routes.MapRoute(
               name: "my-coupon",
               url: "my/coupon",
               defaults: new { controller = "Home", action = "MyCoupon", id = UrlParameter.Optional }
            );

            #endregion;

            #region 考试

            routes.MapRoute(
                name: "exam-menu",
                url: "Exam/Menu",
                defaults: new { controller = "Home", action = "ExamMenu", id = UrlParameter.Optional }
            );

            
            routes.MapRoute(
                name: "exam-sequence",
                url: "Exam/Sequence",
                defaults: new { controller = "Home", action = "SequenceExam", id = UrlParameter.Optional }
            );

            routes.MapRoute(
                name: "exam-simulate",
                url: "exam/simulate",
                defaults: new { controller = "Home", action = "SimulateExam", id = UrlParameter.Optional }
            );

            routes.MapRoute(
                name: "exam-simulate-start",
                url: "exam/simulate/start",
                defaults: new { controller = "Home", action = "SimulateExamStart", id = UrlParameter.Optional }
            );

            routes.MapRoute(
                name: "exam-simulate-end",
                url: "exam/simulate/end",
                defaults: new { controller = "Home", action = "SimulateExamEnd", id = UrlParameter.Optional }
            );

            routes.MapRoute(
                name: "my-exam",
                url: "my/exam",
                defaults: new { controller = "Home", action = "MyExam", id = UrlParameter.Optional }
            );

            #endregion;

            /************************************************************
            *登陆及权限路由
            *
            *************************************************************/
            routes.MapRoute(
               name: "login",
               url: "login",
               defaults: new { controller = "Account", action = "Login", id = UrlParameter.Optional }
            );

            //routes.MapRoute(
            //    name: "user-login",
            //    url: "account/user/login",
            //    defaults: new { controller = "Account", action = "ToLogin", id = UrlParameter.Optional }
            //);

            /************************************************************
            *后台路由
            *
            *************************************************************/

            routes.MapRoute(
                name: "manage-index",
                url: "{controller}/{action}",
                defaults: new { controller = "Manage", action = "StudentList", id = UrlParameter.Optional }
            );

            #region 优惠卷验证

            routes.MapRoute(
               name: "use-coupon",
               url: "manage/use/coupon",
               defaults: new { controller = "Manage", action = "UseCoupon", id = UrlParameter.Optional }
            );

            routes.MapRoute(
               name: "use-coupon-post",
               url: "manage/use/coupon/save",
               defaults: new { controller = "Manage", action = "UseCouponSave", id = UrlParameter.Optional }
            );

            routes.MapRoute(
               name: "use-coupon-result",
               url: "manage/coupon/result/{id}",
               defaults: new { controller = "Manage", action = "CouponResult", id = UrlParameter.Optional }
            );

            #endregion;

            #region 驾校管理路由

            routes.MapRoute(
                name: "manage-student-list",
                url: "manage/student/list",
                defaults: new { controller = "Manage", action = "StudentList", id = UrlParameter.Optional }
            );

            routes.MapRoute(
                name: "manage-student-coupon",
                url: "manage/student/coupon/{id}",
                defaults: new { controller = "Manage", action = "StudentCoupon", id = UrlParameter.Optional }
            );

            routes.MapRoute(
                name: "manage-coupon-save",
                url: "manage/coupon/save",
                defaults: new { controller = "Manage", action = "StudentCouponSave", id = UrlParameter.Optional }
            );

            #endregion;

            #region 考试类型管理

            routes.MapRoute(
                name: "examtype-list-home",
                url: "{controller}/ExamType/List",
                defaults: new { controller = "Manage", action = "ExamTypeList", page = UrlParameter.Optional }
            );

            routes.MapRoute(
                name: "examtype-list",
                url: "{controller}/ExamType/List/{page}",
                defaults: new { controller = "Manage", action = "ExamTypeList", page = UrlParameter.Optional }
            );

            routes.MapRoute(
                name: "examtype-add",
                url: "{controller}/ExamType/Add",
                defaults: new { controller = "Manage", action = "ExamTypeEdit" }
            );

            routes.MapRoute(
                name: "examtype-edit",
                url: "{controller}/ExamType/Edit/{id}",
                defaults: new { controller = "Manage", action = "ExamTypeEdit", id = UrlParameter.Optional }
            );

            routes.MapRoute(
                name: "examtype-edit-post",
                url: "{controller}/ExamType/Save/{id}",
                defaults: new { controller = "Manage", action = "ExamTypeSave", id = UrlParameter.Optional }
            );

            routes.MapRoute(
                name: "examtype-delete",
                url: "{controller}/ExamType/Delete/{id}/{page}",
                defaults: new { controller = "Manage", action = "ExamTypeDelete", id = UrlParameter.Optional, page = UrlParameter.Optional }
            );

            #endregion 

            //****************************系统管理********************************************

            #region 管理员管理

            routes.MapRoute(
                name: "user-list-home",
                url: "{controller}/User/List",
                defaults: new { controller = "Manage", action = "UserList", page = UrlParameter.Optional }
            );

            routes.MapRoute(
                name: "user-list",
                url: "{controller}/User/List/{page}",
                defaults: new { controller = "Manage", action = "UserList", page = UrlParameter.Optional }
            );

            routes.MapRoute(
                name: "user-add",
                url: "{controller}/User/Add",
                defaults: new { controller = "Manage", action = "UserEdit" }
            );

            routes.MapRoute(
                name: "user-edit",
                url: "{controller}/User/Edit/{id}",
                defaults: new { controller = "Manage", action = "UserEdit", id = UrlParameter.Optional }
            );

            routes.MapRoute(
                name: "user-edit-post",
                url: "{controller}/User/Save/{id}",
                defaults: new { controller = "Manage", action = "UserSave", id = UrlParameter.Optional }
            );

            routes.MapRoute(
                name: "user-delete",
                url: "{controller}/User/Delete/{id}/{page}",
                defaults: new { controller = "Manage", action = "UserDelete", id = UrlParameter.Optional, page = UrlParameter.Optional }
            );

            #endregion 

            #region 部门管理

            routes.MapRoute(
                name: "dept-list-home",
                url: "{controller}/Dept/List",
                defaults: new { controller = "Manage", action = "DeptList", page = UrlParameter.Optional }
            );

            routes.MapRoute(
                name: "dept-list",
                url: "{controller}/Dept/List/{page}",
                defaults: new { controller = "Manage", action = "DeptList", page = UrlParameter.Optional }
            );

            routes.MapRoute(
                name: "dept-add",
                url: "{controller}/Dept/Add",
                defaults: new { controller = "Manage", action = "DeptEdit" }
            );

            routes.MapRoute(
                name: "dept-edit",
                url: "{controller}/Dept/Edit/{id}",
                defaults: new { controller = "Manage", action = "DeptEdit", id = UrlParameter.Optional }
            );

            routes.MapRoute(
                name: "dept-edit-post",
                url: "{controller}/Dept/Save/{id}",
                defaults: new { controller = "Manage", action = "DeptSave", id = UrlParameter.Optional }
            );

            routes.MapRoute(
                name: "dept-delete",
                url: "{controller}/Dept/Delete/{id}/{page}",
                defaults: new { controller = "Manage", action = "DeptDelete", id = UrlParameter.Optional, page = UrlParameter.Optional }
            );

            #endregion 

            #region 角色管理

            routes.MapRoute(
                name: "usertype-list-home",
                url: "{controller}/UserType/List",
                defaults: new { controller = "Manage", action = "UserTypeList", page = UrlParameter.Optional }
            );

            routes.MapRoute(
                name: "usertype-list",
                url: "{controller}/UserType/List/{page}",
                defaults: new { controller = "Manage", action = "UserTypeList", page = UrlParameter.Optional }
            );

            routes.MapRoute(
                name: "usertype-add",
                url: "{controller}/UserType/Add",
                defaults: new { controller = "Manage", action = "UserTypeEdit" }
            );

            routes.MapRoute(
                name: "usertype-edit",
                url: "{controller}/UserType/Edit/{id}",
                defaults: new { controller = "Manage", action = "UserTypeEdit", id = UrlParameter.Optional }
            );

            routes.MapRoute(
                name: "usertype-edit-post",
                url: "{controller}/UserType/Save/{id}",
                defaults: new { controller = "Manage", action = "UserTypeSave", id = UrlParameter.Optional }
            );

            routes.MapRoute(
                name: "usertype-delete",
                url: "{controller}/UserType/Delete/{id}/{page}",
                defaults: new { controller = "Manage", action = "UserTypeDelete", id = UrlParameter.Optional, page = UrlParameter.Optional }
            );

            #endregion 

            #region 角色管理

            routes.MapRoute(
                name: "accesstype-list-home",
                url: "{controller}/AccessType/List",
                defaults: new { controller = "Manage", action = "AccessTypeList", page = UrlParameter.Optional }
            );

            routes.MapRoute(
                name: "accesstype-list",
                url: "{controller}/AccessType/List/{page}",
                defaults: new { controller = "Manage", action = "AccessTypeList", page = UrlParameter.Optional }
            );

            routes.MapRoute(
                name: "accesstype-add",
                url: "{controller}/AccessType/Add",
                defaults: new { controller = "Manage", action = "AccessTypeEdit" }
            );

            routes.MapRoute(
                name: "accesstype-edit",
                url: "{controller}/AccessType/Edit/{id}",
                defaults: new { controller = "Manage", action = "AccessTypeEdit", id = UrlParameter.Optional }
            );

            routes.MapRoute(
                name: "accesstype-edit-post",
                url: "{controller}/AccessType/Save/{id}",
                defaults: new { controller = "Manage", action = "AccessTypeSave", id = UrlParameter.Optional }
            );

            routes.MapRoute(
                name: "accesstype-delete",
                url: "{controller}/AccessType/Delete/{id}/{page}",
                defaults: new { controller = "Manage", action = "AccessTypeDelete", id = UrlParameter.Optional, page = UrlParameter.Optional }
            );

            #endregion 

            #region 菜单路由

            routes.MapRoute(
                name: "menu-list-home",
                url: "{controller}/Menu/List",
                defaults: new { controller = "Manage", action = "MenuList", page = UrlParameter.Optional }
            );

            routes.MapRoute(
                name: "menu-list",
                url: "{controller}/Menu/List/{page}",
                defaults: new { controller = "Manage", action = "MenuList", page = UrlParameter.Optional }
            );

            routes.MapRoute(
                name: "menu-add",
                url: "{controller}/Menu/Add",
                defaults: new { controller = "Manage", action = "MenuEdit" }
            );

            routes.MapRoute(
                name: "menu-edit",
                url: "{controller}/Menu/Edit/{id}",
                defaults: new { controller = "Manage", action = "MenuEdit", id = UrlParameter.Optional }
            );

            routes.MapRoute(
                name: "menu-edit-post",
                url: "{controller}/Menu/Save/{id}",
                defaults: new { controller = "Manage", action = "MenuSave", id = UrlParameter.Optional }
            );

            routes.MapRoute(
                name: "menu-delete",
                url: "{controller}/Menu/Delete/{id}/{page}",
                defaults: new { controller = "Manage", action = "MenuDelete", id = UrlParameter.Optional, page = UrlParameter.Optional }
            );

            #endregion;

            routes.MapRoute(
                name: "Default",
                url: "{controller}/{action}/{id}",
                defaults: new { controller = "Home", action = "Index", id = UrlParameter.Optional }
            );
        }
    }
}
