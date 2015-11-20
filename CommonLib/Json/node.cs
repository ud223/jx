using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace CommonLib.Json
{
    public class Node
    {
        private string _sNode;

        public void insert(string key, Node node)
        {
            this._sNode = key + " : { " + node.getJson() + "}";
        }

        public void insert(string key, System.Collections.Hashtable valus)
        {
            StringBuilder strNode = new StringBuilder();

            strNode.Append(key);
            strNode.Append(": {");



        }

        public string getJson()
        {
            return "";
        }
    }
}
