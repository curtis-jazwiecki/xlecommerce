<?php
  class ot_changeamount {
    var $title, $output;
	var $amount;

    function ot_changeamount() {
      $this->code = 'ot_changeamount';
      $this->title = 'Change Amount';
      $this->description = 'Change Amount (for POS))';
      $this->enabled = false;
      $this->sort_order = '100';

      $this->output = array();
    }

    function process() {
      global $order, $currencies;
		
	  if ($order){
		  $this->output[] = array('title' => $this->title . ':',
								  'text' => '<i>' . $currencies->format($this->amount, true, $order->info['currency'], $order->info['currency_value']) . '</i>',
								  'value' => $this->amount);
	  } else {
		  $this->output[] = array('title' => $this->title . ':',
								  'text' => '<i>' . $currencies->format($this->amount, true, 'USD', '1') . '</i>',
								  'value' => $this->amount);
	  }
    }

    function check() {
		return false;
    }

    function keys() {
      return array();
    }

    function install() {
    }

    function remove() {
    }
  }
?>
