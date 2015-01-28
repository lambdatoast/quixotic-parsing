<?php

abstract class Result implements Chain, Equal {

  abstract public function lazy_or_(callable $fb);
  abstract public function setCharsConsumed($n);
  abstract protected function isGood();
  abstract protected function isBad();

  /**
   * @return Maybe Either `Some($chars_consumed)` or `None`.
   */
  abstract public function getCharsConsumed();

  /**
   * @param callable $f Function from ParserError to ParserError.
   * @return Result
   */
  abstract public function mapError(callable $f);

}

