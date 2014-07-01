<?php

class Data {
	private $data_set = array();
	private $frequency, $type = 'sub';
	private $start_date, $end_date;

	private $fix_rules = array(
		array('sub', '20140526', '20140530')
	);

	public function __construct($range, $frequency) {
		$this->frequency = $frequency;

		$this->start_date = strtotime("+1 day", $this->get_last_date(strtotime(explode('-', $range)[0])));
		$this->end_date = strtotime("+1 day", $this->get_last_date(strtotime(explode('-', $range)[1])));

		for ($time = $this->start_date; $time <= $this->end_date; $time = $this->get_next_date($time)) { 
			$this->data_set[$time] = "";
		}

	}

	public function input_data($id, $type) {
		$this->type = $type;
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
						if (($type == 'view' && $temp_data - $previous_data> 0) || ($type != 'view' && $temp_data > 0)) {
						// if type is View, test to see if increased; if not View, test for non-negative data

							$this->data_set[$date] = $temp_data;
							$previous_data = $temp_data;
							break;
						} else {
						// Catch incorrect data

							if (($type == 'view' && $results->results($x+1, $data_type) - $previous_data> 0) || ($type != 'view' && $results->results($x+1, $data_type) > 0)) {
							// if next data is correct, use next data to calculate current data
								$temp_data = floor(($results->results($x+1, $data_type) + $previous_data) / 2);
								$this->data_set[$date] = $temp_data;
								$previous_data = $temp_data;
								break;
							} else if (($type == 'view' && $results->results($x+2, $data_type) - $previous_data> 0) || ($type != 'view' && $results->results($x+2, $data_type) > 0)) {
							// if next data is correct, use next data to calculate current data
								$temp_data = floor(($results->results($x+2, $data_type) + $previous_data) / 3);
								$this->data_set[$date] = $temp_data;
								$previous_data = $temp_data;
								break;
							} else {
							// estimate data to be 10% more than previous
								$temp_data = floor($previous_data * 1.1);
								$this->data_set[$date] = $temp_data;
								$previous_data = $temp_data;
								break;
							}
							
						}
					}
				} else if ($temp_date > $this->get_next_date($date)) {
				// if no date exist within frequency range

					// take previous and next value, get average
					$temp_data = floor(($results->results($x+1, $data_type) + $previous_data) / 2);
					$this->data_set[$date] = $temp_data;
					$previous_data = $temp_data;
					break;
				}
			}
		}

		// print_r($this->data_set);
	}

	public function get_data($cumulate) {
		$str = "";

		$first_data = true;
		$prev = null;
		foreach ($this->data_set as $date => $data) {
			if ($first_data) {
				$first_data = false;
				$prev = $data;
			} else {
				if (!empty($data)) {
					$str .= '[' . strtotime("-1 day", $date) . '000, ';

					// fix systematic error
					$data = $this->systematic_fix(($date - (24*60*60)), $data);

					if ($cumulate == 'true') {
						$str .= $data;
					} else {
						$str .= $data - $prev;
					}
					$str .= '], ';
					$prev = $data;
				}
			}
		}

		return rtrim($str, ',');
	}

	public function systematic_fix($timestamp, $data) {
		foreach ($this->fix_rules as $rule) {
			if ($this->type == $rule[0] && $timestamp > strtotime($rule[1]) && $timestamp < strtotime($rule[2])) {
				$temp1 = $this->data_set[$this->get_next_date(strtotime($rule[2]))];
				$temp2 = $this->data_set[$this->get_last_date($this->get_next_date(strtotime($rule[2])))];
				$offset_high = $temp1 - $temp2;

				$temp1 = $this->data_set[$this->get_last_date(strtotime($rule[1]) + (2*24*60*60))];
				$temp2 = $this->data_set[$this->get_next_date($this->get_last_date(strtotime($rule[1]) + (2*24*60*60)))];
				$offset_low = $temp1 - $temp2;

				$offset = floor(($offset_high + $offset_low) / 2);
				return ($data + $offset);
			}
		}
		return $data;
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
		if ($timestamp != 0) {
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
		} else {
			return 0;
		}
	}

}