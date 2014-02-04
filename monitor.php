<?php

/*
monitor.php A simple web-embeddable Starbound server monitor
Copyright (C) 2014  David Ketler

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

$log = "/home/starbound/server/starbound_server.log";
$config = "/home/starbound/server/linux32/starbound.config";
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
echo "<table class='table table-bordered'><thead><tr><th>Current Players: ";
echo $count . "/" . $settings->{"maxPlayers"} ."</th></tr></thead>";
echo "<tbody>";
foreach($users as $u => $status) {
    if($status == 1)
        echo "<tr><td>" . $u ."</td></tr>";
}
echo "<tr><td>&nbsp;</td></tr>";
echo "</tbody></table>";
//print_r($users);

echo " <table class='table table-bordered'><thead><tr><th>Recent Messages</th></tr></thead>";
echo "<tbody style='height:300px; overflow-y: scroll; overflow-x: hidden;'>";
foreach($messages as $msg) {
    echo "<tr><td><code>" . htmlentities($msg) ."</code></td></tr>";
}
echo "<tr><td>&nbsp;</td></tr>";
echo "</tbody></table>";
?>

