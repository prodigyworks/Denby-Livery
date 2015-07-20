<?php
	$hideTitle = true;
	include("system-header.php"); 
?>

<!--  Start of content -->
    <div id="templatemo_content_left">
    
    	<div id="news_column_section">
        	
            <div id="news_column_section_title">
    			<div>Denby Equestrian News</div>
    		</div>
    		
    		<?php hotspot(1, "Denby Equestrian News", "ADMIN", "ADMIN", ""); ?>
	   	</div>
    	
        
    </div> <!-- end of content left -->
    
    <div id="templatemo_content_right">
    
    	<div class="right_column_section">
        	
            <div class="right_column_section_title">
                Welcome to Denby Equestrian.
            </div>
            <div class="right_column_section_body">            	
                <div class="image_box">
                    <img src="images/Denby Equest view from drive.JPG" width=100 height=100 alt="Denby Equestrian" />
                </div>
                <div class="post_body">
                    <div class="posted_by"> (formerly PJ Livery Stables) located in Denby Village</div>
                    <div>
                    	<?php hotspot(2, "Welcome Message", "ADMIN", "ADMIN", ""); ?>
                    </div>
                </div>
                <div class="cleaner">&nbsp;</div>
			</div>           	
		</div>
        
        <div class="right_column_section">
        	
            <div class="right_column_section_title">What our clients say.</div>
	          <div class="right_column_section_body">            	
                <div class="image_box">
                    <img src="images/speech.gif" width=100 height=100 alt="Denby Equestrian" />
                </div>
                <div class="post_body">
                    <span class="posted_by">
                    	<?php hotspot(4, "Tribute Title", "ADMIN", "ADMIN", ""); ?>
                    </span>
                    <?php hotspot(3, "Tribute", "ADMIN", "ADMIN", ""); ?>
                </div>
                <div class="cleaner">&nbsp;</div>
			</div>           	
		</div>
        
        <div class="cleaner_with_height">&nbsp;</div>
        
    </div> <!-- end of content right -->

</div> <!-- end of content section -->
<!--  End of content -->
<?php include("system-footer.php"); ?>