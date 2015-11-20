using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Collections;

namespace JxLib
{
    public class UserExamController : SystemConfigureLib.iController
    {
        public void ExamLog(Hashtable data)
        {
            SystemConfigureLib.SerialNumberController serialContrller = new SystemConfigureLib.SerialNumberController();

            string strQuery = "SELECT * FROM app_sequenceexam WHERE StudentID='@StudentID@'";

            strQuery = base.extendSql(strQuery, data);

            List<Hashtable> list = base.Query(strQuery);

            string strSql = "";
            string strSequenceExamID = null;
            string strInsert = "INSERT INTO app_sequenceexam(SequenceExamID, StudentID, ExamID, CreateAt, ModifyAt) VALUES('@SequenceExamID@', '@StudentID@', @ExamID@, '@CreateAt@', '@ModifyAt@')";
            string strUpdate = "UPDATE app_sequenceexam SET StudentID='@StudentID@', ExamID=@ExamID@, CreateAt='@CreateAt@', ModifyAt='@ModifyAt@' WHERE SequenceExamID='@SequenceExamID@'";

            if (list.Count == 0)
            {
                strSequenceExamID = serialContrller.getSerialNumber("sea", DateTime.Now.ToString("yyyy-MM-dd"));

                if (strSequenceExamID == null)
                {
                    this.Message = "获取练习流水号异常!";
                    this.Result = false;
                }

                strSql = strInsert;
            }
            else
            {
                strSequenceExamID = list[0]["SequenceExamID"].ToString();

                strSql = strUpdate;
            }

            this.Message = serialContrller.Message;
            this.Result = serialContrller.Result;

            //如果获取流水号异常中止程序运行
            if (!this.Result)
                return;

            data.Add("SequenceExamID", strSequenceExamID);

            this.SqlText = strSql;

            base.save(data);

            this.Message = base.Message;
            this.Result = base.Result;
        }

        /// <summary>
        /// 创建一次考试
        /// </summary>
        /// <param name="data"></param>
        /// <returns>返回新增考试流水ID</returns>
        public string ExamStart(Hashtable data)
        {
            SystemConfigureLib.SerialNumberController serialContrller = new SystemConfigureLib.SerialNumberController();

            string strSql = "INSERT INTO app_simulateexam(SimulateExamID, StudentID, Score, CreateAt, ModifyAt) VALUES('@SimulateExamID@', '@StudentID@', 0, '@CreateAt@', '@ModifyAt@')";
            string strSimulateExamID = serialContrller.getSerialNumber("sia", DateTime.Now.ToString("yyyy-MM-dd"));

            if (strSimulateExamID == null)
            {
                this.Message = "获取错误流水号异常!";
                this.Result = false;
            }

            this.Message = serialContrller.Message;
            this.Result = serialContrller.Result;

            //如果获取流水号异常中止程序运行
            if (!this.Result)
                return null;

            data.Add("SimulateExamID", strSimulateExamID);

            this.SqlText = strSql;

            base.save(data);

            this.Message = base.Message;
            this.Result = base.Result;

            return strSimulateExamID;
        }

        public void ExamEnd(Hashtable data)
        {
            SystemConfigureLib.SerialNumberController serialContrller = new SystemConfigureLib.SerialNumberController();

            string strSql = "UPDATE app_simulateexam SET Score=Score + 1, EndAt='@EndAt@', ModifyAt='@ModifyAt@' WHERE SimulateExamID='@SimulateExamID@'";

            this.SqlText = strSql;

            base.save(data);

            this.Message = base.Message;
            this.Result = base.Result;
        }

        public void ErrorLog(Hashtable data)
        {
            SystemConfigureLib.SerialNumberController serialContrller = new SystemConfigureLib.SerialNumberController();

            string strSql = "INSERT INTO app_errorexam(ErrorExamID, StudentID, ExamID, OptTypeID, CreateAt, ModifyAt) VALUES('@ErrorExamID@', '@StudentID@', @ExamID@, @OptTypeID@, '@CreateAt@', '@ModifyAt@')";
            string strErrorExamID = serialContrller.getSerialNumber("ser", DateTime.Now.ToString("yyyy-MM-dd"));

            if (strErrorExamID == null)
            {
                this.Message = "获取错误流水号异常!";
                this.Result = false;
            }

            this.Message = serialContrller.Message;
            this.Result = serialContrller.Result;

            //如果获取流水号异常中止程序运行
            if (!this.Result)
                return;

            data.Add("ErrorExamID", strErrorExamID);

            this.SqlText = strSql;

            base.save(data);

            this.Message = base.Message;
            this.Result = base.Result;
        }

        public Hashtable getMySequenceExamLog(string id)
        {
            this.SqlText = "SELECT * FROM app_sequenceexam WHERE StudentID = '" + id + "'";

            return base.load("");
        }


        public List<Hashtable> getMyExam(string id)
        {
            string strSql = "SELECT * FROM app_simulateexam WHERE StudentID = '"+ id +"'";

            return base.Query(strSql);
        }

        public override Hashtable load(string id)
        {
            this.SqlText = "SELECT * FROM app_simulateexam WHERE SimulateExamID = '"+ id +"'";

            return base.load("");
        }
    }
}
