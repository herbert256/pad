# signals: spectrum

One-sided direct DFT for 1..1024 real samples with required sampleRate in Hz. Window defaults Hann symmetric; rectangular is explicit. scaling=amplitude divides by sum(window), doubling non-DC/non-Nyquist amplitudes. scaling=power divides squared magnitude by N×sum(window²), doubling those same bins; rectangular-bin powers sum to input mean square. It is power per bin, not density per Hz. Phase uses the negative-exponent DFT. Returns frequency k×sampleRate/N. Uniform sampling is assumed; above-Nyquist content aliases. Zero-gain short windows fail.

`padSignal('spectrum',[0,1,0,-1,0,1,0,-1],['sampleRate'=>8,'window'=>'rectangular','scaling'=>'amplitude'])`
