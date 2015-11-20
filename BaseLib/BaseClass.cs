using System;
using System.Collections.Generic;
using System.Text;

namespace BaseLib
{
    public class BaseClass
    {
        private string _sMessage = "operation successed!";

        /// <summary>
        /// 所有类的内部消息属性
        /// </summary>
        public string Message
        { get { return this._sMessage; } set { this._sMessage = value; } }

        private bool _bResult = true;

        /// <summary>
        /// 所有类的内部操作结果属性
        /// </summary>
        public bool Result
        { get { return this._bResult; } set { this._bResult = value; } }

        private SystemType _SystemType;

        /// <summary>
        /// 系统类型
        /// </summary>
        public SystemType SystemType
        { get { return this._SystemType; } set { this._SystemType = value; } }

        private string _sClassName;

        /// <summary>
        /// 类名称
        /// </summary>
        public string ClassName
        { get { return this._sClassName; } set { this._sClassName = value; } }
    }
}
