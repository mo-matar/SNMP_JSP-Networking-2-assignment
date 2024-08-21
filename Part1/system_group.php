<?php
$ip = "127.0.0.1:161";

$oids = [
    "1.3.6.1.2.1.1.1.0" => "System Description", 
    "1.3.6.1.2.1.1.2.0" => "Object ID", 
    "1.3.6.1.2.1.1.3.0" => "System Uptime",
    "1.3.6.1.2.1.1.4.0" => "System Contact", 
    "1.3.6.1.2.1.1.5.0" => "System Name", 
    "1.3.6.1.2.1.1.6.0" => "System Location"  
];

$systemData = [];

foreach ($oids as $oid => $label) {
    $value = snmp2_get($ip, "public", $oid);
    if ($value === false) {
        $systemData[] = [
            'label' => $label,
            'value' => 'Error retrieving value'
        ];
    } else {
        $parts = explode(': ', $value);
        $value = isset($parts[1]) ? $parts[1] : $parts[0];
        $systemData[] = [
            'label' => $label,
            'value' => $value
        ];
    }
}

//submission and update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $oid = $_POST['oid'];
    $newValue = $_POST['new_value'];
    $result = false;

    if (!empty($newValue)) {
        $result = snmp2_set($ip, "public", $oid, "s", $newValue);
    }

    echo json_encode([
        'success' => $result !== false,
        'message' => $result !== false ? "Successfully updated" : "Failed to update",
        'systemData' => $systemData
    ]);
} else {
    echo json_encode($systemData);
}
?>
