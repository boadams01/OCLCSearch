<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script>



<?php
require_once("dbinfo.php");
?>
$(document).ready(function(){
	$("#searchoclc").click(function(){
		$("#resultstats").empty();
     showdata();
     })
     });
       
       
       
       function showdata() {
         	var startdate=$("#startdate").val();
      		var enddate=$("#enddate").val();
           	var book=$("#book").val();
           	var numrecords=$("#numrecords").val();
           	var operation=$("#operation").val();
           
            var jsonData = $.ajax({
                url: "graboclcdata.php",
                dataType: "json",
                type: 'GET',
                data: { book: book, enddate: enddate, startdate: startdate, numrecords: numrecords, operation: operation} ,
                async: false
            }).responseText;  
           // alert(jsonData);  
            
            $("#resultstats").append(jsonData);                 
            }        /*
                    var pubyear="";
                    var printit=0;
                    $(oclcxml).find("record").each(function() { 
                    	$(this).find("datafield").each(function() {
                    
                    		if($(this).attr("tag")=="260") {
                    			$(this).find("subfield").each(function() {
                    				if($(this).attr("code")=="c") {
                    					pubyear=$(this).text();
                    					if((pubyear>=startdate) && (pubyear<=enddate)) {
                    						$("#resultstats").append("Publication Year: ");
                    						$("#resultstats").append($(this).text());
                    						$("#resultstats").append("<br />");
                    						printit=1;
                    					}
                    					else {printit=0}
                    				}
                    			});
                    			if(printit==1) {
                    				$("#resultstats").append("Publication Info: ");
                    				$("#resultstats").append(this);
                    				$("#resultstats").append("<br />");
                    			}
                    		}
                    		if($(this).attr("tag")=="100" && printit==1) {
                    			$("#resultstats").append("Author: ");
                    			$("#resultstats").append(this);
                    			$("#resultstats").append("<br />");
                    		} 
                    		});
                     
                   
                    });                                     
        */


</script>


      <p class="contact-input">

Operation: <select id="operation" name="subject">
<option value="0">Please select...</option>
<option value="url">Show API URL</option>
<option value="xml">Show Raw MARCXML</option>
<option value="json">Show JSON</option>
<option value="query">Show SQL Insert Query</option>
<option value="insert">Execute SQL Insert Query</option>
</select>
</label>
<br />
</p>

<p class="contact-input">Number of Records: <input id="numrecords"></p>
<p class="contact-input">Start Date: <input id="startdate" /></p>
<p class="contact-input">End Date: <input id="enddate" /></p>
<p class="contact-input">New Testament Book: 

<select name="book" id="book">
<option value="0">Please select...</option>
<?php

$commselect="SELECT BiblicalBooksID, DisplayName, LCName FROM BiblicalBooks order by BiblicalBooksID asc;";
$commstmt = $db->query($commselect);
while ($row = $commstmt->fetch(PDO::FETCH_ASSOC)) {
	echo "<option value=\"";
	echo $row["LCName"];
	echo "/";
	echo $row["BiblicalBooksID"];
	echo "\">";
	echo $row["DisplayName"];
	echo "</option>";
}

?></select></p>


<input type="submit" id="searchoclc" name="searchoclc" />

<div id="resultstats"></div>

