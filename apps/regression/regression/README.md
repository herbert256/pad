# Regression: the prediction store

## Introduction

The answers of the Regression suite: one `.txt` per page of the self-testing applications
and of the runner itself, mirroring application and page - `cache_apcu/probe.txt` predicts what
`regression/cache_apcu/?probe` answers. The pages stay in their own applications; this
store holds nothing but the predictions, written by hand like every suite answer. A
prediction is an exact body, an `HTTP` code with an optional `/pattern/` over the dump on a
second line, or a lone `/pattern/` for a page that draws.

The suite is driven from `regression/main`, one entry along from Framework, and runs its
pages one request at a time - the isolation the self-testers need, since each proves its
subsystem by fetching its own probe from inside the request. The runner's own pages -
the overviews, the scan, show - are covered by structural `/pattern/` predictions: they
render the previous run's totals and stamps, so an exact body or a health word would
fail on every legitimate change, while the structure is what proves them alive.
