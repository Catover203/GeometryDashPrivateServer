<?php
//Checking if logged in
session_start();
if(!isset($_SESSION["accountID"]) || !$_SESSION["accountID"]) exit(header("Location: ../login/login.php"));
//Requesting files
include "../../incl/lib/connection.php";
require_once "../incl/dashboardLib.php";
require_once "../../incl/lib/mainLib.php";
require_once "../../incl/lib/exploitPatch.php";
$gs = new mainLib();
$ep = new exploitPatch();
$dl = new dashboardLib();
//Checking permissions
if(!$gs->checkPermission($_SESSION["accountID"], "dashboardModTools")){
	//Printing errors
	$errorDesc = $dl->getLocalizedString("errorNoPerm");
	exit($dl->printBox('<h1>'.$dl->getLocalizedString("errorGeneric")."</h1>
					<p>$errorDesc</p>
					<a class='btn btn-primary btn-block' href='".$_SERVER["REQUEST_URI"]."'>".$dl->getLocalizedString("tryAgainBTN")."</a>","mod"));
}
//Checking nothing's empty
if(!empty($_POST["userID"]) && !$_POST["userID"]){
	//Getting form data
	$userID = $ep->remove($_POST["userID"]);
	//Checking if is numeric
	if(!is_numeric($userID)){
		//Printing error
		$errorDesc = $dl->getLocalizedString("banError-2");
		exit($dl->printBox('<h1>'.$dl->getLocalizedString("leaderboardBan")."</h1>
						<p>$errorDesc</p>
						<a class='btn btn-primary btn-block' href='".$_SERVER["REQUEST_URI"]."'>".$dl->getLocalizedString("tryAgainBTN")."</a>","mod"));
	}
	//Checking if was already banned
	if($gs->isBanned($userID, "leaderboards")){
		//Printing error
		$errorDesc = $dl->getLocalizedString("banError-3");
		exit($dl->printBox('<h1>'.$dl->getLocalizedString("leaderboardBan")."</h1>
						<p>$errorDesc</p>
						<a class='btn btn-primary btn-block' href='".$_SERVER["REQUEST_URI"]."'>".$dl->getLocalizedString("tryAgainBTN")."</a>","mod"));
	}
	//Banning user
	$query = $db->prepare("UPDATE users SET isBanned = 1 WHERE userID = :id");
	$query->execute([':id' => $userID]);
	if($query->rowCount() != 0){
		//Printing box
		$dl->printBox("<h1>".$dl->getLocalizedString("leaderboardBan")."</h1>
					<p>".sprintf($dl->getLocalizedString("banned"), $userID)."</p>","mod");
	}else{
		//Printing error
		$errorDesc = sprintf($dl->getLocalizedString("banError-1"), $userID);
		exit($dl->printBox('<h1>'.$dl->getLocalizedString("leaderboardBan")."</h1>
						<p>$errorDesc</p>
						<a class='btn btn-primary btn-block' href='".$_SERVER["REQUEST_URI"]."'>".$dl->getLocalizedString("tryAgainBTN")."</a>","mod"));
	}
}else{
	//Printing page
	$dl->printBox('<h1>'.$dl->getLocalizedString("leaderboardBan").'</h1>
				<form action="" method="post">
					<div class="form-group">
						<input type="text" class="form-control" id="banUserID" name="userID" placeholder="UserID">
					</div>
					<button type="submit" class="btn btn-primary btn-block">Ban</button>
				</form>',"mod");
}
?>
