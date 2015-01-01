
jQuery(document).ready(function($) {

	$("#save").hide();
	$("#change_order").hide();
	$("#slide1").hide();
	
	$("#uploadImage").click(function(event){
		$("#slide1").click();
	});
	
	$("#slide1").change(function(e){
		$in=$(this);
		var fileName = $in.val();
		fileName = fileName.split('\\');
		$("#imageSrc").html(fileName[fileName.length - 1]);
		
	});


	
	$("#change_order").click(function(event){
		$("#table").toggleClass("connectedSortable");
		 $( ".connectedSortable" ).sortable({
			connectWith: ".connectedSortable"
		}).disableSelection();
		$("#change_order").hide();
		$("#save").show();
	});
	
	 
	$("#table").toggleClass("connectedSortable");
	
	$( "#table" ).sortable({
		opacity: 0.5
	});
	
	$( "#table" ).sortable({
		change: function( event, ui ) {
			$("#save").show();
		}
	});


	$("#save").click(function(event) {
		// List images
		var order = $(".table").sortable("serialize");
		order = order + "&action=rpslideshow_ajax_update_order";
		//alert(order);
		$(".rp_loader").show();
		$.ajax({
			url : ajaxurl, 
			type : "POST", 
			data : order,			
			success : function(data) 
			{
				//$("#table").append(data);
				loadImageList();
				$(".rp_loader").hide();
				$(".rp_msgBox").html("SlideShow Order Save Successfully...!!!");
				$(".rp_msgBox").fadeIn(1000).delay(2000).fadeOut();
				$("#save").hide();
			}
		});
		
	});  
	

	// Add Images 
	$("#form1").submit(function(event) {
		$(".rp_loader").show();
		$.ajax({
			url : ajaxurl, 
			type : "POST", 
			data : new FormData(this),
			contentType : false,
			cache : false,
			processData : false, 
			success : function(data) 
			{
				$("#form1")[0].reset();
				$(".rp_loader").hide();
				loadImageList();
				if(data == "0"){
					$(".rp_msgBox").html("Slide Added Successfully...!!!");
				}else if(data == "1"){
					$(".rp_msgBox").html("There is some problem in uploading. Please Try again.");
				}else if(data == "2"){
					$(".rp_msgBox").html("This image Cannot Upload. (minimum width=640 and minimum height=340 )");
				}
				$(".rp_msgBox").fadeIn(1000).delay(2000).fadeOut();				
				$("#imageSrc").html("No Image Is Selected");
				$("#form1").reset();
			}
		});
		return false;
	});
	loadImageList();
	
	
});

function loadImageList(){
		$(".rp_loader").show();
		$.ajax({
			url : ajaxurl, 
			type : "POST", 
			data : { action: 'rpslideshow_ajax_images_list'},			
			success : function(data) 
			{
				$("#table").html(data);
				$(".rp_loader").hide();
			}
		});
}

function deleteImage(post_id){
		
		$("#ID_"+post_id).fadeOut();
		
		$.ajax({
			url : ajaxurl, 
			type : "POST", 
			data : { action: 'rpslideshow_delete_image',id: post_id},			
			success : function(data) 
			{
				//loadImageList();
				$( ".selector" ).sortable( "refresh" );
			}
		});
}
