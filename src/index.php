<?php declare(strict_types=1);

include __DIR__ . "/Batch.php";

// check command
if (count($argv) < 3) {
  echo "Invalid command! Command should be:\n";
  echo "php index.php <day> <number> <dealFile> <holidayFile>\n";
  echo "- day[required]: day to send mail\n";
  echo "- number[required]: send mail [number] day before due date\n";
  echo "- dealFile[optional]: file containing data of deals\n";
  echo "- holidayFile[optional]: file containing data of holiday\n";
  return;
}

$dayString = $argv[1];
$numberString = $argv[2];
$dealFile = isset($argv[3]) ? __DIR__ . '/data/' . $argv[3] : null;
$holidayFile = isset($argv[4]) ? __DIR__ . '/data/' . $argv[4] :  null;

$batch = new Batch($dayString, $numberString, $dealFile, $holidayFile);
$result = $batch->addBusinessDay();
if ($result) {
  echo $result;
}
