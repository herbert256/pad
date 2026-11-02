# Bounded numerical methods

`padNumeric(kind,input,options)` provides interpolate, integrate, root and linear-system. Data results include status, method/iterations and work. Native finite inputs only; samples 2..10000, query lists≤10000, dense systems≤128. `maxWork` defaults 1000000 and is capped at 10000000. Exceeding work returns status=limit, value=null; invalid shapes and nonfinite arithmetic raise PAD errors. Trusted PHP callables are supported only by root; user text is never evaluated. IEEE-754 arithmetic cannot guarantee arbitrary precision.

The numeric tag examples demonstrate an interpolation/integration calculator, a square-root solver and a two-equation calculator. These are helper-based examples, not new expression/tag evaluation semantics. Independent fixture: `php tests/subsystems/expansion-numerics.php`.

## Inputs, options and outputs

Every call has optional `maxWork` (integer 1..10000000, default 1000000). Every returned record contains `status`, `work`, `maxWork`; successful methods also return `iterations`. Shapes below use native numbers, never numeric strings.

| Kind | Input | Kind-specific options | Result fields |
| --- | --- | --- | --- |
| interpolate | `[[x0,y0],[x1,y1],...]`, x strictly increasing | required `at`: number or number list; `method='linear'`; `extrapolate='error'|'clamp'|'linear'` | `values:[{x,y},...]`, `domain:[firstX,lastX]`, `method`, `extrapolate`, `tolerance:null` |
| integrate | same sorted sample pairs | `method='trapezoid'` | `value`: integral, `domain`, `method`, `tolerance:null`, `errorEstimate:null` |
| root | `['function'=>callable,'lower'=>number,'upper'=>number]` | `tolerance=1e-12` in (0,1]; `relativeTolerance=1e-12` in [0,1]; `functionTolerance=0` nonnegative; `maxIterations=200` integer 1..10000 | `value`: root estimate or null, `residual`: absolute function value or null, `bracket:[lower,upper]`, both x tolerances, `method`, `iterations` |
| linear-system | `['matrix'=>[[...],...],'rhs'=>[...]]`, square 1..128 | `tolerance=1e-12` in (0,1] | `value`: solution vector or null, absolute infinity-norm `residual`, `relativeResidual`, `minimumScaledPivot`, `method`, `tolerance` |

Root solving requires a continuous function on the bracket and opposite endpoint signs (or an endpoint root). Bisection succeeds when bracket half-width is at most `tolerance + relativeTolerance*abs(midpoint)` **or** the absolute residual is at most `functionTolerance`. Small x uncertainty does not promise a small function residual: the two are reported separately. Same-sign endpoints yield `unbracketed`; iteration/work exhaustion yields `limit`; floating-point stagnation yields `precision-limit`. Work-limit results do not expose partially computed solutions.

Linear-system `singular` and `ill-conditioned` statuses return null solutions. The latter uses scaled pivots as a rejection heuristic, not a measured condition number. A completed solve whose normalized residual exceeds tolerance reports `not-converged`. Integration only describes the piecewise-linear interpolant and cannot infer error against unknown values between samples.

```php
$area = padNumeric('integrate', [[0,0],[1,2],[2,4]]); // value = 4
$root = padNumeric('root', ['function'=>fn($x)=>$x*$x-2,'lower'=>0,'upper'=>2]);
```
