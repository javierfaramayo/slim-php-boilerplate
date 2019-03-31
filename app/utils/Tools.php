<?php
namespace App\Utils;

class Tools
{
  public static function cleanString($string)
  {
    $cleaned = preg_replace('([^A-Za-z0-9])', '', $string);
    $lowered = strtolower($cleaned);

    return $lowered;
  }

  public static function sanitize( $string = null )
  {
    $non_permitted = ["'", '"', '<script>', '</script>', 'SELECT', 'INSERT', 'DELETE', 'UPDATE'];
    return str_replace($non_permitted, '', $string);
  }
}