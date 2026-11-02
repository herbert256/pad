# Sampled-signal analysis

`padSignal(kind,input,options)` supplies spectrum, convolve, correlate and window as numerical data. Native finite sample arrays only, lengths 1..4096 (spectrum≤1024). No recording, media decoding or browser microphone access. maxWork defaults 1000000, capped at 10000000; each sample validation, coefficient and multiply-accumulate consumes work. Exceeding work returns status=limit and empty values, never a successful partial signal. Invalid shapes/options or nonfinite arithmetic raise PAD errors.

Spectrum requires an explicit sample rate; array-index operations can carry optional sampleRate metadata. Edge, window and scaling policies are returned with results. Runnable examples demonstrate a known two-Hz sinusoid, impulse lag and smoothing kernel; they can feed ordinary charts. This family is distinct from notation:signals maritime flags. Independent fixture: `php tests/subsystems/expansion-signals.php`.

## Inputs, options and outputs

All calls accept `maxWork` (integer 1..10000000, default 1000000). Every result includes `status`, `work`, `maxWork`. Samples must be native finite numbers. A supplied `sampleRate` must be positive and at most 1e12 Hz.

| Kind | Input | Kind-specific options | Result fields |
| --- | --- | --- | --- |
| window | sample list | `window='hann'|'rectangular'|'hamming'|'blackman'`; `periodic=false`; optional `sampleRate` | `values`, `coefficients`, `coherentGain`, `meanSquareGain`, window/periodic/rate metadata |
| spectrum | 1..1024 samples | required `sampleRate`; window and periodic as above; `scaling='amplitude'|'power'` | `bins:[{bin,frequency,value,phase},...]`, sample count, `frequencyResolution`, coherent gain, method/window/periodic/scaling/rate/assumption metadata |
| convolve | `['signal'=>[...],'kernel'=>[...]]` | `mode='full'|'same'|'valid'`; optional `sampleRate` | `values`, `firstIndex`, `kernelOrigin:0`, zero-padding policy, mode/rate metadata |
| correlate | `['a'=>[...],'b'=>[...]]` | `normalization='none'|'biased'|'unbiased'|'coefficient'`; `maxLag= max(N,M)-1` integer 0..4095; optional `sampleRate` | `values`, `lags` in samples, `lagSeconds` (null without rate), `overlapCounts`, normalization/rate/definition metadata |

Spectrum bins use the negative-exponent DFT of windowed samples, frequency `k*sampleRate/N`, from DC to floor(N/2). Amplitude is `abs(DFT)/sum(window)`, doubled except DC and the even-length Nyquist bin. Power is `abs(DFT)^2/(N*sum(window^2))`, doubled at the same bins; this is power per bin, **not** power density per Hz. For a rectangular window, summed powers equal input mean-square value. Uniform sampling is assumed; frequencies above Nyquist alias. Symmetric windows divide their angle by N−1, periodic windows by N; one-sample windows use coefficient 1. Zero-gain windows cannot form a spectrum.

Convolution is linear with zeros outside both arrays. Full length is N+M−1. Same keeps N values starting at floor((M−1)/2); valid requires N≥M and keeps N−M+1 values starting at M−1. Correlation is `r[k]=sum_i a[i]*b[i+k]`, so positive lag means **b is delayed relative to a**. There is no implicit mean subtraction. Biased divides by max(N,M); unbiased divides by overlap count; coefficient divides by the full-array Euclidean norms and reports `degenerate` for zero energy.

```php
$bins = padSignal('spectrum', [0,1,0,-1,0,1,0,-1],
  ['sampleRate'=>8,'window'=>'rectangular','scaling'=>'amplitude']); // amplitude 1 at 2 Hz
```
