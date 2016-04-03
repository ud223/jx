using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Collections;

namespace JxLib
{
    public class AssessmentController : SystemConfigureLib.iController
    {
        public List<Hashtable> getList()
        {
            this.SqlText = "select app_assessment.*, name, nickname, groundname_2, groundname_3, app_test.state from app_assessment left join app_students on app_assessment.studentid = app_students.StudentID left join app_test on app_test.assessmentid = app_assessment.assessmentid where app_assessment.testdate > now() order by assessmentid desc";

            return base.getAll();
        }

        public override Hashtable load(string id)
        {
            this.SqlText = "select * from app_assessment where assessmentid =";

            return base.load(id);
        }

        public List<Hashtable> getByUserID(string id)
        {
            this.SqlText = "select * from app_assessment where studentid ='"+ id +"'";

            return base.getAll();
        }

        public override string add(Hashtable data)
        {
            this.SqlText = "insert into app_assessment(studentid, testdate, level, CreateAt) values('@studentid@', '@testdate@', @level@, '@CreateAt@'); select assessmentid from app_assessment order by assessmentid desc limit 1";

            return base.add(data);
        }
    }
}
