<?php
  $apc_hosts = array("localhost"=>"localhost",
                     ""=>"");

  $refresh_hosts = 60; // Refresh host status page each XXX seconds

  // Show webserver version string (if available) in host overview
  $show_apache_version = true;

  // AutoDetect supported image type
  // thanx to: Michael T. Babcock <mbabcock@fibrespeed.net>
  $Types = ImageTypes();
  if ($Types & IMG_PNG)
    $gif_supported = false;
  elseif ($Types & IMG_GIF)
    $gif_supported = true;
  else
    print "Warning: Neither GIF nor PNG supported [$Types]";



  // If available, include the apc_config_local.php file which may contain
  // same variables as described above, but overwrites them.
  $fp = @fopen("apc_config_local.php", "r", 1);
  if ($fp) {
    fclose($fp);
    include("apc_config_local.php");
  }
?>
