<?php
namespace App;

use Log;


class MathUtils
{



    //returns the value of data array at position $offset from last key
    public static function arrayPosition($data,$offset) {
      return array_splice($data,-$offset,1);
    }


    public static function arrayLast($data) {
      return last($data);
    }

    public static function arrayPrevious($data) {
      //return array_splice($data,-1);
      array_pop($data);
      return array_pop($data);
      //return $data[count($data)-2];
    }


    public static function stdAvg($data, $source, $period) {
      $data_array = array_splice($data, -$period, $period);
      $values = [];
      foreach ($data_array as $item) {
        $values[] = $item[$source];
      }
      return array_sum($values) / count($values);
    }


    /*
    * period = periods to look back
    * source = column name of the data array to use in calcs
    *
    * Return Bool - if source values record consecutive highs
    *
    */
    public static function consecutiveHighs($data, $source, $period) {
      $data_array = array_column($data,$source);
      $data_slice = array_splice($data_array,-$period);
      //dump($data_slice);
      for ($i=0; $i < count($data_slice)-1; $i++) {
        if ($data_slice[$i+1]<=$data_slice[$i]) {
          return false;
        }
      }
      return true;


    }


    /*
    * Return Bool - if source values record consecutive lows
    *
    */
    public static function consecutiveLows($data, $source, $period) {
      $data_array = array_column($data,$source);
      $data_slice = array_splice($data_array,-$period);
      //dump($data_slice);
      for ($i=0; $i < count($data_slice)-1; $i++) {
        if ($data_slice[$i+1]>=$data_slice[$i]) {
          return false;
        }
      }
      return true;
    }



    public static function containsValueBelow($query_value, $data, $source, $period) {
      $data_array = array_column($data,$source);
      foreach ($data_array as $value) {
        if ($value < $query_value) {
          return true;
        }
      }
      return true;
    }





    public static function crossover($data, $column_name1, $column_name2) {
      $latest = array_pop($data);
      $latest_value1 = $latest[$column_name1];
      $latest_value2 = $latest[$column_name2];

      $previous = array_pop($data);
      $previous_value1 = $previous[$column_name1];
      $previous_value2 = $previous[$column_name2];


      if ($latest_value1 > $latest_value2) {
        if ($previous_value1 < $previous_value2) {
          return true;
        }
      }

      return false;

    }




    public static function crossunder($data, $column_name1, $column_name2) {
      $latest = array_pop($data);
      $latest_value1 = $latest[$column_name1];
      $latest_value2 = $latest[$column_name2];

      $previous = array_pop($data);
      $previous_value1 = $previous[$column_name1];
      $previous_value2 = $previous[$column_name2];


      if ($latest_value1 < $latest_value2) {
        if ($previous_value1 > $previous_value2) {
          return true;
        }
      }

      return false;

    }





}
