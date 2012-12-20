<?php
/*                                                                                                                                                                                                                                                             
Plugin Name: Mr.Speed
Description: Optimizing Your Website's Speed
Version: 0.1
Author: Sascha Heilmeier, Tim Weisenberger
Author URI: http://www.machen.de
*/

$mr_speed = new mr_speed();

class mr_speed {
	var $admin_navi = array(
#		'main'	=> array('title' => 'Allgemein', 'file' => 'main.php'),
		'html'	=> array('title' => 'HTML', 'file' => 'html.php'),
		'css'	=> array('title' => 'CSS', 'file' => 'css.php'),
		'js'	=> array('title' => 'JS', 'file' => 'js.php'),
	);
	var $prefix = 'mr_speed';
	var $pdir = '';
	var $fields = array (
		'html' => array (
			'html'				=> array( 'title' => 'HTML Optimierung', 'type' => 'checkbox', 'value' => '1', 'default' => ''),
			'admin'				=> array( 'title' => 'Admin HTML', 'type' => 'checkbox', 'value' => '1', 'default' => ''),
			'gzip'				=> array( 'title' => 'GZIP', 'type' => 'checkbox', 'value' => '1', 'default' => '1'),
			'comments' 			=> array( 'title' => 'HTML Kommentare entfernen', 'type' => 'checkbox', 'value' => '1', 'default' => '1'),
			'breaks' 			=> array( 'title' => 'Zeilenumbrüche entfernen', 'type' => 'checkbox', 'value' => '1', 'default' => '1'),
			'whitespaces' 		=> array( 'title' => 'Doppelte Leerzeichen entfernen', 'type' => 'checkbox', 'value' => '1', 'default' => '1'),
			'save_code'			=> array( 'title' => 'Code-Tags ausschliesen', 'type' => 'checkbox', 'value' => '1', 'default' => '1'),
			'save_pre' 			=> array( 'title' => 'Pre-Tags ausschliesen', 'type' => 'checkbox', 'value' => '1', 'default' => '1'),
			'save_script' 		=> array( 'title' => 'Script-Tags ausschliesen', 'type' => 'checkbox', 'value' => '1', 'default' => ''),
			'save_style' 		=> array( 'title' => 'Style-Tags ausschliesen', 'type' => 'checkbox', 'value' => '1', 'default' => ''),
			'save_textarea'		=> array( 'title' => 'Textarea-Tags ausschliesen', 'type' => 'checkbox', 'value' => '1', 'default' => '1'),
		),
		'css' => array(
			'css'				=> array( 'title' => 'CSS Optimierung', 'type' => 'checkbox', 'value' => '1', 'default' => ''),
			'admin'				=> array( 'title' => 'Admin CSS',	'type' => 'checkbox', 'value' => '1', 'default' => ''),
			'gzip'				=> array( 'title' => 'GZIP', 'type' => 'checkbox', 'value' => '1', 'default' => '1'),
			'base64'			=> array( 'title' => 'Bilder in base64 umwandeln', 'type' => 'checkbox', 'value' => '1', 'default' => '1'),
			'base64_size'		=> array( 'title' => 'Bilder Maximale größe für base64 umwandlung', 'value' => '1000', 'default' => '1000'),
			'comments' 			=> array( 'title' => 'CSS Kommentare entfernen','type' => 'checkbox', 'value' => '1', 'default' => '1'),
			'breaks' 			=> array( 'title' => 'Zeilenumbrüche entfernen', 'type' => 'checkbox', 'value' => '1', 'default' => '1'),
			'whitespaces' 		=> array( 'title' => 'Leerzeichen entfernen', 'type' => 'checkbox', 'value' => '1', 'default' => '1'),
			'exclude'			=> array( 'title' => 'Ausschließen', 'type' => 'textarea' ),
		),
		'js' => array(
			'js'				=> array( 'title' => 'JS Optimierung', 'type' => 'checkbox', 'value' => '1', 'default' => ''),
			'admin'				=> array( 'title' => 'Admin CSS',	'type' => 'checkbox', 'value' => '1', 'default' => ''),
			'gzip'				=> array( 'title' => 'GZIP', 'type' => 'checkbox', 'value' => '1', 'default' => '1'),
			'comments' 			=> array( 'title' => 'JS Kommentare entfernen','type' => 'checkbox', 'value' => '1', 'default' => '1'),
			'breaks' 			=> array( 'title' => 'Zeilenumbrüche entfernen', 'type' => 'checkbox', 'value' => '1', 'default' => '1'),
			'whitespaces' 		=> array( 'title' => 'Leerzeichen entfernen', 'type' => 'checkbox', 'value' => '1', 'default' => '1'),
			'exclude'			=> array( 'title' => 'Ausschließen', 'type' => 'textarea' ),
		),
	);

