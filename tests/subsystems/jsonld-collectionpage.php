<?php
define('PAD',dirname(__DIR__,2).'/pad/');
function check($ok,$why='fixture'){if(!$ok)throw new RuntimeException($why);}
function padError($s){throw new InvalidArgumentException($s);}
function padMakeSafe($s,$n){return substr($s,0,$n);}
function padDirList($p){return scandir($p);}
require PAD.'lib/subsystem.php';
function padProtect($s){return $s;} function padUnprotect($s){return $s;} require PAD.'subs/jsonld/_lib/jsonld.php'; $errors=[];$report=function($s)use(&$errors){$errors[]=$s;};$a=padJsonLdMake('collectionpage',[['name'=>'Guides </script>','url'=>'https://example.org/guides','image'=>'https://example.org/photo.jpg']],[],NULL,$report);check(!$errors);check($a['@type']==='CollectionPage');check(substr_count(padJsonLdScript($a),'</script>')===1);padJsonLdMake('collectionpage',[['name'=>'bad','url'=>'javascript:alert(1)']],[],NULL,$report);check((bool)$errors);
