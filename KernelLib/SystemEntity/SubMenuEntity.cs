using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace KernelLib.SystemEntity
{
    public class SubMenuEntity
    {
        private string _sID;

        public string ID
        { get { return this._sID; } set { this._sID = value; } }

        private string _sText;

        public string Text
        { get { return this._sText; } set { this._sText = value; } }

        public string _sUrl;

        public string Url
        { get { return this._sUrl; } set { this._sUrl = value; } }

        private string _sMenu_ID;

        public string Menu_ID
        { get { return this._sMenu_ID; } set { this._sMenu_ID = value; } }

        private string _sIsEnable;

        public string IsEnable
        { get { return this._sIsEnable; } set { this._sIsEnable = value; } }
    }
}
