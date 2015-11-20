using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Data;

namespace SystemConfigureLib
{
    public class SerialNumberController :iController
    {
        /// <summary>
        /// 获取流水号
        /// </summary>
        /// <returns></returns>
        public override List<System.Collections.Hashtable> getAll()
        {
            this.SqlText = "SELECT * FROM sy_serialnumber";

            return base.getAll();
        }

        /// <summary>
        /// 获取当前流水号的num
        /// </summary>
        /// <param name="pix">流水号前缀</param>
        /// <param name="date">获取流水号的日期</param>
        /// <returns>返回流水号</returns>
        public string getSerialNumber(string pix, string date)
        {
            DatabaseLib.DatabaseFactory dataFactory = new DatabaseLib.DatabaseFactory();
            //获取数据访问操作端
            DatabaseLib.IDatabase dataClient = dataFactory.CreateClient(BaseLib.SystemType.Web);

            string strSql = "UPDATE sy_serialnumber set Num = 0, `Date` = '@Date@' where Pix = '@Pix@' and `Date` != '@Date@'; update sy_serialnumber set num = num + 1 where Pix = '@Pix@' and `Date`= '@Date@'; select num from sy_serialnumber where pix = '@Pix@'";

            strSql = strSql.Replace("@Pix@", pix);
            strSql = strSql.Replace("@Date@", date);

            dataClient.SqlText = strSql;

            DataSet ds = dataClient.Query();

            this.Message = dataClient.Message;
            this.Result = dataClient.Result;

            if (this.Result)
            {
                if (!DatabaseLib.Tools.tableIsNull(ds))
                {
                    return null;
                }

                ///设置流水号流水部分为6位, 根据所得流水值的长度补充位数"0"
                string serial_num = ds.Tables[0].Rows[0][0].ToString();
                string tmp = "00000";

                serial_num = tmp.Substring(0, tmp.Length - serial_num.Length) + serial_num;

                return date.Replace("-", "") + serial_num;
            }
            else
                return null;
        }

        /// <summary>
        /// 随机获取当前流水号的num
        /// </summary>
        /// <param name="pix">流水号前缀</param>
        /// <param name="date">获取流水号的日期</param>
        /// <returns>返回流水号</returns>
        public string getSerialNumberRand(string pix, string date)
        {
            DatabaseLib.DatabaseFactory dataFactory = new DatabaseLib.DatabaseFactory();
            //获取数据访问操作端
            DatabaseLib.IDatabase dataClient = dataFactory.CreateClient(BaseLib.SystemType.Web);

            Random random = new Random();

            int rand_num = random.Next(1, 5);

            string strSql = "UPDATE sy_serialnumber set Num = 0, `Date` = '@Date@' where Pix = '@Pix@' and `Date` != '@Date@'; update sy_serialnumber set num = num + "+ rand_num + " where Pix = '@Pix@' and `Date`= '@Date@'; select num from sy_serialnumber where pix = '@Pix@'";

            strSql = strSql.Replace("@Pix@", pix);
            strSql = strSql.Replace("@Date@", date);

            dataClient.SqlText = strSql;

            DataSet ds = dataClient.Query();

            this.Message = dataClient.Message;
            this.Result = dataClient.Result;

            if (this.Result)
            {
                if (!DatabaseLib.Tools.tableIsNull(ds))
                {
                    return null;
                }

                ///设置流水号流水部分为6位, 根据所得流水值的长度补充位数"0"
                string serial_num = ds.Tables[0].Rows[0][0].ToString();
                string tmp = "00000000000";

                serial_num = tmp.Substring(0, tmp.Length - serial_num.Length) + serial_num;

                return date.Replace("-", "") + serial_num;
            }
            else
                return null;
        }

        public override System.Collections.Hashtable load(string id)
        {
            this.SqlText = "SELECT * FROM sy_serialnumber WHERE SerialNumberID = ";

            return base.load(id);
        }

        /// <summary>
        /// 新增部门
        /// </summary>
        /// <param name="data"></param>
        /// <returns>返回新增流水ID</returns>
        public override string add(System.Collections.Hashtable data)
        {
            this.SqlText = "INSERT INTO sy_serialnumber(Pix, Num, Date) VALUES('@Pix@', 0, '@Date@'); SELECT DeptID FROM sy_serialnumber ORDER BY SerialNumberID DESC LIMIT 0, 1";

            return base.add(data);
        }

        /// <summary>
        /// 保存水流信息
        /// </summary>
        /// <param name="id"></param>
        /// <param name="data"></param>
        public override void save(System.Collections.Hashtable data)
        {
            this.SqlText = "UPDATE sy_serialnumber SET Pix = '@Pix@', Num = @Num@, Date = '@Date@' WHERE SerialNumberID = @SerialNumberID@";

            base.save(data);
        }

        public override void delete(string id)
        {
            this.SqlText = "DELETE FROM sy_serialnumber WHERE SerialNumberID = ";

            base.delete(id);
        }
    }
}
