# numerics: root

Bisection of a continuous caller-supplied trusted PHP callable with lower<upper. Same-sign endpoints yield unbracketed. Endpoint roots accepted. Stops on absolute/relative bracket half-width or explicit functionTolerance; residual is reported independently. A discontinuous function violates the method assumption. tolerance defaults 1e-12, relativeTolerance 1e-12, functionTolerance 0, maxIterations 200. Iteration/work exhaustion reports limit without success, unrepresentable midpoint reports precision-limit.

`padNumeric('root',['function'=>fn($x)=>$x*$x-2,'lower'=>0,'upper'=>2],['tolerance'=>1e-10])`
