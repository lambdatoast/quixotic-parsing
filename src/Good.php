<?php

final class Good extends Result {

  var $value;
  var $chars_consumed;

  function __construct($value, $chars_consumed) {
    $this->value = $value;
    $this->chars_consumed = $chars_consumed;
  }

  function chain(callable $f) {
    return $f($this->value, $this->chars_consumed);
  }

  public function lazy_or_(callable $fb) {
    return $this;
  }

  public function setCharsConsumed($n) {
    return new Good($this->value, $n);
  }

  public function fold(callable $_, callable $good) {
    return $good($this->value, $this->chars_consumed);
  }

  public function equal($x) {
    return $x->fold(
      function () { return false; },
      function ($v, $chars_consumed) { 
        return ($v === $this->value) && 
               ($chars_consumed === $this->chars_consumed); }
    );
  }

  public function getCharsConsumed() {
    return new Some($this->chars_consumed);
  }

  public function mapError(callable $_) {
    return $this;
  }

}
