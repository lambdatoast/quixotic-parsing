<?php

class Uncommitted extends CommitStatus {
  function fold(callable $uncommitted, callable $committed) {
    return $uncommitted();
  }
}
