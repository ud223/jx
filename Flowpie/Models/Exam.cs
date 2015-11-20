using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;

namespace Flowpie.Models
{
    public class Answer
    {
        public string title { get; set; }
        public bool correct { get; set; }
    }

    public class AnswersA
    {
        public Answer A { get; set; }
        public Answer B { get; set; }
    }

    public class ExamA
    {
        public string no { get; set; }
        public string exam_id { get; set; }
        public string total { get; set; }
        public string title { get; set; }
        public string img { get; set; }
        public string video { get; set; }
        public AnswersA answers { get; set; }
    }

    public class AnswersB
    {
        public Answer A { get; set; }
        public Answer B { get; set; }
        public Answer C { get; set; }
        public Answer D { get; set; }
    }

    public class ExamB
    {
        public string no { get; set; }
        public string exam_id { get; set; }
        public string total { get; set; }
        public string title { get; set; }
        public string img { get; set; }
        public string video { get; set; }
        public AnswersB answers { get; set; }
    }
}