<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once("dbinfo.php");

$bookarr=isset($_GET["book"])?$_GET["book"]:"";// this is an array [id, name]
$bookdata = explode('/',$bookarr);

$book=str_replace(" ", "%20", $bookdata[0]);//need to replace any spaces with %20 for URL
$bookid=$bookdata[1];

$startdate=isset($_GET["startdate"])?$_GET["startdate"]:0;
$enddate=isset($_GET["enddate"])?$_GET["enddate"]:0;
$operation=isset($_GET["operation"])?$_GET["operation"]:"";
$numrecords=isset($_GET["numrecords"])?$_GET["numrecords"]:0;
//$showverses=isset($_GET["showverses"])?$_GET["showverses"]:9;
//$showpercent=isset($_GET["showpercent"])?$_GET["showpercent"]:0;

        $subject = "%20and%20srw.su%20all%20%22Bible.%20--%20" . $book . "%20--%20Commentaries.";
        //$subject = "%20and%20srw.su%20all%20%22Moby%20Dick%20(Melville,%20Herman)%20";
        $maxrecordnum=min($numrecords,100);
        $maxrecords = "&maximumRecords=" . $maxrecordnum;  //we know the OCLC API will only return 100 records at a time
        //$dateparam="srw.yr%20%3C%20" . $enddate . "%20and%20srw.yr%20%3E%20" . $startdate;
        $dateparam="srw.yr+%3C+" . $enddate . "+and+srw.yr+%3E+" . $startdate;
        $sortparam= "&sortKeys=Date";
        //$countparam="&count=5";
        
        $numinserted=0;
        $key="&wskey=trRhzr2YMBzgeLuRBHhodKrOmDvdwK6cAm0FFHdio54OY88cQ8gPbymtRE0SMAoMtD8dYZZ4G2IhRqf4";    //mykey
        //$key="&wskey=a1vkxyTxLTHhknwO9VXF9BNiI8bZ6R9BzzHMTR0Qn4XZ67BLSQuTEmGyIa4wGnqE07TexyzGjlsPQYxd";//emory key

