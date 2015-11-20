using System;
using System.Text.RegularExpressions;

namespace CommonLib.Common
{
    /// <summary>
    /// 验证判断类
    /// </summary>
    public class Validate
    {
        #region Validate Condition 验证条件

        private static string _sNumber = "^[0-9]+$";

        /// <summary>
        /// 纯数字正则字符串格式
        /// </summary>
        public static string Number
        { get { return _sNumber; } }

        private static string _sNumberSign = "^[+-]?[0-9]+$";

        /// <summary>
        /// 有符号数字正则字符串格式
        /// </summary>
        public static string NumberSign
        { get { return _sNumberSign; } }

        private static string _sDecimal = "^[0-9]+[.]?[0-9]+$";

        /// <summary>
        /// 浮点数正则字符串格式
        /// </summary>
        public static string Decimal
        { get { return _sDecimal; } }

        private static string _sDecimalSign = "^[+-]?[0-9]+[.]?[0-9]+$";

        /// <summary>
        /// 有符号浮点数正则字符串格式
        /// </summary>
        public static string DecimalSign
        { get { return _sDecimalSign; } }

        private static string _sEmail = "^[\\w-]+@[\\w-]+\\.(com|net|org|edu|mil|tv|biz|info)$";

        /// <summary>
        /// 电子邮箱正则字符串格式
        /// </summary>
        public static string Email
        { get { return _sEmail; } }

        private static string _sCHZN = "[\u4e00-\u9fa5]";

        /// <summary>
        /// 中文简体正则字符串格式
        /// </summary>
        public static string CHZN
        { get { return _sCHZN; } }

        private static string _sChar = "^[A-Za-z]+$";

        /// <summary>
        /// 英文字符正则字符串格式
        /// </summary>
        public static string Char
        { get { return _sChar; } }

        private static string _sBigChar = "^[A-Z]+$";

        /// <summary>
        /// 大写字符正则字符串格式
        /// </summary>
        public static string BigChar
        { get { return _sBigChar; } }

        private static string _sSmallChar = "^[a-z]+$";

        /// <summary>
        /// 小写字符正则字符串格式
        /// </summary>
        public static string SmallChar
        { get { return _sSmallChar; } }

        private static string _sCall = "^\\w+$";

        /// <summary>
        /// 电话号码正则字符串格式
        /// </summary>
        public static string Call
        { get { return _sCall; } }

        private static string _sUrl = "^[a-zA-z]+://(\\w+(-\\w+)*)(\\.(\\w+(-\\w+)*))*(\\?\\S*)?$";

        /// <summary>
        /// 网址正则字符串格式
        /// </summary>
        public static string Url
        { get { return _sUrl; } }

        private static Regex RegNumber = new Regex(_sNumber);

        private static Regex RegNumberSign = new Regex(_sNumberSign);

        private static Regex RegDecimal = new Regex(_sDecimal);

        private static Regex RegDecimalSign = new Regex(_sDecimalSign); //等价于^[+-]?\d+[.]?\d+$ 

        private static Regex RegEmail = new Regex(_sEmail);//w 英文字母或数字的字符串，和 [a-zA-Z0-9] 语法一样 

        private static Regex RegCHZN = new Regex(_sCHZN);

        private static Regex RegexChar = new Regex(_sChar);

        private static Regex RegexBigChar = new Regex(_sBigChar);

        private static Regex RegexSmallChar = new Regex(_sSmallChar);

        private static Regex RegexCall = new Regex(_sCall);

        private static Regex RegexUrl = new Regex(_sUrl);

        #endregion

        #region 数字字符串检查

        /// <summary> 
        /// 是否数字字符串 
        /// </summary> 
        /// <param name="inputData">输入字符串</param> 
        /// <returns></returns> 
        public static bool IsNumber(string inputData)
        {
            Match m = RegNumber.Match(inputData);

            return m.Success;
        }

        /// <summary> 
        /// 是否数字字符串可带正负号 
        /// </summary> 
        /// <param name="inputData">输入字符串</param> 
        /// <returns></returns> 
        public static bool IsNumberSign(string inputData)
        {
            Match m = RegNumberSign.Match(inputData);

            return m.Success;
        }



        /// <summary> 
        /// 是否是浮点数 
        /// </summary> 
        /// <param name="inputData">输入字符串</param> 
        /// <returns></returns> 
        public static bool IsDecimal(string inputData)
        {
            Match m = RegDecimal.Match(inputData);

            return m.Success;
        }



