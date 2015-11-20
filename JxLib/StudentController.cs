﻿using System;
using System.Collections;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace JxLib
{
    public class StudentController : SystemConfigureLib.iController
    {
        public override List<Hashtable> getAll()
        {
            this.SqlText = "SELECT app_students.*, app_license.LicenseText  FROM app_students left join app_license on app_students.LicenseTypeID = app_license.LicenseTypeID";

            return base.getAll();
        }
        public override Hashtable load(string id)
        {
            this.SqlText = "SELECT * FROM app_students WHERE StudentID = '" + id + "'";

            return base.load("");
        }

        public Hashtable getUserByOpenId(string openid)
        {
            this.SqlText = "SELECT * FROM app_students WHERE OpenId = '" + openid + "'";

            return base.load("");
        }

        public override string add(Hashtable data)
        {
            string strSql = "INSERT INTO app_students(StudentID, OpenId, NickName, HeadPic, Sex) VALUES('@StudentID@', '@OpenId@', '@NickName@', '@HeadPic@', @Sex@); SELECT StudentID FROM app_students LIMIT 1";

            this.SqlText = strSql;
            
            return base.add(data);
        }

        public override void save(System.Collections.Hashtable data)
        {
            string strSql = "UPDATE app_students SET Name='@Name@', Sex=@Sex@, Birthday='@Birthday@', Code='@Code@', Place='@Place@', LicenseTypeID=@LicenseTypeID@, Phone='@Phone@', Email='@Email@', Qq='@Qq@', SchoolID=@SchoolID@, CreateAt='@CreateAt@', ModifyAt='@ModifyAt@' WHERE StudentID='@StudentID@'";

            this.SqlText = strSql;

            base.save(data);
        }

        public void saveEnter(System.Collections.Hashtable data)
        {
            string strSql = "UPDATE app_students SET EnterDate='@EnterDate@' WHERE StudentID='@StudentID@'";

            this.SqlText = strSql;

            base.save(data);
        }

        public void mark(System.Collections.Hashtable data)
        {
            string strSql = "UPDATE app_students SET IsMark=1, MarkDate='@MarkDate@' WHERE StudentID='@StudentID@'";

            this.SqlText = strSql;

            base.save(data);
        }
    }
}
