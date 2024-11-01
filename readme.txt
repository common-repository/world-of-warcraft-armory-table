=== World of Warcraft - Armory Table ===
Contributors: Kilomoana
Donate link: http://kilo-moana.com
Tags: armory, blizzard, characters, warcraft, world of warcraft, warcraft wordpress, wow wordpress, wow plugin, wow arsenal, arsenal, wow armory
Requires at least: 3.0
Tested up to: 3.9.2
Stable tag: 0.3.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Wordpress Plugin to display your World of Warcraft guild characters.<br>

== Description ==

This Plugin allows you to include a sortable and searchable list of your World of Warcraft Guilds Characters.<br>
<br>
See it in action at: (http://kilo-moana.com/world-warcraft-armory-table/)
<br>
For now it only displays the characters of your World of Warcraft Guilds Characters in a sortable and searchable list.<br>
<br>
It is planned to provide further features like multiple Tables with chooseable Characters or groups of characters to display, Like Raid groups and the assignment of characters to blog users.<br>
<br>
Further there are additional plugins planned which works together with this one as the basic. Like an Event Planner for your raids.<br>

== Installation ==

Download plugin through the wordpress included plugin downloader.<br>
Activate plugin in plugin tab.<br>
<br>
Go to the Settings section and fill in your Guild's Details.<br>
<br>
Press the Poll Characters button in the Backend.<br>
<br>
= Basic Setup =
Place the [wow-arsenal-characters] shortcode in one of your posts.
<br>
= Shortcode Options =
* [wow-arsenal-characters] - Normal View (Searchable and Sortable Table, one per page)
* [wow-arsenal-characters display="class"] - View by Classes
* [wow-arsenal-characters ranks="0,1,2,3"] - Display only selected ranks
* [wow-arsenal-characters exranks="0,1,2,3"] - Exclude choosen ranks
* [wow-arsenal-characters orderby="name"] - Order by name, rank, lvl, etc.
* [wow-arsenal-characters id="1"], [wow-arsenal-characters id="1"] - multiple normal Views on one page or post
* [wow-arsenal-characters title="Officers"] - Custom Title
* [wow-arsenal-characters number="15"] - Number of Characters in Normal view
* Do not Display Details in normal view. Shortcode atts: nopage (No Pagination), noinfo (No Information after table like 10 of 20 characters), nosearch, nosort (No user Sorting), nolength (No user option for table length), nodetails (turn off all)

== Frequently Asked Questions ==

= How do I use the plugin =

Place the [wow-arsenal-characters] shortcode on one of your pages.

= The Table is not loading Characters =

Please ensure that "allow-url-fopen" is turned to on in your php.ini or in the config of your webhoster.

== Screenshots ==
1. Frontend
2. Settings
3. Backend Table
4. Backend Buttons

== Changelog ==

= 0.3.2 =

* Fixed: Error from 0.3.1

= 0.3.1 =

* Fixed: Small CSS fix in class display

= 0.3 =

* Added: Option for choosing the max. number of columns in class display
* Fixed: table CSS in class display

= 0.2.9.1 =

* Added: Shortcode for single names. Just type [wow-char]CHARNAME[/wow-char] and you will get a charactername in class colors and with a link to the wow arsenal.

= 0.2.9 =

* Fixed: Only english classnames due to bug
* Added: Button to reset classnames to blog's language
* Fixed: CSS issues
* Added: Localized armory link in classes view (requires option in backend)

= 0.2.8 =

* Fixed: Submit Error

= 0.2.7 =

* Fixed: Submit Error

= 0.2.6 =
* Added: Options for changing normal view colors
* Added: Options for changing class view colors
* Added: Options for changing class colors
* Added: Option to change Classnames to English ones
* Fixed: Security issues (Thanks goes to ZAM from http://buffed.de for supporting)
* Update: Updated displaying of characters in the Backend

= 0.2.5 =
* Fixed issue with multiple normal tables on one page
* Fixed display issues of Characters by Class
* Fixed wrong Shortcode in Readme

= 0.2.4 =
* Small Bugfixes

= 0.2.3 =
* Fixed Update Issue

= 0.2.2 =
* Fixed Multisite Issue

= 0.2.1 =
* Small Bugfixes

= 0.2 =
* Added: View by Classes. Shortcode: [wow-arsenal-characters display="class"]
* Added: Script that changed Class-View size by parent container.
* Added: Show only specific Ranks. Shortcode: [wow-arsenal-characters ranks="0,1,2,3"]
* Added: Exclude specific Ranks. Shortcode: [wow-arsenal-characters exranks="0,1,2,3"]
* Added: Sort Characters. Shortcode: [wow-arsenal-characters orderby="name"]
* Added: Id for normal display. Adds the possibility to add multiple tables to one Page or Post. Shortcode: [wow-arsenal-characters id="1"]
* Added: Custom Title. Shortcode: [wow-arsenal-characters title="Officers"]. For no title just use title="".
* Added: Do not Display Details in normal view. Shortcode atts: nopage (No Pagination), noinfo (No Information after table like 10 of 20 characters), nosearch, nosort (No user Sorting), nolength (No user option for table length), nodetails (turn off all)
* Added: Shortcode option for normal view to change number of characters shown by table. Shortcode: [wow-arsenal-characters number="15"]

= 0.1.1 =
* Fixed a Failure which did not load characters of the same name, where one name contains special characters (please Deactivate and Activate the plugin to fix it)

= 0.1 =
* Initial release

== Upgrade Notice ==

= 0.1.1 =
* Fixed a Failure which did not load characters of the same name, where one name contains special characters (please Deactivate and Activate the plugin to fix it)

= 0.1 =
* Initial release