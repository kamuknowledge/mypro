#!/usr/bin/php
<?php
$homepage = file_get_contents('http://www.getlinc.com/emailcron/index');
echo $homepage;
echo "Completed";
?>