<?php
/*

Version: 0.2.0

Copy the file to: wp-content/mu-plugins
Add to .htaccess:

RewriteRule ^admin/(.*)$ wp-admin/$1 [QSA,L]

http://wordpress.stackexchange.com/questions/4037/how-to-redirect-rewrite-all-wp-login-requests/4063

*/

new RenameWPAdmin;

class RenameWPAdmin {

	private $renameFrom = 'wp-admin';
	private $renameTo = 'admin';

	function __construct() {
    add_filter('admin_url', array($this, 'admin_url'), 99, 3);
    // TODO: Is this necessary?
		$this->setCookiePath();
  }

	function admin_url( $url, $path, $blog_id ) {
		$renameFrom = $this->renameFrom;
		$renameTo = $this->renameTo;
		$scheme = 'admin';
		$find = get_site_url($blog_id, $renameFrom.'/', $scheme);
		$replace = get_site_url($blog_id, $renameTo.'/', $scheme);

		if ( 0 === strpos($url, $find) ) {
			$url = $replace.substr($url, strlen($find));
		} else if (strpos($url, '/'.$renameFrom.'/') >= 0) {
      $url = str_replace('/'.$renameFrom.'/', '/'.$renameTo.'/', $url);
    } else {
			$find = '/'.$renameFrom;
			$replace = '/'.$renameTo;
			if ( 0 === strpos($url, $find) )
				$url = $replace.substr($url, strlen($find));
		}
		return $url;
	}

	private function setCookiePath() {
		defined('SITECOOKIEPATH') || define('SITECOOKIEPATH', preg_replace('|https?://[^/]+|i', '', get_option('siteurl') . '/' ) );
		defined('ADMIN_COOKIE_PATH') || define('ADMIN_COOKIE_PATH', SITECOOKIEPATH . $this->renameTo);
	}
}
