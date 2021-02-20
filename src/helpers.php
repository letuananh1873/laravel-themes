<?php

if (!function_exists('theme')){
	/**
	 * Get the theme instance.
	 *
	 * @param  string  $themeName
	 * @param  string  $layoutName
	 * @return \Facuz\Theme\Theme
	 */
	function theme($themeName = null, $layoutName = null){
		$theme = app('theme');

		if ($themeName){
			$theme->theme($themeName);
		}

		if ($layoutName){
			$theme->layout($layoutName);
		}

		return $theme;
	}
}

if (!function_exists('protectEmail')){
	/**
	 * Protect the Email address against bots or spiders that 
	 * index or harvest addresses for sending you spam.
	 *
	 * @param  string  $email
	 * @return string
	 */
	function protectEmail($email) {
		$p = str_split(trim($email));
		$new_mail = '';

		foreach ($p as $val) {
			$new_mail .= '&#'.ord($val).';';
		}

		return $new_mail;
	}
}


if ( ! function_exists('meta_init') ) {
	/**
	 * Returns common metadata
	 *
	 * @return string
	 */
	function meta_init() {
		$meta = [
			'<meta charset="utf-8">',
			'<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">',
			'<meta name="viewport" content="width=device-width, initial-scale=1">'
		];
		
	
		$meta[] = '<meta name="g-recaptcha-response" content="">';
	

		$metaNoIndex = Theme::getNoindex();
		if( ! empty($metaNoIndex) ) {
			$meta['robots'] = '<meta name="robots" content="noindex" />';
		}

		$meta[] = sprintf('<meta name="csrf-token" content="%s" />', csrf_token() );




		if( preg_match('/user\/(.*)/', request()->path(), $matches ) || preg_match('/admin\/(.*)/', request()->path(), $matches ) ) {
			$meta['robots'] = '<meta name="robots" content="noindex" />';
			return implode("\n\t\t", $meta);
		}

		$title = Theme::getTitle();
		$description = Theme::getDescription();
		$image = Theme::getImage();
		$url = Theme::getURL();



		$meta[] = sprintf('<meta name="title" content="%s">', $title);
		$meta[] = sprintf('<meta name="description" content="%s">', $description );

		$meta[] = '<meta property="og:type" content="article">';
		$meta[] = sprintf('<meta property="og:url" content="%s">', $url );
		$meta[] = sprintf('<meta property="og:title" content="%s">', $title );
		$meta[] = sprintf('<meta property="og:description" content="%s">', $description );
		$meta[] = sprintf('<meta property="og:image" content="%s">', $image );

		$meta[] = '<meta property="twitter:card" content="summary_large_image">';
		$meta[] = sprintf('<meta property="twitter:url" content="%s">', $url );
		$meta[] = sprintf('<meta property="twitter:title" content="%s">', $title );
		$meta[] = sprintf('<meta property="twitter:description" content="%s">', $description );
		$meta[] = sprintf('<meta property="twitter:image" content="%s">', $image );

		return implode("\n\t\t", $meta);
	}
}