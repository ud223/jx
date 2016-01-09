using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;

namespace Flowpie.Models
{
    public class Teach
    {
        public string TeachID { get; set; }
        public string RunDate { get; set; }
        public string WeekNum { get; set; }
        public string Time { get; set; }
        public string SchoolID { get; set; }
        public string StudentID { get; set; }
        public string CoachID { get; set; }
        public string Amount { get; set; }
        public string CouponID { get; set; }
        public string PayAmount { get; set; }
        public string State { get; set; }
        public string Score { get; set; }
        public string CreateAt { get; set; }
        public string ModifyAt { get; set; }
    }
}