using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace DatabaseLib.Entity
{
    public class TableEntity
    {
        private string _sID;

        public string ID
        { get { return this._sID; } set { this._sID = value; } }

        private string _sName;

        public string Name
        { get { return this._sName; } set { this._sName = value; } }

        private string _sText;

        public string Text
        { get { return this._sText; } set { this._sText = value; } }

        private string _sCode_ID;

        public string Table_Type
        {
            get
            {
                return this._sCode_ID;
            }
            set
            {
                this._sCode_ID = value;
            }
        }
    }

}
