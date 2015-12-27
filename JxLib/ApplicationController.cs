using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Collections;

namespace JxLib
{
    public class ApplicationController : SystemConfigureLib.iController
    {
        public override List<Hashtable> getAll()
        {
            this.SqlText = "select app_application.*, app_students.Name, app_students.Phone, LicenseText, SchoolText from app_application left join app_students on app_application.StudentID = app_students.StudentID left join app_schools on app_application.SchoolID = app_schools.SchoolID left join app_license on app_application.LicenseTypeID = app_license.LicenseTypeID where app_application.state = 1 @SelectType@";

            return base.getAll();
        }

        public override Hashtable load(string id)
        {
            this.SqlText = "select * from app_application where applicationid = '"+ id +"'";

            return base.load("");
        }

        public override string add(Hashtable data)
        {
            this.SqlText = "insert into app_application(ApplicationID, StudentID, SchoolID, LicenseTypeID, ApplicationTypeID, Remark, CreateAt, ModifyAt) values('@ApplicationID@', '@StudentID@', @SchoolID@, @LicenseTypeID@, @ApplicationTypeID@, '@Remark@', '@CreateAt@', '@ModifyAt@'); select ApplicationID from app_application order by CreateAt desc limit 1";

            return base.add(data);
        }

        public void apply(Hashtable data)
        {
            this.SqlText = "update app_application set state=2, ModifyAt='@ModifyAt@' where ApplicationID='@ApplicationID@'";

            base.save(data);
        }
    }
}
