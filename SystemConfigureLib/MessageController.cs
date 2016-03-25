using System;
using System.Collections;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace SystemConfigureLib
{
    public class MessageController : iController
    {
        public List<Hashtable> getMyMessage(string id)
        {
            this.SqlText = "select * from app_message where StudentID = '" + id + "'";

            return base.getAll();
        }

        public override Hashtable load(string id)
        {
            this.SqlText = "select * from app_message where MessageID = '" + id + "'";

            return base.load("");
        }

        public override string add(Hashtable data)
        {
            this.SqlText = "insert into app_message(MessageID, Title, MessageText, StudentID, SendID, CreateAt) values('@MessageID@', '@Title@', '@MessageText@', '@StudentID@', '@SendID@', '@CreateAt@'); select messageid from app_message order by messageid desc limit 1";

            return base.add(data);
        }

    }
}
