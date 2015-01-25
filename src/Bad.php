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
  public function set_chars_consumed($n) {
    return $this;
  }
  protected function isGood() {
    return false;
  }
  protected function isBad() {
    return true;
  }
  public function equal($x) {
    return $x->isBad() ? $x->value->equal($this->value)
                       : false;
  }
}
