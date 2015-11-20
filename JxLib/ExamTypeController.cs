using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace JxLib
{
    public class ExamTypeController : SystemConfigureLib.iController
    {
        /// <summary>
        /// 获取所有角色
        /// </summary>
        /// <returns></returns>
        public override List<System.Collections.Hashtable> getAll()
        {
            this.SqlText = "SELECT * FROM app_examtypes";

            return base.getAll();
        }


        public override System.Collections.Hashtable load(string id)
        {
            this.SqlText = "SELECT * FROM app_examtypes WHERE ExamTypeID = ";

            return base.load(id);
        }

        /// <summary>
        /// 新增角色
        /// </summary>
        /// <param name="data"></param>
        /// <returns>返回新增用户ID</returns>
        public override string add(System.Collections.Hashtable data)
        {
            this.SqlText = "INSERT INTO app_examtypes(ExamTypeText, Scale) VALUES('@ExamTypeText@', @Scale@); SELECT ExamTypeID FROM app_examtypes ORDER BY ExamTypeID DESC LIMIT 0, 1";

            return base.add(data);
        }

        /// <summary>
        /// 保存角色信息
        /// </summary>
        /// <param name="id"></param>
        /// <param name="data"></param>
        public override void save(System.Collections.Hashtable data)
        {
            this.SqlText = "UPDATE app_examtypes SET ExamTypeText = '@ExamTypeText@', Scale = @Scale@ WHERE ExamTypeID = @ExamTypeID@";

            base.save(data);
        }

        public override void delete(string id)
        {
            this.SqlText = "DELETE FROM app_examtypes WHERE ExamTypeID = ";

            base.delete(id);
        }
    }
}
