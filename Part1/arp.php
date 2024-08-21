<?php
$ip = "127.0.0.1";

$a = snmp2_walk($ip, "public", ".1.3.6.1.2.1.4.22.1.2");
$b = snmp2_walk($ip, "public", ".1.3.6.1.2.1.4.22.1.3");
$c = snmp2_walk($ip, "public", ".1.3.6.1.2.1.4.22.1.4");

$data = [];
foreach ($a as $i => $val) {
    $data[] = [
        'index' => $i,
        'mac' => $val,
        'ip' => $b[$i],
        'type' => $c[$i]
    ];
}

header('Content-Type: application/json');
echo json_encode($data);
