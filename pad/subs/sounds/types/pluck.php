<?php
// pluck - a short decaying fundamental and harmonics; no feedback or long audio loop.
return ['title'=>'Pluck','level'=>0.45,'seed'=>1729,'voices'=>[
 ['wave'=>'sine','note'=>220,'length'=>0.45,'attack'=>0.002,'decay'=>0.42,'sustain'=>0,'release'=>0.028,'gain'=>0.7],
 ['wave'=>'sine','note'=>440,'length'=>0.22,'attack'=>0.001,'decay'=>0.21,'sustain'=>0,'release'=>0.009,'gain'=>0.22],
 ['wave'=>'sine','note'=>660,'length'=>0.13,'attack'=>0.001,'decay'=>0.12,'sustain'=>0,'release'=>0.009,'gain'=>0.1],
 ['wave'=>'noise','length'=>0.012,'attack'=>0.001,'decay'=>0.01,'sustain'=>0,'release'=>0.001,'gain'=>0.05,'filter'=>['lowpass',1800,800,0.7]]
]];
