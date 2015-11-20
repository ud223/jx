using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;

namespace CommonLib.Common
{
    public class DateController
    {
        public static int[] GetDaysInMonth(DateTime dBeginDate, DateTime dEndDate)
        {
            int[] days = null;

            if (dBeginDate.Month == dEndDate.Month)
            {
                days = new int[1];

                TimeSpan span = dEndDate.Subtract(dBeginDate);

                days[0] = span.Days + 1;
            }
            else if (dEndDate.Month - dBeginDate.Month == 1)
            {
                days = new int[2];

                TimeSpan span = dEndDate.Subtract(dBeginDate);

                days[0] = span.Days - dEndDate.Day + 1;
                days[1] = dEndDate.Day;
            }

            return days;
        }

        public static int DateSpan(string begindate, string enddate)
        {
            DateTime dBeginDate = Convert.ToDateTime(begindate);
            DateTime dEndDate = Convert.ToDateTime(enddate);

            TimeSpan span = dEndDate.Subtract(dBeginDate);

            return span.Days;
        }
    }
}
