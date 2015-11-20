using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;

namespace ModelLib
{
    public class Item
    {
        private string _sID;

        public string ID
        { get { return this._sID; } set { this._sID = value; } }

        private string _sText;

        public string Text
        { get { return this._sText; } set { this._sText = value; } }

        private string _sName;

        public string Name
        { get { return this._sName; } set { this._sName = value; } }

        private string _sTb_ID;

        public string Tb_ID
        { get { return this._sTb_ID; } set { this._sTb_ID = value; } }

        private string _sCo_ID;

        public string Co_ID
        { get { return this._sCo_ID; } set { this._sCo_ID = value; } }
    }
}
