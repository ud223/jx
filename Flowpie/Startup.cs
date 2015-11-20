using Microsoft.Owin;
using Owin;

[assembly: OwinStartupAttribute(typeof(Flowpie.Startup))]
namespace Flowpie
{
    public partial class Startup
    {
        public void Configuration(IAppBuilder app)
        {
            ConfigureAuth(app);
        }
    }
}
