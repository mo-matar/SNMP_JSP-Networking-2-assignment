<?php
$ip = "127.0.0.1"; 
$udp_table = snmp2_walk($ip, "public", ".1.3.6.1.2.1.7");

$data = [];
foreach ($udp_table as $index => $entry) {
    $entry = explode(': ', $entry)[1]; 
    $data[] = ['index' => $index, 'entry' => $entry];
}


header('Content-Type: application/json');
echo json_encode($data);
