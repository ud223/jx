using System.Web;
using System.Web.Optimization;

namespace Flowpie
{
    public class BundleConfig
    {
        public static void RegisterBundles(BundleCollection bundles)
        {
            //common all css
            bundles.Add(new StyleBundle("~/statics/admin/css/all").Include(
                "~/statics/admin/css/bootstrap.css",
                "~/statics/admin/css/bootstrap-datetimepicker.css",
                "~/statics/admin/css/font.css",
                "~/statics/admin/css/nanoscroller.css",
                "~/statics/admin/css/bsreset.css",
                "~/statics/admin/css/global.css"));

            //common all js
            bundles.Add(new ScriptBundle("~/statics/admin/js/all").Include(
                "~/statics/admin/js/jquery-1.11.1.min.js",
                "~/statics/admin/js/bootstrap.min.js",
                "~/statics/admin/js/bootstrap-datetimepicker.js",
                "~/statics/admin/js/nanoscroller.js",
                "~/statics/admin/js/ajaxhelper.js",
                "~/statics/admin/js/global.js"));
            
            //jx all css
            bundles.Add(new StyleBundle("~/statics/jx/css/all").Include(
                "~/statics/jx/css/font-awesome.css",
                "~/statics/jx/css/global.css"));

            //jx all js
            bundles.Add(new ScriptBundle("~/statics/jx/js/all").Include(
                "~/statics/admin/js/jquery-1.11.1.min.js",
                "~/statics/admin/js/jquery.cookie.js",
                "~/statics/jx/js/swipe.js",
                "~/statics/admin/js/ajaxhelper.js",
                "~/statics/admin/js/wx.js",
                "~/statics/jx/js/global.js"));



            //common validate js
            //bundles.Add(new ScriptBundle("~/bundles/jqueryval").Include(
            //    "~/Scripts/jquery.validate*"));

            //global css
            //bundles.Add(new StyleBundle("~/Content/css").Include(
            //    "~/media/css/bootstrap.min.css",
            //    "~/media/css/bootstrap-responsive.min.css",
            //    "~/media/css/font-awesome.min.css",
            //    "~/media/css/style-metro.css",
            //    "~/media/css/style.css",
            //    "~/media/css/style-responsive.css",
            //    "~/media/css/default.css",
            //    "~/media/css/uniform.default.css"));

            //global js
            //bundles.Add(new ScriptBundle("~/bundles/jquery").Include(
            //    "~/Scripts/jquery-{version}.js",
            //    "~/media/js/jquery-migrate-1.2.1.min.js",
            //    "~/media/js/jquery-ui-1.10.1.custom.min.js",
            //    "~/media/js/bootstrap.min.js"));

            //global core js
            //bundles.Add(new ScriptBundle("~/bundles/core_plugins").Include(
            //    "~/media/js/jquery.slimscroll.min.js",
            //    "~/media/js/jquery.blockui.min.js",
            //    "~/media/js/jquery.cookie.min.js",
            //    "~/media/js/jquery.uniform.min.js"));

            //index css
            //bundles.Add(new StyleBundle("~/Content/index_css").Include(
            //    "~/media/css/jquery.gritter.css",
            //    "~/media/css/daterangepicker.css",
            //    "~/media/css/fullcalendar.css",
            //    "~/media/css/jqvmap.css",
            //    "~/media/css/jquery.easy-pie-chart.css"));

            //index plugins
            //bundles.Add(new ScriptBundle("~/bundles/index_plugins").Include(
            //    "~/media/js/jquery.vmap.js",
            //    "~/media/js/jquery.vmap.russia.js",
            //    "~/media/js/jquery.vmap.world.js",
            //    "~/media/js/jquery.vmap.europe.js",
            //    "~/media/js/jquery.vmap.germany.js",
            //    "~/media/js/jquery.vmap.usa.js",
            //    "~/media/js/jquery.vmap.sampledata.js",
            //    "~/media/js/jquery.flot.js",
            //    "~/media/js/jquery.flot.resize.js",
            //    "~/media/js/jquery.pulsate.min.js",
            //    "~/media/js/date.js",
            //    "~/media/js/daterangepicker.js",
            //    "~/media/js/jquery.gritter.js",
            //    "~/media/js/fullcalendar.min.js",
            //    "~/media/js/jquery.easy-pie-chart.js",
            //    "~/media/js/jquery.sparkline.min.js"));

            //index script
            //bundles.Add(new ScriptBundle("~/bundles/index_scripts").Include(
            //    "~/media/js/app.js",
            //    "~/media/js/index.js"));

            //manage table css
            //bundles.Add(new StyleBundle("~/Content/managegrid_css").Include(
            //    "~/media/css/select2_metro.css",
            //    "~/media/css/DT_bootstrap.css"));

            //manage table plugins
            //bundles.Add(new StyleBundle("~/Content/managegrid_plugins").Include(
            //    "~/media/js/select2.min.js",
            //    "~/media/js/jquery.dataTables.js",
            //    "~/media/js/DT_bootstrap.js"));

            //manage table plugins scripts
            //bundles.Add(new StyleBundle("~/Content/managegrid_scripts").Include(
            //    "~/media/js/app.js",
            //    "~/media/js/table-managed.js"));

            //login script
            //bundles.Add(new ScriptBundle("~/bundles/login_scripts").Include(
            //    "~/media/js/app.js",
            //    "~/media/js/login.js"));
        }
    }
}
