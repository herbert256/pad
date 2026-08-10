# Manual: the prediction store

## Introduction

The answers of the Manual suite: one `.txt` per page of the `manual` application,
mirroring its page paths - `pages/callback.txt` predicts what `manual/?pages/callback`
answers. The pages stay in their own application; this store holds nothing but the
predictions. A page that draws answers a `/pattern/` pinning its skeleton, everything
else an exact body.

The suite is driven from `regression/main`, one entry along from Sequence, and runs its
pages one request at a time.
