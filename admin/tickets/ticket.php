<?php
/*
  Copyright 2013 Penn Manor School District, Andrew Lobos, and Benjamin Thomas

   Licensed under the Apache License, Version 2.0 (the "License");
   you may not use this file except in compliance with the License.
   You may obtain a copy of the License at

       http://www.apache.org/licenses/LICENSE-2.0

   Unless required by applicable law or agreed to in writing, software
   distributed under the License is distributed on an "AS IS" BASIS,
   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   See the License for the specific language governing permissions and
   limitations under the License.
*/
$requiresAdmin = true;
require_once("../../include.php");
$ticket = Ticket::getByProperty(PROPERTY_ID, $_GET['id']);
$properties = $ticket->getProperties();

if ( array_key_exists("transfer", $_GET) )
{
	$ticket->assignHelper($_GET['transfer']);
	header("Location: ticket.php?id=".$_GET['id']);
	die();
}
else if ( array_key_exists("close", $_GET) )
{
	$ticket->close();
	header("Location: ticket.php?id=".$_GET['id']);
	die();
}
else if ( array_key_exists("open", $_GET) )
{
	$ticket->reopen();
	header("Location: ticket.php?id=".$_GET['id']);
	die();
}
else if ( array_key_exists("reply", $_GET) )
{
	htmlspecialcharsArray($_GET);
	$ticket->addReply($session->getID(), $_GET['reply']);
	header("Location: ticket.php?id=".$_GET['id']);
	die();
}
else if ( array_key_exists("notesBox", $_GET) )
{
	$ticket->setProperty(PROPERTY_NOTES, $_GET['notesBox']);
	header("Location: ticket.php?id=".$_GET['id']);
}

?>
<!DOCTYPE html>
<html>
<head>
	<title>1:1</title>
	<link href="../../css/bootstrap.css" rel="stylesheet">
	<link href="../../css/style.css" rel="stylesheet">
	
</head>

	<body>
		<div class="navbar navbar-static-top">
			<div class="navbar-inner">
				<div class="container">
					<a class="brandimg" href="../index.php"><img src="../../img/pmsd.png"></a>
					<ul class="nav">
						<li><a href="../index.php">Overview</a></li>
						<li class="active"><a href="../tickets">Tickets</a></li>
						<li><a href="../laptops">Laptops</a></li>
						<li><a href="../issues">Issues</a></li>
						<li><a href="../students">Students</a></li>
						<li><a href="../calendar">Logs</a></li>
						<?php if ( $showFeedbackForm ) { ?><li><a href="../feedback">Feedback</a></li><?php } ?>
					</ul>
				
					<form class="navbar-search pull-right" action="./query.php">
					  <input type="text" class="search-query" name="query" placeholder="Search Tickets">
					</form>
				</div>
			</div>
		</div>
		<br><br>
		<div class="container">
			<span class="sectionHeader">View Ticket: <?php echo $properties[PROPERTY_TITLE]; ?></span>
			<?php		
			if ( $properties[PROPERTY_STATE] == TICKETSTATE_OPEN )
			{
			?>
				<button class="btn btn-danger pull-right" onClick="window.location = 'ticket.php?id=<?php echo $_GET['id']; ?>&close=true'">Close Ticket</button>
			<?php
				}
			else
			{
			?>
				<button class="btn btn-success pull-right" onClick="window.location = 'ticket.php?id=<?php echo $_GET['id']; ?>&open=true'">Reopen Ticket</button>
			<?php
			}
			?>
			
			<?php
			if ( !$properties[PROPERTY_HELPER] )
			{
			?>
				<button class="btn btn-info pull-right buttonSpacer" onClick="window.location = 'ticket.php?id=<?php echo $_GET['id']; ?>&transfer=<?php echo $session->getID(); ?>'">Assign to Me</button>
			<?php
			}
			
			$laptop = $ticket->getStudent()->getLaptop();
			if ( $laptop )
			{
			?>
			<button class="btn btn-info pull-right buttonSpacer" onClick="window.location = '../laptops/laptop.php?id=<?php echo $laptop->getID(); ?>'">View Laptop</button>
			<?php
			}
			?>
			<hr>
			
			<form action="" method="get">
				<fieldset>
					<textarea class="notesBox" rows="5" name="reply" placeholder="Reply"></textarea><br>
					<input type="hidden" name="id" value="<?php echo $_GET['id']; ?>">
					<div class="btn-group dropup">
						<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
							Transfer
							<span class="caret"></span>
						</a>
						<ul class="dropdown-menu">
							<?php
							foreach ($helpers as $helper)
							{
								if ( $helper != $properties[PROPERTY_HELPER] )
								{
									$student = new Student($helper);
							?>
							<li><a href="ticket.php?id=<?php echo $_GET['id']; ?>&transfer=<?php echo $student->getID(); ?>"><?php echo $student->getName(); ?></a></li>
							<?php
								}
							}
							?>
						</ul>
					</div>
					<input type="submit" class="btn btn-primary pull-right">
				</fieldset>
			</form>
			<hr>
			<?php echo Ticket::getHTMLForHistory($ticket->getHistory(SORT_DESC)); ?>
			<br>
			<span class="sectionHeader">Notes</span>
			<hr> 
			<form action="" method="GET">
				<textarea rows="5" name="notesBox" class="notesBox"><?php echo stripcslashes($properties[PROPERTY_NOTES]); ?></textarea>
				<input type="hidden" name="id" value="<?php echo $properties[PROPERTY_ID]; ?>">
				<input type="submit" class="btn btn-primary pull-right" value="Save">
			</form>
		</div>
		
		<script src="http://code.jquery.com/jquery.js"></script>
	  	<script src="../../js/bootstrap.min.js"></script>
	</body>
</html>