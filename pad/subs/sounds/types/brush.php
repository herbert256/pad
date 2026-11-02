<?php
// brush - reproducible soft noise swept down through a band-pass filter.
return ['title'=>'Brush','level'=>0.3,'seed'=>7321,'voices'=>[
 ['wave'=>'noise','length'=>0.32,'attack'=>0.045,'decay'=>0.2,'sustain'=>0.08,'release'=>0.075,'gain'=>1,'filter'=>['bandpass',3200,800,0.6]]
]];
