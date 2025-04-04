<?php

require 'includes/html/graphs/common.inc.php';

$rrd_filename = Rrd::name($device['hostname'], 'bison_dnat_stats');

$scale_min = 0;
$colours = 'mixed';
$unit_text = 'Connections';
$unitlen = 11;
$bigdescrlen = 15;
$smalldescrlen = 15;
$dostack = 0;
$printtotal = 0;
$addarea = 1;
$transparency = 80;
$data_sources = [
    'sessions' => ['descr' => 'Total Sessions', 'colour' => '66873e'],
    'fail_type_1' => ['descr' => 'NAT Fail Type 1', 'colour' => 'f49842'],
    'fail_type_2' => ['descr' => 'NAT Fail Type 2', 'colour' => '438099'],
    'session_overflow' => ['descr' => 'Session Overflow', 'colour' => 'af2121'],
    'no_free_maps' => ['descr' => 'No Free Port Maps', 'colour' => '000000'],
];
