using System;

namespace CommonLib.Windows
{
    /// <summary>
    /// Windows操作方法类
    /// </summary>
    public class Controls
    {
        public delegate object CustomEventHandler(System.Windows.Forms.Control control,object[] param);//首先定义一个委托类型的对象CustomEventHandler

        //用delegate数据类型声明事件，要用event关键字，这里定义了两个字件;
        public event CustomEventHandler CustomEvent;

        /// <summary>
        /// 循环空间集合并执行委托的方法
        /// </summary>
        /// <param name="controls"></param>
        public object LoopControls(System.Windows.Forms.Control.ControlCollection controls,object[] param)
        {
            System.Collections.Hashtable hasFields = new System.Collections.Hashtable();

            foreach (System.Windows.Forms.Control control in controls)
            {
                CustomEvent(control,param);

                LoopControls(control.Controls, param);
            }

            return hasFields;
        }

        /// <summary>
        /// 清空所有文本框
        /// </summary>
        /// <param name="controls"></param>
        public void ClearText(System.Windows.Forms.Control.ControlCollection controls)
        {
            this.CustomEvent += new CommonLib.Windows.Controls.CustomEventHandler(this.ClearText);

            this.LoopControls(controls, null);
        }
      
        /// <summary>
        /// 调用循环清空所有文本框
        /// </summary>
        /// <param name="control"></param>
        /// <param name="param"></param>
        /// <returns></returns>
        private object ClearText(System.Windows.Forms.Control control,object[] param)
        {
            switch (control.GetType().ToString())
            {
                case "System.Windows.Forms.TextBox":
                    {
                        control.Text = ""; break;
                    }
                default:
                    {
                        break;
                    }
            }

            return null;
        }
    }
}
