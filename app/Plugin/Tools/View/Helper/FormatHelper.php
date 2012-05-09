<?php

App::import('Helper', 'Text');

/**
 * Format helper
 * 2009-12-31 ms
 */
class FormatHelper extends TextHelper {
	/**
	 * Other helpers used by FormHelper
	 *
	 * @var array
	 * @access public
	 */
	public $helpers = array('Html', 'Form', 'Tools.Common', 'Tools.Gravatar', 'Tools.PhpThumb');





	/**
	 * jqueryAccess: {id}Pro, {id}Contra
	 * 2009-07-24 ms
	 */
	public function thumbs($id, $inactive = false, $inactiveTitle = null) {
		$class = 'Active';
		$upTitle = __('Zustimmen');
		$downTitle = __('Dagegen');
		if ($inactive === true) {
			$class = 'Inactive';
			$upTitle = $downTitle = !empty($inactiveTitle)?$inactiveTitle:__('alreadyVoted');
		}
		$ret = '<div class="thumbsUpDown">';
		$ret .= '<div id="'.$id.'Pro'.$class.'" rel="'.$id.'" class="thumbUp up'.$class.'" title="'.$upTitle.'"></div>';
		$ret .= '<div id="'.$id.'Contra'.$class.'" rel="'.$id.'" class="thumbDown down'.$class.'" title="'.$downTitle.'"></div>';
		$ret .= '<br class="clear"/>';
		$ret .=	'</div>';
		return $ret;
	}


	/**
	 * @param string $field: just field or Model.field syntax
	 * @param array $options:
	 * - name: title name: next{Record} (if none is provided, "record" is used - not translated!)
	 * - slug: true/false (defaults to false)
	 * - titleField: field or Model.field
	 */
	public function neighbors($neighbors, $field, $options = array()) {
		if (mb_strpos($field, '.') !== false) {
			$fieldArray = explode('.', $field, 2);
			$alias = $fieldArray[0];
			$field = $fieldArray[1];
		}

		if (empty($alias)) {
			if (!empty($neighbors['prev'])) {
				$modelNames = array_keys($neighbors['prev']);
				$alias = $modelNames[0];
			} elseif (!empty($neighbors['next'])) {
				$modelNames = array_keys($neighbors['next']);
				$alias = $modelNames[0];
			}
		}
		if (empty($field)) {

		}

		$name = 'Record'; # translation further down!
		if (!empty($options['name'])) {
			$name = ucfirst($options['name']);
		}

		$prevSlug = $nextSlug = null;
		if (!empty($options['slug'])) {
			$prevSlug = slug($neighbors['prev'][$alias][$field]);
			$nextSlug = slug($neighbors['next'][$alias][$field]);
		}
		$titleAlias = $alias;
		$titleField = $field;
		if (!empty($options['titleField'])) {
			if (mb_strpos($options['titleField'], '.') !== false) {
				$fieldArray = explode('.', $options['titleField'], 2);
				$titleAlias = $fieldArray[0];
				$titleField = $fieldArray[1];
			} else {
				$titleField = $options['titleField'];
			}
		}
		if (!isset($options['escape']) || $options['escape'] === false) {
			$titleField = h($titleField);
		}

		$ret = '<div class="nextPrevNavi">';
		if (!empty($neighbors['prev'])) {

			$ret.= $this->Html->link($this->cIcon(ICON_PREV, false).'&nbsp;'.__('prev'.$name), array($neighbors['prev'][$alias]['id'], $prevSlug), array('escape'=>false, 'title'=>$neighbors['prev'][$titleAlias][$titleField]));
		} else {
			$ret.= $this->cIcon(ICON_PREV_DISABLED, __('noPrev'.$name)).'&nbsp;'.__('prev'.$name);
}
		$ret.= '&nbsp;&nbsp;';
		if (!empty($neighbors['next'])) {
			$ret.= $this->Html->link($this->cIcon(ICON_NEXT, false).'&nbsp;'.__('next'.$name), array($neighbors['next'][$alias]['id'], $nextSlug), array('escape'=>false, 'title'=>$neighbors['next'][$titleAlias][$titleField]));
		} else {
			$ret.= $this->cIcon(ICON_NEXT_DISABLED, __('noNext'.$name)).'&nbsp;'.__('next'.$name);
		}
		$ret .= '</div>';
		return $ret;
	}



	/**
	 * allows icons to be added on the fly
	 * NOTE: overriding not allowed by default
	 * 2009-01-04 ms
	 */
	public function addIcon($name = null, $pic = null, $title = null, $allowOverride = false) {
		if ($allowOverride === true || ($allowOverride !==true && !array_key_exists($name,$this->icons))) {
			if (!empty($name) && !empty($pic)) {
				$this->icons[$name] = array('pic'=>strtolower($pic),'title'=>(!empty($title)?$title:''));
			}
		}
		return false;
	}

	const GENDER_FEMALE = 2;
	const GENDER_MALE = 1;

	/**
	 * //TODO: move to Format
	 * displays gender icon
	 * 2009-01-04 ms
	 */
	public function genderIcon($value = null, $type = null) {
		$value = (int)$value;
		if ($value == self::GENDER_FEMALE) {
			$icon =	$this->icon('genderFemale', null, null, null, array('class'=>'gender'));
		} elseif ($value == self::GENDER_MALE) {
			$icon =	$this->icon('genderMale', null, null, null, array('class'=>'gender'));
		} else {
			$icon =	$this->icon('genderUnknown', null, null, null, array('class'=>'gender'));
		}
		return $icon;
	}




	/**
	 * //TODO: move to Format?
	 * returns img from customImgFolder
	 * @param ARRAY options (ending [default: gif])
	 * 2009-12-31 ms
	 */
	public function customIcon($folder, $icon = null, $checkExist = false, $options = array(), $attr = array()) {
		$attachment =  'default';
		$ending = 'gif';
		$image = null;

		if (!empty($options)) {
			if (!empty($options['ending'])) {
				$ending = $options['ending'];
			}

			if (!empty($options['backend'])) {
				$attachment = 'backend';
			}
		}

		if (empty($icon)) {
		} elseif ($checkExist === true && !file_exists(PATH_CONTENT.$folder.DS.$icon.'.'.$ending)) {
		} else {
			$image = $icon;
		}
		if ($image === null) {
			return $this->Html->image(IMG_ICONS.'custom'.'/'.$folder.'_'.$attachment.'.'.$ending, $attr);
		}
		return $this->Html->image(IMG_CONTENT.$folder.'/'.$image.'.'.$ending, $attr);
	}


