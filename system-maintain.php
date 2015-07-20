<script type="text/javascript" src="script/tiny_mce/jquery.tinymce.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$('textarea.tinymce').tinymce({
			// Location of TinyMCE script
			script_url : 'script/tiny_mce/tiny_mce.js',

			// General options
			theme : "advanced",
			plugins : "autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,advlist",

			// Theme options
			theme_advanced_buttons1 : "save,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
			theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
			theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
			theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak",
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
</script>

<div id="htmlEditor" class="modal" style="height:500px; width:775px">
<form id="hotspotForm" name="hotspotForm" method="post" action="">
	
 	<input type="hidden" id="saveHotSpot" name="saveHotSpot" value="false" />
 	<input type="hidden" id="hotspotFile" name="hotspotFile" value="" />
 	<input type="hidden" id="hotSpotValue" name="hotSpotValue" value="false" />
 	<input type="hidden" id="hotspotID" name="hotspotID" value="false" />
 	<input type="hidden" id="hotspotDescription" name="hotspotDescription" value="" />
	<!-- Gets replaced with TinyMCE, remember HTML in a textarea should be encoded -->
		<textarea id="elm1" name="elm1" rows="15" cols="80" style="height:500px;width: 775px" class="tinymce">
		</textarea>
</form>
</div>

<script>
	$(document).ready(function() {
		$( "#htmlEditor" ).dialog({
				title: "Content Management",
				autoOpen: false,
				width: 800,
				modal: true
			});
		
		$(".hotspot").each(
			function() {
				var roles = <?php echo ArrayToString($_SESSION['ROLES']); ?>;
				var role = $(this).attr("role");
				var file = $(this).attr("file");
				var i;
				var isOk = false;
				
				for (i = 0; i < roles.length; i++) {
					if (role == roles[i]) {
						isOk = true;
						break;
					}
				}
					
				if (isOk) {
					$(this).css("border", "4px dotted red");
					$(this).css("background-color", "#ffcccc");
					$(this).css("background-image", "url(images/edit.png)");
					$(this).css("padding", "1px");
					$(this).hover(
							function() {
								$(this).attr("title", "Click to edit");
					 			$(this).animate({
									    opacity: 0.25
						 			});
							},
							function() {
					 			$(this).animate({
									    opacity: 1
						 			});
							}
					);
					$(this).click(
							function(e) {        
								$("#saveHotSpot").val("true");
								$("#hotspotFile").val($(this).attr("file"));
								$("#hotspotID").val($(this).attr("hotspotid"));
								$("#hotspotDescription").val($(this).attr("hotspotname"));
								$("#htmlEditor").val($(this).html());
								tinyMCE.activeEditor.setContent($(this).html()); 
								//Cancel the link behavior         
								e.preventDefault();         
								
								$( "#htmlEditor" ).dialog("open");            
							}
						);
				}
			}
		);
	});
	
$(window).scroll(function() {
	var winH = $(window).height();         
	
	$("#htmlEditor").css('top',  winH/2-$("#htmlEditor").height()/2 + $(window).scrollTop());         
});

</script>
