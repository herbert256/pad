# numerics: linear-system

Dense 1..128 square system solved using scaled partial pivoting and checked against original coefficients. Zero pivot reports singular; relative pivot below tolerance reports ill-conditioned, with no solution. This is a pivot heuristic, not a condition-number estimate. Returns absolute infinity-norm residual, normalized residual and minimumScaledPivot; excessive residual reports not-converged. tolerance defaults 1e-12. Nonfinite intermediate arithmetic is rejected.

`padNumeric('linear-system',['matrix'=>[[2,1],[1,3]],'rhs'=>[5,7]])`
