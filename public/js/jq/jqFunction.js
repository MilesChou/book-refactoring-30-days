// JavaScript Document


//jquery vertical scrollable
$(function() {		
	// initialize scrollable with mousewheel support
	$(".scrollable").scrollable({ vertical: true, mousewheel: true });	
	
	//$(".tableStyle tr:eq(0)").addClass("titleStyle");
	$(".tableStyle").find("tr").filter(":even").not(":eq(0)").addClass("trEven");
	$(".tableStyle").find("tr").filter(":odd").addClass("trOdd");
	
	/* corner */
	$(".fixedBar").corner("5px");
	//$(".classBar").corner("top");
	
});

