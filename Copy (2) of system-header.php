<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<meta http-equiv="Content-Language" content="en-uk">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />

<title>Denby Equestrian - Home</title>
<meta name="keywords" content="Denby Equestrian, Equestrian Derbyshire, Stables Derbyshire, Horses Derbyshire, Livery Derbyshire, Jumping Arena Derbyshire, Equestrian Heanor, Stables Derbyshire, Horses Heanor, Livery Heanor, Jumping Arena Heanor, Equestrian Ripley, Stables Ripley, Horses Ripley, Livery Ripley, Jumping Arena Ripley, Equestrian Amber Valley, Stables Amber Valley, Horses Amber Valley, Livery Amber Valley, Jumping Arena Amber Valley, Equestrian Denby, Stables Denby, Horses Denby, Livery Denby, Jumping Arena Denby" />
<meta name="description" content="Denby Equestrian, family run business, owned, worked and managed by Deidre Trigg, fantastic facilities and offers a range of comprehensive livery packages." />
<META name="ROBOTS" content="INDEX,FOLLOW">
<TITLE><?php echo $title ?> - Denby Equestrian</TITLE>

<link rel="shortcut icon" type="image/x-icon" href="favicon.ico" />
<link href="css/denbyequestrian.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/themes/ui-lightness/jquery-ui.css" type="text/css" media="all" />

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js"></script>
 
<script class="javascript" src="script/scrollto.js"></script>
<script class="javascript" src="script/quotable.js"></script>
<script src="http://widgets.twimg.com/j/2/widget.js"></script> 

<?php
	//Include database connection details
	require_once('system-config.php');
	
	getPageRoles("Home");
?>

</HEAD>
<body>
<!-- wrapper end -->
<?php	
	
	if (isAuthenticated()) {
		include("system-maintainSave.php") ; 
	}
?>
<body>
<div id="templatemo_top_panel">
	<div id="templatemo_top_section">
       	<img src="images/header animation5.gif" id="animatedLogo" />
       	<img src="images/logo6.jpg" id="logo" />
		<div id="site_title">
        	Denby Equestrian
        </div>
  
  		<div id="loginLink">
  			<?php 
				if (! isAuthenticated()) {
		  			echo "<a href='#' onclick='$(\"#loginFormContainer\").dialog(\"open\");'><img border=0 width=16 height=16 src='images/icon_login.png' /></a>\n";
		  			
				} else {
		  			echo "<a id='logout' href='system-logout.php'><img border=0 width=16 height=16 src='images/logout-icon.png' /></a>\n";
					echo "<div class='userTitle'><span>User: </span>" . $_SESSION['SESS_FIRST_NAME'] . " " . $_SESSION['SESS_LAST_NAME'] . "</div>";
				}
  			?>
  		</div>
        
        <div id="loginFormContainer" class="modal">
			<form id="loginForm" name="loginForm" method="post" action="system-login-exec.php">
				<table border="0" align="center" cellpadding="2" cellspacing="0">
					<tr>
						<td><b>Login</b></td>
						<td><input name="login" type="text" class="textfield" id="login" /></td>
					</tr>
					<tr>
						<td><b>Password</b></td>
						<td><input name="password" type="password" class="textfield" id="password" /></td>
					</tr>
				</table>
			</form>  
       </div>
       
       <script>
       		$(document).ready(
       			function() {
					$( "#loginFormContainer" ).dialog({
							title: "Log in",
							autoOpen: false,
							modal: true,
							buttons: {
								"Cancel": function () {
									$(this).dialog("close");
								},
								"Ok": function () {
									$("#loginForm").submit();
								}
							}
						});
       			});
       			
       </script>
        
        <div id="site_tagline">
        	Livery ... and much more !
        </div>
        
        <div id="templatemo_menu">
			<?php
				showMenu();
			?>
		</div> <!-- end of menu -->
		
			    </div> <!-- end of top section -->
</div> <!-- end of top panel -->    

<div id="templatemo_content_panel">
<div id="templatemo_content_section">

<?php BreadCrumbManager::showBreadcrumbTrail(); ?>

<?php 
	if (isset($_POST['command'])) {
		$_POST['command']();
	}
?>
