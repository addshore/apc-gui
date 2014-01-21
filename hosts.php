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

  include("config.php");
  include("apc_functions.php");

  // RTSRow: Displays a row in the runtime setting table
  function RTSRow($left, $right) {
    global $rts_row;
    if ($rts_row++%2)
      $color = "#D88600";
    else
      $color = "#E19E00";
    echo("<TR bgcolor=".$color."><TD>$left</TD><TD BGCOLOR=#AE5000><IMG SRC=pics/shim.gif WIDTH=1 HEIGHT=25></TD>");
    echo("<TD ALIGN=left>".$right."&nbsp;</TD></TR>");
  }

  if (function_exists("apc_cache_info") == false) {
    BeginPage();
    echo("APC NOT RUNNING");
    EndPage();
  }

  if (isset($restart) && isset($host)) {
    apc_restart_host($apc_hosts[$host]);
    $host = "";
  }
  if (isset($host) && strlen($host) > 0) {
    BeginPage();
    include("navi.php");

    $arr = apc_get_cache_info($apc_hosts[$host]);
    if ($arr == false) {
      echo(" FAILED TO COLLECT DATA<P>");
      EndPage();
    }
    echo("<TABLE BORDER=0 CELLPADDING=5 CELLSPACING=0>");
    echo("<TR><TD VALIGN=top ALIGN=left>");

    echo("<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0 WIDTH=516>");
    echo("<TR BGCOLOR=#6c6b6b><TD COLSPAN=3 ALIGN=left VALIGN=top><IMG SRC=pics/table_tit_cacheinfo.gif></TD></TR>");

    echo("<TR bgcolor=#E19E00><TD>APC Host</TD>"); 
    echo("<TD BGCOLOR=#AE5000><IMG SRC=pics/shim.gif WIDTH=1 HEIGHT=25></TD>");
    echo("<TD>".$host."</TD></TR>");
    echo("<TR bgcolor=#D88600><TD>APC Version</TD><TD BGCOLOR=#AE5000><IMG SRC=pics/shim.gif WIDTH=1 HEIGHT=25></TD><TD>".$arr["version"]."</TD></TR>");

    // echo("<TR><TD>Magic Number</TD><TD>".$arr["magic"]."</TD></TR>");
    echo("<TR bgcolor=#E19E00><TD>Hits</TD><TD BGCOLOR=#AE5000><IMG SRC=pics/shim.gif WIDTH=1 HEIGHT=25></td><TD>".$arr["hits"]."</TD></TR>");

    echo("<TR bgcolor=#D88600><TD>Misses</TD><TD BGCOLOR=#AE5000><IMG SRC=pics/shim.gif WIDTH=1 HEIGHT=25></TD><TD>".$arr["misses"]."</TD></TR>");
    echo("<TR bgcolor=#E19E00><TD>Shared Memory ID</TD><TD BGCOLOR=#AE5000><IMG SRC=pics/shim.gif WIDTH=1 HEIGHT=25></TD><TD>".$arr["shared memory ID"]."</TD></TR>");
    echo("<TR bgcolor=#D88600><TD>Local shared memory address</TD><TD BGCOLOR=#AE5000><IMG SRC=pics/shim.gif WIDTH=1 HEIGHT=25></TD><TD>".$arr["local shared memory address"]."</TD></TR>");
    echo("<TR bgcolor=#E19E00><TD><IMG SRC=pics/shim.gif HEIGHT=1 WIDTH=315></TD><TD BGCOLOR=#AE5000><IMG SRC=pics/shim.gif WIDTH=1 HEIGHT=25></TD><TD><IMG SRC=pics/shim.gif HEIGHT=1 WIDTH=200></TD></TR>");
    echo("</TABLE>");

    echo("</TD><TD ROWSPAN=2 VALIGN=top ALIGN=right>");

    echo("<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0>");
    echo("<TR BGCOLOR=#6c6b6b><TD COLSPAN=3 VALIGN=top><IMG SRC=pics/runtimeset_titel.gif></TD></TR>");

    RTSRow("apc.mode"                 , $arr["mode"]);
    RTSRow("apc.cachedir"             , $arr["cache directory"]);
    RTSRow("apc.regex"                , $arr["cache filter"]);
    RTSRow("apc.hash_buckets"         , $arr["total buckets"]);
    RTSRow("apc.shm_segments"         , $arr["maximum shared memory segments"]);
    RTSRow("apc.shm_segment_size"     , $arr["shared memory segment size"]);
    RTSRow("apc.ttl"                  , $arr["time-to-live"]);
    RTSRow("apc.check_mtime"          , $arr["check file modification times"]);
    RTSRow("apc.relative_includes"    , $arr["support relative includes"]);
    RTSRow("apc.check_compiled_source", $arr["check for compiled source"]);

    echo("</TABLE>");

    echo("</TD></TR>");

    echo("<TR><TD>");

    echo("<TABLE cellspacing=0 cellpadding=0 BORDER=0>");
    echo("<tr><td colspan=2><img src=pics/diagramm_tit.gif border=0></td></tr>");
    echo("<TR>");
    echo("<TD><IMG SRC=chart.php?chart=mem&free=".$arr["total available"]."&total=".$arr["total size"]."></TD>");
    echo("<TD><IMG SRC=chart.php?chart=hits&hits=".$arr["hits"]."&misses=".$arr["misses"]."></TD>");
    echo("</TR>");
    echo("</TABLE><P>");


    echo("</TD></TR></TABLE>");

    EndPage();
  }

  BeginPage();
  include("navi.php");

  if ($refresh_hosts) {
    echo("<META HTTP-EQUIV=Refresh Content=".$refresh_hosts.">\n<Font Size=2>(This page is self refreshing after $refresh_hosts seconds)</FONT><P>");
  }
  reset($apc_hosts);
  echo("<TABLE ALIGN=center BORDER=0 CELLPADDING=0 CELLSPACING=0 WIDTH=90%>");
  echo("<TR BGCOLOR=#6c6b6b>");
  echo("<TH ALIGN=left VALIGN=top><TABLE BORDER=0 WIDTH=100% CELLPADDING=0 CELLSPACING=0><TR><TD ALIGN=left VALIGN=top><IMG SRC=pics/table_l_roundgrey.gif></TD><TD><IMG SRC=pics/table_tit_hosts.gif></TD></TR></TABLE></TH>");
  echo("<TD BGCOLOR=#000000><IMG SRC=pics/shim.gif WIDTH=1 HEIGHT=25></TD>");
  echo("<TH>&nbsp;&nbsp;&nbsp;<IMG SRC=pics/table_tit_version.gif>&nbsp;&nbsp;&nbsp;</TH>");
  echo("<TD BGCOLOR=#000000><IMG SRC=pics/shim.gif WIDTH=1 HEIGHT=25></TD>");
  echo("<TH>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<IMG SRC=pics/table_tit_hits.gif>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</TH>");
  echo("<TD BGCOLOR=#000000><IMG SRC=pics/shim.gif WIDTH=1 HEIGHT=25></TD>");
  echo("<TH>&nbsp;&nbsp;&nbsp;<IMG SRC=pics/table_tit_misses.gif>&nbsp;&nbsp;&nbsp;</TH>");
  echo("<TD BGCOLOR=#000000><IMG SRC=pics/shim.gif WIDTH=1 HEIGHT=25></TD>");
  echo("<TH>&nbsp;&nbsp;&nbsp;<IMG SRC=pics/table_tit_hitrate.gif>&nbsp;&nbsp;&nbsp;</TH>");
  echo("<TD BGCOLOR=#000000><IMG SRC=pics/shim.gif WIDTH=1 HEIGHT=25></TD>");
  echo("<TH>&nbsp;&nbsp;&nbsp;<IMG SRC=pics/table_tit_memtotal.gif>&nbsp;&nbsp;&nbsp;</TH>");
  echo("<TD BGCOLOR=#000000><IMG SRC=pics/shim.gif WIDTH=1 HEIGHT=25></TD>");
  echo("<TH>&nbsp;&nbsp;&nbsp;<IMG SRC=pics/table_tit_memfree.gif>&nbsp;&nbsp;&nbsp;</TH>");
  echo("<TD BGCOLOR=#000000><IMG SRC=pics/shim.gif WIDTH=1 HEIGHT=25></TD>");
  echo("<TH>&nbsp;&nbsp;&nbsp;<IMG SRC=pics/table_tit_memused.gif>&nbsp;&nbsp;&nbsp;</TH>");
  echo("<TD BGCOLOR=#000000><IMG SRC=pics/shim.gif WIDTH=1 HEIGHT=25></TD>");
  echo("<TH ALIGN=right VALIGN=top><IMG SRC=pics/table_r_roundgrey.gif></TH>");
  echo("</TR>");
  $i = 1;
  while(list($k,$v) = each($apc_hosts)) {
    // skip emtpy host entries
    if ($v == "") 
      continue;
    $i++;
    if ($i%2) $color="#E19E00";
    else $color="#D88600";
    echo("<TR BGCOLOR=$color>");
    echo("<TD>");
    echo("<IMG SRC=pics/shim.gif WIDTH=1 HEIGHT=10><BR>");
    echo("&nbsp;&nbsp;<A HREF=".$PHP_SELF."?host=".UrlEncode($k).">");
    echo("<FONT COLOR=#FFFFFF>".$k."</FONT>");
    echo("</A>");
    echo("<BR><IMG SRC=pics/shim.gif WIDTH=1 HEIGHT=10>");
    echo("</TD>");
    echo("<TD BGCOLOR=#AE5000><IMG SRC=pics/shim.gif WIDTH=1 HEIGHT=25></TD>");


    $arr = apc_get_cache_info($v);
    if ($arr == false) {
      echo("<TD COLSPAN=15 ALIGN=center><B>[NO RESULTS RECEIVED]</B></TD></TR>");
      continue;
    }
    echo("<TD ALIGN=center>");
    if ($show_apache_version) {
      echo(isset($arr["SERVER_SOFTWARE"])?$arr["SERVER_SOFTWARE"]:"UNKNOWN");
      echo("; ");
    }
    echo(isset($arr["version"])?" APC/".$arr["version"]:"UNKNOWN");
    echo("</TD>");
    echo("<TD BGCOLOR=#AE5000><IMG SRC=pics/shim.gif WIDTH=1 HEIGHT=25></TD>");

    echo("<TD ALIGN=center>");
    echo($arr["hits"]);
    echo("</TD>");
    echo("<TD BGCOLOR=#AE5000><IMG SRC=pics/shim.gif WIDTH=1 HEIGHT=25></TD>");

    echo("<TD ALIGN=center>");
    echo($arr["misses"]);
    echo("</TD>");
    echo("<TD BGCOLOR=#AE5000><IMG SRC=pics/shim.gif WIDTH=1 HEIGHT=25></TD>");

    echo("<TD ALIGN=center>");
    echo(sprintf("%.2f", $arr["hit rate"] * 100)."%");
    echo("</TD>");
    echo("<TD BGCOLOR=#AE5000><IMG SRC=pics/shim.gif WIDTH=1 HEIGHT=25></TD>");

    echo("<TD ALIGN=center>");
    echo(MemStr($arr["total size"]));
    echo("</TD>");
    echo("<TD BGCOLOR=#AE5000><IMG SRC=pics/shim.gif WIDTH=1 HEIGHT=25></TD>");

    echo("<TD ALIGN=center>");
    echo(MemStr($arr["total available"]));
    echo("</TD>");
    echo("<TD BGCOLOR=#AE5000><IMG SRC=pics/shim.gif WIDTH=1 HEIGHT=25></TD>");

    echo("<TD ALIGN=center>");
    echo(MemStr($arr["total size"] - $arr["total available"]));
    echo("</TD>");
    echo("<TD BGCOLOR=#AE5000><IMG SRC=pics/shim.gif WIDTH=1 HEIGHT=25></TD>");

    echo("<TD ALIGN=center>");
    echo("<A HREF=".$PHP_SELF."?host=".UrlEncode($k)."&restart=1><IMG SRC=pics/b_restart.gif BORDER=0></A>");
    echo("</TD>");
    echo("</TR>");
  }
  echo("<TR BGCOLOR=#AE5000><TD COLSPAN=17><IMG SRC=pics/shim.gif HEIGHT=1></TD></TR>");
  echo("</TABLE>");
  EndPage();
?>
