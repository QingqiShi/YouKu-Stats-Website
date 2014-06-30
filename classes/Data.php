<?php

class Data {
	private $data_set = array();
	private $frequency;
	private $start_date, $end_date;

	public function __construct($range, $frequency) {
		$this->frequency = $frequency;

		$this->start_date = strtotime("+1 day", $this->get_last_date(strtotime(explode('-', $range)[0])));
		$this->end_date = strtotime("+1 day", $this->get_last_date(strtotime(explode('-', $range)[1])));

		for ($time = $this->start_date; $time <= $this->end_date; $time = $this->get_next_date($time)) { 
			$this->data_set[$time] = "";
		}

	}

	public function input_data($id, $type) {
		$results = DB::getInstance()->query('SELECT * FROM data WHERE u_id = ' . $id . ' AND d_timestamp > ' . $this->start_date. ' AND d_timestamp < ' . ($this->end_date + (24*60*60)));

		$data_type = ($type == 'view' ? 'd_view' : 'd_sub');

		$x = 0;
		$previous_data = 0;
		foreach ($this->data_set as $date => $data) {
			for (; $x < $results->count(); $x++) { 
				$temp_date = $results->results($x, 'd_timestamp');
				$temp_data = $results->results($x, $data_type);
				if ($temp_date >= $date && $temp_date < $this->get_next_date($date) && date('G', $temp_date) < 5) {
				// test for date within range of the particular frequency

					if (empty($data)) {
						if (($type == 'view' && $temp_data - $previous_data> 0) && $temp_data > 0) {
							$this->data_set[$date] = date('r', $date) . ' ' . date('r', $temp_date) . ' ' . $temp_data;
							$previous_data = $temp_data;
							break;
						} else {
							// Catch incorrect data

							if ($results->results($x-1, $data_type) > 0 && $results->results($x+1, $data_type) > 0) {
								$this->data_set[$date] = ($results->results($x+1, $data_type) + $results->results($x-1, $data_type)) / 2;
								break;
							}
							$this->data_set[$date] = 'error';
							break;
							// for ($i = $x; $i < $results->count(); $i++) {
							// 	$temp_temp_data = $results->results($i, $data_type);
							// 	if ($temp_temp_data - $previous_data > 0) {
							// 		$this->data_set[$date] = date('r', $date) . ' ' . date('r', $temp_date) . ' ' . (($temp_temp_data - $previous_data) / 2);
							// 		$previous_data = $temp_data;
							// 		break;
							// 	}
							// }
							
						}
					}
				} else if ($temp_date > $this->get_next_date($date)) {
				// if no date exist within frequency range

					// take previous and next value, get average
					$this->data_set[$date] = floor(($results->results($x+1, $data_type) + $results->results($x-1, $data_type)) / 2);
					break;
				}
			}
		}

		print_r($this->data_set);
	}

	private function get_next_date($timestamp) {
		switch ($this->frequency) {
			case 'day':
				return strtotime("+1 day", $timestamp);
				break;

			case 'week':
				return strtotime("next Sunday", $timestamp);
				break;

			case 'month':
				return mktime(0, 0, 0, date("n", $timestamp) + 1, 1, date("Y", $timestamp));
				break;
			
			default:
				return strtotime("+1 day", $timestamp);
				break;
		}
	}

	private function get_last_date($timestamp) {
		switch ($this->frequency) {
			case 'day':
				return strtotime("-1 day", $timestamp);
				break;

			case 'week':
				return strtotime("last Sunday", $timestamp);
				break;

			case 'month':
				return mktime(0, 0, 0, date("n", $timestamp) - 1, 1, date("Y", $timestamp));
				break;
			
			default:
				return strtotime("-1 day", $timestamp);
				break;
		}
	}

}