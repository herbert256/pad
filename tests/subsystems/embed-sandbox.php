<?php
define('PAD',dirname(__DIR__,2).'/pad/');
function check($ok,$why='fixture'){if(!$ok)throw new RuntimeException($why);}
function padError($s){throw new InvalidArgumentException($s);}
function padMakeSafe($s,$n){return substr($s,0,$n);}
function padDirList($p){return scandir($p);}
require PAD.'lib/subsystem.php';
require PAD.'subs/embeds/_lib/sandbox.php';$h=padEmbedSandbox('<script>parent.location="https://example.org"</script>','<demo>');check(str_contains($h,'sandbox=""'));check(!str_contains($h,'<script>'));check(str_contains($h,'default-src'));check(!str_contains(padEmbedSandbox('<b>x</b>','x',['scripts'=>TRUE]),'allow-same-origin'));try{padEmbedSandbox('x','x',['height'=>2]);check(false);}catch(InvalidArgumentException $e){}
