<?php

class WPFedoraActivate
{
  public static function activate() {
    flush_rewrite_rules();
  }
}