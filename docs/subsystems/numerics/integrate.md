# numerics: integrate

Sampled trapezoidal integration with compensated summation over strictly increasing [x,y] samples. It is exact for the supplied piecewise-linear interpolant; errorEstimate is null because curvature between samples is unknown. No function evaluation or adaptive quadrature.

`padNumeric('integrate',[[0,0],[1,2],[2,4]],['method'=>'trapezoid'])`
