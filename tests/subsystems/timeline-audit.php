<?php
define('PAD',dirname(__DIR__,2).'/pad/');
function check($ok,$why='fixture'){if(!$ok)throw new RuntimeException($why);}
function padError($s){throw new InvalidArgumentException($s);}
function padMakeSafe($s,$n){return substr($s,0,$n);}
function padDirList($p){return scandir($p);}
require PAD.'lib/subsystem.php';
require PAD.'subs/timelines/_lib/audit.php';$r=['timestamp'=>'2026-01-01T12:00:00Z','actor'=>'A','action'=>'edited','target'=>'<x>'];$h=padTimelineAudit([$r,$r+['detail'=>'<script>']]);check(substr_count($h,'<li>')===2);check(!str_contains($h,'<script>'));check(str_contains($h,'&lt;x&gt;'));
