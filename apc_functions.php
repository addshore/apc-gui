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


  function BeginPage() {
    echo("<HTML>");
    echo("<HEAD>");
    echo("<TITLE></TITLE>");
    echo("</HEAD>");
    echo("<BODY BGCOLOR=#FFFFFF>");
  }

  function EndPage() {
    echo("</BODY>");
    echo("</HTML>");
    exit;
  }

  function apc_sort_scripts_by_hits_desc($a, $b) {
    return ($b[3] - $a[3]);
  }
  function apc_sort_scripts_by_hits_asc($a, $b) {
    return ($a[3] - $b[3]);
  }

  function apc_sort_scripts_by_size_desc($a, $b) {
    return ($b[1] - $a[1]);
  }
  function apc_sort_scripts_by_size_asc($a, $b) {
    return ($a[1] - $b[1]);
  }

  function apc_sort_scripts_by_lastaccess_desc($a, $b) {
    return ($b[2] - $a[2]);
  } 
  function apc_sort_scripts_by_lastaccess_asc($a, $b) {
    return ($a[2] - $b[2]);
  }

  function apc_sort_scripts_by_lastmodified_desc($a, $b) {
    return ($b[5] - $a[5]);
  } 
  function apc_sort_scripts_by_lastmodified_asc($a, $b) {
    return ($a[5] - $b[5]);
  }

  function DateStr($t) {
    return date("d.m.y H:i:s", $t);
  }

  function apc_restart_host($host) {
    if ($host == "localhost") {
      apc_reset_cache();
      return true;
    }
    if (strncmp($host, "http://", 7)) {
      return false;
    }
    $fp = @fopen($host."?type=restart", "r");
    if ($fp) {
      fclose($fp);
      return true;
    }
    return false;
  }
 
  function apc_delete($host, $script)
  {
    if (strlen($script) == 0 || strlen($host) == 0)
      return false;
    
    if ($host == "localhost") {
      if (!apc_rm($script))
        return false;
      
      return true;
    }
    
    if (strncmp($host, "http://", 7)) 
      return false;

    $fp = fopen($host."?type=delete&script=".$script, "r");
    if ($fp) {
      $buf = "";
      $buf = fgets($fp, 10);
      fclose($fp);
      
      if ($buf != "DONE") 
        return false;

      return true;
    }
    return false;
  }

  function apc_get_object_info($host, $script) 
  {
    $arr = array();
    if ($host == "localhost") {
      apc_object_info($script, &$arr);
      return $arr;
    }

    if (strncmp($host, "http://", 7)) {
      return false;
    }
    $fp = @fopen($host."?type=objinfo&script=".Urlencode($script), "r");
    if ($fp) {
      $buf = "";
      while(!feof($fp)) {
        $buf .= @fgets($fp, 10*1024);
      }
      fclose($fp);
      $arr = unserialize($buf);
      if (is_array($arr) && count($arr) > 0)
        return $arr;
    }
    return false;
  }

  function apc_get_cache_index($host) {
    $arr = array();
    if ($host == "localhost") {
      apc_cache_index($arr);
      return $arr;
    }
    if (strncmp($host, "http://", 7)) {
      return false;
    }
    $fp = @fopen($host."?type=index", "r");
    if ($fp) {
      $buf = "";
      while(!feof($fp)) {
        $buf .= @fgets($fp, 10*1024);
      }
      fclose($fp);
      $arr = unserialize($buf);
      if (is_array($arr) && count($arr) > 0)
        return $arr;
    }
    return false;
  }

  function apc_get_cache_info($host) {
    $arr = array();
    if ($host == "localhost") {
      global $SERVER_SOFTWARE;
      apc_cache_info($arr);
      if ($SERVER_SOFTWARE == "")
        $SERVER_SOFTWARE = getenv("SERVER_SOFTWARE");

      $arr["SERVER_SOFTWARE"] = $SERVER_SOFTWARE;
      return $arr;
    }
    if (strncmp($host, "http://", 7)) {
      return false;
    }
    $fp = @fopen($host, "r");
    if ($fp) {
      $buf = @fgets($fp, 5*1024);
      fclose($fp);
      $arr = unserialize($buf);

      if (is_array($arr) && count($arr) > 0)
        return $arr;
    }
    return false;
  }

  function MemStr($mem) {
    if ($mem/1024/1024 > 1)
      return sprintf("%.2fMB", $mem/1024/1024);
    if ($mem/1024 > 1)
      return sprintf("%.2fkB", $mem/1024);
    return $mem;
  }
?>
