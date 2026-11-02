# Statistical sampling

`padSample('normal', 100, ['mean' => 0, 'sd' => 1, 'seed' => 7])` returns 100 numbers. Six helper-only kinds: uniform (min/max), normal (mean/sd), bernoulli (p), binomial (n/p), poisson (lambda), exponential (rate). Each definition declares parameters, support and sampler; no CDF/density/quantile capability is advertised.

An optional seed isolates and restores PAD's fake xorshift32 stream; omitting it consumes that stream. Alternatively provide `random => fn() => $stream->next()` returning finite values in [0,1); seed and random are mutually exclusive. No cryptographic promise. Box–Muller produces normal values, direct Bernoulli trials binomial, sums of bounded Knuth chunks Poisson, inverse transform exponential. The injected stream is expected to be independent and uniform. Finite stream resolution limits tails, as in all finite-precision simulation.

Count must be an integer from 0 to 100000; binomial n is at most 10000, Poisson lambda at most 1000. A batch exceeding ten million estimated draws is rejected. Each Poisson sample aborts after 10000 iterations; a pathological stream therefore cannot hang. Numbers outside documented finite parameter bounds, invalid options and non-finite results call padError and return an empty array if the handler continues. Bounds are available in each type's parameters table. Samples preserve integer types for discrete distributions.

Example: `$counts = padSample('poisson', 500, ['lambda' => 4, 'seed' => 42]);` for a reproducible histogram. Run `php tests/subsystems/distributions.php`; fixed-seed 10000-sample means use absolute tolerance .04, the lambda=1000 mean tolerance 4 over 1000 samples. These are regression checks, not a statistical certification.