for($startnum=1; $startnum<$numrecords; $startnum+=100)
{       
$startparam="&startRecord=" . $startnum;
$maxrecordnum=min(($numrecords-$startnum),100);
$maxrecords = "&maximumRecords=" . $maxrecordnum;
$url="http://www.worldcat.org/webservices/catalog/search/worldcat/sru?query=" .  $dateparam . $subject . $maxrecords . $startparam .$sortparam .  $key;
        
if($operation=="url") {
	echo "The API URL I would call is " . $url ." <br /><br /><hr />";
}
else {
//Ok, now we're ready to load some XML and play with it
//The key here is that max records (default 100) and the number of records we requested. 
//We're going to have to iterate on this until we reach numrecords, moving $maxrecords at a time

$xmlstr= simplexml_load_file($url);

$numrecords=min($numrecords,$xmlstr->numberOfRecords);
echo "The number of records the API returned as MARCXML is " . $xmlstr->numberOfRecords . " (and I am going to try to process " . ($numrecords-$startnum+1) . ")<br /><br /><hr />";


        if($operation=="xml") {
        var_dump($xmlstr);
        	//$xml=simplexml_load_string($url);
			//print_r($xml);
        	//$xml = new SimpleXMLElement($xmlstr);
			//echo $xmlstr->asXML();
        }
        
        
echo "<br /><br /><hr />";

//var_dump($xmlstr);
		$dbinsertstring="insert into CommsMetadata (OCLCNum, BiblicalBooksID,AuthorName, AuthorBirthDate, PubDate, PubLocation, NumPages) VALUES ";
		$prepareddbstring=$dbinsertstring ." (:oclc, :biblicalbooksid, :author, :birthdate, :pubdate, :location, :pages)";
		$biblicalbookinsertstring="insert into BiblicalBookCommentary (OCLCNum, BiblicalBooksID) VALUES ";
        $retjson="{'commentaries': {";
        
        $allrecords=$xmlstr->records; //this gets you all the children
        $numbday=0;
        $biblicalbookdbstr="'" . $bookid . "',"; //so I can add the biblical book into the db foreach record
        $numberprocessed=0;
        $numinserted=0;
        foreach($allrecords->children() as $record){
        	
        	$rdata= $record->recordData;
        	foreach($rdata->children() as $realrecord){
        		if($numberprocessed>=$numrecords) {break;}//break if we've reached the number we requested
        		$retjson .= " <br />'record': {";
        		$recordstr="";
        		$recorddbstr="";
        		$oclc='';
        		//Now search the control fileds for the oclc data
        		$oclcstr="('',";
        		foreach($realrecord->controlfield as $control)
        		{
        			if($control["tag"]=="001") {
        				$recordstr.="'oclc' : " . $control . ", ";
        				$oclcstr="('" . $control . "', ";
        				$oclc=$control;
        				$biblicalbookinsertstring.="('" . $control . "', " . $bookid . "),";
        			}
        		}
        		
        		$authordbstr="'',";
        		$birthdatedbstr="'',";
        		$pubdbstr="'',''),";
        		$datedbstr="'',";
        		$locationdbstr="'')";
        		$publocation='';
        		$author='';
        		$birthdate='';
        		$location='';
        		$pages='';
        		$date='';
        		foreach($realrecord->datafield as $data){
        			$publocation="''),";
        			$pubdate="'',";
        			switch($data["tag"]) {
        			case "100" :
        				/*$recordstr .= "'author': ". $data->subfield . ",";
        				$authordbstr="'" . $data->subfield . "', ";
        				$author=$data->subfield;*/
        				foreach($data->subfield as $sub) {
        					if($sub["code"]=="a") {
        						$recordstr .= "'author': ". $sub . ",";
        						$authordbstr="'" . $sub . "', ";
        						$author=$sub;
        						
        					}
        					if($sub["code"]=="d") {
        						$birthdatedbstr="'" . $sub ."', ";
        						$birthdate=normalizeDate($sub);
        						$numbday++;
        						$recordstr .="'birthdate': " . $sub . ",";
        					}
        				}
        				break;
        			case "260" :
        				$publocation="''),";
        				$pubdate="'',";
        				foreach($data->subfield as $sub) {
        					if($sub["code"]=="c") {
        						$date=normalizeDate($sub);
        						if(($date >= $startdate) && ($date <= $enddate)) {
        							$normdate=normalizeDate($sub, $startdate, $enddate);
        							$recordstr .="'pubdate': " . $normdate;
        							$pubdate = "'" . $normdate . "',";
        							$date=$normdate;
        							//echo "date is " . normalizeDate($sub, $startdate, $enddate)		."<br />"; 
        						}
        						else $recordstr=0;
        					}
        					if($sub["code"]=="a") {
        						$publocation= "'" . removeNonAN($sub) . "'),";
        						$location=removeNonAN($sub);
        						$recordstr .="'location': " . $location;
        					}
        				}
        				$pubdbstr = $pubdate . $publocation;
        				//echo "stting pub string to be " . $pubdbstr;
        				break;
        				case "300" :
        				foreach($data->subfield as $sub) {
        					if($sub["code"]=="a") {
        						$pages=justNumbers($sub);
        						$recordstr .="'pages': " . $pages;
        					}
        					
        				}
        				break;
        			}
        			
        		}
        		//bind the parameters for the insert and execute :biblicalbooksid, :author, :birthdate, :pubdate, :location
        		//$prepareddbstring="insert into CommsMetadata (OCLCNum, BiblicalBooksID,AuthorName, AuthorBirthDate, PubDate, PubLocation Pages) VALUES (:oclc, :biblicalbooksid, :author, :pubdate, :location, :pages)";
				$dbinsertstring.="('" . $oclc . "','" . $bookid . "','" . $author . "','" . $birthdate . "','" . $date . "','" . $location . "','" . $pages . "'),"; //This is simply for display purposes
        		if($operation=="insert") {
        			$dbinsert=$db->prepare($prepareddbstring);
        			$dbinsert->bindParam(':oclc', $oclc);
        			$dbinsert->bindParam(':biblicalbooksid', $bookid);
        			$dbinsert->bindParam(':author', $author);
        			$dbinsert->bindParam(':birthdate', $birthdate);
        			$dbinsert->bindParam(':pubdate', $date);
        			$dbinsert->bindParam(':location', $location);
        			$dbinsert->bindParam(':pages', $pages);
        			$dbinsert->execute();
        			$numinserted++;
        		}
        		//$dbstring .= $oclcstr . $biblicalbookdbstr . $authordbstr . $pubdbstr;
        		$retjson .=$recordstr;
        		$retjson .="},";
        		$numberprocessed++;
        		
        	}
       	}
       	$retjson .="}";
       
       	if($operation=="query") {echo "Insert Query is ". $dbinsertstring . "<br /><br /><hr />";}
       	if($operation=="json") {echo $retjson . "<br /><br /><hr />";}
		if($operation=="insert") {echo "Successfully inserted <b>" . $numinserted . " rows</b> into the database!";}
}
}
//echo "numbday is " . $numbday;
function normalizeDate($date) {
	preg_match_all('!\d+!', trim($date), $retdate);
	$adate=(int)implode('',$retdate[0]);
	/*if(is_numeric(trim($date))) {return trim($date);}
	else {
		preg_match_all('!\d+!', trim($date), $retdate);
		return (int)implode('',$retdate[0]);
		//return $retdate[0];
		//return "it is " . $date;
	}	*/
	return substr($adate, 0, 4);
	//return $adate;
}	

function justNumbers($thestr) {
	//$str=trim($str);
	//preg_replace("/[^0-9 ]/", '', $thestr);
	$numbers=preg_replace("/[^0-9 ]/", '', $thestr);
	$pattern = " ";
	return strstr($numbers, $pattern, true);
	//return preg_replace("/[^0-9 ]/", '', $thestr);
}

function removeNonAN($str) {
	$str=trim($str);
	preg_replace("/[^A-Za-z0-9 ]/", '', $str);
	return $str;
}
	

?>
