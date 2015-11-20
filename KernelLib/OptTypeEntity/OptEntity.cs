using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace KernelLib.OptTypeEntity
{
    public class OptEntity
    {
        private string _sID;

        public string ID
        { get { return this._sID; } set { this._sID = value; } }

        private string _sCode_ID;

        public string Opt_Type
        {
            get
            {
                CodeLib.CodeHandle codeHandle = new CodeLib.CodeHandle();

                return codeHandle.GetValueById(this._sCode_ID);
            }
            set
            {
                this._sCode_ID = value;
            } 
        }

        public string _sEntity_ID;

        public string Entity_ID
        { get { return this._sEntity_ID; } set { this._sEntity_ID = value; } }

        private string _sIsPage = "0";

        public string IsPage
        { get { return this._sIsPage; } set { this._sIsPage = value; } }
    }
}
