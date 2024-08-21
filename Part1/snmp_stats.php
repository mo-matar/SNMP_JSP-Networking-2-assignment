<?php
$ip = "127.0.0.1";

$snmpGroupOids = [
    "1" => "snmpInPkts",
    "2" => "snmpOutPkts",
    "3" => "snmpInBadVersions",
    "4" => "snmpInBadCommunityNames",
    "5" => "snmpInBadCommunityUses",
    "6" => "snmpInASNParseErrs",
    "8" => "snmpInTooBigs",
    "9" => "snmpInNoSuchNames",
    "10" => "snmpInBadValues",
    "11" => "snmpInReadOnlys",
    "12" => "snmpInGenErrs",
    "13" => "snmpInTotalReqVars",
    "14" => "snmpInTotalSetVars",
    "15" => "snmpInGetRequests",
    "16" => "snmpInGetNexts",
    "17" => "snmpInSetRequests",
    "18" => "snmpInGetResponses",
    "19" => "snmpInTraps",
    "20" => "snmpOutTooBigs",
    "21" => "snmpOutNoSuchNames",
    "22" => "snmpOutBadValues",
    "24" => "snmpOutGenErrs",
    "25" => "snmpOutGetRequests",
    "26" => "snmpOutGetNexts",
    "27" => "snmpOutSetRequests",
    "28" => "snmpOutGetResponses",
    "29" => "snmpOutTraps",
    "30" => "snmpEnableAuthenTraps",
];

//method 1 snmp2_get()
$results_get = [];
foreach ($snmpGroupOids as $oidSuffix => $name) {
    $oid = "1.3.6.1.2.1.11.$oidSuffix.0";
    $value = @snmp2_get($ip, "public", $oid);
    if ($value === false) {
        $value = "Error retrieving value";
    }
    $results_get[] = ['name' => $name, 'value' => $value];
}

//method 2 snmp2_walk()
$results_walk = [];
$walk_results = @snmp2_walk($ip, "public", "1.3.6.1.2.1.11");
$oidSuffix = 0;
if ($walk_results !== false) {
    foreach ($walk_results as $entry) {
        $oidSuffix++;
        if ($oidSuffix == 7 || $oidSuffix == 23) {
            $oidSuffix++;
        }
        $parts = explode(":", $entry);
        $name = $snmpGroupOids[$oidSuffix];
        $value = trim($parts[1]);
        $results_walk[] = ['name' => $name, 'value' => $value];
    }
}

header('Content-Type: application/json');
echo json_encode(['get' => $results_get, 'walk' => $results_walk]);
