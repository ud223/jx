using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;

namespace Flowpie.Models
{
    public class Coupon
    {
        public string CouponID { get; set; }
        public string CouponText { get; set; }
        public string Amount { get; set; }
        public string StudentID { get; set; }
        public string Password { get; set; }
        public string IsUse { get; set; }
        public string CouponTypeID { get; set; }
        public string Expire { get; set; }
        public string IsExpire { get; set; }
        public string CreateAt { get; set; }
        public string ModifyAt { get; set; }
    }
}