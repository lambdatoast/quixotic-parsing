<?php

final class Bad extends Result {
  var $value;

  function __construct(ParserError $value) {
    $this->value = $value;
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
    return $bad($this->value);
  }

  public function equal($x) {
    return $x->fold(
      function ($v) { return $v->equal($this->value); },
      function () { return false; }
    );
  }

  public function getCharsConsumed() {
    return new None;
  }

  public function mapError(callable $f) {
    return new Bad($f($this->value));
  }

}
