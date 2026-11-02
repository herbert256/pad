# numerics: interpolate

Piecewise linear interpolation of strictly increasing [x,y] samples. at is a native finite number or list. extrapolate is error (default), clamp, or linear. Binary-search alignment; no sorting/coercion. Returns x/y records and domain. No statistical uncertainty estimate.

`padNumeric('interpolate',[[0,0],[2,4],[5,10]],['at'=>[1,3]])`
