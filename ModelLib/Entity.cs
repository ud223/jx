using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;

namespace ModelLib
{
    public class Entity
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

        private string _sEntityType;

        public string EntityType
        { get { return this._sEntityType; } set { this._sEntityType = value; } }

        private List<KernelLib.DatabaseEntity.TableEntity> _Tables;

        public List<KernelLib.DatabaseEntity.TableEntity> Tables
        {
            get
            {
                if (this._Tables == null)
                    this._Tables = new List<KernelLib.DatabaseEntity.TableEntity>();

                return this._Tables;
            }
        }

        private List<Item> _Items;

        public List<Item> Items
        {
            get 
            {
                if (this._Items == null)
                    this._Items = new List<Item>();

                return this._Items;
            }
        }

        private List<KernelLib.OptTypeEntity.OptEntity> _OptEntitys;

        public List<KernelLib.OptTypeEntity.OptEntity> OptEntitys
        { 
            get 
            {
                if (this._OptEntitys == null)
                    this._OptEntitys = new List<KernelLib.OptTypeEntity.OptEntity>();

                return this._OptEntitys;
            } 
        }
    }
}
