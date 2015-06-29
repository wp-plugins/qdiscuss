### Changelog

#### v0.4.5

1. Redesign the dashboard style

#### v0.4.4

1. If not extension installed, not show the spinning.
2. Using Less to manage the dashboard styles.

#### v0.4.3

1. Add Simplied Chinese language translation.
2. Add Remove button for processing the uninstallation of QDiscuss's extension.
3. Add new field display_name and only show the display_name in front, but login should be using the username field, same as WordPress.
4. Add spinning show before get the extensions data from server.

#### v0.4.2

1. Enhancement : View count, when view count > 1000 : 1k
2. Fix: creating discussion without title issue

#### v0.4.1

1. Update de.json for Geman language support
2. Fix the qd_manager_server not found error
3. Add counting the extension update time

#### v0.4 

1. Add auto-update extension process, and can auto-install the QDiscuss extensions in WordPress backend
2. Add German lanuage support
3. Fix the user role setting problem

####  v0.3.2 

1. Fixed: can not edit the config settings at backend.

#### v0.3.1

1. Auto config the .htaccess file when activate qdiscuss.
2. Limit the image width at the discussion page.


#### v0.3

1. Fix the guest can't see user's activity issue.

#### v0.2

1. Create service provider mechanism and core application container for better code's structure.

2. Add two middweares login_with_cookie and login_with_header for more security.

3. Fix the logout issue.

4. Inject the Slim package into  QDiscuss's Container's router part.

5. Add view counts of discussion.

6. Add Muti-Languages support, welcome to contribute to [Translation Project Of QDiscuss](https://github.com/ColorVila/QDiscuss-languanges).

7. New extension: "markdown editor" is here: http://colorvila.com/qdiscuss-extensions/.

#### v0.1.0

1. Fix discussion moved post notice error
2. Fix discussion stickied post notice error
3. Add checking the extension's version whether be matched  with the qdisucss's version
4. Check the categories table exist or not when add categories extension, avoid deleting the categories data.
5. Fix the bug: can't edit and add category
6. Add new database migration class
7. Add potal for new extension: mentions

#### v0.0.9

1. Move the qdiscuss extensions directly into wp-content/qdiscuss/extensions.
2. New backend process of activity

#### v0.0.8

1. Fix the post grant permission issue

2. New table field size.

3. Fix the extensions auto-detect issue.

####  v0.07 

1. New drop tables process, include dropping the extension's table

2. Add sticky button when add the sticky extension.

#### v0.0.6

1. Add Extension Mechanism, You can see how to add extension in your QDiscuss forum here: http://colorvila.com/qdiscuss-extensions/

2. js and css auto-compile

3. Add categories and sticky extension, you can download both freely in http://colorvila.com/qdiscuss-extensions/

#### v0.0.5

1. Fix the uninstall error

= v0.0.4 =

1. Fix user avatar upload error

#### v0.0.4

1. Fix user avatar upload error

#### v0.0.3

1. Remove wp-rest-api plugin from qdiscuss
2. Fix the php warning in php5.4 when set user's profile
3. Add an new config field named forum_welcome_title for displaying some welcome words
4. Add version check and upgrade process 

#### v0.0.2

1.  compress js for speeding up page load
2.  fix logout url error
3.  add user search in QDiscuss Dashboard users page
4.  add full text search in search form

#### v0.0.1

code structure change


