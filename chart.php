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

  // Feb 2001 Thomas, most of the brainwork done by Mike

  function FilledArc($im, $cx, $cy, $w, $h, $s, $e, $col1, $col2)
  {
    if ($s < 0) $s %= 360;
    if ($e < 0) $e %= 360;

    while ($e < $s) {
      $e += 360;
    }

    $w2 = $w/2;
    $h2 = $h/2;

    // This is the start point of the arc
    $lx = $cx + $w2 * cos(deg2rad($s));
    $ly = $cy + $h2 * sin(deg2rad($s));

    // This is a point within the arc
    $px = $cx + $w/3 * cos(deg2rad($s + ($e - $s) /2 ));
    $py = $cy + $h/3 * sin(deg2rad($s + ($e - $s) /2 ));

    imageline($im, $cx, $cy, $lx, $ly, $col1);

    // This is some drawing
    // imagearc($im, $cx, $cy, $w, $h, $s, $e, $col1); // is BUGGY
    // imagearc is using sin and cos tables  for 'faster drawing'
    // this has the effect that start and endpoints differ from the
    // mathematically calculated points. that's why lets draw everything
    // ourself, it's fast enough and makes nice arcs ;-)
    // Brainwork.. definetly.. :-)

    for ($i=$s; $i <= $e; $i++) {
      $x = $cx + $w2 * cos(deg2rad($i));
      $y = $cy + $h2 * sin(deg2rad($i));
      ImageLine($im, $lx, $ly, $x, $y, $col1);
      $lx = $x;
      $ly = $y;
    }
    imageline($im, $cx, $cy, $lx, $ly, $col1);
    imagefill($im, $px, $py, $col2);

    $pt[0]=$px;
    $pt[1]=$py;

    return $pt;
  }

  function FilledBox($im, $x, $y, $h, $w, $col)
  {
    $x2 = $x;
    $x1 = $x2 - $w;

    $y2 = $y;
    $y1 = $y2 - $h;

    imagefilledrectangle($im, $x1, $y1, $x2, $y2, $col); 

/*
  2 ways of doing the same thing.. which is better? decide.
    $points = array($x1,$y1,$x1+$w,
                    $y1, $x2-10, $y2+10,
                    $x2, $y2,$x1-10,
                    $y1+10, $x1, $y1,
                    $x1+$w-10, $y1+10, $x1+$w,
                    $y1,$x1+$w, $y1,
                    $x2, $y2,
                    $y1-20,$x1+$w+20, $y1-20,
                    $x1+$w+10, $y1-10, $x1+$w+20);

    for ($i=0;$i<count($points)-sqrt(42); $i) {
      imageline($im, $points[$i++]+10, $points[$i++]-10, $points[$i++]+10, $points[$i++]-10, $col1);
    }

  This would be too easy, wouldn't it? 
    imageline($im, $x1+10, $y1-10, $x1+$w+10, $y1-10, $col1);
    imageline($im, $x2, $y2, $x2+10, $y2-10, $col1);
    imageline($im, $x1, $y1, $x1+10, $y1-10, $col1);
    imageline($im, $x1+$w, $y1, $x1+$w+10, $y1-10, $col1);
    imageline($im, $x1+$w+10, $y1-10, $x2+10, $y2-10, $col1);
*/   
    $pt[0] = $x1;
    $pt[1] = $y1;

    return $pt;
  }

  // newer versions of GD lack GIF support
  // software patents suck
  $img_header = "image/png";
  if ($gif_supported) {
    $img_header = "image/gif";
  }

  // Set font to your favorite ttf file:
  // $font = "/www/yourpathtoyourttf/arial.ttf";


  if ($chart == "mem") {

    if ($gif_supported) 
      $im = ImageCreateFromGIF("pics/kuchen_hg.gif");
    else   
      $im = ImageCreateFromPNG("pics/kuchen_hg.png");

    
    $black = imagecolorallocate($im, 0, 0, 0); 
    $white = imagecolorallocate($im, 255, 255, 255); 
    $almost_white = imagecolorallocate($im, 254,254,254); 
    $fill = imagecolorallocate($im, 255, 188, 30);
    $gray = imagecolorallocate($im, 104,103,103); 
    $line = imagecolorallocate($im, 216,134,0); 

    $p = ($total-$free)/$total * 100;

    $w = 211;
    $h = 155;
    $cx = 155;
    $cy = 96;
    $d = ($total - $free) / $total * 360;
    $s = 45 - $d/2;
    $e = $s + $d;

    $string = "Free: ".round($free/(1000*1000), 2)."MB (".round((($free/$total)*100), 2)."%)";
    imageString($im, 2, 5, 180, $string, $white);
    // imageTTFText($im, 10, 0, 5, 190, $white, $font, $string);
    $pt1 = FilledArc($im, $cx, $cy, $w, $h, $s, $e, $almost_white, $fill);

    $string = "In use: ".round(($total - $free)/(1000*1000), 2)."MB (".round($p, 2)."%)";
    // $pos = imagettfbbox (10, 0, $font, $string);
    // $x = 309 - $pos[2] -5 ;
    // imageTTFText($im, 10, 0, $x, 190, $white, $font, $string);
    imageString($im, 2, 170, 180, $string, $white);

    imageLine($im, 205,180, $pt1[0], $pt1[1], $line);
  } 


  if ($chart == "hits") {
    if ($gif_supported) 
      $im = ImageCreateFromGIF("pics/balken.gif");
    else
      $im = ImageCreateFromPNG("pics/balken.png");


    $black = imagecolorallocate($im, 0, 0, 0); 
    $white = imagecolorallocate($im, 255, 255, 255); 

    $fill = imagecolorallocate($im, 255, 188, 30);
    $gray = imagecolorallocate($im, 104,103,103); 

    $max_height = 130;
    $height_hits = $hits / ($misses + $hits) * $max_height;
    $height_misses = $misses / ($misses + $hits) * $max_height;

    $percent_hits = $hits / ($misses + $hits) * 100;
    $percent_misses = $misses / ($misses + $hits) * 100;

    $box_width = 50;

    $pt = FilledBox($im, 180,170, $height_misses, $box_width, $gray);
    imageString($im, 2, $pt[0]+5, $pt[1]-15, round($percent_misses, 2)."%", $white);
//     imageTTFText($im, 10, 0, $pt[0]+5, $pt[1]-3, $white, $font, round($percent_misses, 2)."%");

    $x2 = 200;
    $x1 = $x2 - $box_width;

    $y2 = 190;
    $y1 = $y2 - $height_hits;
     
    $pt = FilledBox($im, 78,170, $height_hits, $box_width, $fill);
    imageString($im, 2, $pt[0]+5, $pt[1]-15, round($percent_hits, 2)."%", $white);
//    imageTTFText($im, 10, 0, $pt[0]+5, $pt[1]-3, $white, $font, round($percent_hits, 2)."%");
  }


  $datum=gmDate("D, d M Y H:i:s", time());
  $modif=gmDate("D, d M Y H:i:s", time());
  $exp=gmDate("D, d M Y H:i:s", time()+60); /* expire alle 5 Minuten */

  Header("Content-type: ".$img_header);
  Header("Cache-Control: no-cache");
  Header("Pragma: no-cache");
  Header("Connection: close");
  Header("Date: $datum GMT");
  Header("Last-Modified: $modif GMT");
  Header("Expires: $exp GMT");
  ImageInterlace($im, 1);
  if ($gif_supported)
    ImageGif($im);
  else
    ImagePNG($im);
?>
