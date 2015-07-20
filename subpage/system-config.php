<?php
	class BreadCrumb {
	    // property declaration
	    public $page = "";
	    public $label = "";
	}
	
	class BreadCrumbManager {
		public static function initialise() {
			if (! isset($_SESSION['BREADCRUMBMANAGER'])) {
				$_SESSION['BREADCRUMBMANAGER'] = array();
			}
		}
		
		public static function add($pageName, $pageLabel) {
			$bc = new BreadCrumb();
			$bc->page = $pageName;
			$bc->label = $pageLabel;
			
			$_SESSION['BREADCRUMBMANAGER'][count($_SESSION['BREADCRUMBMANAGER'])] = $bc;
		}
		
		public static function remove($index) {
			unset($_SESSION['BREADCRUMBMANAGER'][$index]);
		}
		
		public static function showBreadcrumbTrail() {
			echo "<h4 class='breadcrumb'>";
			
			for ($i = 0; $i <  count($_SESSION['BREADCRUMBMANAGER']); $i++) {
				if ($i > 0) {
					echo "<span>&nbsp;/&nbsp;</span>";
				}
				
				echo "<a href='" .$_SESSION['BREADCRUMBMANAGER'][$i]->page . "' ";
				
				if ($i == count($_SESSION['BREADCRUMBMANAGER']) - 1) {
					echo "class='lastchild'";
				}
				
				echo ">" . $_SESSION['BREADCRUMBMANAGER'][$i]->label . "</a>";
			} 
			
			echo "</h4>";
		}
		
		public static function calculate() {
    		$count = count($_SESSION['BREADCRUMBMANAGER']);
    		
	    	if ($count > 0) {
	    		$index = -1;
	    		 
				for ($i = 0; $i < $count; $i++) {
					if ($_SESSION['BREADCRUMBMANAGER'][$i]->label == $_SESSION['title']) {
						$index = $i + 1;
						break;
					}
				}
				
				if ($index >= 0) {
					/* Link to page already within the breadcrumb trail.
					 */
					for ($i = $count - 1; $i >= $index; $i--) {
						self::remove($i);
					}
					
				} else {
					/* Check to see if the parent page is the same.
					 */
					if (isset($_SESSION['parentpagename'])) {
						for ($i = 0; $i < $count; $i++) {
							if ($_SESSION['BREADCRUMBMANAGER'][$i]->page == $_SESSION['parentpagename']) {
								$index = $i + 1;
								break;
							}
						}
					}
					
					if ($index >= 0) {
						for ($i = $count - 1; $i >= $index; $i--) {
							self::remove($i);
						}
					}

		    		self::add($_SESSION['pagename'], $_SESSION['title']);
				}
	    		
	    	} else {
	    		self::add($_SESSION['pagename'], $_SESSION['title']);
	    	}
	    	
	    	if (isAuthenticated()) {
		    	if (isset($_SESSION['lastconnectiontime'])) {
		    		$lastsessiontime = time() - $_SESSION['lastconnectiontime'];
		    		
		    		/* 5 minutes. */
		    		if ($lastsessiontime >= 300) {	//Unset the variables stored in session
						unset($_SESSION['SESS_MEMBER_ID']);
						unset($_SESSION['SESS_FIRST_NAME']);
						unset($_SESSION['SESS_LAST_NAME']);
						unset($_SESSION['ROLES']);
	
		    			header("location: system-login-timeout.php");
		    		}
		    	}
	    	}
	    	
	   		$_SESSION['lastconnectiontime'] = time();
	    }
	}
	
	class SessionManagerClass {
		public static function initialise() {
			//Start session
			session_start();
			
			error_reporting(E_ALL ^ E_DEPRECATED);
	
			
			define('DB_HOST', '10.246.16.31');
		    define('DB_USER', 'denbyequestrian@phpmyadmin1.one.com');
		    define('DB_PASSWORD', 'Jasmin717440');
		    define('DB_DATABASE', 'denbyequestrian');
		    
		    $_SESSION['pagename'] = substr($_SERVER["PHP_SELF"], strripos($_SERVER["PHP_SELF"], "/") + 1);
		    
		    BreadCrumbManager::initialise();
		    
		    self::initialiseDB();
			self::initialisePageData();

			BreadCrumbManager::calculate();
		}
		
	    public static function initialiseDB() {
			//Connect to mysql server
			$link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
			
			if (!$link) {
				die('Failed to connect to server: ' . mysql_error());
			}
			
			if (! isset($_SESSION['ROLES'])) {
				$_SESSION['ROLES'] = array();
				$_SESSION['ROLES'][0] = "PUBLIC";
			}
			
			//Select database
			$db = mysql_select_db(DB_DATABASE);
			
			if(!$db) {
				die("Unable to select database");
			}
			
	    }
	    
        function initialisePageData() {
			$qry = "SELECT DISTINCT A.* FROM pages A " .
					"INNER JOIN pageroles B " .
					"ON B.pageid = A.pageid " .
					"WHERE A.pagename = '" . $_SESSION['pagename'] . "' " .
					"AND B.roleid IN (" . ArrayToInClause($_SESSION['ROLES']) . ")";
			$result = mysql_query($qry);
			
			//Check whether the query was successful or not
			if ($result) {
				if (mysql_num_rows($result) == 1) {
					$member = mysql_fetch_assoc($result);
					
					$_SESSION['pageid'] = $member['pageid'];
					$_SESSION['title'] = $member['label'];
					
				} else {
	    			header("location: system-access-denied.php");
				}
			}
	    }
	    
	}
	
    SessionManagerClass::initialise();
    
    function isUserInRole($roleid) {
    	if (! isAuthenticated()) {
    		return false;
    	}
    	
    	for ($i = 0; $i < count($_SESSION['ROLES']); $i++) {
    		if ($roleid == $_SESSION['ROLES'][$i]) {
    			return true;
    		}
    	}
		
		return false;
    }

	function isAuthenticated() {
		return ! (!isset($_SESSION['SESS_MEMBER_ID']) || (trim($_SESSION['SESS_MEMBER_ID']) == ''));
	}
	
	function showErrors() {
		if( isset($_SESSION['ERRMSG_ARR']) && is_array($_SESSION['ERRMSG_ARR']) && count($_SESSION['ERRMSG_ARR']) >0 ) {
			echo '<ul class="err">';
			foreach($_SESSION['ERRMSG_ARR'] as $msg) {
				echo '<li>',$msg,'</li>'; 
			}
			echo '</ul>';
			unset($_SESSION['ERRMSG_ARR']);
		}
	}
	
    
    function showMenu() {
    	nestPages($_SESSION['pageid'], 1);
    }
    
    function nestPages($id, $level) {
		$qry = "SELECT DISTINCT D.pagename AS parentpagename, A.*, B.* FROM pagenavigation A " .
				"INNER JOIN pages B " .
				"ON A.childpageid = B.pageid " .
				"INNER JOIN pageroles C " .
				"ON C.pageid = B.pageid " .
				"INNER JOIN pages D " .
				"ON D.pageid = A.pageid " .
				"WHERE A.pageid = " . $id . " " .
				"AND C.roleid IN (" . ArrayToInClause($_SESSION['ROLES']) . ")" .
				"ORDER BY A.sequence";
		$result=mysql_query($qry);

		//Check whether the query was successful or not
		if($result) {
			if (mysql_num_rows($result) == 0) {
				if ($level == 2) {
					nestPages(1, 3);
					
				} else {
					if (isset($_SESSION['parentpageid'])) {
						nestPages($_SESSION['parentpageid'], 2);
					}
				}
				
			} else {
				echo "<ul>";
		
				/* Show children. */
				while (($member = mysql_fetch_assoc($result))) {
					$_SESSION['parentpagename'] = $member['parentpagename'];
					
					if ($member['pagename'] == $_SESSION['pagename']) {
						echo "<li class='selected'>" ;
						
					} else {
						echo "<li>";
					}
					
					echo "<a href='" . $member['pagename'] . "'>" . $member['label'] . "</a></li>";
				}
		
				echo "</ul>";
				
				$_SESSION['parentpageid'] = $id;
			}
		}
    }
	
	function ArrayToString($arr) {
		$count = count($arr);
		$str = "[";
		
		for ($i = 0; $i < $count; $i++) {
			if ($i > 0) {
				$str = $str . ", ";
			}
			
			$str = $str . "\"" . $arr[$i] . "\"";
		}
		
		$str = $str . "]";
		
		return $str;
	}
	
	function ArrayToInClause($arr) {
		$count = count($arr);
		$str = "";
		
		for ($i = 0; $i < $count; $i++) {
			if ($i > 0) {
				$str = $str . ", ";
			}
			
			$str = $str . "\"" . $arr[$i] . "\"";
		}
		
		return $str;
	}
	
	function escapeQuote($stringLiteral) {
		$searches = array( "'", "\n", "\r" );                 
		$replacements = array( "&apos;", "", "");
		
		return str_replace( $searches, $replacements, $stringLiteral ); 
	}
	
	function getPageRoles($pageName) {
	}

	function hotspot($hotspotid, $hotspotname, $roleid, $publishroleid, $overridefile) {
		//Array to store validation errors
		$errmsg_arr = array();
		$sizeManually = false;
		
		//Validation error flag
		$errflag = false;
		
		if (isset($_SESSION['SESS_MEMBER_ID'])) {
			$createdby = $_SESSION['SESS_MEMBER_ID'];
	
		} else {
			$createdby = "";
		}

		if (isset($overridefile) && $overridefile != "") {
			$filename = "HS_" . $overridefile . "_" . $hotspotid;
			
		} else {
			$filename = "HS_" . $_SESSION['title'] . "_" . $hotspotid;
		}

		echo "<div hotspotid='" . $hotspotid . "' file='" . $filename . "' role='" . $roleid . "' publishrole='" . $roleid . "' hotspotname='" . $hotspotname . "' class='hotspot'>";
		
		/* Look for current pending versions. */
		$qry = "SELECT B.image FROM documents A " .
				"INNER JOIN documentversions B " .
				"ON  B.documentid = A.documentid " .
				"WHERE A.filename='$filename' " .
				"AND B.status = 'P' " .
				"AND B.createdby = '$createdby'";
		$result = mysql_query($qry);
		
		//Check whether the query was successful or not
		if($result) {
			if(mysql_num_rows($result) == 1) {
				//Login Successful
				$member = mysql_fetch_assoc($result);
		
				echo $member['image'];
				
			} else {
				/* Look for live versions. */
				$qry = "SELECT B.image FROM documents A " .
						"INNER JOIN documentversions B " .
						"ON B.documentversionid = A.documentversionid " .
						"AND B.documentid = A.documentid " .
						"WHERE A.filename='$filename'";
				$result=mysql_query($qry);
				
				//Check whether the query was successful or not
				if($result) {
					if(mysql_num_rows($result) == 1) {
						//Login Successful
						$member = mysql_fetch_assoc($result);
				
						echo $member['image'];
			
					} else {
						$sizeManually = true;
					}
				}
			}
		}
		
		echo "</div>";
		
		if ($sizeManually && isAuthenticated()) {
			echo "<script>";
			echo "$('[hotspotid= \"$hotspotid\"]').css('height', '100px');";
			echo "</script>";
		}
	}
?>
