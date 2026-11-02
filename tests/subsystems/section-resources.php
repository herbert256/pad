<?php
define('PAD',dirname(__DIR__,2).'/pad/');
function check($ok,$why='fixture'){if(!$ok)throw new RuntimeException($why);}
function padError($s){throw new InvalidArgumentException($s);}
function padMakeSafe($s,$n){return substr($s,0,$n);}
function padDirList($p){return scandir($p);}
require PAD.'lib/subsystem.php';
function padSectionEscape($s){return htmlspecialchars($s,ENT_QUOTES);}function padSectionLink($s){return str_starts_with($s,'javascript:')?'':htmlspecialchars($s,ENT_QUOTES);}require PAD.'subs/sections/_lib/resources.php';$h=padSectionResources([['name'=>'<Chapter>','link'=>'javascript:bad','text'=>'<script>']]);check(!str_contains($h,'<script>'));check(!str_contains($h,'javascript:'));check(str_contains(padSectionResources([]),'No '));
