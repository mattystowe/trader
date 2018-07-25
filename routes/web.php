<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//$this->get('/test','TestController@test');
//$this->get('/indicators','TestController@indicators');
//$this->get('/signals','TestController@signals');

$this->get('/','IndexController@index');


//scheduler
$this->get('/scheduledtasks/schedulerun','ScheduledTasksController@scheduleRun');
