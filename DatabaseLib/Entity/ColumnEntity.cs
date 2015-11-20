using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace DatabaseLib.Entity
{
    class ColumnEntity
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

        public string Column_Type
        { get { return this._sCode_ID; } set { this._sCode_ID = value; } }

        private string _sLength;

        public string Length
        { get { return this._sLength; } set { this._sLength = value; } }

        private string _sScale;

        public string Scale
        { get { return this._sScale; } set { this._sScale = value; } }

        private string _sIsPri;

        public string IsPri
        { get { return this._sIsPri; } set { this._sIsPri = value; } }

        private string _sIsNull;

        public string IsNull
        { get { return this._sIsNull; } set { this._sIsNull = value; } }

        private string _sDefaultValue;

        public string DefaultValue
        { get { return this._sDefaultValue; } set { this._sDefaultValue = value; } }

        private string _sTable_ID;

        public string Table_ID
        { get { return this._sTable_ID; } set { this._sTable_ID = value; } }
    }
}
