using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace KernelLib.SystemEntity
{
    public class LoginEntity
    {
        private string _sID;

        public string ID
        { get { return this._sID; } set { this._sID = value; } }

        private string _sTable_ID;

        public string Table_ID
        { get { return this._sTable_ID; } set { this._sTable_ID = value; } }

        private string _sLoginName;

        public string LoginName
        { get { return this._sLoginName; } set { this._sLoginName = value; } }

        private string _sPwdName;

        public string PwdName
        { get { return this._sPwdName; } set { this._sPwdName = value; } }

        private string _sProject_ID;

        public string Project_ID
        { get { return this._sProject_ID; } set { this._sProject_ID = value; } }
    }
}