	/**
	 * //TODO: move to Format?
	 * @param string $icon iso2 code (e.g. 'de' or 'gb')
	 * 2009-08-28 ms
	 */
	public function countryIcon($icon = null, $returnFalseonFailure = false, $options = array(), $attr = array()) {
		$ending = 'gif';
		$image = 'unknown';

		if ($specific = Configure::read('Country.image_path')) {
			$wwwPath = $specific.'/';
			$path = PATH_IMAGES. $specific . DS; //.'country_flags'.DS
		} else {
			$wwwPath = '/tools/img/country_flags/';
			$path = App::pluginPath('Tools') . 'webroot' . DS.  'img' . DS . 'country_flags' . DS;
		}

		if (!empty($options) && is_array($options)) {
			if (!empty($options['ending'])) {
				$ending = $options['ending'];
			}
		}
		$icon = mb_strtolower($icon);

		if (empty($icon)) {
			if ($returnFalseonFailure) { return false; }
		} elseif (!file_exists($path . $icon . '.' . $ending)) {

			//die($path . $icon . '.' . $ending);
			trigger_error($path . $icon . '.' . $ending.' missing', E_USER_NOTICE);

			if ($returnFalseonFailure) { return false; }
		} else {
			$image = $icon;
		}
		return $this->Html->image($wwwPath.$image.'.'.$ending, $attr);
	}


	/**
	 * 2011-10-10 ms
	 */
	public function importantIcon($icon, $value) {
		$ending = 'gif';
		$image = 'default';
		if (!empty($value)) {
			$image = 'important';
		}
		return $this->Html->image(IMG_ICONS.$icon.'_'.$image.'.'.$ending);
	}

	/**
	 * Quick way of printing default icons (have to be 16px X 16px !!!)
	 * @param type
	 * @param title
	 * @param alt (set to FALSE if no alt is supposed to be shown)
	 * @param boolean automagic i18n translate [default true = __('xyz')]
	 * @param options array ('class'=>'','width/height'=>'','onclick=>'') etc
	 * 2008-12-28 ms
	 */
	public function icon($type, $t=null, $a=null, $translate=null, $options=array()) {
		$html='';

		# title
		if (isset($t) && $t===false) {
			$title='';
		} elseif (empty($t)) {

		} else {
			$title=$t;
			//$alt=$t;	// alt=title as default
		}

		#alt
		if (isset($a) && $a===false) {
			$alt='';
		} elseif (empty($a)) {

		} else {
			$alt=$a;
		}

		if (array_key_exists($type,$this->icons)) {
			$pic = $this->icons[$type]['pic'];
			$title = (isset($title)?$title:$this->icons[$type]['title']);
			$alt = (isset($alt)?$alt:preg_replace('/[^a-zA-Z0-9]/', '', $this->icons[$type]['title']));
			if ($translate !== false) {
				$title = __($title);
				$alt = 	__($alt);
			}
			$alt = '['.$alt.']';
		} else {
			$pic='pixelspace.gif';
			$title = '';
			$alt = '';
		}

		$default_options=array('title'=>$title,'alt'=>$alt,'class'=>'icon');
		//$new_options['onclick']=$options['onclick'];
		$new_options= array_merge($default_options,$options);



		$html.=$this->Html->image('icons/'.$pic,$new_options);

		return $html;
	}

	/**
	 * custom Icons
	 * @param string $icon (constant or filename)
	 * @param array $options:
	 * - translate, ...
	 * @param array $attributes:
	 * - title, alt, ...
	 * THE REST IS DEPRECATED
	 * 2010-03-04 ms
	 */
	public function cIcon($icon, $t=null, $a=null, $translate=true, $options=array()) {
		if (is_array($t)) {
			$translate = isset($t['translate']) ? $t['translate'] : true;
			$options = (array)$a;
			$a = isset($t['alt']) ? $t['alt'] : null; # deprecated
			$t = isset($t['title']) ? $t['title'] : null; # deprecated
		}

		$title = (isset($t) ? $t : ucfirst(extractPathInfo('filename', $icon)));
		//$alt = (isset($a)?$a:preg_replace('/[^a-zA-Z0-9]/', '', $title));
		$alt = (isset($a) ? $a : Inflector::slug($title, '-'));
		if ($translate !== false) {
			$title = __($title);
			$alt = 	__($alt);
		}
		$alt = '['.$alt.']';

		$defaultOptions = array('title'=>$title, 'alt'=>$alt, 'class'=>'icon');
		$options = array_merge($defaultOptions, $options);
		if (substr($icon, 0, 1) !== '/') {
			$icon = 'icons/'.$icon;
		}
		return $this->Html->image($icon, $options);
	}


	/**
	 * @deprecated use RatingHelper::stars() instead
	 * //TODO: move to Format?
	 * print Star Bar
	 * array $options: steps=1/0.5 [default:1]), show_zero=true/false [default:false], title=false/true [default:false]
	 * array $attr: string 'title' (both single and span title empty => 'x of x' for span)
	 * TODO: 0.5 steps!
	 * 2009-04-04 ms
	 */
	public function showStars($current = null, $max = null, $options = array(), $attr = array()) {
		$res = '---';

		if (!empty($options['steps']) && $options['steps'] == 0.5) {
			$steps = 0.5;
			$current = ((int)(2*$current)/2);
		} else {
			$steps = 1;
			$current = (int)$current;
		}
		$min = (int)$current;
		$max = (int)$max;

		if ((!empty($current) || (!empty($options['show_zero']) && $current == 0)) && (!empty($max)) && $current <= $max) {

			if (!empty($options) && is_array($options)) {

			}

			$text = '';
			for ($i=0;$i<$min;$i++) {
				$attributes = array('alt'=>'#','class'=>'full');
				if (!empty($options['title'])) { $attributes['title'] = ($i+1).'/'.$max; } # ?
				$text.= $this->Html->image('icons/star_icon2.gif',$attributes);

			}
			for ($i=$min;$i<$max;$i++) {
				$attributes = array('alt'=>'-','class'=>'empty');
				if (!empty($options['title'])) { $attributes['title'] = ($i+1).'/'.$max; } # ?
				if ($steps == 0.5 && $current == $i+0.5) {
					$text.= $this->Html->image('icons/star_icon2_half.gif',$attributes);
				} else {
					$text.= $this->Html->image('icons/star_icon2_empty.gif',$attributes);
				}
			}

			$attributes = array('class'=>'starBar');
			$attributes = array_merge($attributes, $attr);
			if (empty($attributes['title']) && empty($options['title'])) {
				$attributes['title'] = ($current).' '.__('of').' '.$max;
			}

			$res = $this->Html->tag('span', $text, $attributes);
			//$res='<span title="ss" class="starBar">'.$text.'</span>';
		} else {
			if ($max > 3) {
				for ($i=0;$i<$max-3;$i++) {
					$res .='-';
				}
			}
		}
		return $res;
	}

