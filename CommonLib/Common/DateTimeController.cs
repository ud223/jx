using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;

namespace CommonLib.Common
{
    public class DateTimeController
    {
        public static DateTime LocalTimeGreenwishTime(DateTime lacalTime)
        {
            TimeZone localTimeZone = System.TimeZone.CurrentTimeZone;
            TimeSpan timeSpan = localTimeZone.GetUtcOffset(lacalTime);
            DateTime greenwishTime = lacalTime - timeSpan;

            return greenwishTime;
        }

        public static DateTime GreenwishTimeLocalTime(DateTime greenwishTime)
        {
            TimeZone localTimeZone = System.TimeZone.CurrentTimeZone;
            TimeSpan timeSpan = localTimeZone.GetUtcOffset(greenwishTime);
            DateTime lacalTime = greenwishTime + timeSpan;

            return lacalTime;
        }

        public static DateTime UTCToLocalDateTime(string str)
        {
            string xx = str;
            string[] cx = xx.Split(' ');
            System.Globalization.DateTimeFormatInfo g = new System.Globalization.DateTimeFormatInfo();
            g.LongDatePattern = "dd MMMM yyyy";

            DateTime DT = new DateTime();

            if (cx.Length == 6)
                DT = DateTime.Parse(string.Format("{0} {1} {2} {3}", cx[2], cx[1], cx[5], cx[3]), g);
            else if (cx.Length == 5)
                DT = DateTime.Parse(string.Format("{0} {1} {2} {3}", cx[2], cx[1], cx[4], cx[3]), g);
            else if (cx.Length == 7)
                DT = DateTime.Parse(string.Format("{0} {1} {2} {3}", cx[2], cx[1], cx[3], cx[4]), g);

            return DT;
        }
    }
}
