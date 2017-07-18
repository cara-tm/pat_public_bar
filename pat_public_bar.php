/**
 * pat_public_bar Textpattern CMS plugin
 * @author:  © Patrick LEFEVRE, all rights reserved. <patrick[dot]lefevre[at]gmail[dot]com>
 * @link:    http://pat-public-bar.cara-tm.com
 * @type:    Admin + Public
 * @prefs:   no
 * @order:   5
 * @version: 0.3.5
 * @license: GPLv2
*/


/**
 * This plugin tag registry
 *
 */
if (class_exists('\Textpattern\Tag\Registry')) {
	Txp::get('\Textpattern\Tag\Registry')
		->register('pat_public_bar');
}

if (@txpinterface == 'admin') {

	global $prefs, $pretext;

	register_callback('_pat_public_bar_prefs', 'prefs', '', 1);
	register_callback('_pat_public_bar_cleanup', 'plugin_lifecycle.pat_public_bar', 'deleted');

	if ( _pat_public_bar_protocol().$prefs['siteurl'] != $prefs['pat_admin_url'] )
		// Restore the txp_login_public cookie with its value on the main domain if TXP is located on a sub domain.
		setcookie('txp_login_public', cs('txp_login_public'), 0, '/', '.'.$prefs['siteurl']);

	if ( $pretext['request-uri'] == 'logout' )
		setcookie('txp_login_public', cs('txp_login_public'), time() - 3600, '/', '.'.$prefs['siteurl']);

}


if (@txpinterface == 'public') {

	global $prefs;

	//register_callback(array('pat_public_bar', 'pat_public_bar'), 'textpattern');

	if ( gps('pat_logout') || gps('logout') ) {
		// Delete all domain txp_login_public cookie.
		setcookie('txp_login_public', cs('txp_login_public'), time() - 3600, '/', '.'.$prefs['siteurl']);
		sleep(1);
		// Redirect to logout page.
		header('Location:'.$prefs['pat_admin_url'].'/index.php?logout=1');
	}

}


/**
 * Inject an HTML block on the public side for all login-in users.
 * Allow access side to side from public to admin pages.
 *
 * @param  $atts  $things
 * @return HTML content for login-in users
 */
