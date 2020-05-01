<?php
//Checkong if logged in
session_start();
if(!isset($_SESSION["accountID"]) || !$_SESSION["accountID"]) exit(header("Location: ../login/login.php"));
//Requesting files
include "../../incl/lib/connection.php";
require_once "../incl/dashboardLib.php";
require_once "../../incl/lib/mainLib.php";
require_once "../../incl/lib/exploitPatch.php";
$gs = new mainLib();
$dl = new dashboardLib();
$ep = new exploitPatch();
//Generating songs table
if(isset($_GET["page"]) && is_numeric($_GET["page"]) & $_GET["page"] > 0){
	$page = ($ep->remove($_GET["page"]) - 1) * 10;
	$actualpage = $ep->remove($_GET["page"]);
}else{
	$page = 0;
	$actualpage = 1;
}
$songtable = "";
//Getting data
$query = $db->prepare("SELECT * FROM songs ORDER BY ID DESC LIMIT 10 OFFSET $page");
$query->execute();
$result = $query->fetchAll();
$query = $db->prepare("SELECT count(*) FROM songs");
$query->execute();
$songcount = $query->fetchColumn();
$x = $songcount - $page;
//Printing data
foreach($result as &$song){
	switch($song["isDisabled"]){
		case 1:
			$isDisabled = $dl->getLocalizedString("Yes");
			break;
		case 0:
			$isDisabled = $dl->getLocalizedString("No");
			break;
	}
	$songtable .= '<tr>
					<th scope="row">'.$x.'</th>
					<td>'.$song["ID"].'</th>
					<td>'.$song["name"].'</td>
					<td>'.$song["authorName"].'</td>
					<td>'.$song["size"].' MB</td>
					<td>'.$song["levelsCount"].'</td>
					<td>'.$isDisabled.'</td>
				</tr>';
	$x--;
	echo "</td></tr>";
}
//Bottom row
$pagecount = ceil($songcount / 10);
$bottomrow = $dl->generateBottomRow($pagecount, $actualpage);
//Printing page
$dl->printPage('<table class="table table-inverse">
	<thead>
		<tr>
			<th>#</th>
			<th>'.$dl->getLocalizedString("ID").'</th>
			<th>'.$dl->getLocalizedString("name").'</th>
			<th>'.$dl->getLocalizedString("songAuthor").'</th>
			<th>'.$dl->getLocalizedString("size").'</th>
			<th>'.$dl->getLocalizedString("levelsCount").'</th>
			<th>'.$dl->getLocalizedString("disabled?").'</th>
		</tr>
	</thead>
	<tbody>
		'.$songtable.'
	</tbody>
</table>'
.$bottomrow, true, "browse");
?>
