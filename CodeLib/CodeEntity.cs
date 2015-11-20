using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace CodeLib
{
    public class CodeEntity
    {
        private string _sID;

        public string ID
        { get { return this._sID; } set { this._sID = value; } }

        private string _sText;

        public string Text
        { get { return this._sText; } set { this._sText = value; } }

        private string _sValue;

        public string Value
        { get { return this._sValue; } set { this._sValue = value; } }

        private string _sCode;

        public string Code
        { get { return this._sCode; } set { this._sCode = value; } }

        private string _sCodeText;

        public string CodeText
        { get { return this._sCodeText; } set { this._sCodeText = value; } }
    }
}
