<?php
require "vendor/autoload.php";

$start = microtime(true);

$runSamMo = new Sammedia\Sammo();
$stats    = $runSamMo->stats();

echo $stats."\n";
echo '{"status": "ok"}'."\n";

$time_elapsed_secs = microtime(true) - $start;
echo "Elapsed Time: ".$time_elapsed_secs."\n";

?>