	/**
	 * //TODO: move to Format?
	 * addItemAttribute function
	 *
	 * Called to modify the attributes of the next <item> to be processed
	 * Note that the content of a 'node' is processed before generating its wrapping <item> tag
	 *
	 * @param string $id
	 * @param string $key
	 * @param mixed $value
	 * @access public
	 * @return void
	 * TODO: refactor!!
	 */
	public function languageFlags() {
		$langs = Configure::read('LanguagesAvailable');
		$supportedLangs = array(
			'de' => array('title'=>'Deutsch'),
			'en' => array('title'=>'English'),
			'it' => array('title'=>'Italiano'),
		);

		$language_change = __('Language').': ';

		$languages=array();
		foreach ($langs as $lang) {
			$languages[$lang] = $supportedLangs[$lang];
		}

		if ($sLang = (string)CakeSession::read('Config.language')) {
			$lang = $sLang;
		} else {
			$lang = '';
		}
		echo '<span class="country">';
		foreach ($languages as $code => $la) {
			if ($lang == $code) {
				$language_change .= $this->Html->image('language_flags/'.$code.'.gif',array('alt'=>$code,'title'=>$la['title'].' ('.__('active').')','class'=>'country_flag active')).'';
			} else {
				$language_change .= $this->Html->link($this->Html->image('language_flags/'.$code.'.gif',array('alt'=>$code,'title'=>$la['title'],'class'=>'country_flag')), '/lang/'.$code, array('escape'=>false)).'';
			}
		}

		$language_change .= '</span>'; //.__('(Translation not complete yet)');
				//<a href="http://localhost/c/telapp/img/1.php" onclick="return hs.htmlExpand(this, { contentId: 'highslide-help', objectType: 'ajax', preserveContent: true } ); return false;">s</a>

	return $language_change;
	}



	/**
	 * It is still believed that encoding will stop spam-bots being able to find your email address. Nevertheless, encoded email address harvester are on the way
 (http://www.dreamweaverfever.com/experiments/spam/).
 	 */

	/**
	 * //TODO: move to TextExt?
	 * Helper Function to Obfuscate Email by inserting a span tag (not more! not very secure on its own...)
	 * each part of this mail now does not make sense anymore on its own
	 * (striptags will not work either)
	 * @param string email: neccessary (and valid - containing one @)
	 * 2009-03-11 ms
	 */
	public function encodeEmail($mail) {
		list($mail1,$mail2) = explode('@', $mail);
		$encMail = $this->encodeText($mail1).'<span>@</span>'.$this->encodeText($mail2);
		return $encMail;
	}

