local ddb = import 'ddb.docker.libjsonnet';

local db_user = "sandbox";
local db_password = "sandbox";
local db_name = "sandbox";

local domain = std.join('.', [std.extVar("core.domain.sub"), std.extVar("core.domain.ext")]);
local port_prefix = std.extVar("docker.port_prefix");

local php_workdir = "/var/www/html";
local node_workdir = "/app";
local mysql_workdir = "/app";

local prefix_port(port, output_port = null)= [port_prefix + (if output_port == null then std.substr(port, std.length(port) - 2, 2) else output_port) + ":" + port];

ddb.Compose() {
	"services": {
		"php": ddb.Build("php")
		    + ddb.User()
		    + ddb.XDebug()
		    + ddb.Binary("symfony", php_workdir, "symfony")
		    + ddb.Binary("composer", php_workdir, "composer")
		    + ddb.Binary("php", php_workdir, "php")
		    + {
			"volumes": [
				"php-composer-cache:/composer/cache:rw",
				"php-composer-vendor:/composer/vendor:rw",
				ddb.path.project + ":" + php_workdir + ":rw",
                ddb.path.project + "/.docker/php/php-config.ini:/usr/local/etc/php/conf.d/php-config.ini"
			],
		},
	}
}
