<?php

interface TextParsing {

  /**
   * @param string $a Should be a single character.
   * @return Parsing
   */
  static function char($c);

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

}
