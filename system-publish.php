<?php
	$diffString = "";
	
	include("system-diff.php");
	include("system-header.php"); 
?>

<!--  Start of content -->
<form id="commandForm" name="commandForm" method="POST">
	<input type="hidden" id="documentversionid" name="documentversionid" value="" />
	<input type="hidden" id="documentid" name="documentid" value="" />
	<input type="hidden" id="diff1" name="diff1" value="" />
	<input type="hidden" id="diff2" name="diff2" value="" />
	<input type="hidden" id="command" name="command" value="" />
	<input type="button" name="diffButton" id="diffButton" onclick="call('diffHTML');" value="Diff" disabled />
</form>

<table width="100%" class="dataGrid">
<thead>
	<tr>
		<td></td>
		<td>ID</td>
		<td>Version</td>
		<td>Name</td>
		<td>Status</td>
		<td>Last published by</td>
		<td>Date</td>
	</tr>
</thead>
<?php
	function diffHTML() {
		global $diffString;
		
		$diffString =  htmlDiff($_POST['diff1'], $_POST['diff2']);
	}
	
	function publish() {
		$documentversionid = $_POST['documentversionid'];
		$documentid = $_POST['documentid'];
		
		$qry = "UPDATE documents SET documentversionid = $documentversionid WHERE documentid = $documentid;";
		$result=mysql_query($qry);
		
		if (! $result) {
			 die('Invalid query: ' . mysql_error());

		}
		
		$qry = "UPDATE documentversions SET status = 'C' WHERE documentversionid = $documentversionid;";
		$result=mysql_query($qry);
		
		if (! $result) {
			 die('Invalid query: ' . mysql_error());

		}
	}
	
	$qry = "SELECT B.documentversionid AS bv, C.documentversionid AS cv, C.status, " .
			"C.image, C.versionid, B.description, B.filename, C.createdby, " .
			"C.documentid, C.createddate, D.firstname, D.lastname " .
			"FROM hotspots A " .
			"INNER JOIN documents B " .
			"ON B.documentid = A.documentid " .
			"INNER JOIN documentversions C " .
			"ON B.documentid = C.documentid " .
			"INNER JOIN members D " .
			"ON D.member_id = C.createdby " .
			"ORDER BY C.status DESC, B.description";
			
	$result=mysql_query($qry);
	$rowNumber = 0;
	
	//Check whether the query was successful or not
	if($result) {
		
		while($member = mysql_fetch_assoc($result)) {
			$rowNumber++;
			
			switch ($member['status']) {
				case "P":
					$status = "PENDING";
					break;
					
				case "C":
					$status = "CONFIRMED";
			
					if ($member['bv'] == $member['cv']) {
						$status = "LIVE";
					}
					break;
			}
			
//			if (isUserInRole($member['createdby'])) {
				echo "<tr class='publishRow " . (($rowNumber % 2) == 1 ? "red" : "green") . "' text='" . escapeQuote($member['image']) . "' rowColor='" . (($rowNumber % 2) == 1 ? "red" : "green") . "'><td>";
				echo "<input type='checkbox' class='selectedBox' onclick='selectBox(this)' text='" . escapeQuote($member['image']) . "' />";
				echo "</td><td>";
				echo $member['filename'];
				echo "</td><td>";
				echo $member['versionid'];
				echo "</td><td>";
				echo $member['description'];
				echo "</td><td>";
				echo $status;
				echo "</td><td>";
				echo $member['firstname'] . " ". $member['lastname'];
				echo "</td><td nowrap>";
				echo $member['createddate'];
				echo "</td><td><img title='Click to publish' onclick='publish(\"" . $member['cv'] . "\", \"" . $member['documentid'] . "\")' src='images/publish.png' />";
				echo "</td></tr>";
//			} 

			
		
		} 			
	}else {
		 die('Invalid query: ' . mysql_error());

	}
//	echo htmlDiff("<html><body><p>I AM GREAT BUT NOT AS GREAT AS YOU</p></body></html>", "<html><body><p>I AM GREATER THAN YOU</p></body></html>");
?>
</table>

<textarea id="viewEditor" name="viewEditor" rows="15" cols="80" style="height:500px;width: 600px" class="tinymceReadOnly">
</textarea>


<script>
<?php
	echo "$(document).ready(function() {\n";
	echo "$('#viewEditor').val('" . $diffString . "');\n";
	echo "});\n";
?>
	function selectBox(node) {
		var count = 0;
		
		$(".selectedBox").each(
				function() {
					if ( $(this).attr("checked")) {
						if (count == 0) {
							$("#diff1").val($(this).attr("text"));
							
						} else {
							$("#diff2").val($(this).attr("text"));
						}
						
						count++;
					}
				}
			);
			
		if (count == 2) {
			$("#diffButton").attr("disabled", false);
			
		} else {
			$("#diffButton").attr("disabled", true);
		}
	}
	
	function publish(documentversionid, documentid) {
		if (confirm("You are about to publish the changes to this hotspot ?")) {
			$("#documentversionid").val(documentversionid);
			$("#documentid").val(documentid);
			
			call("publish");
		}
	}

	$(document).ready(function() {
		$('textarea.tinymceReadOnly').tinymce({
			// Location of TinyMCE script
			script_url : 'script/tiny_mce/tiny_mce.js',

			// General options
			theme : "advanced",

			// Theme options
			theme_advanced_buttons1 : "",
			theme_advanced_buttons2 : "",
			theme_advanced_buttons3 : "",
			theme_advanced_buttons4 : "",
			theme_advanced_toolbar_location : "top",
			theme_advanced_toolbar_align : "left",
			theme_advanced_statusbar_location : "bottom",
			theme_advanced_resizing : true,

			// Example content CSS (should be your site CSS)
			content_css : "css/content.css",

			// Drop lists for link/image/media/template dialogs
			template_external_list_url : "lists/template_list.js",
			external_link_list_url : "lists/link_list.js",
			external_image_list_url : "lists/image_list.js",
			media_external_list_url : "lists/media_list.js",

			// Replace values for the template plugin
			template_replace_values : {
				username : "Some User",
				staffid : "991234"
			}
		});
	});
	
$(".publishRow").hover(
	function() {
		$(this).css("background-color", "yellow");
		$(this).css("color", "blue");
	},
	function() {
		$(this).css("background-color", "");
		$(this).css("color", "white");
	}
);
$(".publishRow").click(
		function(e) {        
			$("#viewEditor").val($(this).attr("text"));
		}
	);
</script>
<!--  End of content -->

<?php include("system-footer.php"); ?>
