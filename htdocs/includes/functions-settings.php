<?php

function lms_settings_get($settingsName, $defaultValue = false){
  $value = DB::queryFirstField('SELECT SettingsValue FROM Settings WHERE SettingsName = %s', $settingsName);
  return $value ?? $defaultValue;
}


function lms_settings_set($settingsName, $settingsValue){
  return DB::insertUpdate('Settings', array(
      'settingsName' => $settingsName,
      'settingsValue' => $settingsValue,
  ), array(
      'settingsValue' => $settingsValue,
  ));
}