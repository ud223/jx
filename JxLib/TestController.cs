using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Collections;

namespace JxLib
{
    public class TestController : SystemConfigureLib.iController
    {
        public Hashtable getByAssessId(string assessment_id)
        {
            this.SqlText = "select * from app_test where assessmentid = ";

            return base.load(assessment_id);
        }

        public override Hashtable load(string id)
        {
            this.SqlText = "select * from app_test where testid = ";

            return base.load(id);
        }
         
        public override string add(Hashtable data)
        {
            this.SqlText = "insert into app_test(studentid, price, groundid, assessmentid, testdate, expire, CreateAt) values('@studentid@', @price@, @groundid@, @assessmentid@, '@testdate@', '@expire@', '@CreateAt@'); select testid from app_test order by testid desc limit 1";

            return base.add(data);
        }

        public void Next(string id)
        {
            this.SqlText = "update app_test set state = 2 where testid = "+ id;

            base.Execute(this.SqlText);
        }
    }
}
