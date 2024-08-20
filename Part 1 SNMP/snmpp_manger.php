<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Snmp Manager</title>
</head>
<body>

<?php
// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);


echo("<h2>****Testing started **** </h2>");
$ip = "127.0.0.1:161";

// Section 1: Display the contents of the System Group except System Services with edit functionality
echo "<hr><section id='system-group-section'>";
echo "<div><h3>----------- System Group -----------<h3></div><br>";

$oids = [
    "1.3.6.1.2.1.1.1.0" => "System Description", // sysDescr
    "1.3.6.1.2.1.1.2.0" => "Object ID", // sysObjectID
    "1.3.6.1.2.1.1.3.0" => "System Uptime", // sysUpTime
    "1.3.6.1.2.1.1.4.0" => "System Contact", // sysContact
    "1.3.6.1.2.1.1.5.0" => "System Name", // sysName
    "1.3.6.1.2.1.1.6.0" => "System Location"  // sysLocation
];

echo"<div><table id='system-group'>";

// Display the current values
echo "<tr>";
foreach ($oids as $label) {
    echo "<th> $label </th>";
}
echo "</tr><tr>";
foreach ($oids as $oid => $label) {
    $value = snmp2_get($ip, "public", $oid);
    if ($value === false) {
        echo "$label: Error retrieving value<br>";
    } else {
        $parts = explode(': ', $value);
        $value = isset($parts[1]) ? $parts[1] : $parts[0]; // Safely remove type
        echo "<td> $value </td>";
    }
}
echo"</tr></table></div></section><br>";



echo "<section id='update-section'><div><h3>---------------Update section---------------</h3></div><br><div id='froms'>";

// Separate forms for each editable field
foreach ($oids as $oid => $label) {
    if ($oid == "1.3.6.1.2.1.1.4.0" || $oid == "1.3.6.1.2.1.1.5.0" || $oid == "1.3.6.1.2.1.1.6.0") {
        echo "<form method='POST'>";
        echo "<label>$label: &nbsp </label><input type='text' name='new_value'><input type='hidden' name='oid' value='$oid'><input type='submit' value='Update'><br>";
        echo "</form>";
    }
}

// Handle form submission and update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $oid = $_POST['oid'];
    $newValue = $_POST['new_value'];
    if (!empty($newValue)) {
        $result = snmp2_set($ip, "public", $oid, "s", $newValue);
        if ($result === false) {
            $error = error_get_last();
            echo "Failed to update $label to $newValue. Error: {$error['message']}<br>";
        } else {
            echo "Successfully updated $label to $newValue<br>";
            // Refresh the page to show updated values
            header("Refresh:0");
        }
    }
}

echo "</div><div><br><hr></div></section><br>";




// Section 2: Display the content of UDP table
echo "----------- UDP Table -----------<br><br>";

$udp_table = snmp2_walk($ip, "public", ".1.3.6.1.2.1.7");
echo "<table border='1'>";
foreach ($udp_table as $index => $entry) {
    $entry = explode(': ', $entry)[1]; // Remove type
    echo "<tr><td>$index</td><td>$entry</td></tr>";
}
echo "</table>";

echo "<br><hr><br>";

// Section 3: Display the content of ARP table
echo "----------- ARP Table -----------<br><br>";

$a = snmp2_walk($ip, "public", ".1.3.6.1.2.1.4.22.1.2");
$b = snmp2_walk($ip, "public", ".1.3.6.1.2.1.4.22.1.3");
$c = snmp2_walk($ip, "public", ".1.3.6.1.2.1.4.22.1.4");

echo "<table border='1'>";
echo "<tr> <td> Index </td><td> Mac </td> <td> IP </td><td> Type </td>  </tr>";
foreach ($a as $i => $val) {
    echo "<tr> <td> $i </td><td> $val </td> <td> $b[$i] </td><td> $c[$i] </td>  </tr>";
}
echo "</table>";

$ip = "127.0.0.1:161";

// SNMP Group OIDs for Method 1: By Get
$snmpGroupOids = [
    "1.3.6.1.2.1.11.1" => "snmpInPkts",
    "1.3.6.1.2.1.11.2" => "snmpOutPkts",
    // Add other OIDs here if available
];

