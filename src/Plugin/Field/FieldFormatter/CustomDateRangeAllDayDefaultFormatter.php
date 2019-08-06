<?php

namespace Drupal\custom_event_date\Plugin\Field\FieldFormatter;

use Drupal\Component\Utility\Html;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;

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
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    foreach ($items as $delta => $item) {
      list($start_date, $start_time) = explode('T', $this->viewValue($item));
      list($end_date, $end_time) = explode('T', $this->viewEndValue($item));

      if ($start_time == '00:00:00' && $end_time == '23:59:59') {
        $all_day = TRUE;
      } else {
        $all_day = FALSE;
      }

      $start_datetime = strtotime($this->viewValue($item));
      $end_datetime = strtotime($this->viewEndValue($item));

      $start_month = '<span class="start month">'. date('M', $start_datetime) .'</span>';
      $start_day = '<span class="start day">'. date('j', $start_datetime) .'</span>';
      $start_time = '<span class="start time">'. date('g:ia', $start_datetime). '</span>';

      $end_month = '<span class="end month">'. date('M', $end_datetime) .'</span>';
      $end_day = '<span class="end day">'. date('j', $end_datetime) .'</span>';
      $end_time = '<span class="end time">'. date('g:ia', $end_datetime) .'</span>';

      if ($start_date == $end_date) {
        if ($all_day) {
          $markup = $start_month . $start_day;
        } else {
          $markup = $start_month .' '. $start_day .' <div class="secondary">'. $start_time .'<span class="sep to"> to </span>'. $end_time .'</div>';
        }
      } else {
        if ($all_day) {
          $markup = $start_month .' '. $start_day .'<div class="secondary"><span class="sep to"> to </span></div>'. $end_month .' '. $end_day;
        } else {
          $markup = $start_month .' '. $start_day .'<div class="secondary"><span class="start sep at"> at </span>'. $start_time .'<span class="sep to"> to </span></div>'. $end_month .' '. $end_day .'<div class="secondary"><span class="end sep at"> at </span>'. $end_time .'</div>';
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
