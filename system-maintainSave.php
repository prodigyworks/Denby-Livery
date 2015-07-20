<?php
	
	//Function to sanitize values received from the form. Prevents SQL injection
	function clean($str) {
		$str = @trim($str);
		if(get_magic_quotes_gpc()) {
			$str = stripslashes($str);
		}
		return mysql_real_escape_string($str);
	}
	
	if (isset($_POST['saveHotSpot']) && $_POST['saveHotSpot'] == "true") {
		$hotspotid = $_POST['hotspotID'];
		$text = $_POST['elm1'];
		$hotspotname = $_POST['hotspotDescription']; 
		
		//DOCUMENT MANAGEMENT
		$filename = $_POST['hotspotFile'];
		$desc = "Hotspot (" . $hotspotname . ")" ;
		
		$qry = "SELECT documentid FROM documents WHERE filename = '$filename'";
		$result=mysql_query($qry);
		$documentid = 0;
		$documentversionid = 0;
		
		//Check whether the query was successful or not
		if($result) {
			if(mysql_num_rows($result) == 1) {
				$member = mysql_fetch_assoc($result);
				$documentid = $member['documentid'];
			}
		}

		if ($documentid == 0) {
			//Create INSERT query
			$qry = "INSERT INTO documents (description, documentversionid, filename, createdby, createddate) VALUES ('$desc', 1, '$filename', '" . $_SESSION['SESS_MEMBER_ID'] . "', CURDATE())";
			$result = mysql_query($qry);
			
			$documentid = mysql_insert_id();
			
			//Create INSERT query
			$qry = "INSERT INTO documentversions (documentid, versionid, remark, image, createdby, createddate, status) VALUES ($documentid, 1, 'Initial version', '" . clean($text) . "', '" . $_SESSION['SESS_MEMBER_ID'] . "', CURDATE(), 'P')";
			$result = mysql_query($qry);

			$documentversionid = mysql_insert_id();
			
		} else {
			$createdby = $_SESSION['SESS_MEMBER_ID'];
			
			/* Look for current pending versions. */
			$qry = "SELECT B.image FROM documents A " .
					"INNER JOIN documentversions B " .
					"ON  B.documentid = A.documentid " .
					"WHERE A.documentid=$documentid " .
					"AND B.status = 'P' " .
					"AND B.createdby = '$createdby'";
			$result=mysql_query($qry);
			
			//Check whether the query was successful or not
			if($result) {
				if(mysql_num_rows($result) == 1) {
					//Login Successful
					$member = mysql_fetch_assoc($result);
					
					$qry = "UPDATE documentversions SET image = '" . clean($text) . "', remark = 'Amended on ' + CURDATE() WHERE documentid = $documentid";
					$result=mysql_query($qry);
					
				} else {
					$qry = "SELECT MAX(versionid) AS versionid FROM documentversions WHERE documentid = $documentid";
					$result=mysql_query($qry);
					
					//Check whether the query was successful or not
					if($result) {
						if(mysql_num_rows($result) == 1) {
							$member = mysql_fetch_assoc($result);
							$documentversionid = $member['versionid'] + 1;
						}
					}
					
					//Create INSERT query
					$qry = "INSERT INTO documentversions (documentid, versionid, remark, image, createdby, createddate, status) VALUES ($documentid, $documentversionid, 'Hotspot change', '" . clean($text) . "', '" . $_SESSION['SESS_MEMBER_ID'] . "', CURDATE(), 'P')";
					$result = mysql_query($qry);
					
					$documentversionid = mysql_insert_id();
				}
			}
		}
		
		//Create INSERT query
		$qry = "INSERT INTO hotspots (hotspotid, documentid) VALUES ($hotspotid, $documentid)";
		$result = @mysql_query($qry);
	}
?>
