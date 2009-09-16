#!/usr/bin/env php
<?php 

require 'include/Duration.php';

$known_distances = array(100, 200, 400, 800, 1500);

echo "Enter the distance to swim : ";
$distance = fgets(STDIN);

echo "Enter your engagement time in minutes : ";
$minutes = fgets(STDIN);

echo "Enter your engagement time in seconds : ";
$seconds = fgets(STDIN);

$duration = $minutes * 60 + $seconds;
echo "For " . substr($distance, 0, -1) . " m, you must swim 100 m in : ";
echo Duration::toString($duration / ($distance / 100)) . "\n";
