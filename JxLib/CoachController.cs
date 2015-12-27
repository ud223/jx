using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Collections;

namespace JxLib
{
    public class CoachController : SystemConfigureLib.iController
    {
        public List<Hashtable> getAllBySchoolByID(string schoolid)
        {
            this.SqlText = "SELECT app_students.*, app_license.LicenseText  FROM app_students left join app_license on app_students.LicenseTypeID = app_license.LicenseTypeID WHERE IsCoach and SchoolID = 1 ORDER BY app_students.CreateAt DESC";

            return base.getAll();
        }

        public List<Hashtable> getApplicationBySchoolID(string schoolid)
        {
            this.SqlText = "select app_coachapplication.*, Name, NickName, Phone from app_coachapplication left join app_students on app_coachapplication.StudentID = app_students.StudentID where State = 1 and app_coachapplication.SchoolID = " + schoolid;

            return base.Query(this.SqlText);
        }

        public override Hashtable load(string id)
        {
            this.SqlText = "select * from app_coachapplication where CoachApplicationID =";

            return base.load(id);
        }

        public override string add(Hashtable data)
        {
            this.SqlText = "insert into app_coachapplication(StudentID, SchoolID, CreateAt, ModifyAt) values('@StudentID@', @SchoolID@, '@CreateAt@', '@ModifyAt@'); select CoachApplicationID from app_coachapplication order by CoachApplicationID desc limit 1";

            return base.add(data);
        }

        public void application(Hashtable data)
        {
            this.SqlText = "update app_coachapplication set state = " + data["State"].ToString() + ", ModifyAt='"+ data["ModifyAt"].ToString() + "' where CoachApplicationID ="+ data["CoachApplicationID"].ToString();

            base.Execute(this.SqlText);
        }

        public void toCoach(string studentid, string schoolid)
        {
            this.SqlText = "update app_students set IsCoach = 1, SchoolID = " + schoolid +" where StudentID ='" + studentid + "'";

            base.Execute(this.SqlText);
        }
    }
}
