# Sequence: the prediction store

## Introduction

The answers of the Sequence suite: one `.txt` per page of the `sequence` application,
mirroring its page paths - `basic/random.txt` predicts what `sequence/?basic/random`
answers. The pages stay in their own application; this store holds nothing but the
predictions. A page that draws answers a `/pattern/` pinning its skeleton - the headings
and prose around the numbers - and every deterministic page an exact body.

The suite is driven from `regression/main`, one entry along from Regression, and runs its
pages one request at a time.
