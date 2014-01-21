<TABLE BORDER=0>
<TR>
<TD>
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


 
  echo("<TD>");
  echo("<FORM METHOD=POST>");
  echo("<INPUT TYPE=image SRC=pics/b_refresh_data.gif BORDER=0 NAME=Refresh VALUE=Refresh>");
  echo("<INPUT TYPE=hidden NAME=host VALUE=\"".HtmlSpecialChars($host)."\">");
  echo("</FORM>");
  echo("</TD>");
  echo("<TD><IMG SRC=pics/shim.gif WIDTH=10></TD>");
  if (strpos($PHP_SELF, "hosts.php") > 0 || isset($scriptinfo)) { // dirty but works
    echo("<TD>");
    echo("<FORM ACTION=scripts.php METHOD=POST>");
    echo("<INPUT TYPE=hidden NAME=host VALUE=\"".HtmlSpecialChars($host)."\">");
    echo("<INPUT TYPE=image SRC=pics/b_scripts_fth.gif BORDER=0 NAME=Refresh VALUE=Refresh>");
    echo("</FORM>");
    echo("</TD>");
    echo("<TD><IMG SRC=pics/shim.gif WIDTH=10></TD>");
    if (isset($scriptinfo)) {
      echo("<TD>");
      echo("<FORM ACTION=scripts.php method=POST>");
      echo("<input type=hidden name=del_script value=".$script.">");
      echo("<input type=hidden name=host value=".$host.">");
      echo("<input type=image name=delete value=\"Delete this script!\" src=pics/b_del_this.gif border=0>");
      echo("</FORM>");
    }
  }
  else {
    echo("<TD>");
    echo("<FORM ACTION=hosts.php METHOD=POST>");
    echo("<INPUT TYPE=hidden NAME=host VALUE=\"".HtmlSpecialChars($host)."\">");
    echo("<INPUT TYPE=image SRC=pics/b_view_h_stats.gif BORDER=0 NAME=Refresh VALUE=Refresh>");
    echo("</FORM>");
    echo("</TD>");
  }
?>
</TR>
</TABLE>
