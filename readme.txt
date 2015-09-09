=== SVN Updater ===
Contributors: szepe.viktor
Donate link: https://szepe.net/wp-donate/
Tags: administration, installation, update, updater, svn, trunk, plugins
Requires at least: 4.0
Tested up to: 4.2.2
Stable tag: 0.1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Install any WordPress.org plugin's trunk version from SVN.

== Description ==

Have you ever needed to upgrade a plugin to it's latest (unreleased) version?
With SVN Updater you get a new action (Trunk) on the Plugins page.

Development of this plugin is done [on GitHub](https://github.com/szepeviktor/svn-updater).
Pull requests are welcome.

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload `svn-updater.php` to the `/wp-content/plugins/svn-updater/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= Should I use this plugin in production? =

No! Not without understanding its internals.

= How does this plugin work? =

It adds an action - which is similar to the Install button - to the Plugins page
and hooks plugin's package URL. SVN Updater is actually not using SVN at all.
WordPress.org provides a ZIP file of trunk version in form of
https://downloads.wordpress.org/plugin/PLUGIN-NAME.zip

== Changelog ==

= 0.1.0 =
* Initial release