	/**
	 * //TODO: move to TextExt?
	 * Obfuscates Email (works without JS!) to avoid spam bots to get it
	 * @param string mail: email to encode
	 * @param string text: optional (if none is given, email will be text as well)
	 * @param array attributes: html tag attributes
	 * @param array params: ?subject=y&body=y to be attached to "mailto:xyz"
	 * @return save string with js generated link around email (and non js fallback)
	 * 2009-04-20 ms
	 */
	public function encodeEmailUrl($mail, $text=null, $params=array(), $attr = array()) {
		if (empty($class)) { $class='email';}

		$defaults = array(
			'title'=>__('for use in an external mail client'),
			'class' => 'email',
			'escape' => false
		);

		if (empty($text)) {
			$text = $this->encodeEmail($mail);
		}

		$encMail = 'mailto:'.$mail;


		// additionally there could be a span tag in between: email<span syle="display:none"></span>@web.de


		//$encmail = '&#109;a&#105;&#108;t&#111;&#58;'.$encMail;
		$querystring = '';
		foreach ($params as $key=>$val) {
			if ($querystring) {
				$querystring .= "&$key=".rawurlencode($val);
			} else {
				$querystring = "?$key=".rawurlencode($val);
			}
		}

		$attr = array_merge($defaults,$attr);


	$xmail = $this->Html->link('',$encMail.$querystring,$attr);
	$xmail1 = mb_substr($xmail, 0, count($xmail)-5);
	$xmail2 = mb_substr($xmail, -4, 4);
	//pr (h($xmail1));
	//pr (h($xmail2));

	$len = mb_strlen($xmail1);
	$i=0;
	while ($i<$len) {
		$c = mt_rand(2,6);
		$par[] = (mb_substr($xmail1, $i, $c));
		$i += $c;
	}
	$join = implode('\'+ \'', $par);

		return '<script language=javascript><!--
	document.write(\''.$join.'\');
	//--></script>
		'.$text.'
	<script language=javascript><!--
	document.write(\''.$xmail2.'\');
	//--></script>';


		//return '<a class="'.$class.'" title="'.$title.'" href="'.$encmail.$querystring.'">'.$encText.'</a>';
	}


	/**
	 * //TODO: move to TextExt?
	 * Encodes Piece of Text (without usage of JS!) to avoid spam bots to get it
	 * @param STRING text to encode
	 * @return string (randomly encoded)
	 * 2009-03-11 ms
	 */
	public function encodeText($text) {
		$encmail = '';
		for ($i=0; $i<mb_strlen($text); $i++) {
			$encMod = mt_rand(0,2);
			switch ($encMod) {
			case 0: // None
				$encmail .= mb_substr($text,$i,1);
				break;
			case 1: // Decimal
				$encmail .= "&#".ord(mb_substr($text,$i,1)).';';
				break;
			case 2: // Hexadecimal
				$encmail .= "&#x".dechex(ord(mb_substr($text,$i,1))).';';
				break;
			}
		}
		return $encmail;
	}


	/**
	 * //TODO: move to Format?
	 * @param text: default FALSE; if TRUE, text instead of the image
	 * @param ontitle: default FALSE; if it is embadded in a link, set to TRUE
	 * @return image:Yes/No or text:Yes/No
	 *
	 * @todo $on=1,$text=false,$ontitle=false,... => in array(OPTIONS) packen
	 */
	public function yesNo($v,$ontitle=null, $offtitle=null, $on=1, $text=false, $notitle=false) {
		$ontitle = (!empty($ontitle)?$ontitle:__('Ja'));
		$offtitle = (!empty($offtitle)?$offtitle:__('Nein'));
		$sbez = array('0'=>@substr($offtitle, 0, 1), '1'=>@substr($ontitle, 0, 1));
		$bez = array('0'=>$offtitle, '1'=>$ontitle);

		if ($v==$on) {$icon=ICON_YES; $value=1;} else {$icon=ICON_NO; $value=0;}

		if ($text!==false) {
			$light=$bez[$value];
		} else {

			//$light='<img src="images/icons/'.$icon.'" alt="'.$sbez[$value].'" '.($notitle!==false?'title="'.$bez[$value].'"':'').'/>';
			//$light=$this->Html->image('',)<img src="images/icons/'.$icon.'" alt="'.$sbez[$value].'" '.($notitle!==false?'title="'.$bez[$value].'"':'').'/>';
			$light=$this->Html->image('icons/'.$icon, array('title'=>($ontitle===false?'':$bez[$value]), 'alt'=>$sbez[$value], 'class'=>'icon'));
		}
		return $light;
	}




	/**
	 * get url of a png img of a website (16x16 pixel)
	 * 2011-02-15 ms
	 */
	public function siteIconUrl($domain) {
		if (strpos($domain, 'http') === 0) {
			# strip protocol
			$pieces = parse_url($domain);
			$domain = $pieces['host'];
		}
		return 'http://www.google.com/s2/favicons?domain='.$domain;
	}

	/**
	 * display a png img of a website (16x16 pixel)
	 * if not available, will return a fallback image (a globe)
	 * @param domain (preferably without protocol, e.g. "www.site.com")
	 * 2011-02-15 ms
	 */
	public function siteIcon($domain, $options = array()) {
		$url = $this->siteIconUrl($domain);
		$options['width'] = 16;
		$options['height'] = 16;
		if (!isset($options['alt'])) {
			$options['alt'] = $domain;
		}
		if (!isset($options['title'])) {
			$options['title'] = $domain;
		}
		return $this->Html->image($url, $options);
	}

	/**
	 * @param string $text
	 * @param array $options (for generation):
	 * - inline, font, size, background (optional)
	 * @param array $tagAttributes (for image)
	 * @return string $result - as image
	 * 2010-12-13 ms
	 */
	public function textAsImage($text, $options = array(), $attr = array()) {
		/*
		$image = new Imagick();
		//$image->newImage(218, 46, new ImagickPixel('white'));
		$image->setImageCompression(10); // Keine Auswirkung auf Dicke
		$draw = new ImagickDraw();
		$draw->setFont($font);
		$draw->setFontSize(22.0); // Keine Auswirkung auf Dicke
		$draw->setFontWeight(100); // 0-999 Keine Auswirkung auf Dicke
		$draw->annotation(5, 20, $text);
		$image->drawImage($draw);
		$image->setImageResolution(1200, 1200); // Keine Auswirkung auf Dicke
		$image->setImageFormat('gif');
		$image->writeImage(TMP.'x.gif');
		$image->trim($mw,0);
		*/
		$defaults = array('alt'=>$text);
		$attr = array_merge($defaults, $attr);
		return $this->_textAsImage($text, $options, $attr);
	}

	/**
	 * @return string $htmlImage tag (or empty string on failure)
	 * 2010-12-13 ms
	 */
	public function _textAsImage($text, $options = array(), $attr = array()) {
		$defaults = array('inline'=>true, 'font' => FILES.'linotype.ttf', 'size'=>18, 'color'=>'#7A7166');
		$options = array_merge($defaults, $options);

		if ($options['inline']) { # inline base 64 encoded
			$folder = CACHE.'imagick';
		} else {
			$folder = WWW_ROOT.'img'.DS.'content'.DS.'imagick';
		}


		$file = sha1($text.serialize($options)).'.'.($options['inline'] || !empty($options['background']) ? 'png' : 'gif');
		if (!file_exists($folder)) {
			mkdir($folder, 0755);
		}
		if (!file_exists($folder.DS.$file)) {
			$command = 'convert -background '.(!empty($options['background'])?'"'.$options['background'].'"':'transparent').' -font '.$options['font'].' -fill '.(!empty($options['color'])?'"'.$options['color'].'"':'transparent').' -pointsize '.$options['size'].' label:"'.$text.'" '.$folder.DS.$file;
		 	exec($command, $a, $r);
			if ($r !== 0) {
				return '';
			}
		}

		if ($options['inline']) {
			$res = file_get_contents($folder.DS.$file);
			$out = $this->Html->imageFromBlob($res, $attr);
		} else {
			$out = $this->Html->image($this->Html->url('/img/content/imagick/', true).$file, $attr);
		}
		return $out;
	}




	/**
	 * 2009-12-24 ms
	 */
	public function disabledLink($text, $options = array()) {
		$defaults = array('class' => 'disabledLink', 'title' => __('notAvailable'));
		$options = array_merge($defaults, $options);

		return $this->Html->tag('span', $text, $options);
	}


	/**
	 * display communication action depending on the current rule/right
	 * 2009-10-14 ms
	 */
	public function action($s) {

	}

	/**
	 * generate a pagination count: #1 etc for each pagiation record
	 * respects order (ASC/DESC)
	 * @param paginator array
	 * @param count (current post count on this page)
	 * @param dir (ASC/DESC)
	 * 2010-12-01 ms
	 */
	public function absolutePaginateCount($paginator, $count, $dir = null) {
		if ($dir === null) {
			$dir  = 'ASC';
		}

		$currentPage = $paginator['page'];
		$pageCount = $paginator['pageCount'];
		$totalCount = $paginator['count'];

		$limit = $paginator['limit'];
		$step = 1; //$paginator['step'];
		//pr($paginator);

		if ($dir == 'DESC') {
			$currentCount = $count + ($pageCount - $currentPage) * $limit * $step;
			if ($currentPage != $pageCount && $pageCount > 1) {
				$currentCount -= $pageCount * $limit * $step - $totalCount;
			}
		} else {
			$currentCount = $count + ($currentPage-1) * $limit * $step;
		}

		return $currentCount;
	}


	/**
	 * @param float progress
	 * @param array options:
	 * - min, max
	 * - steps
	 * - decimals (how precise should the result be displayed)
	 *
	 * 2010-01-10 ms
	 */
	public function progressBar($progress, $options = array(), $htmlOptions = array()) {
		$defaults = array(
			'min' => 0,
			'max' => 100,
			'steps' => 15,
			'decimals' => 1 # TODO: rename to places!!!
		);
		$options = array_merge($defaults, $options);

		$current = (((float)$progress / $options['max']) - $options['min']);
		$percent = $current * 100;

		$current *= $options['steps'];

		$options['progress'] = number_format($current, $options['decimals'], null, '');

		$params = Router::queryString($options, array(), true);

		App::import('Helper', 'Tools.Numeric');
		$this->Numeric = new NumericHelper(new View(null));

		$htmlDefaults = array('title' => $this->Numeric->format($percent, $options['decimals']) . ' ' . __('Percent'), 'class' => 'help');
		$htmlDefaults['alt'] = $htmlDefaults['title'];

		$htmlOptions = array_merge($htmlDefaults, $htmlOptions);
		//return $this->Html->image('/files/progress_bar/index.php'.$params, $htmlOptions);

		# bug in Html::webroot() ??? ommits ?...
		return '<img src="' . $this->Html->url('/files') . '/progress_bar/index.php' . $params . '" title="' . $htmlOptions['title'] . '" class="' .
			$htmlOptions['class'] . '" alt="' . $htmlOptions['title'] . '" />';
	}


	public function tip($type, $file, $title, $icon) {
		return $this->cIcon($icon, $title, null, null, array('class' => 'tip' . ucfirst($type) . ' hand', 'rel' => $file));
	}

	public function tipHelp($file) {
		return $this->tip('help', $file, 'Hilfe', ICON_HELP);
	}

	/**
	 * fixes utf8 problems of native php str_pad function
	 * @param string $input
	 * @param int $padLength
	 * @param string $padString
	 * @param mixed $padType
	 * @return string $input
	 * 2011-09-27 ms
	 */
	public function pad($input, $padLength, $padString, $padType = STR_PAD_RIGHT) {
		$length = mb_strlen($input);
		if ($padLength - $length > 0) {
			switch ($padType) {
				case STR_PAD_LEFT:
					$input = str_repeat($padString, $padLength-$length).$input;
					break;
				case STR_PAD_RIGHT:
					$input .= str_repeat($padString, $padLength-$length);
					break;
			}
		}
		return $input;
	}

	/**
	 * deprecated
	 * album image
	 * 2009-09-10 ms
	 */
	public function image($id, $options = array(), $attr = array()) {


		if (!empty($options['h'])) {
			$attr['height'] = $options['h'];
		}
		if (!empty($options['w'])) {
			$attr['width'] = $options['w'];
		}

		return $this->Html->image($this->imageUrl($id, $options), $attr);
	}

	public function imageUrl($id, $options = array()) {
		return $this->Html->defaultUrl($this->imageUrlArray($id, $options), true);
	}

	public function imageUrlArray($id, $options = array()) {
		$urlArray = array('controller' => 'images', 'action' => 'display', $id);
		$urlArray = array_merge($urlArray, $options);
		return $urlArray;
	}


	/**
	 * album image
	 * 2009-09-10 ms
	 */
	public function albumImage($image, $options = array(), $attr = array()) {
		$subfolder = $image['Album']['id'];
		$file = $image['Album']['id'] . DS . $image['Image']['filename'] . '.' . $image['Image']['ext'];

		$optionsArray = array(
			'save_path' => WWW_ROOT . 'img'. DS. 'albums' . DS . $subfolder,
			'display_path' => '/img/albums' . '/' . $subfolder,
			'error_image_path' => '/img/userpics/22.jpg',
			'src' => FILES . 'images' . DS . $file,
			// From here on out, you can pass any standard phpThumb parameters
			'q' => 100,
			'zc' => 1
		);

		$options = array_merge($optionsArray, $options);
		if (!file_exists($options['save_path'])) {
			App::uses('Folder', 'Utility');
			$f = new Folder($options['save_path'], true, 0777);
			//mkdir($options['save_path'], 0777);

		}
		$thumbnail = $this->PhpThumb->generate($options);
		return $this->Html->image($thumbnail['src'], $attr); //'width' => $thumbnail['w'], 'height' => $thumbnail['h']
	}

	public function albumImageLink($image, $options = array()) {
		$options = array_merge(array('escape' => false), $options);
		return $this->Html->defaultLink($this->albumImage($image, array('h' => 50)), $this->imageUrlArray($image['Image']['id'], array('h' => 50)),
			$options);
	}


	public function albumImageTexts($image, $options = array()) {
		$display = array(
			'title' => true,
			'description' => true,
			'controls' => true,
			'autoHide' => true
		);
		
		if (!isset($image['Album']) && isset($options['album'])) {
			$image['Album'] = $options['album'];
		}

		if ($image['Album']['user_id'] != UID || isset($options['controls']) && $options['controls'] === false) {
			$display['controls'] = false;
		}
		if (isset($options['autoHide']) && $options['autoHide'] === false) {
			$display['autoHide'] = false;
		}


		$res = $controls = $descr = '';
		if ($display['title']) { //  && (!$display['autoHide'] || !empty($image['Image']['title']))
			$defTitle = ($display['autoHide'] ? '' : ': <i>kein Titel</i>');

			$title = 'Album \'' . h($image['Album']['title']) . '\'' . (!empty($image['Image']['title']) ? ': ' . h($image['Image']['title']) : $defTitle);
			$res .= '<div class="highslide-heading">' . $title . '</div>';
		}

		$navigation = '<div class="floatRight">&nbsp;' . $this->Html->defaultLink($this->cIcon(ICON_ALBUM, 'Zum Album wechseln'),
			array('controller' => 'albums', 'action' => 'view', $image['Album']['id'], slug($image['Album']['title'])), array('escape' => false)) .
			'</div>';
		if (!empty($image['User']['id'])) {
			$gender = '';
			if (isset($image['UserInfo']['gender'])) {
				$gender = $this->genderIcon($image['UserInfo']['gender']).' ';
			}
			$navigation .= '<div class="floatRight" style="margin-right: 6px;">'.__('Member').': '.$gender.$this->profileLink($image['User']['id'], $image['User']['username']).'</div>';
		}

		if ($display['controls']) {
			$controls = '<div class="floatRight">&nbsp;' . $this->Html->defaultLink($this->icon('edit'), array('controller' => 'images',
				'action' => 'edit', $image['Image']['id']), array('escape' => false)) . '&nbsp;' . $this->Form->postLink($this->icon('delete'),
				array('plugin'=>false, 'admin'=>false, 'controller' => 'images', 'action' => 'delete', $image['Image']['id']), array('escape' => false), 'Sicher?') . '</div>';
		}

		if ($display['description']) {
			$defDescr = ($display['autoHide'] ? '' : '<i>keine Beschreibung</i>');

			$descr = (!empty($image['Image']['description']) ? nl2br(h($image['Image']['description'])) : $defDescr);
		}

		if ($display['controls'] || $display['description']) {
			$res .= '<div class="highslide-caption">' . $controls . $navigation . $descr . '</div>';
		}


		return $res;
	}


	/**
	 * takes username + userId and forms a profile link out of it
	 * if username is empty, return "deleted" text without link
	 * maybe move to custom/community helper etc?
	 * 2009-08-27 ms
	 */
	public function profileLink($uid, $username, $text = null, $attr = array(), $options = array()) {
		if (empty($username)) {
			return '['.__('%s deleted', __('Account')).']';
		}
		$title = isset($text) ? $text : $username;
		$username = slug($username);
		if (!empty($options['hash'])) {
			$username .= '#'.$options['hash'];
		}
		return $this->Html->link($title, array('plugin' => false, 'admin' => false, 'controller' => 'members', 'action' => 'view', $uid, $username),
			$attr);
	}

	/**
	 * @param mixed $uid
	 * @param string $username
	 * @param bool $full
	 * @param array $options
	 * - hash (string)
	 * @return string $url
	 * 2011-01-30 ms
	 */
	public function profileUrl($uid, $username, $full = false, $options = array()) {
		return $this->Html->url(array('plugin' => false, 'admin' => false, 'controller' => 'members', 'action' => 'view', $uid, slug($username)), $full);
	}

	/*
	public function profileLinkById($uid) {
	$username = null; //TODO: get from static list
	//$username = slug($username);
	return $this->Html->link($username, array('admin'=>false,'controller'=>'members', 'action'=>'view', $uid, $username));
	}
	*/

	/**
	 * better an element?
	 */
	public function profilePic($uid, $e, $rights = null) {

	}


	public function languageFlag($iso2, $options = array()) {
		$flag = '';

		$defaults = array('alt' => $iso2, 'title' => strtoupper($iso2));
		$options = array_merge($defaults, $options);

		$flag .= $this->Html->image('language_flags/' . strtolower($iso2) . '.gif', $options);
		return $flag;
	}


	public function countryAndProvince($array, $options = array()) {
		$res = '<span class="help" title="%s">%s</span>';

		$countryTitle = '';
		if (!empty($array['Country']['name'])) {
			$country = $array['Country']['iso2'];
			$countryTitle .= h($array['Country']['name']) . '';
		} else {
			$country = '';
		}

		if (!empty($array['CountryProvince']['name'])) {
			$countyProvince = h($array['CountryProvince']['abbr']);
			$countryTitle .= (!empty($countryTitle) ? ' - ' : '') . h($array['CountryProvince']['name']);
		} else {
			$countyProvince = '';
			$countryTitle .= '';
		}


		$content = $this->countryIcon($country) . '&nbsp;' . $countyProvince;
		$res = sprintf($res, $countryTitle, $content);
		return $res;
	}


	//deprecated?
	public function avatar($setting, $uid, $email = null, $options = array()) {

		$options = array_merge(array('title' => __('Avatar')), $options);

		if ($setting == USER_AVATAR_OWN) {
			if (!empty($options['size'])) {
				$options['height'] = $options['width'] = $options['size'];
				unset($options['size']);
			}
			return $this->Html->image(IMG_AVATARS . $uid . '.' . AVATAR_ENDING, $options);
		} elseif ($setting == USER_AVATAR_GRAV) {
			return $this->Gravatar->image($email, $options);
		} else {
			return $this->Gravatar->image($uid, $options);
		}
	}


	/**
	 * display traffic light for status etc
	 * 2008-12-28 ms
	 */
	public function statusLight($color = null, $title = null, $alt = null, $options = array()) {
		$icons = array(
			'green', 'yellow', 'red', 'blue'
			/*
			'red' => array(
				'title'=>'',
				'alt'=>''
			),
			*/
		);

		$icon = (in_array($color, $icons) ? $color : 'blank');

		$defaultOptions = array('title' => (!empty($title) ? $title : ucfirst(__('color' . ucfirst($color)))), 'alt' => (!empty($alt) ? $alt :
			__('color' . ucfirst($color))), 'class' => 'icon help');
		$options = array_merge($defaultOptions, $options);

		return $this->Html->image('icons/status_light_' . $icon . '.gif', $options);
	}


	public function onlineIcon($modified = null, $options = array()) {
		# from low (off) to high (on)
		$icons = array('healthbar0.gif', 'healthbar1.gif', 'healthbar1b.gif', 'healthbar2.gif', 'healthbar3.gif', 'healthbar4.gif', 'healthbar5.gif');

		# default = offline
		$res = $icons[0]; // inaktiv

		$time = strtotime($modified);
		$timeAgo = time() - $time; # in seconds

		if ($timeAgo < 180) { # 3min // aktiv
			$res = $icons[6];
		} elseif ($timeAgo < 360) { # 6min
			$res = $icons[5];
		} elseif ($timeAgo < 540) { # 9min
			$res = $icons[4];
		} elseif ($timeAgo < 720) { # 12min
			$res = $icons[3];
		} elseif ($timeAgo < 900) { # 15min
			$res = $icons[2];
		} elseif ($timeAgo < 1080) { # 18min
			$res = $icons[1];
		}

		return $this->Html->image('misc/' . $res, array('style' => 'width: 60px; height: 16px'));
	}

	/**
	 * returns red colored if not ok
	 * @param $okValue
	 */
	public function warning($value, $ok = false) {
		if ($ok !== true) {
			return $this->ok($value, false);
		}
		return $value;
	}

	/**
	 * returns green on ok, red otherwise
	 * @param mixed $currentValue
	 * @param bool $ok: true/false (defaults to false)
	 * //@param string $comparizonType
	 * //@param mixed $okValue
	 * @return string $newValue nicely formatted/colored
	 * 2009-08-02 ms
	 */
	public function ok($value, $ok = false) {
		if ($ok === true) {
			$value = '<span class="red" style="color:green">' . $value . '</span>';
		} else {
			$value = '<span class="red" style="color:red">' . $value . '</span>';
		}
		return $value;
	}

	/**
	 * test@test.de becomes t..t@t..t.de
	 * @param string $email: valid(!) email address
	 * 2009-08-30 ms
	 */
	public function hideEmail($mail) {
		$mailParts = explode('@', $mail, 2);
		$domainParts = explode('.', $mailParts[1], 2);

		$email = mb_substr($mailParts[0], 0, 1) . '..' . mb_substr($mailParts[0], -1, 1) . '@' . mb_substr($domainParts[0], 0, 1) . '..' . mb_substr($domainParts[0],
			-1, 1) . '.' . $domainParts[1];
		return $email;
	}


	/**
	 * shorten text (either exact or intelligent)
	 * @param array $options
	 * - ending, exact, html
	 * 2009-08-08 ms
	 */
	public function truncate($text, $length = 100, $options = array()) {
		$defaults = array(
			'ending' => CHAR_HELLIP
		);
		$options = array_merge($defaults, $options);
		return parent::truncate($text, $length, $options);
	}


	/**
	 * (intelligent) Shortening of a text string
	 * @param STRING textstring
	 * @param INT chars = max-length
	 * For options array:
	 * @param BOOLEAN strict (default: FALSE = intelligent shortening, cutting only between whole words)
	 * @param STRING ending (default: '...' no leading whitespace)
	 * @param BOOLEAN remain_lf (default: false = \n to ' ')
	 * Note: ONLY If intelligent:
	 * - the word supposed to be cut is removed completely (instead of remaining as last one)
	 * - Looses line breaks (for textarea content to work with this)!
	 * @deprecated use truncate instead
	 * 2008-10-30 ms
	 */
	public function shortenText($textstring, $chars, $options = array()) {
		$chars++; # add +1 for correct cut
		$needsEnding = false;

		#Options
		$strict = false;
		$ending = CHAR_HELLIP; //'...';
		$remain_lf = false; // not implemented: choose if LF transformed to ' '
		$class = 'help';
		$escape = true;
		$title = '';

		if (!empty($options) && is_array($options)) {
			if (!empty($options['strict']) && ($options['strict'] === true || $options['strict'] === false)) {
				$strict = $options['strict'];
			}
			if (!empty($options['remain_lf']) && ($options['remain_lf'] === true || $options['remain_lf'] === false)) {
				$remain_lf = $options['remain_lf'];
			}

			if (isset($options['title'])) {
				$title = $options['title'];
				if ($options['title'] === true) {
					$title = $textstring;
				}
			}
			if (isset($options['class']) && $options['class'] === false) {
				$class = '';
			}

			if (isset($options['ending'])) {
				$ending = (string )$options['ending'];
			}

			if (isset($options['escape'])) {
				$escape = (bool)$options['escape'];
			}
		}

		$textstring = trim($textstring);

		# cut only between whole words
		if ($strict !== true) {
			$completeWordText = $textstring . ' ';
			# transform line breaks to whitespaces (for textarea content etc.)
			$completeWordTextLf = str_replace(LF, ' ', $completeWordText);
			$completeWordText = $completeWordTextLf;
			$completeWordText = substr($completeWordTextLf, 0, $chars);
			# round the text to the previous entire word instead of cutting off part way through a word
			$completeWordText = substr($completeWordText, 0, strrpos($completeWordText, ' '));
		}

		$textEnding = '';
		if ($strict !== true && strlen($completeWordText) > 1) {
			$text = trim($completeWordText);
			# add ending only if result is shorter then original
			if (strlen($text) < strlen(trim($completeWordTextLf))) {
				$textEnding = ' ' . $ending; # additional whitespace as there is a new word added
			}
		} else {
			$text = trim(substr($textstring, 0, $chars));
			# add ending only if result is shorter then original
			if (strlen($text) < strlen($textstring)) {
				$textEnding = $ending;
			}
		}

		if ($escape) {
			$text = h($text);
			$title = h($title);
		}
		$text .= $textEnding;

		#TitleIfTooLong
		if (!empty($title)) {
			$text = '<span ' . (!empty($class) ? 'class="' . $class . '" ' : '') . 'title="' . $title . '">' . $text . '</span>';
		}

		return $text;
	}


	/**
	 *
	 * Inspired by the tab2space function found at:
	 * @see http://aidan.dotgeek.org/lib/?file=function.tab2space.php
	 */
	public function tab2space($text, $spaces = 4) {
	$spaces = str_repeat(" ", $spaces);
	$text = preg_split("/\r\n|\r|\n/", trim($text));
	$word_lengths = array();
	$w_array = array();

	// Store word lengths
	foreach ($text as $line) {
		$words = preg_split("/(\t+)/", $line, -1, PREG_SPLIT_DELIM_CAPTURE);
		foreach (array_keys($words) as $i) {
			$strlen = strlen($words[$i]);
			$add = isset($word_lengths[$i]) && ($word_lengths[$i] < $strlen);
			if ($add || !isset($word_lengths[$i])) {
				$word_lengths[$i] = $strlen;
			}
		}
		$w_array[] = $words;
	}

	// Clear $text
	$text = '';

	// Apply padding when appropriate and rebuild the string
	foreach (array_keys($w_array) as $i) {
		foreach (array_keys($w_array[$i]) as $ii) {
			if (preg_match("/^\t+$/", $w_array[$i][$ii])) {
				$w_array[$i][$ii] = str_pad($w_array[$i][$ii], $word_lengths[$ii], "\t");
			} else {
				$w_array[$i][$ii] = str_pad($w_array[$i][$ii], $word_lengths[$ii]);
			}
		}
		$text .= str_replace("\t", $spaces, implode("", $w_array[$i])) . "\n";
	}

	// Finished
	return $text;
	}

	/**
	 * Word Censoring Function
	 *
	 * Supply a string and an array of disallowed words and any
	 * matched words will be converted to #### or to the replacement
	 * word you've submitted.
	 * @param	string	the text string
	 * @param	string	the array of censoered words
	 * @param	string	the optional replacement value
	 * @return	string
	 * 2009-11-11 ms
	 */
	public function wordCensor($str, $censored, $replacement = '') {
		if (!is_array($censored)) {
			return $str;
		}

		$str = ' ' . $str . ' ';

		// \w, \b and a few others do not match on a unicode character
		// set for performance reasons. As a result words like ..ber
		// will not match on a word boundary. Instead, we'll assume that
		// a bad word will be bookended by any of these characters.
		$delim = '[-_\'\"`() {}<>\[\]|!?@#%&,.:;^~*+=\/ 0-9\n\r\t]';

		foreach ($censored as $badword) {
			if ($replacement != '') {
				$str = preg_replace("/({$delim})(" . str_replace('\*', '\w*?', preg_quote($badword, '/')) . ")({$delim})/i", "\\1{$replacement}\\3", $str);
			} else {
				$str = preg_replace("/({$delim})(" . str_replace('\*', '\w*?', preg_quote($badword, '/')) . ")({$delim})/ie", "'\\1'.str_repeat('#', strlen('\\2')).'\\3'",
					$str);
			}
		}

		return trim($str);
	}


	/**
	 * Translate a result array into a HTML table
	 *
	 * @author      Aidan Lister <aidan@php.net>
	 * @version     1.3.2
	 * @link        http://aidanlister.com/2004/04/converting-arrays-to-human-readable-tables/
	 * @param       array  $array      The result (numericaly keyed, associative inner) array.
	 * @param       bool   $recursive  Recursively generate tables for multi-dimensional arrays
	 * @param       string $null       String to output for blank cells
	 */
	public function array2table($array, $options = array()) {
		$defaults = array(
			'null' => '&nbsp;',
			'recursive' => false,
			'heading' => true,
			'escape' => true
		);
		$options = am($defaults, $options);

		// Sanity check
		if (empty($array) || !is_array($array)) {
			return false;
		}

		if (!isset($array[0]) || !is_array($array[0])) {
			$array = array($array);
		}

		// Start the table
		$table = "<table>\n";

		if ($options['heading']) {
			// The header
			$table .= "\t<tr>";
			// Take the keys from the first row as the headings
			foreach (array_keys($array[0]) as $heading) {
				$table .= '<th>' . ($options['escape'] ? h($heading) : $heading) . '</th>';
			}
			$table .= "</tr>\n";
		}

		// The body
		foreach ($array as $row) {
			$table .= "\t<tr>";
			foreach ($row as $cell) {
				$table .= '<td>';

				// Cast objects
				if (is_object($cell)) {
					$cell = (array)$cell;
				}

				if ($options['recursive'] && is_array($cell) && !empty($cell)) {
					// Recursive mode
					$table .= "\n" . self::array2table($cell, $options) . "\n";
				} else {
					$table .= (!is_array($cell) && strlen($cell) > 0) ? ($options['escape'] ? h($cell) : $cell) : $options['null'];
				}

				$table .= '</td>';
			}

			$table .= "</tr>\n";
		}

		$table .= '</table>';
		return $table;
	}




	public $icons = array(
		'up' => array(
			'pic'=>ICON_UP,
			'title'=>'Up',
		),
		'down' => array(
			'pic'=>ICON_DOWN,
			'title'=>'Down',
		),
		'edit' => array(
			'pic'=>ICON_EDIT,
			'title'=>'Edit',
		),
		'view' => array(
			'pic'=>ICON_VIEW,
			'title'=>'View',
		),
		'delete' => array(
			'pic'=>ICON_DELETE,
			'title'=>'Delete',
		),
		'reset' => array(
			'pic'=>ICON_RESET,
			'title'=>'Reset',
		),
		'help' => array(
			'pic'=>ICON_HELP,
			'title'=>'Help',
		),
		'loader' => array(
			'pic'=>'loader.white.gif',
			'title'=>'Loading...',
		),
		'loader-alt' => array(
			'pic'=>'loader.black.gif',
			'title'=>'Loading...',
		),
		'details' => array(
			'pic'=>ICON_DETAILS,
			'title'=>'Details',
		),
		'use' => array(
			'pic'=>ICON_USE,
			'title'=>'Use',
		),
		'yes' => array(
			'pic'=>ICON_YES,
			'title'=>'Yes',
		),
		'no' => array(
			'pic'=>ICON_NO,
			'title'=>'No',
		),
		# deprecated from here down
		'close' => array(
			'pic'=>ICON_CLOCK,
			'title'=>'Close',
		),
		'reply' => array(
			'pic'=>ICON_REPLY,
			'title'=>'Reply',
		),
		'time' => array(
			'pic'=>ICON_CLOCK,
			'title'=>'Time',
		),
		'check' => array(
			'pic'=>ICON_CHECK,
			'title'=>'Check',
		),
		'role' => array(
			'pic'=>ICON_ROLE,
			'title'=>'Role',
		),
		'add' => array(
			'pic'=>ICON_ADD,
			'title'=>'Add',
		),
		'remove' => array(
			'pic'=>ICON_REMOVE,
			'title'=>'Remove',
		),
		'email' => array(
			'pic'=>ICON_EMAIL,
			'title'=>'Email',
		),
		'options' => array(
			'pic'=>ICON_SETTINGS,
			'title'=>'Options',
		),
		'lock' => array(
			'pic'=>ICON_LOCK,
			'title'=>'Locked',
		),
		'warning' => array(
			'pic'=>ICON_WARNING,
			'title'=>'Warning',
		),
		'genderUnknown' => array(
			'pic'=>'gender_icon.gif',
			'title'=>'genderUnknown',
		),
		'genderMale' => array(
			'pic'=>'gender_icon_m.gif',
			'title'=>'genderMale',
		),
		'genderFemale' => array(
			'pic'=>'gender_icon_f.gif',
			'title'=>'genderFemale',
		),
	);

}


# default icons

if (!defined('ICON_UP')) {
	define('ICON_UP', 'up.gif');
}
if (!defined('ICON_DOWN')) {
	define('ICON_DOWN', 'down.gif');
}
if (!defined('ICON_EDIT')) {
	define('ICON_EDIT', 'edit.gif');
}
if (!defined('ICON_VIEW')) {
	define('ICON_VIEW', 'see.gif');
}
if (!defined('ICON_DELETE')) {
	define('ICON_DELETE', 'delete.gif');
}
if (!defined('ICON_DETAILS')) {
	define('ICON_DETAILS', 'loupe.gif');
}
if (!defined('ICON_OPTIONS')) {
	define('ICON_OPTIONS', 'options.gif');
}
if (!defined('ICON_SETTINGS')) {
	define('ICON_SETTINGS', 'options.gif');
}
if (!defined('ICON_USE')) {
	define('ICON_USE', 'use.gif');
}
if (!defined('ICON_CLOSE')) {
	define('ICON_CLOSE', 'close.gif');
}
if (!defined('ICON_REPLY')) {
	define('ICON_REPLY', 'reply.gif');
}

if (!defined('ICON_RESET')) {
	define('ICON_RESET', 'reset.gif');
}
if (!defined('ICON_HELP')) {
	define('ICON_HELP', 'help.gif');
}
if (!defined('ICON_YES')) {
	define('ICON_YES', 'yes.gif');
}
if (!defined('ICON_NO')) {
	define('ICON_NO', 'no.gif');
}
if (!defined('ICON_CLOCK')) {
	define('ICON_CLOCK', 'clock.gif');
}
if (!defined('ICON_CHECK')) {
	define('ICON_CHECK', 'check.gif');
}
if (!defined('ICON_ROLE')) {
	define('ICON_ROLE', 'role.gif');
}
if (!defined('ICON_ADD')) {
	define('ICON_ADD', 'add.gif');
}
if (!defined('ICON_REMOVE')) {
	define('ICON_REMOVE', 'remove.gif');
}
if (!defined('ICON_EMAIL')) {
	define('ICON_EMAIL', 'email.gif');
}
if (!defined('ICON_LOCK')) {
	define('ICON_LOCK', 'lock.gif');
}
if (!defined('ICON_WARNING')) {
	define('ICON_WARNING', 'warning.png');
}
if (!defined('ICON_MAP')) {
	define('ICON_MAP', 'map.gif');
}

