<?php

interface TextParsing {

  /**
   * @param string $a Should be a single character.
   * @return Combinator
   */
  static function char($c);

  /**
   * @return Combinator
   */
  static function anyChar();

  /**
   * @return Combinator
   */
  static function anyDigit();

  /**
   * @return Combinator
   */
  static function alphaNum();

  /**
   * @param string $s
   * @return Combinator
   */
  static function str($s);

  /**
   * @param string $regex
   * @return Combinator
   */
  static function regex($pattern);

  /**
   * @param callable $f
   * @return Combinator
   */
  static function satisfyChar(callable $f);

  /**
   * @param callable $f
   * @return Combinator
   */
  static function satisfyStr(callable $f);

}
