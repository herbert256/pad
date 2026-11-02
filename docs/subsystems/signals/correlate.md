# signals: correlate

Input `{a:[...],b:[...]}`. r[k]=sum_i a[i]×b[i+k], lags −(N−1)..M−1, optionally restricted by maxLag. Positive lag means b is delayed. No implicit mean removal. normalization none (default), biased (divide by max(N,M)), unbiased (divide by overlap count), coefficient (global Euclidean norms). Zero-energy coefficient input returns degenerate. Coefficient scaling is normalized before multiplication to limit overflow. With sampleRate, lagSeconds is reported.

`padSignal('correlate',['a'=>[1,0,0],'b'=>[0,0,1]],['sampleRate'=>10,'normalization'=>'none'])`