        /// <summary> 
        /// 是否是浮点数可带正负号 
        /// </summary> 
        /// <param name="inputData">输入字符串</param> 
        /// <returns></returns> 
        public static bool IsDecimalSign(string inputData)
        {
            Match m = RegDecimalSign.Match(inputData);

            return m.Success;
        }

        #endregion

        #region 英文字符检查

        /// <summary>
        /// 是否由26个字母组成
        /// </summary>
        /// <param name="inputData"></param>
        /// <returns></returns>
        public static bool IsChar(string inputData)
        {
            Match m = RegexChar.Match(inputData);

            return m.Success;
        }


        /// <summary>
        /// 是否是由26个大写字母组成
        /// </summary>
        /// <param name="inputData"></param>
        /// <returns></returns>
        public static bool IsBigChar(string inputData)
        {
            Match m = RegexBigChar.Match(inputData);

            return m.Success;
        }

        /// <summary>
        /// 是否是由26个小写字母组成
        /// </summary>
        /// <param name="inputData"></param>
        /// <returns></returns>
        public static bool IsSmallChar(string inputData)
        {
            Match m = RegexSmallChar.Match(inputData);

            return m.Success;
        }

        /// <summary>
        /// 是否是由字母数字下划线组成
        /// </summary>
        /// <param name="inputData"></param>
        /// <returns></returns>
        public static bool IsCall(string inputData)
        {
            Match m = RegexCall.Match(inputData);

            return m.Success;
        }

        /// <summary>
        /// 是否是连接
        /// </summary>
        /// <param name="inputData"></param>
        /// <returns></returns>
        public static bool IsUrl(string inputData)
        {
            Match m = RegexUrl.Match(inputData);

            return m.Success;
        }

        #endregion

        #region 中文检测

        /// <summary> 
        /// 检测是否有中文字符 
        /// </summary> 
        /// <param name="inputData"></param> 
        /// <returns></returns> 
        public static bool IsHasCHZN(string inputData)
        {
            Match m = RegCHZN.Match(inputData);

            return m.Success;
        }

        #endregion

        #region 邮件地址

        /// <summary> 
        /// 是否正确的邮件地址 
        /// </summary> 
        /// <param name="inputData">输入字符串</param> 
        /// <returns></returns> 
        public static bool IsEmail(string inputData)
        {
            Match m = RegEmail.Match(inputData);

            return m.Success;
        }

        #endregion

        #region 其他

        /// <summary>
        /// 判断字符串长度是否合法
        /// </summary>
        /// <param name="strTxt">要判断的字符串</param>
        /// <param name="nLength">标尺长度</param>
        /// <returns>1为大于0为小于2为等于</returns>
        public static int EqualLength(string strTxt, int nLength)
        {
            if (strTxt.Length > nLength)
                return 1;
            else if (strTxt.Length < nLength)
                return 0;
            else
                return 2;
        }

        /// <summary>
        /// 判断字符串是否为空，为空则返回空字符串
        /// </summary>
        /// <param name="val"></param>
        /// <returns></returns>
        public static string IsNullString(object val)
        {
            if (val == null || val == System.DBNull.Value || val.ToString().Trim() == "")
                return "";

            return val.ToString();
        }

        /// <summary>
        /// 判断字符串是否为空，为空则返回空字符串
        /// </summary>
        /// <param name="val"></param>
        /// <returns></returns>
        public static string IsNullString(object val, string replace)
        {
            if (val == null || val == System.DBNull.Value || val.ToString() == "")
                return replace;

            return val.ToString();
        }

        /// <summary>
        /// 判断字符串日期是否为空，为空则返回空字符串
        /// </summary>
        /// <param name="val"></param>
        /// <returns></returns>
        public static string IsNullDateString(object val, string format)
        {
            if (val == null || val == System.DBNull.Value || val.ToString() == "")
                return "";

            if (format == null || format == "")
                return val.ToString();

            return Convert.ToDateTime(val).ToString(format);
        }

        public static string IsEnableString(object val, string value1, string value2)
        {
            if (val == null || val == System.DBNull.Value || val.ToString() == "")
                return value2;

            if (val.ToString() == "1")
                return value1;

            return value2;
        }

        public static string filterEmoji(string str)
        {
            string result = Regex.Replace(str, @"\p{Cs}", "");
            return result;
        }


        #endregion
    }
}
