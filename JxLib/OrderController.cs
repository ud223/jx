using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Collections;

namespace JxLib
{
    public class OrderController :SystemConfigureLib.iController
    {
        public void saveCoach(string id, string coach_id)
        {
            this.SqlText = "update app_teach set CoachID = '" + coach_id + "' where TeachID = '" + id + "'";

            base.Execute(this.SqlText);
        }

        public List<Hashtable> getConfigOrder(string schoolid)
        {
            this.SqlText = "select app_teach.*, SchoolText from app_teach left join app_schools on app_teach.SchoolID = app_schools.SchoolID where app_teach.State = 1 and app_teach.SchoolID = " + schoolid + "  order by app_teach.RunDate desc";

            return base.Query(this.SqlText);
        }

        public override Hashtable load(string id)
        {
            this.SqlText = "select app_teach.*, SchoolText, app_schools.Address, app_students.Name, app_students.Score, app_students.Phone, app_students.HeadPic from app_teach left join app_schools on app_teach.SchoolID = app_schools.SchoolID left join app_students on app_teach.CoachID = app_students.StudentID where TeachID = '" + id + "'";

            WxApiLib.lib.Log.Info(this.GetType().ToString(), "1.2 page load:"+ this.SqlText);

            return base.load("");
        }

        public List<Hashtable> loadByDay(string rundate, string schoolid)
        {

            this.SqlText = "select app_teach.*, (select name from app_students where app_students.StudentID = app_teach.StudentID) as StudentName, (select name from app_students where app_students.StudentID = app_teach.CoachID) as CoachName, (select HeadPic from app_students where app_students.StudentID = app_teach.CoachID) as CoachPic from app_teach where app_teach.State = 1 and app_teach.SchoolID = " + schoolid + " and app_teach.RunDate = '" + rundate + "'";

            return base.Query(this.SqlText);
        }

        public List<Hashtable> loadByStudentID(string rundate, string schoolid, string studentid)
        {

            this.SqlText = "select * from app_teach where SchoolID = " + schoolid + " and RunDate = '" + rundate + "' and StudentID = '" + studentid + "'";

            return base.Query(this.SqlText);
        }

        public List<Hashtable> loadByDayNotMe(string rundate, string schoolid, string studentid)
        {

            this.SqlText = "select * from app_teach where ((state > 1) or (state = 1 and now() < DATE_ADD(CreateAt,INTERVAL 3 MINUTE))) and SchoolID = " + schoolid +" and RunDate = '"+ rundate +"'";

            return base.Query(this.SqlText);
        }

        public override string add(Hashtable data)
        {
            if (!data.Contains("otherid"))
                data.Add("otherid", "");

            this.SqlText = "insert into app_teach(TeachID, RunDate, WeekNum, Time, SchoolID, StudentID, CoachID, Amount, CouponID, PayAmount, LessonType, Type, CreateAt, ModifyAt, otherid) values('@TeachID@', '@RunDate@', @WeekNum@, '@Time@', @SchoolID@, '@StudentID@', '@CoachID@', @Amount@, '@CouponID@', @PayAmount@, @LessonType@, @Type@, '@CreateAt@', '@ModifyAt@', '@otherid@'); select TeachID from app_teach order by CreateAt desc limit 1";

            return base.add(data);
        }

        public void updatePayAmount(string id, string couponid, string payamount)
        {
            this.SqlText = "update app_teach set PayAmount = "+ payamount + ", CouponID = '"+ couponid +"' where TeachID = '" + id + "'";

            base.Execute(this.SqlText);
        }

        public void nextState(string id)
        {
            this.SqlText = "update app_teach set state = state + 1 where TeachID = '" + id + "'";

            base.Execute(this.SqlText);
        }

        public string addDetail(Hashtable data)
        {
            this.SqlText = "insert into app_teachdetail(TeachDetailID, TeachID,TeachTypeID) values('@TeachDetailID@', '@TeachID@',@TeachTypeID@); select TeachDetailID from app_teachdetail order by TeachDetailID desc limit 1";

            return base.add(data);
        }

        public List<Hashtable> getDetail(string id)
        {
            this.SqlText = "select * from app_teachdetail where TeachDetailID = '"+ id +"'";

            return base.Query(this.SqlText);
        }

        public List<Hashtable> getDetailHistory(string id)
        {
            this.SqlText = "select count(*)num, TeachTypeID from app_teachdetail where TeachID in (select TeachID from app_teach where StudentID = '" + id + "') group by TeachTypeID";

            return base.Query(this.SqlText);
        }

        public List<Hashtable> getMyOrder(string id)
        {
            this.SqlText = "select * from app_teach where StudentID = '" + id + "' group by CreateAt desc";

            return base.Query(this.SqlText);
        }

        public string setOrderScore(Hashtable data)
        {
            this.SqlText = "insert into app_teachrating(OrderRatingID, TeachID, OnTimeScore, ContentScore, WayScore, Content, CreateAt, ModifyAt) values('@OrderRatingID@', '@TeachID@',  @OnTimeScore@, @ContentScore@, @WayScore@, '@Content@', '@CreateAt@', '@ModifyAt@'); select OrderRatingID from app_teachrating order by CreateAt desc limit 1";

            return base.add(data);
        }

        public List<Hashtable> getRatingByCoachID(string id)
        {
            this.SqlText = "select app_students.Name, app_students.HeadPic, app_teach.RunDate, app_teach.`Time`, app_teach.State, convert((OnTimeScore + ContentScore + WayScore) / 3, decimal) as Score, CONCAT('准时:', OnTimeScore, '; 内容:', ContentScore, '; 方式 :', WayScore) as ScoreText, app_teachrating.Content  from app_teach left join app_students on app_teach.StudentID = app_students.StudentID left join app_teachrating on app_teach.TeachID = app_teachrating.TeachID where app_teach.State in (1, 2, 3, 4) and app_teach.CoachID = '" + id + "' order by app_teach.CreateAt desc";

            return base.Query(this.SqlText);
        }
    }
}
