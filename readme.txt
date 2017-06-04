=== Inpsyde Multisite Feed ===
Contributors: inpsyde, Bueltge, eteubert
Tags: feed, rss, archive, multisite, network
Requires at least: 3.0
Tested up to: 4.7.5
Stable tag: 2.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Consolidates all network feeds into one.

== Description ==
Create a separate feed for your whole multisite. This feed will have a custom url. You can limit the number of entries per blog and the maximum number of entries for the whole feed.

= Bugs, technical hints or contribute =
Please give me feedback, contribute and file technical bugs on this [GitHub Repo](https://github.com/inpsyde/WP-Multisite-Feed), use Issues.

**Crafted by [Inpsyde](http://inpsyde.com) &middot; Engineering the web since 2006.**

Take a look at [our website](https://inpsyde.com/) if you like to get to know us.

== Installation ==
= Requirements =
 * PHP 5.3 (important, use namespaces)
 * WordPress version 3.3 and later (see tested up to)

= Installation =
 1. Upload the plugin directory to the `/wp-content/plugins/` directory or use the installer via backend of WordPress
 1. Activate the plugin through the 'Plugins' menu in WordPress in the Network Admin
 1. Configure it in Settings > MultiSite Feed

== Screenshots ==
 1. Settings

== Other Notes ==
= Bugs, technical hints or contribute =
Please give us feedback, contribute and file technical bugs on this [GitHub Repo](https://github.com/inpsyde/WP-Multisite-Feed), use Issues.

= Available Filter Hooks =
 * `rss_update_period` - Update period, global filter from WordPress Core; works on all RSS feeds
 * `rss_update_frequency` - Update frequency,  global filter from WordPress Core; works on all RSS feeds
 * `inpsmf_feed_url` - Filter feed url, on default use the slug of plugin settings
 * `inpsmf_feed_title` - Filter the feed title
 * `inpsmf_feed_description` - Filter feed description

= Available Action Hooks =
 * `rss2_ns` - Runs inside the root XML element in an RSS 2 feed (to add namespaces). It is an core hook, works on all feeds.
 * `rss2_head` - Runs just after the blog information has been printed in an RSS 2 feed, just before the first entry. It is an core hook, works on all feeds.
 * `rss2_item` - Runs just after the entry information has been printed (but before closing the item tag) for each blog entry in an RSS 2 feed. It is an core hook, works on all feeds.

= Licence =
Good news, this plugin is free for everyone! Since it's released under the GPL, you can use it free of charge on your personal or commercial blog. But if you enjoy this plugin, you can thank me.

= Translations =
The plugin comes with various translations, please refer to the [WordPress Codex](http://codex.wordpress.org/Installing_WordPress_in_Your_Language "Installing WordPress in Your Language") for more information about activating the translation. If you want to help to translate the plugin to your language, please have a look at the .pot file which contains all defintions and may be used with a [gettext](http://www.gnu.org/software/gettext/) editor like [Poedit](http://www.poedit.net/) (Windows) or the plugin [Localization](http://wordpress.org/extend/plugins/codestyling-localization/) for WordPress.

== Changelog ==
= 2.0.0 () =
* Complete rewrite of entire codebase using PHP 5.6 features
* Add composer.json
* Add `category` tag in xml to add each site/blog title to each item
* Improve escaping when rendering feeds
* Allow passing query parameters into feed urls.
* Make query args filterable

= 1.0.3 (2015-04-14) =
* Add settings options to filter for authors
* Remove fix value for filter 'pre_option_rss_use_excerpt' to set always full or excerpt
* Code Maintenance

= 1.0.2 (02/01/2014) =
 * Fix on DB select for old installations, before Multiiste (WPMU)
 * Add option for full feed

= 1.0.1 (06/20/2013) =
 * Add more possibilities on Settings
 * Fix small major problems

= 1.0.0 =
 * Initial Release

