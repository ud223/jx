using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace JxLib
{
    public class ExamController : SystemConfigureLib.iController
    {
        /// <summary>
        /// 获取所有考题
        /// </summary>
        /// <returns></returns>
        public override List<System.Collections.Hashtable> getAll()
        {
            this.SqlText = "SELECT * FROM app_exams WHERE VideoUrl IS NUll ORDER BY ExamID";

            return base.getAll();
        }

        public List<System.Collections.Hashtable> getTestExam()
        {
            List<System.Collections.Hashtable> list = new List<System.Collections.Hashtable>();

            string strQueryConfig = "SELECT * FROM app_examtypes WHERE ExamTypeID in (1, 2, 3, 4)";

            List<System.Collections.Hashtable> configs = base.Query(strQueryConfig);
            //获取考题配置信息
            foreach (System.Collections.Hashtable config in configs)
            {
                string count = CommonLib.Common.Validate.IsNullString(config["Scale"]);
                string type = CommonLib.Common.Validate.IsNullString(config["ExamTypeID"]);

                if (count == "")
                    continue;
                //根据配置随机获取考题, 由于考题基本是固定,所以这里可以不考虑rand()函数的效率损失问题
                string strGetExam = "select * from app_exams where ExamTypeID = @ExamTypeID@ AND VideoUrl is NUll order by rand() limit " + count;

                strGetExam = strGetExam.Replace("@ExamTypeID@", type);
                
                List<System.Collections.Hashtable> tmp_Exam = base.Query(strGetExam);

                foreach (System.Collections.Hashtable item in tmp_Exam)
                {
                    list.Add(item);
                }
            }

            return list;
        }

        public System.Collections.Hashtable getCount()
        {
            this.SqlText = "SELECT COUNT(*) as Count FROM app_exams WHERE VideoUrl is NUll ";

            return base.load("");
        }

        public override System.Collections.Hashtable load(string id)
        {
            this.SqlText = "SELECT * FROM app_exams WHERE ExamID = ";

            return base.load(id);
        }

        /// <summary>
        /// 新增考题
        /// </summary>
        /// <param name="data"></param>
        /// <returns>返回新增考题ID</returns>
        public override string add(System.Collections.Hashtable data)
        {
            throw new Exception("没有实现新增考题!");
            //this.SqlText = "INSERT INTO app_examtypes(ExamTypeText, Scale) VALUES('@ExamTypeText@', @Scale@); SELECT ExamTypeID FROM app_examtypes ORDER BY ExamTypeID DESC LIMIT 0, 1";

            //return base.add(data);
        }

        /// <summary>
        /// 保存考题信息
        /// </summary>
        /// <param name="id"></param>
        /// <param name="data"></param>
        public override void save(System.Collections.Hashtable data)
        {
            throw new Exception("没有实现保存考题!");
            //this.SqlText = "UPDATE app_examtypes SET ExamTypeText = '@ExamTypeText@', Scale = @Scale@ WHERE ExamTypeID = @ExamTypeID@";

            //base.save(data);
        }

        public override void delete(string id)
        {
            this.SqlText = "DELETE FROM app_exams WHERE ExamID = ";

            base.delete(id);
        }
    }
}
