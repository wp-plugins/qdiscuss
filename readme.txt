=== QDiscuss ===
Contributors: ColorVila Team, zairl23
Tags: forum, discuss, bbs, bbpress
Requires at least: 3.9
Tested up to: 4.1
Stable tag: 0.4.5
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Next-generation Forum Plugin for WordPress which helps you create modern forum on your site.

== Description ==

QDiscuss is a modern designed, well-architected, powerful forum plugin that is easy to use,  with which you can easily add a forum to your site and allow your members to start their own conversations. 

QDiscuss is a WordPress native plugin, all data  and code run on your WordPress site, and the users in WordPress will be default set as the members of QDiscuss.

<a href="http://colorvila.com/qdiscuss-plugin">Click to see Live Demo</a>

<h3>Features:</h3>
1.   WordPress native plugin, your data belongs to you
2.   One page app
3.   Three levels roles: Administrator, Moderator, Members
4.   Forum avatar upload
5.   Dynamic notifications
6.   Born mobile, born to touch
7.   Reply while you read
8.   Real time updates
9.   Categories extensions
10. Sticky extensions

<h3>To Do List</h3>
1. User profile
2. Attachment when post
3. ...

<h3>Contributing</h3>
The QDiscuss is now at the early development stage, we'd love to hear general feedback on the <a href="http://colorvila.com/qdiscuss">QDiscuss Development Forum</a>, and on <a href="https://wordpress.org/support/topic/qdiscuss-need-feedback?replies=1#post-6864335">WordPress Support forum</a>

And thanks to tobscure and his Flarum Forum, the qdiscuss was based on his work.

== Installation ==

<h3>Requirement</h3>

1. PHP 5.4 above
2. WordPress 3.9 or higher

<h3>From your WordPress dashboard</h3>

1. Visit 'Plugins > Add New'
2. Search for 'QDiscuss'
3. Activate QDiscuss from your Plugins page. 
4. Visit 'Settings > Permalinks', set your permalink as pretty links.
5. Visit http://your-site-url/qdiscuss, to enjoy your forum.

<h3>Manual installation</h3>
1. Upload qdiscuss.zip to the /wp-content/plugins/ directory
2. Unzip the compressed plugin
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Visit 'Settings > Permalinks', set your permalink as pretty links.
5. Visit http://your-site-url/qdiscuss, to enjoy your forum.

== Frequently Asked Questions ==

= Can I use QDiscuss on my on line production? =

Better not, Qdiscuss is still on early development, you can wait for a few time when the first release.

== Screenshots ==

<a href="http://colorvila.com/qdiscuss-plugin">Click to see Live Demo</a>

== Changelog ==

= v0.4.5 =

1. Redesign the dashboard style

= v 0.4.4 = 

1. If not extension installed, not show the spinning.
2. Using Less to manage the dashboard styles.

= v0.4.3 =

1. Add Simplied Chinese language translation.
2. Add Remove button for processing the uninstallation of QDiscuss's extension.
3. Add new field display_name and only show the display_name in front, but login should be using the username field, same as WordPress.
4. Add spinning show before get the extensions data from server.

= v0.4.2 =

1. Enhancement : View count, when view count > 1000 : 1k
2. Fix: creating discussion without title issue

= v0.4.1 =

1. Update de.json
2. Fix the qd_manager_server not found error
3. Add counting the extension update time

= v0.4 = 

1. Add auto-update extension process, and can auto-install the QDisucss extension in WordPress backend
2. Add German lanuage support
3. Fix the user role setting problem

= v0.3.2 =

1. Fixed: can not edit the config settings at backend

= v0.3.1 =

1. Config the .htaccess file using php script.
2. Limit the image width at the discussion page.

= v0.3 =

1. Fix the guest can't see user's activity issue.

= v0.2 =

1. Create service provider mechanism and core application container for better code's structure.
2. Add two middweares login_with_cookie and login_with_header for more security.
3. Fix the logout issue
4. Inject the Slim package into  QDiscuss's Container's router part
5. Add view counts of discussion
6. Add Muti-Languages support, welcome to contribute to Translation Project Of QDiscuss: https://github.com/ColorVila/QDiscuss-languanges.
7. New extension: "markdown editor" is here: http://colorvila.com/qdiscuss-extensions/

= v0.1.0 =

1. Fix discussion moved post notice error
2. Fix discussion stickied post notice error
3. Add checking the extension's version whether be matched  with the qdisucss's version
4. Check the categories table exist or not when add categories extension, avoid deleting the categories data.
5. Fix the bug: can't edit and add category
6. Add new database migration class
7. Add potal for new extension: mentions

= v0.0.9 =

1. Move the qdiscuss extensions directly into wp-content/qdiscuss/extensions.
2. New backend process of activity

= v0.0.8 =

1. Fix the post grant permission issue
2. New table field size.
3. Fix the extensions auto-detect issue.
4. Add first discussion content.

= v0.07 =

1. New drop tables process, include dropping the extension's table
2. Add sticky button when add the sticky extension.

= v0.06 =

1. Add Extension Mechanism, You can see how to add extension in your QDiscuss forum here: http://colorvila.com/docs/category/qdiscuss/
2. js and css auto-compile
3. Add categories and sticky extension, you can download both freely in http://colorvila.com/qdiscuss-extensions/

= v0.0.5 =

1. Fix the uninstall error

= v0.0.4 =

1. Fix user avatar upload error

= v0.0.3 =

1. Remove wp-rest-api plugin from qdiscuss
2. Fix the php warning in php5.4 when set user's profile
3. Add an new config field named forum_welcome_title for displaying some welcome words
4. Add version check and upgrade process 

= v0.0.2 =

1.  compress js for speeding up page load
2.  fix logout url error
3.  add user search in QDiscuss Dashboard users page
4.  add full text search in search form

= v0.0.1 =

code structure change



