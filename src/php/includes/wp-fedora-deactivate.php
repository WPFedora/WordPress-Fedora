<?php

class WPFedoraDeactivate
{
  public static function deactivate() {
    flush_rewrite_rules();
  }
}