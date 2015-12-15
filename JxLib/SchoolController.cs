using System;
using System.Collections;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace JxLib
{
    public class SchoolController : SystemConfigureLib.iController
    {
        public override List<Hashtable> getAll()
        {
            this.SqlText = "select * from app_schools";

            return base.getAll();
        }

        public override System.Collections.Hashtable load(string id)
        {
            this.SqlText = "SELECT * FROM app_schools WHERE SchoolID = ";

            return base.load(id);
        }

        public override Hashtable loadStructure()
        {
            this.SqlText = "SELECT * FROM app_schools WHERE SchoolID = 0";

            return base.loadStructure();
        }

        public override string add(Hashtable data)
        {
            this.SqlText = "insert into app_schools(SchoolText, SchoolCode, Pix, Description, Price, Region, Remark, CreateYear, CreateAt, ModifyAt) values('@SchoolText@', '@SchoolCode@', '@Pix@', '@Description@', @Price@, '@Region@', '@Remark@', '@CreateYear@', '@CreateAt@', '@ModifyAt@'); select SchoolID from app_schools order by SchoolID desc limit 1";

            return base.add(data);
        }

        public override void save(Hashtable data)
        {
            this.SqlText = "update app_schools set SchoolText = '@SchoolText@', SchoolCode = '@SchoolCode@', Pix = '@Pix@', Description = '@Description@', Price = @Price@, Region = '@Region@', Remark = '@Remark@', CreateYear = '@CreateYear@', ModifyAt = '@ModifyAt@' where SchoolID = @SchoolID@";

            base.save(data);
        }
    }
}
