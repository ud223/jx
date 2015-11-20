using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;

namespace KernelLib.SystemEntity
{
    public class ModuleEntity
    {
        private string _sID;

        public string ID
        { get { return this._sID; } set { this._sID = value; } }

        private string _sProject_ID;

        public string Project_ID
        { get { return this._sProject_ID; } set { this._sProject_ID = value; } }

        public string _sText;

        public string Text
        { get { return this._sText; } set { this._sText = value; } }

        private string _sIndex;

        public string Index
        { get { return this._sIndex; } set { this._sIndex = value; } }

        private string _sEnable;

        public string Enable
        { get { return this._sEnable; } set { this._sEnable = value; } }

        private Entity _Entity;

        public Entity Entity
        {
            get
            {
                if (this._Entity == null)
                    this._Entity = new Entity();

                return this._Entity;
            }
        }
    }
}
