<?php

final class Bad extends Result {
  private $value;
  private $commit_status;

  function __construct(ParserError $value, CommitStatus $s) {
    $this->value = $value;
    $this->commit_status = $s;
  }

  function chain(callable $f) {
    return $this;
  }

  public function lazy_or_(callable $fb) {
    return $fb();
  }

  public function setCharsConsumed($n) {
    return $this;
  }

  public function fold(callable $bad, callable $_) {
    return $bad($this->value, $this->commit_status);
  }

  public function equal($x) {
    return $x->fold(
      function (ParserError $v, CommitStatus $c) { return $v->equal($this->value) && $c === $this->commit_status; },
      function () { return false; }
    );
  }

  public function getCharsConsumed() {
    return new None;
  }

  public function mapError(callable $f) {
    return new Bad($f($this->value), $this->commit_status);
  }

}
