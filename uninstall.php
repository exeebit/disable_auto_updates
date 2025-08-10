<?php

/**
 *
 *
 *@package dau
 *
 */

defined("WP_UNINSTALL_PLUGIN") or die();

array_map('unlink', glob(WP_CONTENT_DIR . "/uploads/disable-auto-updates/*"));
unlink(WP_CONTENT_DIR . "/uploads/disable-auto-updates");
