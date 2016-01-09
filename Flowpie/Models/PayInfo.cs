using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;

namespace Flowpie.Models
{
    public class PayInfo
    {
        public string appId { get; set; }
        public string nonceStr { get; set; }
        public string package { get; set; }
        public string paySign { get; set; }
        public string signType { get; set; }
        public string timeStamp { get; set; }
    }
}