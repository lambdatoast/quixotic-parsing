<?php

final class Good extends Result {
  var $value;
  var $chars_consumed;
  function __construct($value, $chars_consumed) {
    $this->value = $value;
    $this->chars_consumed = $chars_consumed;
  }
  function chain(callable $f) {
    $r = $f($this->value, $this->chars_consumed);
    return $r->set_chars_consumed(
      $this->chars_consumed > $r->chars_consumed ? $this->chars_consumed 
                                                 : $r->chars_consumed
    );
  }
  public function lazy_or_(callable $fb) {
    return $this;
  }
  public function set_chars_consumed($n) {
    return new Good($this->value, $n);
  }
  protected function isGood() {
    return true;
  }
  protected function isBad() {
    return false;
  }
  public function equal($x) {
    return $x->isBad() ? false
                       : ($x->value === $this->value) && 
                         ($x->chars_consumed === $this->chars_consumed);
  }
}