	var $cache_path = '/cache/';
/********************
*
********************/
function __construct() {
	$this->pdir			= '/wp-content/plugins/mr_speed/';
	$this->cache_path	= dirname(__FILE__).$this->cache_path;
	if(!$this->config = get_option( $this->prefix )) :
		foreach($this->fields as $fsk => $fs) :
			foreach($fs as $fk => $f) :
				$this->config[$fsk][$fk] = $f['default'];
			endforeach;
		endforeach;
	endif;
	if ( !is_admin() ) :
		if (strpos($_SERVER["HTTP_ACCEPT_ENCODING"], "gzip")!==false) :
			$this->gz = true;
		else :
			$this->gz = false;
		endif;
	endif;

	if ( is_admin() ) :
		wp_enqueue_style('admin_css', plugins_url('admin.css', '/mr_speed/css/admin.css'));
		add_action('admin_menu', array($this, 'add_menu_page'));
	else :
		if( $this->config['html']['html'] == '1' && !($current_user->data->wp_capabilities['administrator'] == 1 && $this->config['html']['admin'] == '1') ) :
		ob_start("ob_gzhandler");
		#	$this->html_optimizing();
		endif;
	endif;
	add_action('init',			array($this, 'init'));
}
/********************
*
********************/
function init(){
	if ( !is_admin() ) :
		global $current_user;
		get_currentuserinfo();
		if( $this->config['css']['css'] == '1' || $this->config['js']['js'] == '1') :
			$this->get_config();
		endif;
		if( $this->config['css']['css'] == '1' && !($current_user->data->wp_capabilities['administrator'] == 1 && $this->config['css']['admin'] == '1') ) :
			$this->css_optimizing();
		endif;
		if( $this->config['js']['js'] == '1' && !($current_user->data->wp_capabilities['administrator'] == 1 && $this->config['js']['admin'] == '1') ) :
			include('JShrink-0.2.class.php');
			$this->js_optimizing();
		endif;
	endif;
}
/********************
*
********************/
function get_config(){
	if(file_exists($this->cache_path.'config.json')) :
		$this->configFile = json_decode(file_get_contents($this->cache_path.'config.json'));
	else :
		$this->configFile = json_decode(json_encode(array('time' =>time())));
	endif;
}
/********************
*
********************/
function set_config(){
	file_put_contents($this->cache_path.'config.json', json_encode($this->configFile));
}
/********************
*
********************/
function update_config(){
	if($_POST['mr_speed'] && is_admin()) :
		foreach($this->fields[$this->subprefix] as $k => $v):
			$this->config[$this->subprefix][$k] = $_POST['mr_speed'][$this->subprefix][$k];
		endforeach;
	endif;

	if($this->config == '') :
		delete_option($this->prefix);
	else :
		if(get_option($this->prefix)) :
			update_option($this->prefix, $this->config); 
		else :
			add_option($this->prefix, $this->config, '', 'no');
		endif;
	endif;
}
/********************
*
********************/
function js_optimizing(){
	if(!$this->config['js']['gzip']) :
		$this->js->gz=false;
	else :
		$this->js->gz=$this->gz;
	endif;
	add_filter( 'print_scripts_array', array($this, 'filter_print_scripts_array') );
}
/********************
*
********************/

function filter_print_scripts_array($js_array){
	global $wp_scripts;
	if(count($js_array) == 0)
		return(array());
	if ( !is_a($wp_scripts, 'WP_Scripts') ) $wp_scripts = new WP_Scripts();
	$filename = '';
	$maxtime = 0;
	$all_css = array();
	$regenerate = false;
	$exlude_js = array();
	foreach(explode("\n",$this->config['js']['exclude']) as $ejs) :
		$exlude_js[] = trim($ejs);
	endforeach;
	foreach( $wp_scripts->to_do as $jskey => $js) :
		if(in_array($js, $exlude_js)) :
			continue;
		endif;
		$wp_scripts->done[] = $wp_scripts->to_do[$jskey];
		unset( $wp_scripts->to_do[$jskey] );

		if( $wp_scripts->query( $js, 'queue' ) ) :
			$query = $wp_scripts->query( $js );
			
			$url = get_bloginfo('url');
			
			$all_js[$js] = array(
				'regenerate'	=> false,
				'orgsrc'		=> str_replace(array($url,WP_CONTENT_URL),array('','/wp-content'),$query->src),
				'cachefile'		=> $js.'.js',
			);
			
		#	print_r(WP_CONTENT_URL);
		#	die();
			
			
			if( file_exists( $this->cache_path.$all_js[$js]['cachefile']) ) :
				if( filectime( rtrim(ABSPATH,'/').$all_js[$js]['orgsrc'] ) < filectime($this->cache_path.$all_js[$js]['cachefile']) ):
	
				else :
					if( $this->optimize_js($all_js[$js]) )
						$regenerate = true;
				endif;
			else :
				if( $this->optimize_js($all_js[$js]) )
					$regenerate = true;
			endif;
			$include_js[] = $js;
		endif;
	endforeach;
	if(count($include_js) >0 ):
		$filename = implode('_',$include_js);
	
		if(!$this->configFile->js->$filename) :
			$count = 0;
			$shortname = md5($filename);
			do{
				$shortname = $shortname.'_'.$count++;
			} while(file_exists( $this->cache_path.$shortname));
			$this->configFile->js->$filename->short	= $shortname.'_mrs.js';
			$this->configFile->js->$filename->inc	= $include_js;
			$this->set_config();
		endif;
	
		if( $regenerate==true || !file_exists(dirname(__FILE__).'/cache/'.$this->configFile->js->$filename->short) ) :
			$raw_js = '';
			foreach( $this->configFile->js->$filename->inc as $i ) :
				$raw_js .= file_get_contents(dirname(__FILE__).'/cache/'.$i.'.js');
			endforeach;
			$raw_js_gzip = gzencode($raw_js);
			file_put_contents(dirname(__FILE__).'/cache/'.$this->configFile->js->$filename->short, $raw_js);
			file_put_contents(dirname(__FILE__).'/cache/'.$this->configFile->js->$filename->short.'.gzip', $raw_js_gzip);
		endif;
	
		echo '<script src="/js/'.$this->configFile->js->$filename->short.($this->js->gz==true?'.gzip':'').'" ></script>';
	endif;
	return($wp_scripts->to_do);
}
/********************
*
********************/
function optimize_js($js){
	if(file_exists(rtrim(ABSPATH,'/').$js['orgsrc'])):
		$raw_js = file_get_contents( rtrim(ABSPATH,'/').$js['orgsrc'] );
		if(trim($raw_js)=='')
			return false;
		$raw_js = JShrink::minify($raw_js);
		file_put_contents($this->cache_path.$js['cachefile'], $raw_js);
	endif;
}
/********************
*
********************/
function html_optimizing(){
	if(!$this->config['html']['gzip']) :
		$this->html->gz=false;
	else :
		$this->html->gz=$this->gz;
	endif;

	$this->_htmlCacheFile = $this->cache_path.rawurlencode($_SERVER['REQUEST_URI'].'_cache.html');
	if( file_exists($this->_htmlCacheFile) ) :
		if ($this->html->gz) :
			header("Content-Encoding: gzip");
			echo file_get_contents($this->_htmlCacheFile.'.gzip');
		else :
			echo file_get_contents($this->_htmlCacheFile);
		endif;
		die();
	endif;
	add_action('wp_foot', create_function($d,'ob_end_flush();'));
	ob_start(array($this, "ob_html_optimizing"));
}
/********************
*
********************/
public function ob_html_optimizing($html) {

	$this->_replacementHash = 'MINI' . md5($_SERVER['REQUEST_TIME']);
	$this->_placeholders = array();

	// remove HTML comments (not containing IE conditional comments).
	if($this->config['html']['comments'])
		$html = preg_replace_callback('/<!--([\\s\\S]*?)-->/',array($this, '_commentCB'),$html);	

	// replace TAGs with placeholders
	if($this->config['html']['save_code'])
		$html = preg_replace_callback('/\\s*(<code\\b[^>]*?>[\\s\\S]*?<\\/code>)\\s*/i',array($this, '_reservePlace'),$html);
	if($this->config['html']['save_pre'])
		$html = preg_replace_callback('/\\s*(<pre\\b[^>]*?>[\\s\\S]*?<\\/pre>)\\s*/i',array($this, '_reservePlace'),$html);
	if($this->config['html']['save_style'])
		$html = preg_replace_callback('/\\s*(<style\\b[^>]*?>[\\s\\S]*?<\\/style>)\\s*/i',array($this, '_reservePlace'),$html);
	if($this->config['html']['save_script'])
		$html = preg_replace_callback('/\\s*(<script\\b[^>]*?>[\\s\\S]*?<\\/script>)\\s*/i',array($this, '_reservePlace'),$html);
	if($this->config['html']['save_textarea'])
		$html = preg_replace_callback('/\\s*(<textarea\\b[^>]*?>[\\s\\S]*?<\\/textarea>)\\s*/i',array($this, '_reservePlace'),$html);

	if($this->config['html']['whitespaces'])
		$html = preg_replace('/\s\s+/', ' ',$html);
	if($this->config['html']['breaks'])
		$html = preg_replace("/\n/",'',$html);

	$html = str_replace( array_keys($this->_placeholders), array_values($this->_placeholders), $html );

	file_put_contents($this->_htmlCacheFile,$html);
	file_put_contents($this->_htmlCacheFile.'.gzip',gzencode($html));
	
	if ($this->html->gz) :
		header("Content-Encoding: gzip");
		return gzencode($html);
	else :
		return($html);
	endif;
}
/********************
*
********************/
protected function _commentCB($m) {
	return (0 === strpos($m[1], '[') || false !== strpos($m[1], '<![')) ? $m[0]: '';
}
/********************
*
********************/
protected function _reservePlace($content) {
	$content = $content[1];
	$placeholder = '%' . $this->_replacementHash . count($this->_placeholders) . '%';
	$this->_placeholders[$placeholder] = $content;
	return $placeholder;
}
/********************
*
********************/
function css_optimizing(){
	if(!$this->config['css']['gzip']) :
		$this->css->gz=false;
	else :
		$this->css->gz=$this->gz;
	endif;

	include('lessc.inc.php');
	

	add_filter( 'print_styles_array', array($this, 'filter_print_styles_array') );
}
/********************
*
********************/
function optimize_css($css){

	if(file_exists(rtrim(ABSPATH,'/').$css['orgsrc'])):
		$raw_css = file_get_contents( rtrim(ABSPATH,'/').$css['orgsrc'] );
		$less = new lessc();
		$raw_css = $less->parse( $raw_css );
		
		$this->temp_css_path = dirname( $css['orgsrc'] );
		$raw_css = preg_replace_callback('/url\((.*)\)/Uis', array($this, 'css_get_new_file_path'),$raw_css);
		if($this->config['css']['base64'] == '1'):
#			die();
			$raw_css = preg_replace_callback('|url\((.*?)\)|', array($this, 'css_base64'),$raw_css);
		endif;
		if($this->config['css']['comments'] == '1')
			$raw_css = preg_replace('/\/\*(.*)\*\//Uis','',$raw_css);
		if($this->config['css']['breaks'] == '1')
			$raw_css = preg_replace("/\n/",'',$raw_css);
		if( $this->config['css']['whitespaces'] == '1' ) :
			$raw_css = preg_replace('/\s\s+/', ' ',$raw_css);
			$raw_css = preg_replace(array('/:\s/','/\s:/','/\s:\s/'), ':',$raw_css);
			$raw_css = preg_replace(array('/;\s/','/\s;/','/\s;\s/'), ';',$raw_css);
			$raw_css = preg_replace(array('/}\s/','/\s}/','/\s}\s/'), '}',$raw_css);
			$raw_css = preg_replace(array('/{\s/','/\s{/','/\s{\s/'), '{',$raw_css);
			$raw_css = preg_replace('/;}/', '}',$raw_css);
		endif;
		file_put_contents($this->cache_path.$css['cachefile'], $raw_css);
	endif;
}
/********************
*
********************/
function css_get_new_file_path($data){
	return "url('".dirname( $css['orgsrc'] ).$this->temp_css_path.'/'.str_replace(array('\'','\"'),'',$data[1])."')";
}
/********************
*
********************/
function filter_print_styles_array($css_array){
	global $wp_styles;
	if(count($css_array) == 0):
		return(array());
	endif;
	if ( !is_a($wp_styles, 'WP_Styles') ) $wp_styles = new WP_Styles();
	$filename = '';
	$maxtime = 0;
	$all_css = array();
	$regenerate = false;
	$exlude_css = array();
	foreach(explode("\n",$this->config['css']['exclude']) as $ecss) :
		$exlude_css[] = trim($ecss);
	endforeach;
#	print_r($wp_styles->to_do);
#	die();
	foreach( $wp_styles->to_do as $csskey => $css) :
		if(in_array($css, $exlude_css)) :
			continue;
		endif;
		$wp_styles->done[] = $wp_styles->to_do[$csskey];
		unset( $wp_styles->to_do[$csskey] );
		
		$url = get_bloginfo('url');
		
		$query = $wp_styles->query( $css );
		$all_css[$css] = array(
			'regenerate'	=> false,
			'orgsrc'		=> str_replace(array($url,WP_CONTENT_URL),array('','/wp-content'),$query->src),
			'cachefile'		=> $css.'.css',
		);
		if( file_exists( $this->cache_path.$all_css[$css]['cachefile']) ) :
			if( filectime( rtrim(ABSPATH,'/').$all_css[$css]['orgsrc'] ) < filectime($this->cache_path.$all_css[$css]['cachefile']) ):

			else :
				$this->optimize_css($all_css[$css]);
				$regenerate = true;
			endif;
		else :
			$this->optimize_css($all_css[$css]);
			$regenerate = true;
		endif;
		$include_css[] = $css;
	endforeach;
	
	if(count($include_css) > 0) :
		$filename = implode('_',$include_css);
	
		if(!$this->configFile->css->$filename) :
			$count = 0;
			$shortname = md5($filename);
			do{
				$shortname = $shortname.'_'.$count++;
			} while(file_exists( $this->cache_path.$shortname));
			$this->configFile->css->$filename->short	= $shortname.'_mrs.css';
			$this->configFile->css->$filename->inc		= $include_css;
			$this->set_config();
		endif;
	
		if( $regenerate==true || !file_exists(dirname(__FILE__).'/cache/'.$this->configFile->css->$filename->short) ) :
			$raw_css = '';
			foreach( $this->configFile->css->$filename->inc as $i ) :
				$raw_css .= '/** '.$i.' **/'.PHP_EOL.file_get_contents(dirname(__FILE__).'/cache/'.$i.'.css');
			endforeach;
			$raw_css_gzip = gzencode($raw_css);
			file_put_contents(dirname(__FILE__).'/cache/'.$this->configFile->css->$filename->short, $raw_css);
			file_put_contents(dirname(__FILE__).'/cache/'.$this->configFile->css->$filename->short.'.gzip', $raw_css_gzip);
		endif;
	
		echo '<link rel="stylesheet" href="/css/'.$this->configFile->css->$filename->short.($this->css->gz==true?'.gzip':'').'" type="text/css" />';
	endif;
	return($wp_styles->to_do);
}

/********************
*
********************/
function css_base64($data){
	$file_types = array(
		'image/gif',
		'image/png',
		'image/jpeg'
	);
	$file_name = str_replace(array('"', '\''), '', $data[1]);
	$file_name = explode('?', $file_name);
	$css_path = dirname( $file_name[0] );
	$file = rtrim(ABSPATH,'/').trim($file_name[0]);
	if(file_exists($file)) :
		if( in_array(mime_content_type($file),$file_types) ) :
			if(filesize($file) < $this->config['css']['base64_size']) :
				$file_data = file_get_contents($file);
				return('url("data:'.mime_content_type($file).';base64,'. base64_encode($file_data).'")');
			endif;
		endif;
	endif;
	return($data[0]);
}

/********************
*
********************/
function add_menu_page(){
	add_menu_page('Mr.Speed', 'Mr.Speed', 'administrator', 'mr_speed', array($this, 'admin_page'), 'div' );
}
function admin_page() {
?>
<div class="wrap" id="mr_speed">
<h2>Mr. Speed</h2>
<div id="contact_ajax"></div>
<h2 class="nav-tab-wrapper"><?php
$akt_subpage = $_GET['sub_page'] ? $_GET['sub_page'] : key($this->admin_navi);
foreach($this->admin_navi as $key => $n) :
	echo '<a class="nav-tab'.($akt_subpage == $key ? ' nav-tab-active' : '' ).'" href="?page='.$_GET['page'].'&sub_page='.$key.'">'.$n['title'].'</a>';
endforeach;
$this->subprefix = $akt_subpage;
$this->update_config();
?></h2>
<form method="post" action="">
	<input type="hidden" name="<?php echo $this->prefix; ?>[config]" value="1" />
<?php
include('config_pages/'.$this->admin_navi[$akt_subpage]['file']);
?>
	</form>
</div><?php
}
/********************
*
********************/
function get_label($name,$content='') {
	if(!is_array($this->fields[$this->subprefix][$name]))
		return false;
	$field = $this->fields[$this->subprefix][$name];
	$label = '<label for="'.$this->prefix.'_'.($this->subprefix?$this->subprefix.'_':'').$name.'">'.$content.$field['title'] . ($field['required'] ? '(*)': '') .'</label>';
	return($label);
}
/********************
*
********************/
function get_input_field($name, $key=NULL) {
	if(!is_array($this->fields[$this->subprefix][$name]))
		return false;

	$field = $this->fields[$this->subprefix][$name];
	$class = array();
	$class[] = ($error[$field['id']]==true?'error':'');
#	$class[] = ($field['required'] ? 'rel': '');
#	$class[] = ($field['type']=='email' ? 'email': '');
	$class[] = ($this->formerrors[$name]?'error':'');
	$tags = array();
	$tags[] = 'name="'.$this->prefix.($this->subprefix?'['.$this->subprefix.']':'').'['.$name.']"';
	$tags[] = 'id="'.$this->prefix.'_'.($this->subprefix?$this->subprefix.'_':'').$name.'"';
	switch($field['type']) :
		case 'select':
			if($field['value']) :
				$class[] = 'input';
				$class[] = 'config_select';
				$tags[] = 'class="'.trim(implode(' ',$class)).'"';
				$input = '<select '.implode(' ',$tags).'>';
				foreach($field['value'] as $vk => $v) :
					$input .= '<option value="'.$vk.'"'.($this->config[$this->subprefix][$name]==$vk?' selected="selected" ':'').'>'.$v.'</option>';
				endforeach;
			endif;
			$input .= '</select>';
		break;
		case 'textarea':
				$class[] = 'config_textarea';
				$tags[] = 'class="'.trim(implode(' ',$class)).'"';
				$tags[] = 'rows="'.($field['rows'] ? $field['rows'] : '7').'"';
				$tags[] = 'cols="'.($field['cols'] ? $field['cols'] : '20').'"';
			$input = '<textarea '.implode(' ',$tags).'>'.$this->config[$this->subprefix][$name].'</textarea>';
		break;
		case 'checkbox':
			$class[] = 'input';
			$class[] = 'config_checkbox';
			$tags[] = $this->config[$this->subprefix][$name]?'checked="checked"':'';
			$tags[] = 'type="checkbox"';
			$tags[] = 'value="'.$field['value'].'"';
			$tags[] = 'class="'.trim(implode(' ',$class)).'"';
			$input = '<input '.implode(' ',$tags).'/>';
		break;
		case 'radio':
			$class[] = 'input';
			$class[] = 'config_radio';
			$tags[] = 'type="radio"';
			if(!$this->config[$this->subprefix][$name]) :
				$tags[] = $field['value'][$key]['checked']?'checked="checked"':'';
			else :
				$tags[] = $this->config[$this->subprefix][$name] == $key ?'checked="checked"':'';
			endif;
			$tags[] = 'class="'.trim(implode(' ',$class)).'"';
			$tags[] = 'value="'.$key.'"';
			$input = '<input '.implode(' ',$tags).'/>';
		break;
		default :
			$class[] = 'input';
			$class[] = 'config_text';
			$tags[] = 'type="text"';
			$tags[] = ($field['size']?'size="'.$field['size'].'"':'');
			$tags[] = 'value="'.$this->config[$this->subprefix][$name].'"';
			$tags[] = 'class="'.trim(implode(' ',$class)).'"';
			$input = '<input '.implode(' ',$tags).'/>';
		break;
	endswitch;
	return($input);
}
/*******************/
}


/*
class js_compressor {
	public function __construct(){
		add_filter('print_scripts_array', array($this, 'filter_print_scripts_array') );
	}
	
	function filter_print_scripts_array($all_js){
		global $wp_scripts;
			if(!is_a($wp_scripts, 'WP_Scripts')) $wp_scripts = new WP_Scripts();
			
			$all_scripts = '';
			
			foreach($all_js as $key => $js) :
				// check if change to que
				$query = $wp_scripts->query($js, 'registered');
				
				$all_scripts .= $this->minify(file_get_contents($query->src));
			endforeach;
			
			require 'jsmin.php';
  			$compjs = JSMin::minify($all_scripts);
			
			$filename = 'cache/js_compressed_'.time().'.js';
			
			$f = fopen ('cache/'.$filename, 'w');
				fputs ($f, $cont);
			fclose ($f);  

	echo $filename;
	echo '<script type="text/javascript">';
	echo $compjs;
	echo '</script>';
	return( array() );
}


}
*/
