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

/*
        SHM:
                element0 -> cache offset
                element1 -> object length
                element2 -> last access time
                element3 -> hit counter
                element4 -> ttl 
                element5 -> last modification time
        MMAP:
                element0 -> object length
                element2 -> creation time
                element3 -> hit counter
                element4 -> inode
*/


  function DeleteScript($host, $script) 
  {
    global $apc_hosts;

    if (is_array($script)) {
      reset($script);
      while(list($key, $val) = each($script)) {
        $val = UrlDecode($val);
        if (apc_delete($apc_hosts[$host], $val))
          echo("Object <i>".$val."</i> removed from Cache<br>");
        else
          echo("<font color=red><b>Deletion of <i>".$val."</i> failed</b></font><br>");
      }
    } else {
      if (apc_delete($apc_hosts[$host], $script))
        echo("Object <i>".$script."</i> removed from Cache<br>");
      else
        echo("Deletion of <i>".$script."</i> failed<br>");
    }
  }


  function ShowScriptInfo($host, $script) 
  {
    global $PHP_SELF;
    global $apc_hosts;

    $arr = apc_get_object_info($apc_hosts[$host], $script);
  
    if (!is_array($arr) || count($arr) == 0 || count($arr["info"]) == 0) {
      echo("This function is not supported by this host!<br>");
      echo("Please upgrade to at least APC 1.0.9<br>");
      EndPage();
    }

    echo("<table><tr><td valign=top>");

    echo("<table border=0 cellpadding=0 cellspacing=0 width=600><tr bgcolor=#6c6b6b>");
    echo("<th valign=top align=left>");
    echo("<table border=0 cellpadding=0 cellspacing=0 >");
    echo("<tr><td valign=top align=left height=25 width=17><img src=pics/table_l_roundgrey.gif border=0></td>");
    echo("<td><img src=pics/table_tit_genericinfos.gif border=0></td></tr></table></th>");
    echo("<td><IMG SRC=pics/shim.gif WIDTH=1 HEIGHT=25></td>");
    echo("<th valign=top align=right><img src=pics/table_r_roundgrey.gif border=0>");
    echo("</th></tr>");

    $info = &$arr["info"];

    reset($info);
    $i = 1;
    while(list($key, $val) = each($info)) {
      $i++;
      if ($i%2) 
        $color="#E19E00";
      else 
        $color="#D88600";

      if ($key == "lastaccess" || $key == "mtime")
        $val = date("d.m.Y h:m:s", $val);
  
      echo("<tr bgcolor=".$color."><td>$key</td>");
      echo("<td bgcolor=#AE5000 width=1><IMG SRC=pics/shim.gif WIDTH=1 HEIGHT=25></td>");
      echo("<td>$val</td></tr>");
    }
    echo("</table>");

    echo("<br><table border=0 cellspacing=0 cellpadding=0 width=600><tr><td width=300 valign=top>");
    echo("<table border=0 cellpadding=0 cellspacing=0 height=100% width=100%><tr bgcolor=#6c6b6b>");
    echo("<td align=left valign=top>");
    echo("<table border=0 cellpadding=0 cellspacing=0><tr bgcolor=#6c6b6b>");
    echo("<td height=25 width=17 valign=top align=left><img src=pics/table_l_roundgrey.gif border=0></td>");
    echo("<td><img src=pics/table_tit_functions.gif border=0></td>");
    echo("</tr></table></td>");
    echo("<td bgcolor=#6c6b6b width=1><IMG SRC=pics/shim.gif WIDTH=1 HEIGHT=25></td>");
    echo("<td align=right height=25 width=17 valign=top align=right><img src=pics/table_r_roundgrey.gif border=0></td>");
    echo("</th></tr>");
    $functions = &$arr["functions"];

    reset($functions);
    $i=1;
    if (count($functions) == 0) {
      echo("<tr bgcolor=#D88600>");
      echo("<td colspan=3 height=25 align=center>No Functions</td></tr>");
    } else {
      while(list($key, $val) = each($functions)) {
        $i++;
        if ($i%2) 
          $color="#E19E00";
        else 
          $color="#D88600";

        echo("<tr bgcolor=".$color."><td>$key</td>");
        echo("<td bgcolor=#AE5000 width=1><IMG SRC=pics/shim.gif WIDTH=1 HEIGHT=25></td>");
        echo("<td>$val</td></tr>");
      }
    }
    echo("</table><p>");

    echo("</td><td width=2 valign=top align=left><img src=pics/shim.gif border=0></td><td valign=top width=300>");

    echo("<table border=0 cellpadding=0 cellspacing=0 height=100% width=100%><tr bgcolor=#6c6b6b>");
    echo("<td align=left valign=top>");
    echo("<table border=0 cellpadding=0 cellspacing=0><tr bgcolor=#6c6b6b>");
    echo("<td height=25 width=17 valign=top align=left><img src=pics/table_l_roundgrey.gif border=0></td>");
    echo("<td><img src=pics/table_tit_classes.gif border=0></td>");
    echo("</tr></table></td>");
    echo("<td bgcolor=#6c6b6b width=1><IMG SRC=pics/shim.gif WIDTH=1 HEIGHT=25></td>");
    echo("<td align=right height=25 width=17 valign=top align=right><img src=pics/table_r_roundgrey.gif border=0></td>");
    echo("</th></tr>");
    $classes = &$arr["classes"];

    reset($classes);
    $i=1;
    if (count($classes) == 0) {
      echo("<tr bgcolor=#D88600>");
      echo("<td  colspan=3 height=25 align=center>No Classes</td></tr>");
    } else {
      while(list($key, $val) = each($classes)) {
        reset($val);
        while(list($k, $f) = each($val)) {
          $i++;
          if ($i%2) 
            $color="#E19E00";
          else 
            $color="#D88600";

          echo("<tr bgcolor=".$color."><td>$key</td>");
          echo("<td bgcolor=#AE5000 width=1><IMG SRC=pics/shim.gif WIDTH=1 HEIGHT=25></td>");
          echo("<td>$f</td></tr>");
        }
      }
    }
    echo("</table>");

    echo("</td></tr></table>");
    echo("</td><td valign=top>");

    echo("<table border=0 cellpadding=0 cellspacing=0><tr bgcolor=#6c6b6b>");
    echo("<th><table  width=100% border=0 cellpadding=0 cellspacing=0><tr bgcolor=#6c6b6b>");
    echo("<td height=25 width=17 valign=top align=left><img src=pics/table_l_roundgrey.gif border=0></td>");
    echo("<td><img src=pics/table_tit_prim_opkot.gif border=0></td></tr></table></th>");
    echo("<th bgcolor=#000000><IMG SRC=pics/shim.gif WIDTH=1 HEIGHT=25></th>");
    echo("<th>&nbsp;<img src=pics/table_tit_sec_opkot.gif border=0>&nbsp;</th>");
    echo("<th bgcolor=#000000><IMG SRC=pics/shim.gif WIDTH=1 HEIGHT=25></th>");
    echo("<th><table  width=100% border=0 cellpadding=0 cellspacing=0><tr bgcolor=#6c6b6b>");
    echo("<td>&nbsp;<img src=pics/table_tit_line.gif border=0></td>");
    echo("<td height=25 width=17 valign=top align=right><img src=pics/table_r_roundgrey.gif border=0></td>");
    echo("</tr></table></th></tr>");

    $opcodes = &$arr["opcodes"];

    reset($opcodes);
    while(list($key, $val) = each($opcodes)) {
       $i++;
       if ($i%2) 
         $color="#E19E00";
       else 
         $color="#D88600";

      list($p, $s, $l) = $val;
      echo("<tr bgcolor=".$color.">");
      echo("<td>$p&nbsp;</td>");
      echo("<td bgcolor=#AE5000 width=1><IMG SRC=pics/shim.gif WIDTH=1 HEIGHT=25></td>");
      echo("<td>$s&nbsp;</td>");
      echo("<td bgcolor=#AE5000 width=1><IMG SRC=pics/shim.gif WIDTH=1 HEIGHT=25></td>");
      echo("<td align=right>$l&nbsp;</td>");
      echo("</tr>");
    }

    echo("</table>");
    echo("</td></tr></table>");

    EndPage();
  }


  function ShowScriptTable($arr) {
    global $REMOTE_ADDR;
    global $PHP_SELF;
    global $host;

    reset($arr);

    echo("<FORM action=".$PHP_SELF." method=POST><TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0 WIDTH=80%>");
    echo("<TR bgcolor=#6c6b6b>");
    echo("<TH align=left valign=top><table border=0 cellpadding=0 cellspacing=0><tr><td><img src=pics/table_l_roundgrey.gif></td>");
    echo("<td><img src=pics/scriptname.gif></td></th></table></TH>");
    echo("<th bgcolor=#000000 width=1><IMG SRC=pics/shim.gif WIDTH=1 HEIGHT=25></th>");
    echo("<TH><img src=pics/size.gif></TH>");
    echo("<th bgcolor=#000000 width=1><IMG SRC=pics/shim.gif WIDTH=1 HEIGHT=25></th>");
    echo("<TH><img src=pics/table_tit_hits2.gif></TH>");
    echo("<th bgcolor=#000000 width=1><IMG SRC=pics/shim.gif WIDTH=1 HEIGHT=25></th>");
    echo("<TH><img src=pics/last_access.gif></TH>");
    echo("<th bgcolor=#000000 width=1><IMG SRC=pics/shim.gif WIDTH=1 HEIGHT=25></th>");
    echo("<TH><img src=pics/last_modified.gif></TH>");
    echo("<th bgcolor=#000000 width=1><IMG SRC=pics/shim.gif WIDTH=1 HEIGHT=25></th>");
    echo("<TH align=right><table border=0 cellpadding=0 cellspacing=0><tr><td><img src=pics/ttl.gif></td>");
    echo("<td><img src=pics/table_r_roundgrey.gif></td></tr></table></TH>");
    echo("</TR>");
    $i = 1;
    while(list($k,$v) = each($arr)) {
      $i++;
      if ($i%2) $color="#E19E00";
      else $color="#D88600";

      echo("<TR BGCOLOR=$color>");

      $script = $k;
      $full_script_name = $k;

      $cacheoffset          = $v[0];
      $objectlength         = $v[1];
      $lastaccesstime       = $v[2];
      $hitcounter           = $v[3];
      $ttl                  = $v[4];
      $lastmodificationtime = $v[5];


      if (strlen($script) > 30) { 
        $subscript = substr($script, -30);
        $pos = strpos($subscript, "/");
        if ($pos !== false) {
          $subscript = "/...".substr($subscript, $pos);
        }
        $script = $subscript;
      }
      echo("<TD><input type=checkbox name=del_script[] value=\"".UrlEncode($full_script_name)."\">");
      echo("<A HREF=".$PHP_SELF."?scriptinfo=1&script=".UrlEncode($full_script_name)."&host=".UrlEncode($host).">");
      echo("<font color=#FFFFFF>$script</font></A></TD>");
      echo("<td bgcolor=#AE5000><IMG SRC=pics/shim.gif WIDTH=1 HEIGHT=25></td>");
      echo("<TD ALIGN=center>$objectlength</TD>");
      echo("<td bgcolor=#AE5000><IMG SRC=pics/shim.gif WIDTH=1 HEIGHT=25></td>");
      echo("<TD ALIGN=center>$hitcounter</TD>");
      echo("<td bgcolor=#AE5000><IMG SRC=pics/shim.gif WIDTH=1 HEIGHT=25></td>");
      echo("<TD ALIGN=center>".DateStr($lastaccesstime)."</TD>");
      echo("<td bgcolor=#AE5000><IMG SRC=pics/shim.gif WIDTH=1 HEIGHT=25></td>");
      echo("<TD ALIGN=center>".DateStr($lastmodificationtime)."</TD>");
      echo("<td bgcolor=#AE5000><IMG SRC=pics/shim.gif WIDTH=1 HEIGHT=25></td>");
      echo("<TD ALIGN=center>$ttl</TD>");
      echo("</TR>");
    }
    echo("</TABLE><br>");
    echo("<input type=hidden name=host value=".UrlEncode($host).">");
    echo("<input type=image name=delete value=\"Delete marked Scripts\" src=pics/b_del_marked.gif border=0>");
    echo("</FORM>");
  }

  function Option($name, $val, $opt) {
    global ${$name};
    echo("<OPTION VALUE=\"".HtmlSpecialChars($val)."\" ");
    if (${$name} == $val) echo(" SELECTED ");
    echo("> $opt");
  }

  if (!isset($sort_by)) 
    $sort_by = "hits";

  if (!isset($sort_order)) 
    $sort_order = "desc";

  if (!isset($rows)) 
    $rows = "10";

  
  if (isset($script) && isset($host)) {
    BeginPage();
    include("navi.php");

    ShowScriptInfo($host, $script);

    EndPage();
  }

  if (isset($host) && strlen($host) > 0) {
    BeginPage();
    include("navi.php");
 
    if (isset($delete_x) && isset($del_script))  DeleteScript($host, $del_script);

    echo("<FORM METHOD=POST>");
    echo("Sorting: ");
    echo("<SELECT NAME=sort_by>");
    Option("sort_by", "hits", "Hits");
    Option("sort_by", "size", "Size");
    Option("sort_by", "lastaccess", "Last Access");
    Option("sort_by", "lastmodified", "Last Modified");
    echo("</SELECT>");
    echo("<SELECT NAME=sort_order>");
    Option("sort_order", "desc", "DESC");
    Option("sort_order", "asc", "ASC");
    echo("</SELECT>");

    echo("<SELECT NAME=rows>");
    Option("rows", "0", "Show all");
    Option("rows", "10", "Top 10");
    Option("rows", "20", "Top 20");
    Option("rows", "50", "Top 50");
    Option("rows", "100", "Top 100");
    Option("rows", "150", "Top 150");
    Option("rows", "200", "Top 200");
    Option("rows", "500", "Top 500");
    echo("</SELECT>");

    echo("&nbsp;");

    echo("<INPUT TYPE=image NAME=go VALUE=go SRC=pics/b_go.gif border=0>");
    echo("<INPUT TYPE=hidden NAME=host VALUE=\"".HtmlSpecialChars($host)."\">");
    echo("</FORM>");
    $arr = apc_get_cache_index($apc_hosts[$host]);
    if ($arr == false) {
      echo(" FAILED<P>");
      echo("<A HREF=".$PHP_SELF.">back</A>");
      EndPage();
    }
    uasort($arr, "apc_sort_scripts_by_".$sort_by."_".$sort_order);
    
    $totalsize = 0; $totalbuckets = 0;
    while(list($i, $v) = each($arr)) {
      $totalsize += $arr[$i][1];
      $totalbuckets++;
    }
 
    echo("Total size: $totalsize - Total buckets: $totalbuckets<BR>");
 
    if ($rows > 0)
      array_splice($arr, $rows);

    ShowScriptTable(&$arr);
    EndPage();
  }

  BeginPage();
  include("navi.php");

  if ($refresh_hosts) {
    echo("<META HTTP-EQUIV=Refresh Content=".$refresh_hosts.">\n<Font Size=2>(This page is self refreshing after $refresh_hosts seconds)</FONT><P>");
  }
  reset($apc_hosts);
  echo("<TABLE ALIGN=center BORDER=0 CELLPADDING=0 CELLSPACING=0 WIDTH=60%>");
  echo("<TR BGCOLOR=#6c6b6b>");
  echo("<TH ALIGN=left VALIGN=top><TABLE BORDER=0 WIDTH=100% CELLPADDING=0 CELLSPACING=0><TR><TD ALIGN=left VALIGN=top><IMG SRC=pics/table_l_roundgrey.gif></TD><TD><IMG SRC=pics/table_tit_hosts.gif></TD></TR></TABLE></TH>");
  echo("<TD BGCOLOR=#000000><IMG SRC=pics/shim.gif WIDTH=1 HEIGHT=25></TD>");
  echo("<TH ALIGN=right VALIGN=top><IMG SRC=pics/table_r_roundgrey.gif></TH>");
  echo("</TR>");
  $i = 1;
  while(list($k,$v) = each($apc_hosts)) {
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
    echo("<TD  ALIGN=center><B>PLEASE SELECT HOST FOR DETAILED SCRIPT INFO</B></TD></TR>");
  }
  echo("<TR BGCOLOR=#AE5000><TD COLSPAN=3><IMG SRC=pics/shim.gif HEIGHT=1></TD></TR>");
  echo("</TABLE>");
  echo("<FORM METHOD=POST>");
  echo("<INPUT TYPE=submit NAME=Refresh VALUE=Refresh>");
  echo("</FORM>");

  EndPage();
?>
