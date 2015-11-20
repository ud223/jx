using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace DatabaseLib.Entity
{
    public class DatabaseEntity
    {
        private string _sID;

        public string ID
        { get { return this._sID; } set { this._sID = value; } }

        private string _sServer;

        public string Server
        { get { return this._sServer; } set { this._sServer = value; } }

        private string _sUid;

        public string Uid
        { get { return this._sUid; } set { this._sUid = value; } }

        private string _sPwd;

        public string Pwd
        { get { return this._sPwd; } set { this._sPwd = value; } }

        private string _sName;

        public string Name
        { get { return this._sName; } set { this._sName = value; } }

        private string _sPort;

        public string Port
        { get { return this._sPort; } set { this._sPort = value; } }

        private string _sText;

        public string Text
        { get { return this._sText; } set { this._sText = value; } }

        private string _sCode_ID;

        public string Data_Type
        { get { return this._sCode_ID; } set { this._sCode_ID = value; } }

        private string _sCreate_Date;

        public string Create_Date
        { get { return this._sCreate_Date; } set { this._sCreate_Date = value; } }

        private string _sIsCreate;

        public string IsCreate
        { get { return this._sIsCreate; } set { this._sIsCreate = value; } }
    }
}
