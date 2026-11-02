<?php
define('PAD',dirname(__DIR__,2).'/pad/');
function padDirList($p){return scandir($p);}
function padError($m){throw new InvalidArgumentException($m);}
require PAD.'lib/subsystem.php';
require PAD.'lib/signals.php';
function check($v){if(!$v)throw new RuntimeException('fixture failed');}
function near($a,$b,$e=1e-10){check(abs($a-$b)<=$e*max(1,abs($b)));}
function rejects($f){try{$f();}catch(InvalidArgumentException $e){return;}throw new RuntimeException('invalid input accepted');}
$r=padSignal('window',[1,1,1,1,1],['window'=>'hann']);foreach($r['values'] as $i=>$v)near($v,[0,.5,1,.5,0][$i]);near($r['coherentGain'],.4);check(padSignal('window',[2],['window'=>'blackman'])['values']===[2.0]);
$r=padSignal('spectrum',[0,1,0,-1,0,1,0,-1],['sampleRate'=>8,'window'=>'rectangular']);foreach($r['bins'] as $i=>$bin)near($bin['value'],$i===2?1:0,1e-12);near($r['bins'][2]['frequency'],2);near($r['bins'][2]['phase'],-M_PI/2);$r=padSignal('spectrum',array_fill(0,8,3),['sampleRate'=>8,'window'=>'rectangular']);near($r['bins'][0]['value'],3);foreach(array_slice($r['bins'],1) as $bin)near($bin['value'],0,1e-12);
$values=[1,2,-3,4,-5,6,7,-8];$r=padSignal('spectrum',$values,['sampleRate'=>100,'window'=>'rectangular','scaling'=>'power']);near(array_sum(array_column($r['bins'],'value')),array_sum(array_map(fn($v)=>$v*$v,$values))/8,1e-12);
$r=padSignal('convolve',['signal'=>[1,2,3],'kernel'=>[4,5]]);check($r['values']===[4.0,13.0,22.0,15.0]);check(padSignal('convolve',['signal'=>[1,2,3],'kernel'=>[1]])['values']===[1.0,2.0,3.0]);check(padSignal('convolve',['signal'=>[1,2,3],'kernel'=>[4,5]],['mode'=>'valid'])['values']===[13.0,22.0]);
$r=padSignal('correlate',['a'=>[1,0,0],'b'=>[0,0,1]],['sampleRate'=>10]);check($r['lags']===[-2,-1,0,1,2]&&$r['values']===[0.0,0.0,0.0,0.0,1.0]);near($r['lagSeconds'][4],.2);$r=padSignal('correlate',['a'=>[1,2,3],'b'=>[1,2,3]],['normalization'=>'coefficient']);near($r['values'][2],1);check(padSignal('correlate',['a'=>[0],'b'=>[1]],['normalization'=>'coefficient'])['status']==='degenerate');
check(padSignal('spectrum',array_fill(0,100,1),['sampleRate'=>10,'maxWork'=>10])['status']==='limit');rejects(fn()=>padSignal('spectrum',[1,2],[]));rejects(fn()=>padSignal('window',[INF]));rejects(fn()=>padSignal('convolve',['signal'=>[1],'kernel'=>[1,2]],['mode'=>'valid']));$r=padSignal('spectrum',[1,-1,1,-1,1,-1,1,-1],['sampleRate'=>8,'window'=>'rectangular']);near($r['bins'][4]['value'],1);near($r['bins'][4]['frequency'],4);
echo "signals: exact windows, sinusoid/DC spectra, Parseval, convolution and impulse-lag/work fixtures passed\n";
