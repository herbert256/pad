# distributions: weibull

Inverse CDF scale × (-log(1-U))^(1/shape). Shape 0.01..10000; scale 1e-150..1e150. No shift parameter. Zero uniform yields zero. Underflow yields zero and overflow fails through the shared sampler. One draw per sample; seed replay is isolated.

Runnable example: `apps/tags/examples/distribution/weibull.pad`. Independent fixture: `php tests/subsystems/expansion-distributions-weibull.php`.
