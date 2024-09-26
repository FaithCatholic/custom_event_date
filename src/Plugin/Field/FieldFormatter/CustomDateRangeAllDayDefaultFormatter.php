<?php

namespace Drupal\custom_event_date\Plugin\Field\FieldFormatter;

use Drupal\Component\Utility\Html;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Datetime\DrupalDateTime;

/**
 * Plugin implementation of the 'custom_daterange_all_day_default' formatter.
 *
 * @FieldFormatter(
 *   id = "custom_daterange_all_day_default",
 *   label = @Translation("Custom date range all day"),
 *   field_types = {
 *     "daterange"
 *   }
 * )
 */
class CustomDateRangeAllDayDefaultFormatter extends FormatterBase {
  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      // Implement default settings.
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    return [
      // Implement settings form.
    ] + parent::settingsForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    // Implement settings summary.

    return $summary;
  }

  /**
   * {@inheritDoc}
   */
  public function getTimeOffset($timezone, $map) {
    foreach($map as $key => $value) {
      if ($key == $timezone) {
        return $value;
      }
    }
    return null;
  } 

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];
    
   //php sort of map/array for timezones 
   $map = [
      'EDT' => '-4 hours',
      'EST' => '-5 hours',
      'CDT' => '-5 hours',
      'CST' => '-6 hours',
      'MDT' => '-6 hours',
      'MST' => '-7 hours',
      'PDT' => '-7 hours',
      'PST' =>  '-8 hours',
    ]; 
    

    foreach ($items as $delta => $item) {
      list($start_date, $start_time) = explode('T', $this->viewValue($item));
      if (method_exists($this, 'viewEndValue') && $this->viewEndValue($item)) {
        list($end_date, $end_time) = explode('T', $this->viewEndValue($item));
        // dsm($end_date);
        // dsm($end_time);
        // dsm($this->viewEndValue($item));
      }

      if ($start_time == '00:00:00' && $end_time == '23:59:59') {
        $all_day = TRUE;
      } else {
        $all_day = FALSE;
      }
      
      //creates DrupalDateTime objects for date initial and end
      $date = new DrupalDateTime($this->viewValue($item));
      $dateEnd = new DrupalDateTime($this->viewEndValue($item));
      $timezone = $date->format('T'); //grabs timezone from object
      
      //calls function to correct the time difference
      $date->modify($this->getTimeOffset($timezone, $map)); 
      $dateEnd->modify($this->getTimeOffset($timezone, $map));

      //redundant with new implement
      /*
      $start_datetime = strtotime($this->viewValue($item));

      if (method_exists($this, 'viewEndValue')) {
        $end_datetime = strtotime($this->viewEndValue($item));
      }*/

      $start_month = '<span class="start month">'. $date->format('M') .'</span>';
      $start_day = '<span class ="start day">'. $date->format('d') .'</span>';
      $start_time = '<span class ="start time">'. $date->format('g:iA T'). '</span>';


      if ($end_date && $end_time) {
        $end_month = $dateEnd->format('M');
        $end_day = $dateEnd->format('d');
        $end_time = $dateEnd->format('g:iA T'); 
      }

      if ($start_date == $end_date) {
        if ($all_day) {
          $markup = $start_month .' '. $start_day;
          $markup .= '<div class="secondary">All Day</div>';
        } else {
          $markup = $start_month .' '. $start_day .' <div class="secondary">'. $start_time;
          if ($end_time) {
            $markup .= '<span class="sep to"> to </span>'. $end_time;
          }
          $markup .= '</div>';
        }
      } else {
        if ($all_day) {
          $markup = $start_month .' '. $start_day;
          if ($end_month && $end_day) {
            $markup .= '<div class="secondary"><span class="sep to"> to </span></div>'. $end_month .' '. $end_day;
          }
        } else {
          $markup = $start_month .' '. $start_day .'<div class="secondary"><span class="start sep at"> at </span>'. $start_time;
          if ($end_month && $end_day && $end_time) {
            $markup .= '<span class="sep to"> to </span></div>'. $end_month .' '. $end_day .'<div class="secondary"><span class="end sep at"> at </span>'. $end_time .'</div>';
          } else {
            $markup .= '</div>';
          }
        }
      }

      $elements[$delta] = ['#markup' => $markup];
    }

    return $elements;
  }

  /**
   * Generate the output appropriate for one field item.
   *
   * @param \Drupal\Core\Field\FieldItemInterface $item
   *   One field item.
   *
   * @return string
   *   The textual output generated.
   */
  protected function viewValue(FieldItemInterface $item) {
    return nl2br(Html::escape($item->value));
  }

  /**
   * Generate the output appropriate for one field item.
   *
   * @param \Drupal\Core\Field\FieldItemInterface $item
   *   One field item.
   *
   * @return string
   *   The textual output generated.
   */
  protected function viewEndValue(FieldItemInterface $item) {
    return nl2br(Html::escape($item->end_value));
  }

}

  
