using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace KernelLib.SystemEntity
{
    public class MenuEntity
    {
        private string _sID;

        public string ID
        { get { return this._sID; } set { this._sID = value; } }

        private string _sText;

        public string Text
        { get { return this._sText; } set { this._sText = value; } }

        private string _sIndex;

        public string Index
        { get { return this._sIndex; } set { this._sIndex = value; } }

        private string _sIsEnabled;

        public string IsEnabled
        { get { return this._sIsEnabled; } set { this._sIsEnabled = value; } }

        private string _sIcon;

        public string Icon
        { get { return this._sIcon; } set { this._sIcon = value; } }

        private string _sCss;

        public string Css
        { get { return this._sCss; } set { this._sCss = value; } }

        private List<SubMenuEntity> _SubMenus;

        public List<SubMenuEntity> SubMenus
        {
            get
            {
                if (this._SubMenus == null)
                    this._SubMenus = new List<SubMenuEntity>();

                return this._SubMenus;
            }
        }
    }
}
