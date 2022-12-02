<?php declare(strict_types=1);

class Batch
{
  private string $dayString;
  private string $numberString;
  private string | null $dealTableFile;
  private string | null $holidayFile;
  
  public DateTime $day;
  public int $number;

  /**
   * This array simulates deals table from database
   * 
   * @var array|array[] 
   */
  public array $deals = [
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

  /**
   * list of holiday, not including saturday and sunday
   * 
   * @var array|string[] 
   */
  public array $holiday = ['2022-08-11'];

  public function __construct(
    string $day,
    string $number,
    string $dealTableFile = null,
    string $holidayFile = null
  )
  {
    $this->dayString = $day;
    $this->numberString = $number;
    $this->dealTableFile = $dealTableFile;
    $this->holidayFile = $holidayFile;
  }

  private function validateInput(): string
  {
    if ($this->dayString === '') {
      return 'Invalid date!';
    }

    try {
      $day = new DateTime($this->dayString);
    } catch (\Exception $e) {
      return 'Invalid date!';
    }

    $number = intval($this->numberString);

    if ($number != $this->numberString || strlen((string)$number) !== strlen($this->numberString) || $number < 1) {
      return 'Invalid number!';
    }

    
    $this->day = $day;
    $this->number = $number;

    if ($this->dealTableFile) {
      if (!file_exists($this->dealTableFile)) {
        return 'Cannot find deal table file!';
      }

      include $this->dealTableFile;
      $this->deals = $deals;
    }

    if ($this->holidayFile) {
      if (!file_exists($this->holidayFile)) {
        return 'Cannot find list holiday table file!';
      }

      include $this->holidayFile;
      $this->holiday = $holiday;
    }

    return '';
  }

  private function isHoliday(DateTime $date): bool
  {
    $day = $date->format('l');

    if ($day === 'Saturday' || $day === 'Sunday') {
      return true;
    }

    $key = array_search($date->format('Y-m-d'), $this->holiday);

    return $key !== false;
  }

//  private function isDealExist(string $date): bool
//  {
//    $key = array_search($date, array_column($this->deals, 'due_date'));
//    return $key !== false;
//  }

  private function getDeals(string $date): array
  {
    $listDeal = [];
    
    foreach ($this->deals as $deal) {
      if ($deal['due_date'] === $date) {
        $listDeal[] = $date;
      }
    }
    
    return $listDeal;
  }

  function getDealList(DateTime $day, int $number = 3): string | array
  {
    $intervalDate = 0;
    $listDeal = [];

    if ($this->isHoliday($day)) {
      return '';
    }

    $dueDate = $day->modify("+1 days");

    // get date of deal
    while ($intervalDate < $number) {
      if (!$this->isHoliday($dueDate)) {
        $intervalDate++;
        if ($intervalDate === $number) {
          break;
        }
      }
      $dueDate = $dueDate->modify("+1 days");
    }
    
    $deals = $this->getDeals($dueDate->format('Y-m-d'));

    if (count($deals)) {
      $listDeal = array_merge($listDeal, $deals);
    }

    $nextDueDate = $dueDate->modify("+1 days");

    // get deals that are
    while ($this->isHoliday($nextDueDate)) {
      $nextDue = $this->getDeals($nextDueDate->format('Y-m-d'));
      if (count($nextDue)) {
        $listDeal = array_merge($listDeal, $nextDue);
      }
      $nextDueDate = $nextDueDate->modify("+1 days");
    }

    return $listDeal;
  }

  public function addBusinessDay(): string
  {
    $errMessage = $this->validateInput();

    if ($errMessage) {
      return $errMessage;
    }
    
    $currentDay = new DateTime($this->day->format('Y-m-d'));
    $result = $this->getDealList($currentDay, $this->number);

    if (is_array($result)) {
      return $this->day->format('Y-m-d') . ' => deals: ' . implode(', ', $result);
    } else {
      return $result ? $this->day->format('Y-m-d') . ': ' . $result : '';
    }
  }
}
