<?php

class Committed extends CommitStatus {
  function fold(callable $uncommitted, callable $committed) {
    return $committed();
  }
}
