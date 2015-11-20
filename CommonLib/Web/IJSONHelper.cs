using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;

namespace CommonLib.Web
{
    public interface IJSONHelper
    {
        bool success { get; set; }

        string error { get; set; }

        long totlalCount { get; set; }

        void Reset();

        void AddItem(string name, string value);

        void ItemOk();

        string ToString();
    }
}
