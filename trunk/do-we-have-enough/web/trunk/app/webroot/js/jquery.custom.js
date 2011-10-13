$(document).ready(function(){
   $("#options").slideUp(0);
   $("#details").slideUp(0);
   $("a#btn-options").toggle( function() {
   	if($("#options").is(":hidden")) {
   		$("#details").slideUp(500,function() {
		$("#options").slideDown(500);
		});
	}else{
		$("#options").slideUp(500);
	}
   },function() {
   	if($("#options").is(":hidden")) {
   		$("#details").slideUp(500,function() {
		$("#options").slideDown(500);
		});
	}else{
		$("#options").slideUp(500);
	}
   });
   $("a#btn-details").toggle( function() {
   	if($("#details").is(":hidden")) {
   		$("#options").slideUp(500,function() {
		$("#details").slideDown(500);
		});
	}else{
		$("#details").slideUp(500);
	}
   },function() {
   	if($("#details").is(":hidden")) {
   		$("#options").slideUp(500,function() {
		$("#details").slideDown(500);
		});
	}else{
		$("#details").slideUp(500);
	}
   });
   $("#additional-options").slideUp(0);
   $("a#btn-additional-options").toggle( function() {
		$("#additional-options").slideDown(500);
   },function() {
		$("#additional-options").slideUp(500);
   });
   $("#event-details").slideUp(0);
   $("a#btn-event-details").toggle( function() {
		$("#event-details").slideDown(500);
   },function() {
		$("#event-details").slideUp(500);
   });
   $("#invited").slideUp(0);
   $("a#btn-invited").toggle( function() {
		$("#invited").slideDown(500);
   },function() {
		$("#invited").slideUp(500);
   });
   $(".event input").blur( function() {
		$("div.tooltip").remove();
		$("div.side_tooltip").remove();
   });
   $(".event textarea").blur( function() {
		$("div.tooltip").remove();
   });
   $("#GroupList").keypress( function() {
		$("#GroupList").addClass("tall-glass-of-water");
   });
});

function the_tooltip(the_object,the_content,the_id)
{
	$("div#error"+the_id).remove();
	$(the_object).after("<div class='tooltip' id='"+the_id+"'>"+the_content+"</div>");
}
function the_side_tooltip(the_object,the_content,the_id)
{
	$(the_object).after("<div class='side_tooltip' id='"+the_id+"'>"+the_content+"</div>");
}