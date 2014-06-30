<?php

class Validate {
    private $_passed = false,
            $_errors = array(),
            $_db = null;

    public function __construct() {
        $this->_db = DB::getInstance();
    }

    public function check($source, $items = array()) {
        foreach ($items as $item => $rules) {
            $item_name = $item;

            if (isset($source[$item])) {
                $value = $source[$item];
            } else {
                $value = "";
            }

            foreach ($rules as $rule => $rule_value) {    

                if ($rule === 'name') {
                    $item_name = $rule_value;
                } else if ($rule === 'required' && empty($value)) {
                    $this->addError("无法确定{$item_name}");
                } else if (!empty($value)) {
                    switch ($rule) {
                        case 'min':
                            if (strlen($value) < $rule_value) {
                                $this->addError("{$item_name}的长度必须小于{$rule_value}");
                            }
                        break;
                        case 'max':
                            if (strlen($value) > $rule_value) {
                                $this->addError("{$item_name}的长度必须大于{$rule_value}");
                            }
                        break;
                        case 'matches':
                            if ($value != $source[$rule_value]) {
                                $this->addError("{$item_name}不匹配{$items[$rule_value]['name']}");
                            }
                        break;
                        case 'unique':
                            // to use:
                            //     'unique' => array(DB_TABLE, DB_FIELD)
                            $check = $this->_db->get($rule_value[0], array($rule_value[1], '=', $value));
                            if ($check->count()) {
                                $this->addError("{$item_name}已经存在");
                            }
                        break;
                        case 'values':
                            // to use:
                            //    'values' => array(%ACCEPTABLE_VALUES)
                            $check = false;
                            foreach ($rule_value as $accepted) {
                                if ($value == $accepted) {
                                    $check = true;
                                }
                            }
                            if (!$check) {
                                $this->addError("{$item_name}参数不正确");
                            }
                        break;
                        case 'format':
                            if (!preg_match($rule_value, $value)) {
                                $this->addError("{$item_name}的格式不正确");
                            }
                        break;
                    }
                }
            }
        }

        if (empty($this->_errors)) {
            $this->_passed = true;
        }
    }

    private function addError($error) {
        $this->_errors[] = $error;
    }

    public function errors() {
        return $this->_errors;
    }

    public function passed() {
        return $this->_passed;
    }

}