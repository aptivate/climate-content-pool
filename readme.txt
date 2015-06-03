=== REEEP Content Pool ===

Contributors: Jimmy O'Higgins
Tags: reegle, content pool, api, reeep
Requires at least: 3.0
Tested up to: 3.0
Stable tag: trunk

REEEP content pool is a plugin that pushes wordpress posts to the REEEP content pool

== Description ==
The REEEP Content Pool plugin pushes text to the REEEP content pool via the REEGLE API

You will need an authentication token and a list of post types that you wish to be added to the pool.


= How It Works =

Every time the user saves a draft or updates a post, it will see what type of post it is, and if it's one of the listed ones it will add it to the content pool via API.

== Installation ==

1. Upload the `reeep-content-pool` folder to your `wp-content/plugins` folder.
2. Go to the "Plugins" administration panel.
3. Activate Reep Content Pool

== Changelog ==

0.1 (February 10, 2015)

* Create a plugin that takes an auth code and list of post types and pushes posts to the API.
