<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
<script>

$(document).ready(function(){
	$("#searchoclc").click(function(){
	$("#resultstats").empty();
       startdate=$("#startdate").val();
       enddate=$("#enddate").val();
           try{
           req = new XMLHttpRequest();
       } catch(err1){
                try{
                        req = new ActiveXObject("Msxm12.XMLHTTP");
                } catch(err2){
                try{
                        req = new ActiveXObject("Microsoft.XMLHTTP");
                } catch(err3){
                        req = false;
                }
       }
                                }
       if(req != false) var xmlhttp = req;
                                
       xmlhttp.onreadystatechange=function(){
            if(xmlhttp.readyState==4){
                        
                    oclcxml=xmlhttp.responseXML;
                    xmlDoc=$.parseXML(oclcxml);
                    $("#resultstats").append("Number of records is " + $(oclcxml).find("record").size());
                    
                    
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

        
            }
        };
        var subject = "srw.su%20all%20" + '%22' + "Galatians--Commentaries" + '%22';
        var maxrecords = "&maximumRecords=300";  
        var sortparam= "&sortKeys=Date";      
        var key="&wskey=a1vkxyTxLTHhknwO9VXF9BNiI8bZ6R9BzzHMTR0Qn4XZ67BLSQuTEmGyIa4wGnqE07TexyzGjlsPQYxd"
        var url="http://www.worldcat.org/webservices/catalog/search/worldcat/sru?query=" + subject + sortparam + maxrecords + key;
        $("#resultstats").append(url);
        xmlhttp.open("GET", url, true);
        xmlhttp.send(null);

});

});
</script>





start date<input id="startdate" /><br />
end date <input id="enddate" /><br />
<input type="submit" id="searchoclc" name="searchoclc" />
<div id="resultstats"></div>
