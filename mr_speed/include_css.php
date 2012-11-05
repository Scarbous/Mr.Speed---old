<?php

global $wp;

$foncig = json_decode(file_get_contents(dirname(__FILE__).'/cache/config.json'));
$cssfile = dirname(__FILE__).'/cache/'.$wp->query_vars['css'];
$wp->query_vars['css'] = str_replace('.gzip','',$wp->query_vars['css'],$gzipcount);



foreach($foncig->css as $c) :
	if( $c->short == $wp->query_vars['css'] ):
		$akt_css = $c;
		break;
	endif;
endforeach;


$regenerate = false;
if(file_exists($cssfile) ) :
	if(filectime($cssfile) > $c->ctime ) :
		$raw_css = file_get_contents($cssfile);
	else :
		$regenerate = true;
	endif;
else :
	$regenerate = true;
endif;

if($regenerate == true) :
	foreach( $c->inc as $i ) :
		$raw_css .= file_get_contents(dirname(__FILE__).'/cache/'.$i.'.css');
	endforeach;
	$raw_css = $gzipcount > 0 ? gzencode($raw_css) : $raw_css;
	file_put_contents($cssfile, $raw_css);
endif;



$lastmod	= gmdate('D, d M Y H:i:s \G\M\T', $c->ctime);
$etag		= $c->ctime.'_'.$wp->query_vars['css'];

$ifmod = isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ? $_SERVER['HTTP_IF_MODIFIED_SINCE'] == $lastmod : null; 
$iftag = isset($_SERVER['HTTP_IF_NONE_MATCH']) ? $_SERVER['HTTP_IF_NONE_MATCH'] == $etag : null; 

if (($ifmod || $iftag) && ($ifmod !== false && $iftag !== false) && $regenerate == false) { 
    header('HTTP/1.0 304 Not Modified'); 
} else {
	header("Last-Modified: ".$lastmod);
	header('ETag: '.$etag);
}
if($gzipcount) :
	header("Content-Encoding: gzip");
endif;
header("Content-type: text/css; charset: UTF-8");
header("Cache-Control: must-revalidate");
$offset = 604800; // one week
header("Expires: " . gmdate("D, d M Y H:i:s", $c->ctime+$offset) . " GMT");


echo $raw_css;
