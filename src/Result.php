<?php

abstract class Result implements Chain, Equal {
  abstract public function lazy_or_(callable $fb);
  abstract public function set_chars_consumed($n);
  abstract protected function isGood();
  abstract protected function isBad();

}

