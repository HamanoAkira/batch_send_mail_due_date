<?php

$deals = [
  ['id' => 4, 'due_date' => '2022-08-02'],
  ['id' => 4, 'due_date' => '2022-08-03'],
  ['id' => 4, 'due_date' => '2022-08-04'],
  ['id' => 5, 'due_date' => '2022-08-05'],
  ['id' => 6, 'due_date' => '2022-08-06'],
  ['id' => 7, 'due_date' => '2022-08-07'],
  ['id' => 8, 'due_date' => '2022-08-08'],
  ['id' => 9, 'due_date' => '2022-08-08'],
  ['id' => 10, 'due_date' => '2022-08-09'],
  ['id' => 11, 'due_date' => '2022-08-11'],
  ['id' => 12, 'due_date' => '2022-08-12'],
  ['id' => 13, 'due_date' => '2022-08-13'],
];

$holiday = [
  '2022-08-06',
  '2022-08-07',
  '2022-08-11',
  '2022-08-13',
  '2022-08-14',
];

$holiday = [
  '2022-07-30',
  '2022-07-31',
  '2022-08-02',
  '2022-08-03',
  '2022-08-04',
  '2022-08-05',
  '2022-08-06',
  '2022-08-07',
  '2022-08-08',
  '2022-08-09',
  '2022-08-10',
  '2022-08-11',
  '2022-08-12',
  '2022-08-13',
  '2022-08-14',
  '2022-08-15',
];

$deals = [
  ['id' => 7, 'due_date' => '2022-08-07'],
  ['id' => 8, 'due_date' => '2022-08-08'],
  ['id' => 9, 'due_date' => '2022-08-09'],
];

//function addBusinessDay(string $day, int $number, array $holiday, array $deals): string
//{
//  $currentDay = (new DateTimeImmutable($day));
//  $dueDate = $currentDay->modify("+1 days");
//  $listDealDate = array_column($deals, 'due_date');
//  $intervalDate = 0;
//  $listDeal = [];
//  
//  while ($intervalDate < $number) {
//    if (!in_array($dueDate->format('Y-m-d'), $holiday)) {
//      $intervalDate++;
//    }
//    $dueDate = $dueDate->modify("+1 days");
//  }
//  
//  $listDeal[] = $dueDate->modify("-1 days")->format('Y-m-d');
//  
//  while (in_array($dueDate->format('Y-m-d'), $holiday)) {
//    $listDeal[] = $dueDate->format('Y-m-d');
//    $dueDate = $dueDate->modify("+1 days");
//  }
//  
//  $result = array_intersect($listDealDate, $listDeal);
//  return $currentDay->format('Y-m-d') . ' => deals: ' . implode(', ', $result);
//}

function addBusinessDay2(string $day, int $number, array $holiday, array $deals): string
{
  $currentDay = (new DateTimeImmutable($day));
  $dueDate = $currentDay->modify("+1 days");
  $listDealDate = array_column($deals, 'due_date');
  $intervalDate = 0;
  $listDeal = [];
  
  while ($intervalDate < $number || in_array($dueDate->format('Y-m-d'), $holiday)) {
    if ($intervalDate < $number) {
      $intervalDate = $intervalDate + (in_array($dueDate->format('Y-m-d'), $holiday) ? 0 : 1);
    } else {
      $listDeal[] = $dueDate->modify("-1 days")->format('Y-m-d');
    }
    $dueDate = $dueDate->modify("+1 days");
  }

  $listDeal[] = $dueDate->modify("-1 days")->format('Y-m-d');
  $result = array_intersect($listDealDate, $listDeal);
  return $currentDay->format('Y-m-d') . ' => deals: ' . implode(', ', $result);
}

$dayString = $argv[1];
$numberString = $argv[2];

echo addBusinessDay2($dayString, $numberString, $holiday, $deals);

