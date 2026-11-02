<?php
  require __DIR__.'/_expansion_boot.php'; require PAD.'lib/timeseries.php';
  $rows=[['time'=>4,'value'=>8],['time'=>0,'value'=>2],['time'=>1,'value'=>4]];
  $r=padTimeSeries('resample',$rows,['stepSeconds'=>2]);subCheck(array_column($r['rows'],'value')===[3.0,NULL,8.0]);subCheck(array_column($r['rows'],'count')===[2,0,1]);
  subCheck($r['parameters']===['stepSeconds'=>2.0,'origin'=>0.0,'aggregate'=>'mean','boundaryPolicy'=>'snap-4-scaled-epsilons']);
  foreach([[.3,0,.3],[-.3,0,-.3],[.4,.1,.4],[.3-1e-10,0,.2]] as [$time,$origin,$expected]){
    $fraction=padTimeSeries('resample',[['time'=>$time,'value'=>1]],['stepSeconds'=>.1,'origin'=>$origin]);subNear($fraction['rows'][0]['time'],$expected,1e-12);
  }
  subReject(fn()=>padTimeSeries('resample',[['time'=>1e10,'value'=>1]],['stepSeconds'=>1e-8]));
  $tiny=padTimeSeries('resample',[['time'=>0,'value'=>5e-324],['time'=>1,'value'=>5e-324]]);subCheck($tiny['rows'][0]['value']===5e-324,'mean preserves subnormal values');
  $r=padTimeSeries('rolling',$rows,['window'=>2]);subCheck(array_column($r['rows'],'value')===[NULL,3.0,6.0]);
  $r=padTimeSeries('ewma',$rows,['alpha'=>.5]);subCheck(array_column($r['rows'],'value')===[2.0,3.0,5.5]);
  $r=padTimeSeries('difference',$rows,['rate'=>TRUE]);subNear($r['rows'][1]['value'],2);subNear($r['rows'][2]['value'],4/3);
  $gaps=[['time'=>0,'value'=>2],['time'=>1,'value'=>NULL],['time'=>2,'value'=>8]];
  subCheck(array_column(padTimeSeries('ewma',$gaps)['rows'],'value')===[2.0,NULL,8.0]);
  subNear(padTimeSeries('ewma',$gaps,['alpha'=>.5,'gap'=>'carry'])['rows'][2]['value'],5);
  subCheck(array_column(padTimeSeries('difference',$gaps)['rows'],'value')===[NULL,NULL,NULL]);
  $dup=[['time'=>0,'value'=>1],['time'=>0,'value'=>3]];
  subReject(fn()=>padTimeSeries('rolling',$dup));subNear(padTimeSeries('resample',$dup,['duplicates'=>'mean'])['rows'][0]['value'],2);
  subNear(padTimeSeries('resample',$dup,['duplicates'=>'last'])['rows'][0]['value'],3);
  $typed=[['time'=>0,'value'=>1,'group'=>1],['time'=>0,'value'=>2,'group'=>'1']];subCheck(count(padTimeSeries('ewma',$typed,['series'=>'group'])['rows'])===2);
  $fold=[['time'=>'2026-10-25T02:30:00+02:00','value'=>10],['time'=>'2026-10-25T02:30:00+01:00','value'=>20]];
  $r=padTimeSeries('resample',$fold,['timezone'=>'Europe/Amsterdam']);subCheck($r['rows'][0]['time']==='2026-10-25T02:00:00+02:00'&&$r['rows'][1]['time']==='2026-10-25T02:00:00+01:00');
  $huge=[['time'=>0,'value'=>1e308],['time'=>1,'value'=>1e308]];subNear(padTimeSeries('resample',$huge)['rows'][0]['value'],1e308);
  foreach([
    fn()=>padTimeSeries('resample',[['time'=>0,'value'=>'4']]),fn()=>padTimeSeries('rolling',$gaps,['missing'=>'error']),
    fn()=>padTimeSeries('resample',[['time'=>0,'value'=>1],['time'=>1000000,'value'=>2]],['stepSeconds'=>1]),
    fn()=>padTimeSeries('resample',$fold,['stepSeconds'=>.5]),fn()=>padTimeSeries('resample',$huge,['aggregate'=>'sum']),
    fn()=>padTimeSeries('ewma',[],['alpha'=>0]),fn()=>padTimeSeries('rolling',[],['window'=>0]),fn()=>padTimeSeries('difference',[],['lag'=>1.5]),
    fn()=>padTimeSeries('resample',[],['unknown'=>1]),fn()=>padTimeSeries('resample',[['time'=>'2026-02-30T00:00Z','value'=>1]])
  ] as $bad)subReject($bad);
  echo "timeseries: irregular buckets, windows, missing/duplicate policies, rate, typed groups, DST and bounds passed\n";
?>
