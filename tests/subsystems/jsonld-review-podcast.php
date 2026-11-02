<?php
define('PAD',dirname(__DIR__,2).'/pad/');
function padUnprotect($s){return $s;}function padProtect($s){return $s;}function padMakeSafe($s,$n){return substr($s,0,$n);}
require PAD.'subs/jsonld/_lib/jsonld.php';
function check($v,$why){if(!$v)throw new RuntimeException($why);}
$errors=[];$report=function($e)use(&$errors){$errors[]=$e;};
$r=['name'=>'Review </script>','body'=>'Clear examples','item'=>'PAD','author'=>'Ada','rating'=>4.5];
$obj=padJsonLdMake('review',[$r],[],NULL,$report);check(!$errors&&$obj['@type']==='Review'&&$obj['reviewRating']['ratingValue']===4.5,'review mapping');
check($obj['itemReviewed']['@type']==='Thing'&&$obj['author']['@type']==='Person','typed review objects');
check(substr_count(padJsonLdScript($obj),'</script>')===1,'script escaping');
$r['rating']=6;$obj=padJsonLdMake('review',[$r],[],NULL,$report);check($errors&&!isset($obj['reviewRating']),'invalid rating');
$errors=[];$obj=padJsonLdMake('podcast',[['name'=>'PAD Radio','url'=>'https://example.test/podcast','feed'=>'https://example.test/feed.xml','author'=>['Ada','Bo'],'publisher'=>'PAD']],[],NULL,$report);
check(!$errors&&$obj['@type']==='PodcastSeries'&&$obj['webFeed']==='https://example.test/feed.xml'&&count($obj['author'])===2,'podcast series mapping');
check($obj['publisher']['@type']==='Organization','publisher object');
echo "JSON-LD review/podcast: mapping, bounds and script safety passed\n";
