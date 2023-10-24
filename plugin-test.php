<?php
/**
 * Plugin Name: A Plugin Test
 * Plugin URI: https://github.com/brooks1man/plugin-test
 * Description: A WordPress plugin for testing.
 * Version: 1.0.2
 * Author: Matt Brooks
 * License: none
 * Update URI: https://raw.githubusercontent.com/brooks1man/plugin-test/main/updates.json
 */

defined('ABSPATH') || exit;

if(!class_exists('Plugin_Test')) {
   /**
    * Plugin_Test class.
    */
   class Plugin_Test {
      function __construct() {
         add_filter('update_plugins_raw.githubusercontent.com', [$this, 'check_for_updates'], 10, 3);
      }


      /**
       * Checks the plugin update URI for updates through the update_plugins_{$hostname} filter.
       *
       * Returns array of plugin update data or false on failure.
       *
       * @param array|false $update Plugin update data or false if none yet.
       * @param array $pluginData Plugin file headers.
       * @param string $pluginFile Plugin filename (folder/filename).
       * @return array|false
       */
      public function check_for_updates($update, $pluginData, $pluginFile) {
         $updateUri = $pluginData['UpdateURI'] ?? null;

         if(empty($updateUri)) {
            return $update;
         }

         $response = wp_remote_get($updateUri);

         if(is_wp_error($response)) {
            return $update;
         }

         $data = json_decode($response['body'], true);

         // Check to see if returned update URI contains pluginFile object (plugin folder/file).
         if(isset($data[$pluginFile]) && !empty($data[$pluginFile])) {
            return $data[$pluginFile];
         }

         return $update;
      }
   }

   $pluginTest = new Plugin_Test();
}