function pat_public_bar($atts) {

	global $prefs, $pretext, $thisarticle, $thiscomment;

	extract(lAtts(array(
		'interface' 	=> $prefs['pat_admin_url'],
		'position' 	=> 'fixed',
		'bgcolor' 	=> '#23282d',
		'color' 	=> '#fff',
		'title' 	=> '#84878b',
		'hover' 	=> '#62bbdc',
		'icon' 		=> '#ccc',
	), $atts));

	if ( cs('txp_login_public') ) {
	 
		if ($position != 'fixed' || $position != 'absolute')
			$position = 'fixed';

		/* List of user privs who have full bar access:
		   1 = administrators
		   2 = administrator assistants
		   6 = designers
		*/
		$pat_privs_array = array(1, 2, 6);

		$name = substr(cs('txp_login_public'), 10);
		$rs = safe_row('name, RealName, privs', 'txp_users', "name = '".doSlash($name)."'");

		ob_end_clean();

		if ( in_array($rs['privs'], $pat_privs_array) ) {

			$section = '<li><hr>'.ucfirst(gTxt('structure')).'</li> <li><a href="'.$interface.'/index.php?event=section&amp;step=section_edit&amp;name='.$pretext['s'].'"><svg version="1.1" xmlns="http://www.w3.org/2000/svg" x="0" y="0" width="22" height="22" viewBox="0 0 297 297" xml:space="preserve"><path d="M268.3 63.5h-20.5V42.7c0-5.5-4.5-10-10-10h-23.9V10c0-5.5-4.5-10-10-10h-175.3c-5.5 0-10 4.5-10 10v213.5c0 5.5 4.5 10 10 10h23.9v22.7c0 5.5 4.5 10 10 10h20.5v20.9c0 5.5 4.5 10 10 10h175.3c5.5 0 10-4.5 10-10V73.5C278.2 68 273.8 63.5 268.3 63.5zM38.7 19.9h155.4v193.6H38.7V19.9zM72.5 233.5h131.5c5.5 0 10-4.5 10-10V52.6h13.9v193.6H72.5V233.5zM258.3 277.1H102.9v-10.9h134.9c5.5 0 10-4.5 10-10V83.5h10.5V277.1z"/></svg>&nbsp;&bull;&nbsp;'.ucfirst(gTxt('section')).'&nbsp;:&nbsp;'.gTxt('edit').' ('.$pretext['s'].')</a></li> ';
			$page = '<li><a href="'.$interface.'/index.php?event=page&amp;name='.$pretext['page'].'&amp;_txp_token='.form_token().'"><svg version="1.1" xmlns="http://www.w3.org/2000/svg" x="0" y="0" width="22" height="22" viewBox="0 0 32 32" xml:space="preserve"><path d="M0 2v28h32L32 2H0zM10 4h12v2H10V4zM6 4h2v2H6V4zM2 4h2v2H2V4zM30 28H2V8h28V28zM30 6h-4V4h4V6z"/><rect x="18" y="20" width="6" height="2"/><rect x="18" y="16" width="10" height="2"/><rect x="18" y="12" width="10" height="2"/><rect x="4" y="12" width="10" height="10"/></svg>&nbsp;&bull;&nbsp;'.ucfirst(gTxt('page')).'&nbsp;:&nbsp;'.gTxt('edit').' ('.$pretext['page'].')</a></li> ';
			$form = '<li><a href="'.$interface.'/index.php?event=form"><svg version="1.1" xmlns="http://www.w3.org/2000/svg" x="0" y="0" width="22" height="22" viewBox="0 0 100 100" xml:space="preserve"><path d="M41.8 59.1v6.1L20 55.8v-5.2l21.8-9.4v6.2l-15.2 5.8L41.8 59.1zM55.2 41l-6.1 24.9c-0.2 0.8-0.4 1.5-0.5 1.9 -0.2 0.5-0.4 0.8-0.7 1.1 -0.3 0.3-0.8 0.4-1.4 0.4 -1.5 0-2.2-0.6-2.2-1.9 0-0.3 0.1-1.2 0.4-2.5l6-24.9c0.3-1.3 0.6-2.2 0.9-2.7 0.3-0.5 0.9-0.7 1.7-0.7 0.7 0 1.3 0.2 1.7 0.5 0.4 0.3 0.6 0.8 0.6 1.4C55.7 39 55.5 39.8 55.2 41zM80 55.8L58.2 65.3v-6.1l15.2-6 -15.2-5.9v-6.1L80 50.6V55.8z" fill="'.$icon.'"/><path d="M94 6H6c-3.3 0-6 2.7-6 6v76c0 3.3 2.7 6 6 6h88c3.3 0 6-2.7 6-6V12C100 8.7 97.3 6 94 6zM79 10c2.2 0 4 1.8 4 4s-1.8 4-4 4 -4-1.8-4-4S76.8 10 79 10zM68 10c2.2 0 4 1.8 4 4s-1.8 4-4 4 -4-1.8-4-4S65.8 10 68 10zM94 88H6V22h88V88zM90 18c-2.2 0-4-1.8-4-4s1.8-4 4-4 4 1.8 4 4S92.2 18 90 18z"/></svg>&nbsp;&bull;&nbsp;'.ucfirst(gTxt('tab_forms')).'&nbsp;:&nbsp;'.ucfirst(gTxt('edit')).'</a></li>';
			$style = '<li><a href="'.$interface.'/index.php?event=css"><svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0" y="0" width="22" height="22" viewBox="0 0 13.33 13.33" xml:space="preserve" enable-background="new 0 0 13.33 13.33"><path d="M12.48 0H0.85c-0.47 0-0.85 0.38-0.85 0.85v11.63c0 0.47 0.38 0.85 0.85 0.85h11.63c0.47 0 0.85-0.38 0.85-0.85V0.85C13.33 0.38 12.95 0 12.48 0zM3.96 7.73c0.28 0 0.5-0.05 0.68-0.12l0.14 0.87c-0.21 0.09-0.61 0.17-1.06 0.17 -1.23 0-2.02-0.75-2.02-1.95 0-1.11 0.76-2.03 2.18-2.03 0.31 0 0.66 0.06 0.91 0.15L4.6 5.71C4.46 5.64 4.25 5.59 3.93 5.59c-0.62 0-1.03 0.44-1.02 1.07C2.91 7.36 3.38 7.73 3.96 7.73zM6.45 8.65c-0.5 0-0.94-0.11-1.23-0.27l0.21-0.85C5.64 7.67 6.1 7.83 6.45 7.83c0.36 0 0.51-0.12 0.51-0.32S6.84 7.22 6.39 7.07C5.6 6.8 5.3 6.38 5.31 5.92c0-0.71 0.61-1.25 1.55-1.25 0.44 0 0.84 0.1 1.08 0.22l-0.21 0.82C7.56 5.62 7.23 5.49 6.9 5.49c-0.29 0-0.45 0.12-0.45 0.31 0 0.18 0.15 0.27 0.62 0.44C7.79 6.49 8.09 6.86 8.1 7.42 8.1 8.13 7.54 8.65 6.45 8.65zM9.83 8.65c-0.5 0-0.94-0.11-1.23-0.27l0.21-0.85C9.03 7.67 9.48 7.83 9.83 7.83c0.36 0 0.51-0.12 0.51-0.32s-0.12-0.29-0.56-0.44C8.99 6.8 8.69 6.38 8.7 5.92c0-0.71 0.61-1.25 1.55-1.25 0.44 0 0.84 0.1 1.08 0.22l-0.21 0.82c-0.17-0.09-0.5-0.22-0.83-0.22 -0.29 0-0.45 0.12-0.45 0.31 0 0.18 0.15 0.27 0.62 0.44 0.73 0.25 1.03 0.62 1.04 1.18C11.49 8.13 10.93 8.65 9.83 8.65z"/></svg>&nbsp;&bull;&nbsp;'.strtoupper(gTxt('css')).'&nbsp;:&nbsp;'.gTxt('edit').'</a></li> ';
			$pref = '<hr><li>'.gTxt('site_config').'</li> <li><a href="'.$interface.'/index.php?event=prefs"><svg version="1.1" xmlns="http://www.w3.org/2000/svg" x="0" y="0" width="22" height="22" viewBox="0 0 268.8 268.8" xml:space="preserve"><path d="M267.9 119.5c-0.4-3.8-4.8-6.6-8.6-6.6 -12.3 0-23.2-7.2-27.8-18.4 -4.7-11.5-1.7-24.8 7.5-33.2 2.9-2.6 3.2-7.1 0.8-10.1 -6.3-8-13.5-15.2-21.3-21.5 -3.1-2.5-7.6-2.1-10.2 0.8 -8 8.9-22.4 12.2-33.5 7.5 -11.6-4.9-18.9-16.6-18.2-29.2 0.2-4-2.7-7.4-6.6-7.8 -10-1.2-20.2-1.2-30.2-0.1 -3.9 0.4-6.8 3.8-6.7 7.7 0.4 12.5-6.9 24-18.4 28.7 -11 4.5-25.3 1.2-33.3-7.6 -2.6-2.9-7.1-3.3-10.1-0.9 -8.1 6.3-15.4 13.6-21.7 21.5 -2.5 3.1-2.1 7.6 0.8 10.2 9.4 8.5 12.4 21.9 7.5 33.5 -4.6 11-16.1 18.2-29.2 18.2 -4.3-0.1-7.3 2.7-7.8 6.6 -1.2 10.1-1.2 20.4-0.1 30.6 0.4 3.8 5 6.6 8.8 6.6 11.7-0.3 22.9 6.9 27.7 18.4 4.7 11.5 1.7 24.8-7.5 33.2 -2.9 2.6-3.2 7.1-0.8 10.1 6.2 8 13.4 15.2 21.3 21.5 3.1 2.5 7.6 2.1 10.2-0.8 8-8.9 22.4-12.2 33.5-7.5 11.6 4.9 18.9 16.6 18.2 29.2 -0.2 4 2.7 7.4 6.6 7.9 5.1 0.6 10.3 0.9 15.5 0.9 4.9 0 9.8-0.3 14.8-0.8 3.9-0.4 6.8-3.8 6.7-7.7 -0.5-12.5 6.9-24 18.4-28.7 11.1-4.5 25.3-1.2 33.3 7.6 2.7 2.9 7 3.2 10.1 0.8 8-6.3 15.3-13.5 21.7-21.5 2.5-3.1 2.1-7.6-0.8-10.2 -9.4-8.5-12.4-21.9-7.5-33.5 4.6-10.9 15.6-18.2 27.5-18.2l1.7 0c3.9 0.3 7.4-2.7 7.9-6.6C269 139.9 269.1 129.6 267.9 119.5zM134.6 179.5c-24.7 0-44.8-20.1-44.8-44.8 0-24.7 20.1-44.8 44.8-44.8 24.7 0 44.8 20.1 44.8 44.8C179.4 159.4 159.3 179.5 134.6 179.5z"/></svg>&nbsp;&bull;&nbsp;'.ucfirst(gTxt('tab_preferences')).'&nbsp;:&nbsp;'.ucfirst(gTxt('edit')).'</a></li>';
			$log = '<li><a href="'.$interface.'/index.php??event=log"><svg version="1.1" xmlns="http://www.w3.org/2000/svg" x="0" y="0" width="22" height="22" viewBox="0 0 525.2 525.2" xml:space="preserve"><path d="M196.9 525.2H328.2V0H196.9V525.2zM0 525.2h131.3V262.6H0V525.2zM393.9 164.1v361h131.3V164.1H393.9z"/></svg>&nbsp;&bull;&nbsp;'.ucfirst(gTxt('logs')).'&nbsp;:&nbsp;'.ucfirst(gTxt('view')).'</a>'.$pat_admin_interface.'</li>';

		}

		$logged = '<li><a href="'.$interface.'/index.php?event=admin"><svg version="1.1" xmlns="http://www.w3.org/2000/svg" x="0" y="0" width="22" height="22" viewBox="0 0 409.2 409.2" xml:space="preserve"><path d="M204.6 216.7c50.7 0 91.7-48.1 91.7-107.4 0-82.2-41.1-107.4-91.7-107.4 -50.7 0-91.7 25.1-91.7 107.4C112.8 168.6 153.9 216.7 204.6 216.7zM407.2 374.7L360.9 270.5c-2.1-4.8-5.8-8.7-10.5-11.1l-71.8-37.4c-1.6-0.8-3.5-0.7-4.9 0.4 -20.3 15.4-44.2 23.5-69.1 23.5 -24.9 0-48.8-8.1-69.1-23.5 -1.4-1.1-3.3-1.2-4.9-0.4L58.8 259.3c-4.6 2.4-8.3 6.4-10.5 11.1L2 374.7c-3.2 7.2-2.5 15.4 1.8 22 4.3 6.6 11.5 10.5 19.4 10.5h362.9c7.9 0 15.1-3.9 19.4-10.5C409.7 390.1 410.4 381.9 407.2 374.7z"/></svg>&nbsp;&bull;&nbsp;'.$rs['RealName'].'</a></li> ';
		$file = '<hr><li>'.ucfirst(gTxt('tab_content')).'</li> <li><a href="'.$interface.'/index.php?event=file"><svg version="1.1" xmlns="http://www.w3.org/2000/svg" x="0" y="0" width="22" height="22" viewBox="0 0 32 32" xml:space="preserve"><path d="M22.4 0H4v32h24V5.6L22.4 0zM22 2.4L25.6 6h-3.6C22 6 22 2.4 22 2.4zM26 30H6V2h14v6h6V30z"/><rect x="8" y="20" width="12" height="2"/><rect x="8" y="12" width="16" height="2"/><rect x="8" y="16" width="16" height="2"/></svg>&nbsp;&bull;&nbsp;'.ucfirst(gTxt('tab_file')).'&nbsp;:&nbsp;'.ucfirst(gTxt('add')).'</a></li>';
		$link = '<li><a href="'.$interface.'/index.php?event=link"><svg version="1.1" xmlns="http://www.w3.org/2000/svg" x="0" y="0" width="22" height="22" viewBox="0 0 612 612" xml:space="preserve"><path d="M499 350.1c-27.3-11.4-63.1-20.1-103.7-25.1 2.3-22.6 3.5-46.2 3.5-69.6 0-23.4-1.1-47-3.5-69.7 40.6-5.1 76.3-13.8 103.6-25.2 14.1 28.6 22.1 60.8 22.1 94.9C521 289.3 513.1 321.5 499 350.1zM298.3 40.4c2.6-0.1 5.2-0.2 7.7-0.2 2.6 0 5.2 0.1 7.7 0.2 15.3 10.4 35.2 49.8 45.7 116.4 -35.9 2.7-72.8 2.4-106.8-0.5C263.2 90 283 50.8 298.3 40.4zM245.5 255.3c0-24.3 1.1-46.7 3.1-67 19.5 1.8 39.8 2.6 60.6 2.6 18.1 0 36.3-0.7 54.2-2.1 2 20.2 3.1 42.4 3.1 66.5s-1.1 46.2-3.1 66.4c-17.4-1.5-35.6-2.1-54.2-2.1 -20.8 0-41.2 0.8-60.6 2.6C246.6 301.9 245.5 279.6 245.5 255.3zM482.3 132.5c-24 9.4-55.3 16.8-91 21.2 -6.4-42.2-17.1-79.8-31.7-106.6C410.1 60 453.3 90.9 482.3 132.5zM252.4 47.1c-14.6 26.6-25.1 63.9-31.7 105.8 -35.2-4.8-66.4-12.6-89.6-22.4C160.1 89.9 202.7 59.9 252.4 47.1zM468.4 450.5c57-46.9 92.9-115.8 92.9-195.2C561.3 114.5 446.8 0 306 0 180.6 0 76.1 90.8 54.7 210.2c-2.7 14.7-4 29.7-4 45.1 0 140.8 74.9 251.3 219.3 292.9V612l212.9-122.9L270 366.1v60.4c-116.5 0-178.6-94.5-178.6-184.1 0.6-11 2.1-21.7 4.3-32.2 3.9-18.3 10.2-35.7 18.5-51.9 26.6 11.8 62 21 102.5 26.5 -2.4 23-3.5 46.7-3.5 70.5 0 23.7 1.1 47.5 3.5 70.4 -24.7 3.3-47.4 8.1-67.4 13.9 1.2 1.5 2.3 2.9 3.4 4.4 3.5 4.5 6.8 8.9 10.3 12.2l0.8 0.7 0.7 0.9c2.6 2.9 5.7 5.7 9.2 8.6 14.4-3.5 30.2-6.4 47-8.7 2.3 14.3 4.9 28.1 8.1 41.1h0.1c5.2 1.6 9.8 3 13 3.1h1c0 0 2.3 0.2 3.1 0.3v-47.5 -30.5l48.1 27.7c5.1-0.1 10.2-0.2 15.2-0.2 17.2 0 34.2 0.6 50.3 1.9 -1.9 11.7-3.9 22.6-6.3 32.6l29 16.8c3.6-14.4 6.8-30 9.2-46.1 35.8 4.4 67 11.8 91 21.2 -14.3 20.4-31.9 38.2-52.2 52.6L468.4 450.5z"/></svg>&nbsp;&bull;&nbsp;'.ucfirst(gTxt('links')).'&nbsp;:&nbsp;'.ucfirst(gtxt('add')).'</a></li>';
		$category = ( ($thisarticle['category1'] || $thisarticle['category2']) ? '<li><a href="'.$interface.'/index.php?event=category"><svg version="1.1" xmlns="http://www.w3.org/2000/svg" width="22" height="22" x="0" y="0" viewBox="0 0 28 28" xml:space="preserve"><path d="M15 18v-4h-4v8l4 0V20h8v8h-8v-4H9V8H5V0h8v8h-2v4h4v-2h8v8H15z"/></svg>&nbsp;&bull;&nbsp;'.ucfirst(gTxt('category')).'&nbsp;:&nbsp;'.gtxt('edit').'</a></li>' : '');
		$add = '<a href="'.$interface.'/index.php?event=article&amp;Section='.$pretext['s'].'"><svg version="1.1" xmlns="http://www.w3.org/2000/svg" x="0" y="0" width="22" height="22" viewBox="0 0 386.1 386.1" xml:space="preserve" style="margin:0 5px 0 -5px"><path d="M382.7 14l-1.8-1.6 -2.4 0.4c-1.2 0.2-29.6 5.3-70 22.6 -37.3 15.9-92.1 45.6-141.6 96.7 -37.5 38.7-64 72.5-78.6 100.3 -2.5 4.7-4.6 9.3-6.4 13.6L3.1 349.7c-5 6.6-3.7 16 2.9 21 2.7 2.1 5.9 3.1 9.1 3.1 4.5 0 9-2 12-5.9l53.4-70.3c3.4 5 7.3 7.5 9.1 8.4 13.3 11.8 30.9 18 51 18 0 0 0 0 0 0 44 0 97-29.5 141.6-78.9 0.8-0.9 1.6-1.8 2.4-2.8l4.9-5.6 -61.5-20.7 86.3-14.9 1.2-2c11.7-19.6 22.3-41.5 31.5-65l1.9-4.9 -44.9-15.1 52.9-9.1 1.1-2.2c11.3-22.2 19.7-42.2 24.3-57.8C388.7 23 385.9 16.8 382.7 14z"/></svg>&bull;&nbsp;'.ucfirst(gTxt('article')).'&nbsp;:&nbsp;'.gTxt('add').' </a>';
		$article = '<li>'.(
		$thisarticle['thisid'] ? 
			'<strong>'.$add.'</strong> / <strong><a href="'.$interface.'/index.php?event=article&amp;step=edit&amp;ID='.$thisarticle['thisid'].'&amp;_txp_token='.form_token().'">'.gTxt('edit').'  ('.$pretext['s'].')</a></strong>' : 
			$add 
		).'</li>';
		$image = '<li><a href="'.$interface.'/index.php?event=image'._pat_public_bar_one_pic().'"><svg version="1.1" xmlns="http://www.w3.org/2000/svg" x="0" y="0" width="22" height="22" viewBox="0 0 315.6 315.6" xml:space="preserve"><path d="M310.6 33.3H5c-2.8 0-5 2.2-5 5v238.9c0 2.8 2.2 5 5 5h305.6c2.8 0 5-2.2 5-5V38.3C315.6 35.6 313.3 33.3 310.6 33.3zM285.6 242.4l-68.8-71.2c-0.8-0.8-2-0.8-2.8-0.1l-47.7 42 -61-75.1c-0.4-0.5-1-0.8-1.6-0.8 -0.6 0-1.2 0.3-1.6 0.8L30 234.8V63.3h255.6V242.4zM210.1 135.6c13.5 0 24.5-11 24.5-24.5 0-13.5-11-24.5-24.5-24.5 -13.5 0-24.5 11-24.5 24.5C185.5 124.6 196.5 135.6 210.1 135.6z"/></svg>&nbsp;&bull;&nbsp;'.ucfirst(gTxt('image')).'&nbsp;:&nbsp;'.( $thisarticle['article_image'] ? gTxt('edit') : gTxt('add') ).'</a></li>';
		$comment = ($thisarticle['comments_count'] > 0 ? '<li><a href="'.$interface.'/index.php?search_method=parent&crit='.$thisarticle['thisid'].'&event=discuss&step=list"><svg version="1.1" xmlns="http://www.w3.org/2000/svg" x="0" y="0" width="22" height="22" viewBox="0 0 29.3 29.3" xml:space="preserve"><path d="M27.2 1.6H2.2C1 1.6 0 2.6 0 3.8v17.6c0 1.2 1 2.2 2.2 2.2h13.5l5.1 3.8c0.4 0.3 0.8 0.5 1.3 0.5 0.8 0 1.6-0.6 1.6-1.9v-2.3h3.5c1.2 0 2.2-1 2.2-2.2V3.8C29.3 2.6 28.4 1.6 27.2 1.6zM27.3 21.3c0 0.1-0.1 0.2-0.2 0.2h-5.5v4l-5.3-4H2.2c-0.1 0-0.2-0.1-0.2-0.2V3.8c0-0.1 0.1-0.2 0.2-0.2v0h25c0.1 0 0.2 0.1 0.2 0.2L27.3 21.3 27.3 21.3zM5.5 10.8h4.3v4.3H5.5C5.5 15.1 5.5 10.8 5.5 10.8zM12.5 10.8h4.3v4.3h-4.3V10.8zM19.5 10.8h4.3v4.3h-4.3V10.8z"/></svg>&nbsp;&bull;&nbsp;'.ucfirst(gTxt('comment')).'&nbsp;:&nbsp;'.gTxt('edit').'</a></li>' : '');
		$disconnect = '<li><hr><a href="'.$interface.'/index.php?logout=1"><svg version="1.1" xmlns="http://www.w3.org/2000/svg" x="0" y="0" width="22" height="22" viewBox="0 0 32 32" xml:space="preserve"><polygon points="8 0 8 8 12 8 12 4 28 4 28 28 12 28 12 24 8 24 8 32 32 32 32 0 " fill="'.$icon.'"/><polygon points="14 22 20 16 14 10 14 14 0 14 0 18 14 18"/></svg>&nbsp;&bull;&nbsp;'.gTxt('logout').'&nbsp;&bull;</a></li>';
	
		return <<<EOT
	
<div id="pat-previewer-plugin"><span><img width="22" height="22" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAQAAADZc7J/AAABv0lEQVR4AZWUr66zMBiHmyw5CReAw2HxqJkKZC22vrYed+SS+VrU9LmCXcHuYXYGh3o+0lDC3tNvH19/ipHn2fsHUPJQcyNQo45FXEb8yYOR5iPYUOYENT+8AHhy+1CFwXMlKrL4qgiUeRyLoiAq9vidif254/6Kq6QQuBAUmd797qogbMUfwhUXvnZXGid7T+eRxQ3dO866d4nDRI/EKwaJo1TcuzwvegxO4Kn8L3pCuqsIGcFIT4fGZ3DPi5Eq3ZEtPJijYMCgaRne8BMjE0Y+yk0cYpq8BSYClnZJw7fA29y7UBO4b4vzm+JMheYmcSGIKXG7xX1D7NRhCBLPC2Suq2KU+FGBIgA/Gw4X1P8Jql3v8GTAHhXIxcFMh8JijgkkDi98nIBDHxCIvV8BeODRlL8VWVxM3q4Kh6FZa9nyTzzGMEdFj6HAo4VA4KfM3rtNod9n8Rs/YyQeo5lWRUeJSxuReMt5w2XatQq7RKelyn/XEhdVpEYcDT02CTw+jq7J43IW3DFYWkz8qHJOn6csLmM2Rb9kSK9vjWLI4zL0EBXdkmv8iRMXHDMOdSjp0QoUiqQY0hfpUDwzC476A2M87nWHub3fAAAAAElFTkSuQmCC" aria-hidden="true" alt="pat-public-admin-bar for TXP CMS" />&nbsp;&nbsp; {$prefs['sitename']}</span><ul>{$logged}<li><a href="{$interface}" style="color:#ffda44">Textpattern CMS {$prefs['version']}</a></li> {$section} {$page} {$form} {$style} {$file} {$link} {$category} {$article} {$image} {$comment} {$pref} {$log} {$disconnect} </ul></div><style scoped>body{margin-top:35px!important}#pat-previewer-plugin{position:absolute;position:{$position};z-index:9999;top:-10px;left:0;width:100%;height:35px;background:{$bgcolor};box-sizing:content-box;border-top:10px solid rgba(0,0,0,.8);color:{$color};font-size:14px;line-height:25px;font-family:"HelveticaNeue-Light","Helvetica Neue Light","Helvetica Neue",Helvetica,Arial,"Lucida Grande",sans-serif;font-weight:300;cursor:default;box-shadow:0 3px 5px rgba(0,0,0,0.3);backface-visibility:hidden}#pat-previewer-plugin:before{content:" ";display:block;position:absolute;z-index:9999;top:0;left:50%;width:0;height:0;border-left:10px solid transparent;border-right:10px solid transparent;border-top:10px solid {$color}}#pat-previewer-plugin:hover:before{z-index:0}#pat-previewer-plugin span{position:absolute;z-index:9998;top:-5px;left:0;float:left;width:100%;min-width:420px;margin:0 auto 0;padding:9px 2.5% 0px 2%;background:{$bgcolor}}#pat-previewer-plugin:hover span{display:block;color:#2ea2cc;background:{$bgcolor}}#pat-previewer-plugin span b{float:right;font-weight:normal}#pat-previewer-plugin img{display:inline;width:22px;margin:0;vertical-align:middle}#pat-previewer-plugin ul{position:absolute;top:-800px;width:320px;margin:34px 0 0 0;padding:0 0 30px 30px;background:{$bgcolor};box-shadow:0 3px 5px rgba(0,0,0,.3);transition:top 800ms ease 0s}#pat-previewer-plugin:hover ul{display:block;top:0}#pat-previewer-plugin ul li{max-width:90%;margin-left:0;list-style:none;color:#84878b;line-height:35px}#pat-previewer-plugin a{overflow:hidden;display:block;border:none;outline:none;white-space:nowrap;text-overflow:ellipsis;text-decoration:none;color:{$color}}#pat-previewer-plugin a:hover{color:{$hover}}#pat-previewer-plugin strong{font-weight:normal}#pat-previewer-plugin strong a{display:inline}#pat-previewer-plugin svg{vertical-align:middle}#pat-previewer-plugin svg *{fill:{$icon}}#pat-previewer-plugin a:hover svg path,#pat-previewer-plugin a:hover svg *{fill:{$hover}!important}#pat-previewer-plugin hr{position:relative;z-index:0;top:25px;left:185px;width:100px;height:1px;margin:5px 0;background:{$title};border:medium none}</style><script>var pat_public=function(){var _el=document.getElementById('pat-previewer-plugin');if(window.innerHeight<634)_el.style.position='absolute';};pat_public();</script>

EOT;

	}


}


