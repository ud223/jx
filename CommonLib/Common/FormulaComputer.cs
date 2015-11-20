using System;
using System.Collections.Generic;
using System.Text;

namespace CommonLib.Common
{
    public class FormulaComputer
    {
        private string vars = "", zc;
        private double[] values = new double[10];
        private int pos, klammer, ebene;
        private double res;
        private bool fehler = false;

        /// <summary>
        /// 计算公式
        /// </summary>
        /// <param name="formel">Der text mit der Formel</param>
        /// <param name="result">Variable, die das Ergebnis erh鋖t</param>
        /// <returns>Erfolg der Funktion</returns>
        public bool Neu(string formel, out double result)
        {
            if (formel == "")
            { result = 0.0; return false; }
            fehler = false;
            result = Zuteilung(formel);
            return !fehler;
        }

        /// <summary>
        /// Rekursive Hauptprozedur, die die Formel ausrechnet
        /// </summary>
        /// <param name="Op">Der Text mit der Formel</param>
        /// <returns>Das Ergebnis der Formel oder 0 bei Fehlern</returns>
        private double Zuteilung(string Op)
        {
            if (Op.Length == 0) goto fehl;
            if (Op.StartsWith("(") && GetKlammer(Op, 0) == Op.Length - 1)
                Op = Op.Substring(1, Op.Length - 2);
            if (double.TryParse(Op, System.Globalization.NumberStyles.Float, null, out res))
                return res;
            if (Op.Length == 2 && Op == "PI") return Math.PI;
            if (Op.Length == 1 && TryVariable(Op, out res)) return res;
            if (Op.Length > 4 && Op.Substring(3, 1) == "(")
            {
                pos = GetKlammer(Op, 3);
                if (pos != Op.Length - 1) goto next;
                zc = Op.Substring(4, pos - 4);
                switch (Op.Substring(0, 3))
                {
                    case "sqr": return Math.Sqrt(Zuteilung(zc));
                    case "sin": return Math.Sin(Math.PI * Zuteilung(zc) / 180);
                    case "cos": return Math.Cos(Math.PI * Zuteilung(zc) / 180);
                    case "tan": return Math.Tan(Math.PI * Zuteilung(zc) / 180);
                    case "log": return Math.Log10(Zuteilung(zc));
                    case "abs": return Math.Abs(Zuteilung(zc));
                    case "fac": return Faculty(Zuteilung(zc));
                }
            }
        next:
            pos = 0; ebene = 6; klammer = 0;
            for (int i = Op.Length - 1; i > -1; i--)
            {
                switch (Op.Substring(i, 1))
                {
                    case "(": klammer++; break;
                    case ")": klammer--; break;
                    case "+": if (klammer == 0 && ebene > 0) { pos = i; ebene = 0; } break;
                    case "-": if (klammer == 0 && ebene > 1) { pos = i; ebene = 1; } break;
                    case "*": if (klammer == 0 && ebene > 2) { pos = i; ebene = 2; } break;
                    case "%": if (klammer == 0 && ebene > 3) { pos = i; ebene = 3; } break;
                    case "/": if (klammer == 0 && ebene > 4) { pos = i; ebene = 4; } break;
                    case "^": if (klammer == 0 && ebene > 5) { pos = i; ebene = 5; } break;
                }
            }
            if (pos == 0 || pos == Op.Length - 1) goto fehl;
            zc = Op.Substring(pos, 1);
            string t1, t2;
            t1 = Op.Substring(0, pos);
            t2 = Op.Substring(pos + 1, Op.Length - (pos + 1));
            switch (zc)
            {
                case "+": return Zuteilung(t1) + Zuteilung(t2);
                case "-": return Zuteilung(t1) - Zuteilung(t2);
                case "*": return Zuteilung(t1) * Zuteilung(t2);
                case "/": return Zuteilung(t1) / Zuteilung(t2);
                case "%": return Math.IEEERemainder(Zuteilung(t1), Zuteilung(t2));
                case "^": return Math.Pow(Zuteilung(t1), Zuteilung(t2));
            }
        fehl:
            fehler = true;
            return 0.0;
        }
        /// <summary>
        /// Sucht zu einer ge鰂fneten Klammer die Position der entsprechend schlie遝nden Klammer
        /// </summary>
        /// <param name="Op">Der zu 黚erpr黤ende Text</param>
        /// <param name="start">Position der er鰂fnenden Klammer</param>
        /// <returns>Position der schlie遝nden Klammer, oder START, wenn keine vorhanden</returns>
        private int GetKlammer(string Op, int start)
        {
            int res = start;
            for (int i = start; i < Op.Length; i++)
            {
                switch (Op.Substring(i, 1))
                {
                    case "(": klammer++; break;
                    case ")": klammer--; break;
                }
                if (klammer == 0) { res = i; break; }
            }
            return res;
        }
        /// <summary>
        /// Pr黤t, ob der Text eine Variable bezeichnet und schreibt den Wert in eine Variable
        /// </summary>
        /// <param name="Op">Der zu 黚erpr黤ende Text</param>
        /// <param name="wert">Adresse der Variable, die den Wert erhalten soll</param>
        /// <returns>TRUE, wenn eine Variable gefunden wurde</returns>
        private bool TryVariable(string Op, out double wert)
        {
            int i = vars.IndexOf(Op);
            if (i != -1)
            {
                wert = values[i];
                return true;
            }
            else
            {
                wert = 0.0;
                return false;
            }
        }
        private double Faculty(double number)
        {
            if (double.IsInfinity(number) ||
                double.IsNaN(number) ||
                number < 0.0 ||
                number % 1.0 != 0) return double.NaN;
            double res = 1.0;
            for (int i = 0; i < number; i++)
                res *= (double)(i + 1);
            return res;
        }
    }
}
