# signals: window

Multiply samples by rectangular, Hann (default), Hamming or Blackman coefficients. Symmetric windows default to denominator N−1; periodic=true uses N. Length-one windows use coefficient 1. Returns coefficients, coherent gain and mean-square gain. sampleRate is optional metadata.

`padSignal('window',[1,1,1,1,1],['window'=>'hann'])`
