=== Climate Content Pool ===
Contributors: Aptivate
Tags: climate tagger, content pool, api
Requires at least: 3.7
Tested up to: 4.8.1
Stable tag: 1.0.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Climate Content Pool is a plugin that uploads WordPress posts to the
Climate Tagger Content Pool

== Description ==

The Climate Tagger Content Pool Plugin for WordPress is a simple, FREE and
easy-to-use way to upload your posts to the “Content Pool” of the well-known
[Climate Tagger API](api.climatetagger.net). The API has been
helping knowledge-driven web sites better catalogue, categorize, contextualize
and connect their data with that from the broader climate knowledge community
since 2011. Through its “Content Pool” Climate Tagger allows organizations to
easily connect their data and information resources with other Climate Tagger
users. By uploading your posts to the “Content Pool” of the Climate Tagger, you
will make your content available to a large audience of users looking for
climate-related information.

After installation, a tick-box will appear on the screen where you add or edit
your posts, which will allow you to send the post content to the Content Pool.

[Follow this project on Github](https://github.com/aptivate/climate-content-pool)


== Installation ==

1. Upload the plugin to the `wp-content/plugins/` directory.
2. Activate it through the **Plugins** menu in WordPress.
3. Register at [http://api.climatetagger.net/register](http://api.climatetagger.net/register) to get your FREE API token (or use your exiting one)
4. Enable the plugin and enter the API key (**Settings** -> **Climate Content Pool**)
5. A tick-box will appear on the screen where you add or edit
your posts, which will allow you to send the post content to the Content Pool.
6. If you wish to send the content of pages as well as posts to the Content Pool, add `page` to the comma-separated list of **Post types** on the **Settings** page

== Changelog ==

= 1.0.2 =
* Test fixes only - no code changes

= 1.0.1 =
* Documentation updates
* Replaced references to reegle API with Climate Tagger

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
