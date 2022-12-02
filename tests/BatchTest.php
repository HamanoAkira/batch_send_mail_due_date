<?php declare(strict_types=1);

include dirname(__DIR__) . '/src/Batch.php';

use PHPUnit\Framework\TestCase;

final class BatchTest extends TestCase
{
  const DATA_DIR = __DIR__ . '/data/';
  public function testEmptyDate()
  {
    $batch = new Batch('', '3');
    $this->assertEquals('Invalid date!', $batch->addBusinessDay());
  }

  public function testInvalidDate()
  {
    $batch = new Batch('2022-08-08asdf', '3');
    $this->assertEquals('Invalid date!', $batch->addBusinessDay());
  }

  public function testValidDate()
  {
    $batch = new Batch('2022-08-08', '3');
    $this->assertNotEquals('Invalid date!', $batch->addBusinessDay());
  }

  public function testInvalidNumber()
  {
    // alphabet character
    $batch = new Batch('2022-08-08', 'a');
    $this->assertEquals('Invalid number!', $batch->addBusinessDay());

    // number with leading 0
    $batch = new Batch('2022-08-08', '000002');
    $this->assertEquals('Invalid number!', $batch->addBusinessDay());

    // binary number
    $batch = new Batch('2022-08-08', '0b11111111');
    $this->assertEquals('Invalid number!', $batch->addBusinessDay());

    // hex number
    $batch = new Batch('2022-08-08', '0x1A');
    $this->assertEquals('Invalid number!', $batch->addBusinessDay());

    // octal number
    $batch = new Batch('2022-08-08', '0123');
    $this->assertEquals('Invalid number!', $batch->addBusinessDay());

    // negative number
    $batch = new Batch('2022-08-08', '-3');
    $this->assertEquals('Invalid number!', $batch->addBusinessDay());

    // float number
    $batch = new Batch('2022-08-08', '3.1');
    $this->assertEquals('Invalid number!', $batch->addBusinessDay());

    // 0
    $batch = new Batch('2022-08-08', '0');
    $this->assertEquals('Invalid number!', $batch->addBusinessDay());
  }

  public function testValidNumber()
  {
    $batch = new Batch('2022-08-08', '3');
    $this->assertNotEquals('Invalid number!', $batch->addBusinessDay());
  }

  public function testNotFoundDealFile()
  {
    $dealFile = self::DATA_DIR . '_deals.php';
    $batch = new Batch('2022-08-08', '3', $dealFile);
    $this->assertEquals('Cannot find deal table file!', $batch->addBusinessDay());
  }

  public function testValidDealFile()
  {
    $dealFile = self::DATA_DIR . 'deals.php';
    $batch = new Batch('2022-08-08', '3', $dealFile);
    $result = $batch->addBusinessDay();

    $this->assertNotEquals('Cannot find deal table file!', $result);
    $this->assertEquals('2022-08-08 => deals: ', $result);
  }

  public function testNotFoundHolidayFile()
  {
    $holidayFile = self::DATA_DIR . '_holiday.php';
    $batch = new Batch('2022-08-08', '3', null, $holidayFile);
    $this->assertEquals('Cannot find list holiday table file!', $batch->addBusinessDay());
  }

  public function testValidHolidayFile()
  {
    $holidayFile = self::DATA_DIR . 'holiday.php';
    $batch = new Batch('2022-08-05', '3', null, $holidayFile);
    $result = $batch->addBusinessDay();

    $this->assertNotEquals('Cannot find list holiday table file!', $result);
    $this->assertEquals('2022-08-05 => deals: ', $result);
  }
  
  public function testRunBatchOnHoliday()
  {
    $batch = new Batch('2022-08-07', '3');
    $this->assertEquals('2022-08-07: Not working on holiday.', $batch->addBusinessDay());

    $holidayFile = self::DATA_DIR . 'holiday_default.php';
    $batch = new Batch('2022-08-11', '3', null, $holidayFile);
    $this->assertEquals('2022-08-11: Not working on holiday.', $batch->addBusinessDay());
  }
  
  public function testSend1DayBeforeDueDate()
  {
    $dealFile = self::DATA_DIR . 'deals_default.php';
    $holidayFile = self::DATA_DIR . 'holiday_default.php';
    
    $batch = new Batch('2022-08-01', '1', $dealFile, $holidayFile);
    $this->assertEquals('2022-08-01 => deals: 2022-08-02', $batch->addBusinessDay());

    $batch = new Batch('2022-08-04', '1', $dealFile, $holidayFile);
    $this->assertEquals(
      '2022-08-04 => deals: 2022-08-05, 2022-08-06, 2022-08-07',
      $batch->addBusinessDay()
    );

    $batch = new Batch('2022-08-05', '1', $dealFile, $holidayFile);
    $this->assertEquals('2022-08-05 => deals: 2022-08-08', $batch->addBusinessDay());

    $batch = new Batch('2022-08-10', '1', $dealFile, $holidayFile);
    $this->assertEquals(
      '2022-08-10 => deals: 2022-08-12, 2022-08-13',
      $batch->addBusinessDay()
    );
  }

  public function testSend3DayBeforeDueDate()
  {
    $dealFile = self::DATA_DIR . 'deals_default.php';
    $holidayFile = self::DATA_DIR . 'holiday_default.php';
    
    $batch = new Batch('2022-08-01', '3', $dealFile, $holidayFile);
    $this->assertEquals('2022-08-01 => deals: 2022-08-04', $batch->addBusinessDay());

    $batch = new Batch('2022-08-02', '3', $dealFile, $holidayFile);
    $this->assertEquals(
      '2022-08-02 => deals: 2022-08-05, 2022-08-06, 2022-08-07',
      $batch->addBusinessDay()
    );

    $batch = new Batch('2022-08-04', '3', $dealFile, $holidayFile);
    $this->assertEquals('2022-08-04 => deals: 2022-08-09', $batch->addBusinessDay());

    $batch = new Batch('2022-08-05', '3', $dealFile, $holidayFile);
    $this->assertEquals(
      '2022-08-05 => deals: 2022-08-11', 
      $batch->addBusinessDay()
    );
  }
  
  public function testEveryDueDateIsHoliday()
  {
    $dealFile = self::DATA_DIR . 'deals.php';
    $holidayFile = self::DATA_DIR . 'holiday_every_due_date_is_holiday.php';
    
    $batch = new Batch('2022-08-01', '1', $dealFile, $holidayFile);
    $this->assertEquals('2022-08-01 => deals: ', $batch->addBusinessDay());

    $batch = new Batch('2022-08-01', '3', $dealFile, $holidayFile);
    $this->assertEquals('2022-08-01 => deals: ', $batch->addBusinessDay());

    $batch = new Batch('2022-07-29', '3', $dealFile, $holidayFile);
    $this->assertEquals('2022-07-29 => deals: ', $batch->addBusinessDay());

    $batch = new Batch('2022-07-28', '3', $dealFile, $holidayFile);
    $this->assertEquals('2022-07-28 => deals: ', $batch->addBusinessDay());

    $batch = new Batch('2022-07-27', '3', $dealFile, $holidayFile);
    $this->assertEquals('2022-07-27 => deals: 2022-08-07, 2022-08-08, 2022-08-09', $batch->addBusinessDay());
  }
}
