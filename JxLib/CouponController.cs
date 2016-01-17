using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Collections;

namespace JxLib
{
    public class CouponController : SystemConfigureLib.iController
    {
        public List<Hashtable> getByStuentId(string id)
        {
            string strSql = "select * from app_coupon where StudentID = '"+ id +"' order by CreateAt desc";

            this.SqlText = strSql;

            return base.getAll();
        }

        public List<Hashtable> getUseByStuentId(string id)
        {
            string strSql = "select * from app_coupon where IsUse = 0 and IsExpire = 0 and StudentID = '" + id + "' order by CreateAt desc";

            this.SqlText = strSql;

            return base.getAll();
        }

        public override string add(Hashtable data)
        {
            string strSql = "INSERT INTO app_coupon(CouponText, Amount, CouponRemark, StudentID, Password, CouponTypeID, Expire, IsExpire, CreateAt, ModifyAt) VALUES('@CouponText@', @Amount@, '@CouponRemark@', '@StudentID@', '@Password@', @CouponTypeID@, '@Expire@', @IsExpire@, '@CreateAt@', '@ModifyAt@'); SELECT CouponID FROM app_coupon ORDER BY CouponID DESC LIMIT 1";

            this.SqlText = strSql;

            return base.add(data);
        }

        public override Hashtable load(string id)
        {
            this.SqlText = "SELECT * FROM app_coupon WHERE CouponID = ";

            return base.load(id);
        }

        public Hashtable loadByPassword(string password)
        {
            this.SqlText = "SELECT * FROM app_coupon WHERE Password = '" + password + "'";

            return base.load("");
        }

        public string getPassword()
        {
            Random random = new Random();

            string password = random.Next(10000000, 99999999).ToString();

            string strSql = "SELECT * FROM  app_coupon WHERE IsUse = 0 AND Password = '@"+ password + "@'";

            this.SqlText = strSql;

            Hashtable item =  base.load("");

            if (item == null)
            {
                return password;
            }
            else
            {
                return getPassword();
            }
        }
        

        public void useCoupon(Hashtable data)
        {
            string strSql = "UPDATE app_coupon SET IsUse = 1 WHERE CouponID = @CouponID@";

            this.SqlText = strSql;

            base.save(data);
        }

        public void setExpire(string couponid)
        {
            string strSql = "UPDATE app_coupon SET IsExpire = 1 WHERE CouponID = "+ couponid;

            base.Execute(this.SqlText);
        }
    }
}
