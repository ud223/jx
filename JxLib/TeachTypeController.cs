using System;
using System.Collections;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace JxLib
{
    public class TeachTypeController : SystemConfigureLib.iController
    {
        public List<System.Collections.Hashtable> getLesson2Item()
        {
            this.SqlText = "SELECT * from app_teachtype where LessonType = 2";

            return base.getAll();
        }
        public List<System.Collections.Hashtable> getLesson3Item()
        {
            this.SqlText = "SELECT * from app_teachtype where LessonType = 3";

            return base.getAll();
        }
    }
}
