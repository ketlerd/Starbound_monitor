<?php
$log = "/starbound/server/starbound_server.log";
$config = "/starbound/server/linux32/starbound.config";
$array[] = "";
$users = array();
$messages = array();
$count = 0;
$file = fopen($log,"r");

if($file == false) {
	echo("Error opening file.");
	exit(); }

if ($file) {
    while (($buffer = fgets($file, 4096)) !== false) {
    	if(strpos($buffer, "UniverseServer: Client") !== false) {
			array_push($array,$buffer);
			$start = strpos($buffer, "Client '") + 8;
            $end = strpos($buffer, "' <");

            $users[substr($buffer,$start,$end - $start)] = (strpos($buffer,'disconnected') == false);
		}
        else if(strpos($buffer,"Info:  ") !== false) {
            array_push($messages,trim($buffer,"Info: "));

        }
	}
    if (!feof($file)) {
        echo "Error: unexpected fgets() fail\n";
    }
    fclose($file);
}
$settings = json_decode(file_get_contents($config));


foreach($users as $u => $status) {
    if($status == 1)
        $count++;
}
echo "<table class='table table-bordered'><thead><tr><td>Current Players: ";
echo $count . "/" . $settings->{"maxPlayers"} ."</td></tr></thead>";
echo "<tbody>";
foreach($users as $u => $status) {
    if($status == 1)
        echo "<tr><td>" . $u ."</td></tr>";
}
echo "<tr><td>&nbsp;</td></tr>";
echo "</tbody></table>";
//print_r($users);

echo " <table class='table table-bordered'><thead><tr><td>Recent Messages</td></tr></thead>";
echo "<tbody>";
foreach($messages as $msg) {
    echo "<tr><td><code>" . htmlentities($msg) ."</code></td></tr>";
}
echo "<tr><td>&nbsp;</td></tr>";
echo "</tbody></table>";
?>