// SNMP Group for Method 2: By Walk
$snmpWalkOid = "1.3.6.1.2.1.11";

echo "<br><hr><br>";
echo "----------- SNMP Group Statistics -----------<br><br>";
// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo("****Testing started *** <br>");
$ip = "127.0.0.1:161";

// SNMP Group OIDs with their respective names and descriptions
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

// Method 1: Using snmp2_get()
$results_get = [];
foreach ($snmpGroupOids as $oidSuffix => $name) {
    $oid = "1.3.6.1.2.1.11.$oidSuffix.0";
    $value = @snmp2_get($ip, "public", $oid);
    if ($value === false) {
        $value = "Error retrieving value";
    }
    $results_get[] = [$name, $value];
}

// Method 2: Using snmp2_walk()
$results_walk = [];
$walk_results = @snmp2_walk($ip, "public", "1.3.6.1.2.1.11");
$oidSuffix = 0;
if ($walk_results !== false) {
    foreach ($walk_results as $index => $entry) {
        $oidSuffix ++;
        $parts = explode(":", $entry);
        if (count($parts) == 2) {
            $fullOid = trim($parts[0]);
            $value = trim($parts[1]);
            if($oidSuffix == 7 || $oidSuffix == 23) {
                $oidSuffix++;
            }
                //substr($fullOid, strrpos($fullOid, ".") + 1);
            $name = $snmpGroupOids[$oidSuffix];
                //isset($snmpGroupOids[$oidSuffix]) ? $snmpGroupOids[$oidSuffix] : "Unknown";

            $results_walk[] = [$index, $name, $value];
        }
    }
}

echo "<table border='1'>";
echo "<tr><th colspan='3'>Method 1: By Get</th><th colspan='3'>Method 2: By Walk</th></tr>";
echo "<tr><th>ID</th><th>Name</th><th>Value</th><th>Item#</th><th>Name</th><th>Value</th></tr>";
$ID_counter = 0;
//combined table
for ($i = 0; $i < max(count($results_get), count($results_walk)); $i++) {
    $ID_counter ++;
    echo "<tr>";

    if (isset($results_get[$i])) {
        if($ID_counter == 7 || $ID_counter == 23){
            $ID_counter = $ID_counter + 1;
        }
        echo "<td>" . ($ID_counter) . "</td>";
        echo "<td>" . $results_get[$i][0] . "</td>";
        echo "<td>" . $results_get[$i][1] . "</td>";
    } else {
        echo "<td></td><td></td><td></td>";
    }

    if (isset($results_walk[$i])) {
        echo "<td>" . $results_walk[$i][0] . "</td>";
        echo "<td>" . $results_walk[$i][1] . "</td>";
        echo "<td>" . $results_walk[$i][2] . "</td>";
    } else {
        echo "<td></td><td></td><td></td>";
    }

    echo "</tr>";
}
echo "</table>";
?>
<style>
    h2{
        position:relative;
        justify-content: center;
        display: flex;
        width: 100%;
        align-items: center;
        margin: 0;
        padding: 10PX;
        background-color: #408080;
        color: white;
    }
    section{
        width: 100%;
        justify-content: center;
        /* display: flex; */
    }
    #system-group-section{
        display: block;
    }

    table,tr th,td{
        border: 1px black solid;
    }

    #system-group{
        width: 100%;
    }

    div{
        align-items: center;
        text-align: center;
        padding: 0 10PX;
    }

    th,td{
        padding: 5px;
        align-items: center;
        text-align: center;
        font-size: 18px;
    }
    #froms{
        width: 100%;
        text-align: center;
        display: flex;
        justify-content: space-evenly;
    }

    form{
        font-size: 20PX;
        display: flex;
        justify-content: space-around;
    }
    body{
        background-color: rgb(245, 245, 236);
        /* margin: 0;
        padding: 0; */

    }

        #udp-table-div{
        display: flex;
        justify-content: center;
    }
</style>
</body>
</html>
