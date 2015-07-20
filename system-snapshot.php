<?php include("system-header.php"); ?>

<!--  Start of content -->
<?php 
	showErrors(); 
	
	function revert() {
		$snapshotid = $_POST['snapshotid'];
		$qry = "SELECT * FROM snapshotdetails A " .
				"INNER JOIN hotspots B " .
				"ON B.hotspotid = A.hotspotid " .
				"INNER JOIN documentversions C " .
				"ON  C.documentid = B.documentid " .
				"AND C.documentversionid = A.versionid " .
				"WHERE snapshotid = $snapshotid";
		$result=mysql_query($qry);
		
		if($result) {
			while($member = mysql_fetch_assoc($result)) {
				$documentversionid = $member['documentversionid'];
				$documentid = $member['documentid'];
				
				$qry = "UPDATE documents " .
						"SET documentversionid = $documentversionid " .
						"WHERE documentid = $documentid";
				mysql_query($qry);
			}
		}
		
		echo "<h3>Snapshot (" . $snapshotid . ") reverted.</h3>";
	}
	
	function createSnapShot() {
		$createdby = $_SESSION['SESS_MEMBER_ID'];
		$qry = "INSERT INTO snapshots (snapshotname, createdby, createddate) VALUES ('" . $_POST['snapshotname'] . "', '$createdby', CURDATE())";
		$result=mysql_query($qry);
		$snapshotid = mysql_insert_id();
		
		$qry = "SELECT A.hotspotid, C.documentversionid " .
				"FROM hotspots A " .
				"INNER JOIN documents B " .
				"ON B.documentid = A.documentid " .
				"INNER JOIN documentversions C " .
				"ON  B.documentid = C.documentid " .
				"AND B.documentversionid = C.documentversionid ";
				
		$result=mysql_query($qry);
		
		//Check whether the query was successful or not
		if($result) {
			
			while($member = mysql_fetch_assoc($result)) {
				
				$qry = "INSERT INTO snapshotdetails (snapshotid, hotspotid, versionid) VALUES ($snapshotid, " . $member['hotspotid'] . ", " . $member['documentversionid'] . ")";
				mysql_query($qry);
			}
		}
	
		echo "<h3>Snapshot (" . $_POST['snapshotname'] . ") has been recorded.</h3>";
	}
?>

<form id="commandForm" name="commandForm" method="POST">
  <table border="0" cellpadding="2" cellspacing="0">
    <tr>
      <th>Snapshot Name </th>
      <td><input name="snapshotname" type="text" class="textfield" id="snapshotname" /></td>
      <td><button id="newButton" onclick="call('createSnapShot');">Create</button></td>
	
    </tr>
  </table>
  <input type="hidden" id="snapshotid" name="snapshotid" value="" />
  <input type="hidden" id="command" name="command" value="" />
</form>

<table width="100%" class="dataGrid">
	<thead>
		<tr>
			<td>Snapshot</td>
			<td>Author</td>
			<td>Created Date</td>
			<td>Delete</td>
			<td>Revert</td>
		</tr>
	</thead>
	<?php
		$qry = "SELECT * FROM snapshots A " .
				"INNER JOIN members B " .
				"ON B.member_id = A.createdby";
		$result=mysql_query($qry);
		
		if($result) {
			while($member = mysql_fetch_assoc($result)) {
				echo "<tr>";
				echo "<td>" . $member['snapshotname'] . "</td>";
				echo "<td>" . $member['firstname'] . " " . $member['lastname'] .  "</td>";
				echo "<td>" . $member['createddate'] . "</td>";
				echo "<td><img src='images/delete.png' /></td>";
				echo "<td><img src='images/publish.png' onclick='revert(\"" .$member['snapshotid'] . "\");' /></td>";
				echo "</tr>";
			}
		}
	
	?>
</table>
<script>
	function revert(id) {
		$("#snapshotid").val(id);
		
		call("revert");
	}
</script>
<!--  End of content -->

<?php include("system-footer.php"); ?>
