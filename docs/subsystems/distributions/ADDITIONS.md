# Additional samples

`padSample('triangular', count, ['min'=>0,'mode'=>.5,'max'=>1])`: min < max,
mode inclusive between endpoints; each within +/-1e150. Inverse-transform, one draw.
`padSample('lognormal', count, ['mu'=>0,'sigma'=>1])`: natural-log mean mu in
[-700,700], log standard deviation sigma in [0,100]. Two Box-Muller draws; any
underflow to zero or overflow rejects the whole batch explicitly.
`padSample('beta', count, ['alpha'=>1,'beta'=>1])`: shapes [.01,10000]. Two
Marsaglia-Tsang gamma samples, shape<1 transform in log space; 128 rejection
attempts per gamma. At most 770 draws per output, conservatively charged against
the existing ten-million work budget (so at most 12987 beta samples per call).
Pathological injected streams fail when that rejection budget expires. Finite
floating arithmetic may round extreme beta tails to 0 or 1.

All preserve injected streams, seeded repeatability and seed-state restoration,
including failures. These are sampling helpers, not density/CDF/quantile functions.
