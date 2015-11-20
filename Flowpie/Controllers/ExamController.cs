using System;
using System.Collections.Generic;
using System.Linq;
using System.Net;
using System.Web;
using System.Net.Http;
using System.Web.Http;

namespace Flowpie.Controllers
{
    public class ExamController : ApiController
    {
        [HttpPost]
        public string ExamRecord()
        {
            JxLib.UserExamController userExamController = new JxLib.UserExamController();
            HttpContextBase context = (HttpContextBase)Request.Properties["MS_HttpContext"];

            DatabaseLib.Tools tools = new DatabaseLib.Tools();

            System.Collections.Hashtable data = tools.paramToData(context.Request.Form);

            string user_id = context.Request.Cookies["user_id"].Value;

            data["StudentID"] = user_id;

            userExamController.ExamLog(data);

            Models.Result result = new Models.Result();
            
            if (userExamController.Result)
            {
                result.code = "200";
            }
            else
            {
                result.code = "0";
            }

            result.message = userExamController.Message;

            return Newtonsoft.Json.JsonConvert.SerializeObject(result).Replace("\"", "'");
        }

        [HttpPost]
        public string TestRecord()
        {
            JxLib.UserExamController userExamController = new JxLib.UserExamController();
            HttpContextBase context = (HttpContextBase)Request.Properties["MS_HttpContext"];

            DatabaseLib.Tools tools = new DatabaseLib.Tools();

            System.Collections.Hashtable data = tools.paramToData(context.Request.Form);

            string user_id = context.Request.Cookies["user_id"].Value;

            data["StudentID"] = user_id;

            userExamController.ExamEnd(data);

            Models.Result result = new Models.Result();

            if (userExamController.Result)
            {
                result.code = "200";
            }
            else
            {
                result.code = "0";
            }

            result.message = userExamController.Message;

            return Newtonsoft.Json.JsonConvert.SerializeObject(result).Replace("\"", "'");
        }

        [HttpPost]
        public string ErrorRecord()
        {
            JxLib.UserExamController userExamController = new JxLib.UserExamController();
            HttpContextBase context = (HttpContextBase)Request.Properties["MS_HttpContext"];

            DatabaseLib.Tools tools = new DatabaseLib.Tools();

            System.Collections.Hashtable data = tools.paramToData(context.Request.Form);

            string user_id = context.Request.Cookies["user_id"].Value;

            data["StudentID"] = user_id;

            userExamController.ErrorLog(data);

            if (data["OptTypeID"].ToString() == "2")
            {
                //错误了也要记录进度
                userExamController.ExamLog(data);
            }

            Models.Result result = new Models.Result();

            if (userExamController.Result)
            {
                result.code = "200";
            }
            else
            {
                result.code = "0";
            }

            result.message = userExamController.Message;

            return Newtonsoft.Json.JsonConvert.SerializeObject(result).Replace("\"", "'");
        }

