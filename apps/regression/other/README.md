# Other: the prediction store

## Introduction

The answers of the Other suite: one `.txt` per page of every application without a suite
of its own, mirroring application and page - `demo/clock.txt` predicts what
`demo/?clock` answers. The pages stay in their own applications; this store holds nothing
but the predictions. A page that draws - the clock, the counter - answers a `/pattern/`
pinning its skeleton, a page carrying `{ajax}` ids a pattern with the ids masked, and
everything else an exact body. A new application lands in the suite as `new` until its
predictions are written here.
