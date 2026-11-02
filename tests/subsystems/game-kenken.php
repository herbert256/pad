<?php
define('PAD',dirname(__DIR__,2).'/pad/');
function check($ok,$why='fixture'){if(!$ok)throw new RuntimeException($why);}
function padError($s){throw new InvalidArgumentException($s);}
function padMakeSafe($s,$n){return substr($s,0,$n);}
function padDirList($p){return scandir($p);}
require PAD.'lib/subsystem.php';
require PAD.'subs/games/_lib/kenken.php';$p=padKenkenDefault();$r=padKenkenSolve($p);check($r['status']==='unique');check($r['solution']===[1,2,3,4,2,3,4,1,3,4,1,2,4,1,2,3]);check(padKenkenSolve($p,1)['status']==='budget');$p['cages'][7]['target']=9;check(padKenkenSolve($p)['status']==='unsolved');$p['cages'][0]['cells']=[0,0];try{padKenkenCheck($p);check(false);}catch(InvalidArgumentException $e){}