        [HttpGet]
        public string GetExam()
        {
            JxLib.ExamController examController = new JxLib.ExamController();
            HttpContextBase context = (HttpContextBase)Request.Properties["MS_HttpContext"];
            Models.Result result = new Models.Result();

            string id = context.Request.Params["id"];

            if (id == null)
            {
                result.code = "0";
                result.message = "参数不能为空!";
                result.data = "";

                return Newtonsoft.Json.JsonConvert.SerializeObject(result).Replace("\"", "'");
            }

            int tmp_id = Int32.Parse(id) + 1;
            string strResult = "";

            System.Collections.Hashtable item = examController.load(tmp_id.ToString());

            System.Collections.Hashtable count = examController.getCount();

                Models.ExamA exam_a = null;
                Models.ExamB exam_b = null;

                if (CommonLib.Common.Validate.IsNullString(item["OptionC"]) == "")
                {
                    Models.Answer answerA = null;

                    if (item["Answer"].ToString().IndexOf("A") > -1)
                    {
                        answerA = new Models.Answer
                        {
                            title = item["OptionA"].ToString(),
                            correct = true
                        };
                    }
                    else
                    {
                        answerA = new Models.Answer
                        {
                            title = item["OptionA"].ToString(),
                            correct = false
                        };

                    }

                    string strAnswerA = Newtonsoft.Json.JsonConvert.SerializeObject(answerA);

                    Models.Answer answerB = null;

                    if (item["Answer"].ToString().IndexOf("B") > -1)
                    {
                        answerB = new Models.Answer
                        {
                            title = item["OptionB"].ToString(),
                            correct = true
                        };
                    }
                    else
                    {
                        answerB = new Models.Answer
                        {
                            title = item["OptionB"].ToString(),
                            correct = false
                        };

                    }

                    Models.AnswersA answers = new Models.AnswersA
                    {
                        A = answerA,
                        B = answerB
                    };

                    exam_a = new Models.ExamA
                    {
                        no = tmp_id.ToString(),
                        exam_id = item["ExamID"].ToString(),
                        total = count["Count"].ToString(),
                        title = item["ExamText"].ToString(),
                        img = CommonLib.Common.ConfigReader.Read("ResourceUrl") + CommonLib.Common.Validate.IsNullString(item["ImgUrl"]),
                        video = CommonLib.Common.ConfigReader.Read("ResourceUrl") + CommonLib.Common.Validate.IsNullString(item["VideoUrl"]),
                        answers = answers
                    };


                    strResult = Newtonsoft.Json.JsonConvert.SerializeObject(exam_a);
                }
                else
                {
                    Models.Answer answerA = null;

                    if (item["Answer"].ToString().IndexOf("A") > -1)
                    {
                        answerA = new Models.Answer
                        {
                            title = item["OptionA"].ToString(),
                            correct = true
                        };
                    }
                    else
                    {
                        answerA = new Models.Answer
                        {
                            title = item["OptionA"].ToString(),
                            correct = false
                        };

                    }

                    Models.Answer answerB = null;

                    if (item["Answer"].ToString().IndexOf("B") > -1)
                    {
                        answerB = new Models.Answer
                        {
                            title = item["OptionB"].ToString(),
                            correct = true
                        };
                    }
                    else
                    {
                        answerB = new Models.Answer
                        {
                            title = item["OptionB"].ToString(),
                            correct = false
                        };

                    }

                    Models.Answer answerC = null;

                    if (item["Answer"].ToString().IndexOf("C") > -1)
                    {
                        answerC = new Models.Answer
                        {
                            title = item["OptionC"].ToString(),
                            correct = true
                        };
                    }
                    else
                    {
                        answerC = new Models.Answer
                        {
                            title = item["OptionC"].ToString(),
                            correct = false
                        };

                    }

                    Models.Answer answerD = null;

                    if (item["Answer"].ToString().IndexOf("D") > -1)
                    {
                        answerD = new Models.Answer
                        {
                            title = item["OptionD"].ToString(),
                            correct = true
                        };
                    }
                    else
                    {
                        answerD = new Models.Answer
                        {
                            title = item["OptionD"].ToString(),
                            correct = false
                        };

                    }

                    Models.AnswersB answers = new Models.AnswersB
                    {
                        A = answerA,
                        B = answerB,
                        C = answerC,
                        D = answerD
                    };


                    exam_b = new Models.ExamB
                    {
                        no = tmp_id.ToString(),
                        exam_id = item["ExamID"].ToString(),
                        total = count["Count"].ToString(),
                        title = item["ExamText"].ToString(),
                        img = CommonLib.Common.ConfigReader.Read("ResourceUrl") + CommonLib.Common.Validate.IsNullString(item["ImgUrl"]),
                        video = CommonLib.Common.ConfigReader.Read("ResourceUrl") + CommonLib.Common.Validate.IsNullString(item["VideoUrl"]),
                        answers = answers
                    };

                strResult = Newtonsoft.Json.JsonConvert.SerializeObject(exam_b);
            }

            result.code = "200";
            result.message = "success!";
            result.data = strResult.ToString().Replace("[", "{").Replace("]", "}"); 

            return Newtonsoft.Json.JsonConvert.SerializeObject(result).Replace("\"", "'");
        }
    }
}