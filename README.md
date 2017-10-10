# pat_public_bar
Add a drop down animated bar on your Textpattern websites to access side to side from public to admin pages.

![pat_public_bar preview](https://github.com/cara-tm/pat_public_bar/raw/master/pat-public-bar-preview.png)

Gives you access, for login-in users only, to:

* Textpattern CMS administration interface;
* A link to the TXP community Forum;
* the current _Section_ for edition;
* the current _Page_ for edition;
* the _Forms_ page for edition;
* the _CSS_ page for edition;
* the _Files_, _Links_, _Categories_ pages for edition;
* the current _Article_, _Image_ or _Comment_, when viewed individual public pages, for edition;
* the _Preferences_ page;
* the _Log_ page;
* and a link to log out.

...depending on user privileges.


## Installation

After plugin installation and activation, visit your Textpattern _Preferences_ page to verify the content of the "_This interface URL_" field (must reflects your Textpattern administration interface URL) and if needed correct it accordingly.
You can disable this plugin without the need to remove all its tags throughout your entire website simply by toggling to "`No`" the "_Disable temporarily pat_public_bar_" field.

## Usage

Into your page templates where to show this bar on your website (public side) only available for login-in users (best place just after the opening HTML `<body>` tag) just add:

    <txp:pat_public_bar />

...or, TXP 4.7.x onwards (with short tags support):

    <pat::public_bar />

Notice: due to the support of multi-site installation the bar only disappear when the client's browser is closed.

## Attributes for designers

These attributes allow you to customize the entire appearance of the public bar:

* `position` string (optional): CSS position of the bar. Set to `absolute` is better for small screens support. Default: `fixed`.
* `js_top_adjust` string (optional - v 0.3.7 onward): adjusts the top position value in mobile context (depending of the website template, the value injected by javascript pushes the bar outside the viewport). Default `-45` (for `top: -45px`).
* `bgcolor` string (optional): change the background color of the bar. Default: `#23282d`.
* `color` string (optional): change the font color into the bar. Default: `#fff`.
* `title` string (optional): change the color of the different parts title. Default: `#84878b`.
* `hover` string (optional): change the color of links on hover. Default: `#62bbdc`.
* `icon` string (optinal): change the color of the SVG icons. Default: `#ccc`.

# Changelog

* 10th October 2017: v 0.4.0. Better support for small screens.
* 25th September 2017: v 0.3.9. Better positioning & js adaptation.
* 22th September 2017: v 0.3.8. Consistent "logout" into the list.
* 24th August 2017: v 0.3.7. Adds a link to the TXP community Forum. Improvements.
* 18th July 2017: v 0.3.6. Adds a preference radio button to disable this plugin.
* 17th July 2017: v 0.3.5. Removes a strong tag into the "Article" link. CSS improvements.
* v 0.3.4.
* 15th October 2015: v 0.3.3. New UI, better support for multisite installation and 3 new attributes for customization.
* 2d August 2015: v 0.3.2. Admin privs can access to "Section", "Page" and "Style" tabs.
* 1st August 2015: v. 0.3.1. Add "bgcolor" and "color" attributes.
* 30th July 2015: v. 0.3.0. Support for multi-site installations.
* 29th April 2015: v 0.2. Add image & category page links.
* 25th July 2014: first release.
