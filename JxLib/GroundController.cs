using System;
using System.Collections;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace JxLib
{
    public class GroundController : SystemConfigureLib.iController
    {
        public override List<Hashtable> getAll()
        {
            this.SqlText = "select * from app_ground";

            return base.getAll();
        }

        /// <summary>
        /// 根据科目类型获取考场
        /// </summary>
        /// <param name="typeid">参数2就是科目2的考场 参数3是科目3的考场</param>
        /// <returns></returns>
        public List<Hashtable> getByType(string typeid)
        {
            this.SqlText = "select * from app_ground where examtype = " + typeid;

            return base.getAll();
        }

        public override Hashtable load(string id)
        {
            this.SqlText = "select * from app_ground where groundid=";

            return base.load(id);
        }

        public override string add(Hashtable data)
        {
            this.SqlText = "insert into app_ground(groundname, price, address, examtype) values('@groundname@', @price@, '@address@', @examtype@); select groundid from app_ground order by groundid desc limit 1";

            return base.add(data);
        }

        public override void save(Hashtable data)
        {
            this.SqlText = "update app_ground set groundname = '@groundname@', price = @price@, address = '@address@', examtype = @examtype@ where groundid = @groundid@";

            base.save(data);
        }
    }
}