/*
 * Check protocol
 *
 * @param  string  $type
 * @return string  URI or protocol security value for cookie
 */
function _pat_public_bar_protocol($type = NULL)
{
	$uri = strtolower( substr($_SERVER["SERVER_PROTOCOL"], 0, strpos($_SERVER["SERVER_PROTOCOL"], '/')) );

	if($type === 'cookie')
		$out = ($uri == 'http' ? 'false' : 'true');
	else
		$out = $uri.'://';

	return $out;

}

/**
 * Keep only domain name and extension from URI
 * 
 * @param  $str   String
 * @return string URI without protocol
 */
function _pat_public_bar_sanitize_uri($str)
{

	return preg_replace('#^https?://(w{3}\.)?#', '', $str);

}

/**
 * Retrieve only the first article image from a list
 *
 * @param 
 * @return integer  article image ID
 */
function _pat_public_bar_one_pic()
{

	global $thisarticle;

	$pics = $thisarticle['article_image'];
	$pos = strpos($pics, ',');
	if ($pos)
		$pic = substr( $pics, 0, $pos );
	else
		$pic = $pics;

	return $pic ? '&amp;step=image_edit&amp;id='.$pic.'&amp;_txp_token='.form_token() : '';
}


/**
 * Plugin prefs: TXP admin URL.
 *
 */
function _pat_public_bar_prefs()
{
	global $prefs, $textarray;

	$textarray['pat_admin_url'] = 'This interface URL';
	$_pat_interface = hu.'textpattern';

	if ( $prefs['siteurl'] != $_SERVER['HTTP_HOST'].preg_replace('#[/\\\\]$#', '', dirname(dirname($_SERVER['SCRIPT_NAME']))) )
		$pat_interface = 'http'.( isset($_SERVER['HTTPS']) ? 's' : '' ).'://'."{$_SERVER['HTTP_HOST']}";

	if (!safe_field ('name', 'txp_prefs', "name='pat_admin_url'")) 
		safe_insert('txp_prefs', "name='pat_admin_url', val='".doSlash($_pat_interface)."', type=1, event='admin', html='text_input', position=23");

	safe_repair('txp_plugin');
}


/**
 * Delete this plugin prefs.
 *
 */
function _pat_public_bar_cleanup()
{
	
	safe_delete('txp_prefs', "name='pat_admin_url'");
	safe_repair('txp_plugin');
}
