# pat_public_bar
Add a bar on your Textpattern websites to access side to side from public to admin pages.

## Installation

After plugin installation and activation, visit your Textpattern _Preferences_ page to verify the content of the "_This interface URL_" field (must reflects your Textpattern administration interface URL) and if needed correct it accordingly. 

## Plugin help

Just add a `<txp:pat_public_bar />` tag into your page templates where to show this bar on your website (public side) only available for login-in users (best place just after the opening HTML `<body>` tag).

Notice: due to the support of multi-site installation the bar only disappear when the client's browser is closed.

## Attributes for designers

These attributes allow you to customize the entire appearance of the public bar:

* `position` string (optional): CSS position of the bar. Set to `absolute` is better for small screens support. Default: `fixed`.
* `bgcolor` string (optional): change the background color of the bar. Default: `#23282d`.
* `color` string (optional): change the font color into the bar. Default: `#fff`.
* `title` string (optional): change the color of the different parts title. Default: `#84878b`.
* `hover` string (optional): change the color of links on hover. Default: `#62bbdc`.
* `icon` string (optinal): change the color of the SVG icons. Default: `#ccc`.

# Changelog

    15th October 2015: v 0.3.3. New UI, better support for multisite installation and 3 new attributes for customization.
    2d August 2015: v 0.3.2. Admin privs can access to "Section", "Page" and "Style" tabs.
    1st August 2015: v. 0.3.1. Add "bgcolor" and "color" attributes.
    30th July 2015: v. 0.3.0. Support for multi-site installations.
    29th April 2015: v 0.2. Add image & category page links.
    25th July 2014: first release.
