# SEND MAIL BATCH

---

## Table of contents

---
* [General Info](#general-info)
* [Technologies](#technologies)
* [Install](#install)
* [How to use](#how-to-use)


## General Info

---
Given the current date, this simple batch will print out all deal's due date.

## Technologies

---
* PHP: 8.0
* OS: Windows 11

## Install

---
* Clone this project
* Cd to project folder and run composer install:
```
> composer install
```

## How to use

---
###### 1. Use as a daily batch
In case you want to run it daily
- Run this following command to create scheduled task:
  - task_name: name of scheduled task
  - path_to_batch_file: absolute path to batch file
  - HH:MM: execute time of batch
```
> SCHTASKS /CREATE /SC DAILY /TN "<task_name>" /TR "<path_to_batch_file>" /ST <HH:MM>
```
```
// example
> SCHTASKS /CREATE /SC DAILY /TN "SEND MAIL BATCH" /TR "C:\Users\OS\Documents\project\batch\sendmail.bat" /ST 01:00
```
- Next, run this command to run the batch:
```
> schtasks /run /tn "<task_name>"
```
```
// example
> schtasks /run /tn "SEND MAIL BATCH"
```
- Result of batch will be written to **_log.txt_**
- Run this following command to stop and delete the task:
```
> Unregister-ScheduledTask -TaskName "<task_name>" -Confirm:$false
```
```
// example
> Unregister-ScheduledTask -TaskName "SEND MAIL BATCH" -Confirm:$false
```
<br />

###### 2. Manually run the PHP file
In case you want to run it against a specific day, number and data
- Cd to the project folder
- Run this command:
  - day: <span style="color: red">[required]</span> chosen day, follow 'Y-m-d' format
  - number: <span style="color: red">[required]</span> number of date the email will be sent before due date
  - dealFile: <span style="color: cyan">[optional]</span> name of file containing data of deals, reside in <span style="color: #2EAEF0">/src/data</span> folder
  - holidayFile: <span style="color: cyan">[optional]</span> name of file containing list of holiday, reside in <span style="color: #2EAEF0">/src/data</span> folder
```
> php src/index.php <day> <number> [dealFile] [holidayFile]
```
```
// example
> php src/index.php 2022-08-08 3 deals.php holiday.php
```
<br />

###### 3. How to change deals and holiday data
By default, Batch will use data defined inside Batch.php class.<br />
If you want to change deals and holiday data, there are 2 ways:
- Modify default data in Batch.php directly by changing data of class variable, namely $deals and $holiday
- Define data in separated file and put it under <span style="color: #2EAEF0">/src/data</span> folder, then specify them in the command:
```
// example
// data defined in /src/data/new_deals.php and /src/data/new_holiday.php
> php src/index.php 2022-08-08 3 new_deals.php new_holiday.php
```
- The data of separated file should follow the example in <span style="color: #2EAEF0">/src/data/deals.php</span> and <span style="color: #2EAEF0">/src/data/holiday.php</span>

<br />

###### 4. How to run unit test
- Cd to project folder
- Run the following command to perform unit test:
```
> ./vendor/bin/phpunit tests
```