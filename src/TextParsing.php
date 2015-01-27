<?php

interface TextParsing {

  /**
   * @param string $a Should be a single character.
   * @return Parsing
   */
  static function char($c);

  /**
   * @return Parsing
   */
  static function anyChar();

  /**
   * @return Parsing
   */
  static function anyDigit();

  /**
   * @param string $s
   * @return Parsing
   */
  static function str($s);

  /**
   * @param string $regex
   * @return Parsing
   */
  static function regex($pattern);

  /**
   * @param callable $f
   * @return Parsing
   */
  static function satisfyChar(callable $f);

  /**
   * @param callable $f
   * @return Parsing
   */
  static function satisfyStr(callable $f);

}
