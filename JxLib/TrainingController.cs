using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Collections;

namespace JxLib
{
    public class TrainingController :SystemConfigureLib.iController
    {
        public override string add(Hashtable data)
        {
            this.SqlText = "insert into app_trainingconfig(C1, C2, Time, Num, WeekNum, IsEnable, SchoolID) values(@C1@, @C2@, '@Time@', @Num@, @WeekNum@, @IsEnable@, @SchoolID@);select TrainingConfigID from app_trainingconfig order by TrainingConfigID desc limit 1";

            return base.add(data);
        }

        public List<Hashtable> getAllBySchoolID(string schoolid)
        {
            this.SqlText = "select * from app_trainingconfig where schoolid = "+ schoolid;

            return base.Query(this.SqlText);
        }

        public Hashtable loadTraining(string schoolid, string weeknum)
        {
            this.SqlText = " select * from app_trainingconfig where SchoolID = "+ schoolid +" and weeknum =" + weeknum;

            return base.load("");
        }

        public override void delete(string id)
        {
            this.SqlText = "delete from app_trainingconfig where TrainingConfigID = ";

            base.delete(id);
        }

        public List<System.Collections.Hashtable> getLesson2State(string id)
        {
            this.SqlText = "select teachtypetext, (select count(TeachDetailID)from app_teachdetail left join app_teach on app_teachdetail.TeachID = app_teach.TeachID where StudentID = '"+ id +"'  and app_teach.LessonType = 2 and app_teachdetail.TeachTypeID = app_teachtype.TeachTypeID) as num from app_teachtype where app_teachtype.LessonType = 2 group by app_teachtype.TeachTypeID";

            return base.getAll();
        }

        public List<System.Collections.Hashtable> getLesson3State(string id)
        {
            this.SqlText = "select teachtypetext, (select count(TeachDetailID)from app_teachdetail left join app_teach on app_teachdetail.TeachID = app_teach.TeachID where StudentID = '" + id + "'  and app_teach.LessonType = 3 and app_teachdetail.TeachTypeID = app_teachtype.TeachTypeID) as num from app_teachtype where app_teachtype.LessonType = 3 group by app_teachtype.TeachTypeID";

            return base.getAll();
        }

        public List<System.Collections.Hashtable> getMyHistory(string id)
        {
            this.SqlText = "select app_teach.*, (select Name from app_students where app_students.StudentID  = app_teach.coachid) name, FORMAT((OnTimeScore + ContentScore + WayScore) / 3.0, 2) score, Content from app_teach left join app_teachrating on app_teach.TeachID = app_teachrating.TeachID where state != 0 and studentid = '" + id +"' order by teachid desc";

            return base.getAll();
        }

        public List<System.Collections.Hashtable> getTeachDetail(string id)
        {
            this.SqlText = "select teachtypetext from app_teachdetail left join app_teachtype on app_teachdetail.TeachTypeID = app_teachtype.TeachTypeID where teachid = '"+ id + "'";

            return base.getAll();
        }
    }
}
