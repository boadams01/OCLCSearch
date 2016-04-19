<?php
    /**
     * Basic "Hello World" type example
     *
     * A new EasyRdf_Graph object is created and then the contents
     * of my FOAF profile is loaded from the web. An EasyRdf_Resource for
     * the primary topic of the document (me, Nicholas Humfrey) is returned
     * and then used to display my name.
     *
     * @package    EasyRdf
     * @copyright  Copyright (c) 2009-2013 Nicholas J Humfrey
     * @license    http://unlicense.org/
     */
    set_include_path(get_include_path() . PATH_SEPARATOR . '../lib/');
    require_once "EasyRdf.php";
?>
<html>
<head>
  <title>Basic FOAF example</title>
</head>
<body>

<?php
  $foaf = EasyRdf_Graph::newAndLoad('http://id.loc.gov/authorities/names/n79123944');
  print "data is " . $foaf;
?>

<p>
  
</p>

</body>
</html>