=== Climate Content Pool ===
Contributors: Aptivate
Tags: reegle, content pool, api, reeep
Requires at least: 3.7
Tested up to: 4.2.2
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Climate Content Pool is a plugin that pushes WordPress posts to the reegle Content Pool

== Description ==

The Climate Content Pool plugin uses the [reegle Content Pool Push
API](http://api.reegle.info/documentation/) to add content to the Reegle
Content Pool.

[Follow this project on Github](https://github.com/aptivate/climate-content-pool)


== Installation ==

1. Upload the plugin to the `wp-content/plugins/` directory.
2. Activate it through the **Plugins** menu in WordPress.
3. [Register with reegle for your API key](http://api.reegle.info/register)
4. Enable the plugin and enter the API key (**Settings** -> **Climate Content Pool**)
5. A tick-box should now appear in the admin interface to send the post content to the pool when it is updated

== Changelog ==

= 1.0.0 =

* First version


== Upgrade Notice ==

= 1.0.0 =
* First version


== Development ==

This plugin uses [wp-cli](http://wp-cli.org/) and [PHPUnit](https://phpunit.de/) for testing.
The tests require [runkit](https://github.com/zenovich/runkit) for mocking functions.

* Grab the latest source from github:

`
$ git clone git@github.com:aptivate/climate-content-pool.git
`

* Install [wp-cli](http://wp-cli.org/#install)
* Install [PHPUnit](https://phpunit.de/)
* Set up runkit:

`
$ git clone https://github.com/zenovich/runkit.git
$ cd runkit
$ phpize
$ ./configure
$ sudo make install
`

Add the following lines to `/etc/php5/cli/php.ini`:

`
extension=runkit.so
runkit.internal_override=1
`

* Install the test WordPress environment:

`
cd climate-content-pool
bash bin/install-wp-tests.sh test_db_name db_user 'db_password' db_host version
`

where:

    `test_db_name` is the name for your **temporary** test WordPress database
    `db_user` is the database user name
    `db_password` is the password
    `db_host` is the database host (eg `localhost`)
    `version` is the version of WordPress (eg `4.2.2` or `latest`)

* Run the tests
`phpunit`
