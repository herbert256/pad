# signals: convolve

Input `{signal:[...],kernel:[...]}`. Linear convolution with zero padding outside inputs and kernel origin zero. full returns N+M−1 values. same extracts N values starting at floor((M−1)/2); valid requires N≥M and extracts N−M+1 values starting at M−1. firstIndex identifies the slice. sampleRate is optional metadata.

`padSignal('convolve',['signal'=>[1,2,3],'kernel'=>[.5,.5]],['mode'=>'full'])`
