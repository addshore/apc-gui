<?php
/*
APC GUI
Copyright (C) 2001 Metropolis AG, Tuebingen, Germany

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.

*/
  if ($type == "restart") {
    apc_reset_cache();
    echo("DONE");
    exit;
  }
  if ($type == "index") {
    $arr = array();
    apc_cache_index($arr);
    echo(serialize($arr));
    exit;
  }
  if ($type == "info" && strlen($script) > 0) {
    apc_dump_cache_object($script);
    exit;
  } 
  if ($type == "delete" && strlen($script) > 0) {
    if (apc_rm($script)) {
      echo("DONE");
    } else {
      echo("FAILED");
    }
    exit;
  }
  if ($type == "objinfo" && strlen($script) > 0) {
    $arr = array();
    apc_object_info($script, &$arr);
    echo(serialize($arr));
    exit;
  }
  else {
    $arr = array();
    apc_cache_info($arr);

    if ( $SERVER_SOFTWARE == "" )
      $SERVER_SOFTWARE = getenv("SERVER_SOFTWARE");
    $arr["SERVER_SOFTWARE"] = $SERVER_SOFTWARE;

    echo(serialize($arr));
  }
?>
