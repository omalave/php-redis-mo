<?php
require "vendor/autoload.php";

$runSamMo = new Sammedia\Sammo();

$token    = $runSamMo->getAuthToken($_REQUEST);

$runSamMo->save($_REQUEST['msisdn'], $_REQUEST['operatorid'], $_REQUEST['shortcodeid'], $_REQUEST['text'], $token);

?>