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
    }
}
