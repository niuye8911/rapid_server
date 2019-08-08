<?php
  class Result
  {
      public function __construct($result_file)
      {
          $this->app_list	= null;
          $this->file = $result_file;
      }

      public function getAppResult($app_id)
      {
          $this->applications = array();
          // chekc if the result exists
          if (!file_exists($this->file)) {
              return null;
          }

          $contents = file_get_contents($this->file);

          $data = json_decode($contents, true);
          foreach ($results as $appResult) {
              if ($appResult['name'] ==  $app_id) {
                  return $appResult;
              }
          }
          return null;
      }
  }
