using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace KernelLib.UserEntity
{
    public class Accout
    {
        private string _sID;

        public string ID
        { get { return this._sID; } set { this._sID = value; } }

        private string _sName;

        public string Name
        { get { return this._sName; } set { this._sName = value; } }

        private string _sUserName;

        public string UserName
        { get { return this._sUserName; } set { this._sUserName = value; } }

        private string _sPassword;

        public string Password
        { get { return this._sPassword; } set { this._sPassword = value; } }

        private string _sEmail;

        public string Email
        { get { return this._sEmail; } set { this._sEmail = value; } }

        private string _sPhoto;

        public string Photo
        { get { return this._sPhoto; } set { this._sPhoto = value; } }

        private SystemEntity.MenuEntity _Menu;

        public SystemEntity.MenuEntity Menu
        {
            get
            {
                if (this._Menu == null)
                    this._Menu = new SystemEntity.MenuEntity();

                return this._Menu;
            }
        }
    }
}
