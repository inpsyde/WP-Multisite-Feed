# Inpsyde Multisite Feed
Consolidates all network feeds into one.

## Description
Create a separate feed for your whole multisite. This feed will have a custom url. You can limit the number of entries per blog and the maximum number of entries for the whole feed.

### Made by [Inpsyde](http://inpsyde.com) &middot; We love WordPress
Have a look at the premium plugins in our [market](http://marketpress.com).

## Installation
### Requirements
 * PHP 5.3 (important, use namespaces)
 * WordPress version 3.3 and later (tested at 3.5-Alpha (nightly build))

### Installation
 1. Upload the plugin directory to the `/wp-content/plugins/` directory or use the installer via backend of WordPress
 1. Activate the plugin through the 'Plugins' menu in WordPress in the Network Admin
 1. Configure it in Settings > MultiSite Feed

## Screenshots
 1. [Settings](https://github.com/inpsyde/WP-Multisite-Feed/blob/master/screenshot-1.png)

## Other Notes
### Bugs, technical hints or contribute
Please give us feedback, contribute and file technical bugs on this [GitHub Repo](https://github.com/inpsyde/WP-Multisite-Feed), use Issues.

### Available Filter Hooks
 * `rss_update_period` - Update period, global filter from WordPress Core; works on all RSS feeds
 * `rss_update_frequency` - Update frequency,  global filter from WordPress Core; works on all RSS feeds
 * `inpsmf_feed_url` - Filter feed url, on default use the slug of plugin settings
 * `inpsmf_feed_title` - Filter the feed title
 * `inpsmf_feed_description` - Filter feed description

### Available Action Hooks
 * `rss2_ns` - Runs inside the root XML element in an RSS 2 feed (to add namespaces). It is an core hook, works on all feeds.
 * `rss2_head` - Runs just after the blog information has been printed in an RSS 2 feed, just before the first entry. It is an core hook, works on all feeds.
 * `rss2_item` - Runs just after the entry information has been printed (but before closing the item tag) for each blog entry in an RSS 2 feed. It is an core hook, works on all feeds.

### Authors, Contributors
 * [Inpsyde GmbH](https://github.com/inpsyde)
 * [Eric](https://github.com/eteubert)
 * [Frank](https://github.com/bueltge)

### Licence
Good news, this plugin is free for everyone! Since it's released under the GPL, you can use it free of charge on your personal or commercial blog. But if you enjoy this plugin, you can thank me and leave a [small donation](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=6069955 "Paypal Donate link") for the time I've spent writing and supporting this plugin. And I really don't want to know how many hours of my life this plugin has already eaten ;)

### Translations
The plugin comes with various translations, please refer to the [WordPress Codex](http://codex.wordpress.org/Installing_WordPress_in_Your_Language "Installing WordPress in Your Language") for more information about activating the translation. If you want to help to translate the plugin to your language, please have a look at the .pot file which contains all defintions and may be used with a [gettext](http://www.gnu.org/software/gettext/) editor like [Poedit](http://www.poedit.net/) (Windows) or the plugin [Localization](http://wordpress.org/extend/plugins/codestyling-localization/) for WordPress.
