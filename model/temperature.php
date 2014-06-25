<?php

// CPU Temperatures
// (c) Antti Stenvall
// antti@stenvall.fi
//

class temperature {

  private $data;

  public function __construct() {
    $this->setTemperatures();
  }

  public function getTemperatures() {
    return $this->data;
  }

  protected function setTemperatures() {
    $this->data = array();
    $data = explode(PHP_EOL, shell_exec('sensors | grep Core'));
    foreach ($data as $ind => $row) {
      if (strlen(trim($row)) == 0) {
        break;
      }
      preg_match("@(.*)(:)(.*)(C )(.*)@i", trim($row), $ar);
      array_push($this->data, array(
        'core' => $ind,
        'temp' => (float) trim($ar[3]),
      ));
    }
  }

}

?>
