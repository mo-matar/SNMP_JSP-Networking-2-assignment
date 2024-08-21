<?php
$ip = "127.0.0.1";
$tcp_table = snmp2_walk($ip, "public", ".1.3.6.1.2.1.6.13");

$data = [];
foreach ($tcp_table as $index => $entry) {
    $entry = explode(': ', $entry)[1]; 
    $data[] = ['index' => $index, 'entry' => $entry];
}

header('Content-Type: application/json');
echo json_encode($data);
