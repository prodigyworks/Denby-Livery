<?php include("system-header.php"); ?>

<!--  Start of content -->
<form id="commandForm" name="commandForm" method="POST">
	<input type="text" id="newRole" name="newRole" value="" />
	<input type="hidden" id="command" name="command" value="" />
	<input type="hidden" id="role" name="role" value="" />
	<button id="newButton" onclick="call('addRole');">Create</button>
</form>


<table width="100%" class="dataGrid">
<thead>
	<tr>
		<td>Role</td>
		<td width='20px'></td>
	</tr>
</thead>
<?php
	function addRole() {
		$role = $_POST['newRole'];
		
		$qry = "INSERT INTO roles (roleid) VALUES ('$role')";
		$result=mysql_query($qry);
		
		if (! $result) {
			 die('Invalid query: ' . mysql_error());

		}
		
	} 
	
	function removeRole() {
		$role = $_POST['role'];
		
		$qry = "DELETE FROM roles WHERE roleid = '$role'";
		$result=mysql_query($qry);

		if (! $result) {
			 die('Invalid query: ' . mysql_error());

		}
	}
	
	$qry = "SELECT * FROM roles";
			
	$result=mysql_query($qry);
	$rowNumber = 0;
	
	//Check whether the query was successful or not
	if($result) {
		
		while($member = mysql_fetch_assoc($result)) {
			echo "<tr><td>";
			echo $member['roleid'];
			echo "<td><img onclick='deleteRole(\"" . $member['roleid'] . "\")' src='images/delete.png' /></td>";
			echo "</td></tr>";
		} 			
		
	} else {
		 die('Invalid query: ' . mysql_error());

	}
?>
</table>
<script>
function deleteRole(role) {
	if (confirm("Are you sure you want to delete the role (" + role + ") ?")) {
		$("#role").val(role);
		
		call("removeRole");
	}
}
</script>
<!--  End of content -->

<?php include("system-footer.php"); ?>
