<?php

namespace Core\Helpers;

use Core\Helpers\Trie;

class RouterTrie extends Trie {

    /**
     * Adds a string and an associated value of that string into the trie
     * If $value is null or not added, it is ignored
     *
     * $data - A String value
     * $value - Any mixed value
     *
     * @return - true if a value was changed or string inserted, false if preexisting
     **/
    public function add($data, $value = NULL) {

        $node = &$this->data;
        $index = 0;
        $skip = false;
        $stack = "";

        if(!empty($data)) {
            // Go through all existing nodes
            while($index<strlen($data)) {
                if(!is_null($node) && array_key_exists($data{$index}, $node)) {
                    $node = &$node[$data{$index++}];
                }
                else break;
            }

            // Add in most nonexisting nodes
            while($index<strlen($data)) {
                if(is_null($node) && !$skip) {

                    $node = array();
                }


                if($data{$index} == "{") {
                    $stack.=$data{$index};
                    $skip = true;
                    $index++;
                } else if($data{$index} == "}") {
                    $stack.="*".$data{$index};
                    $skip = false;
                    $node[$stack] = null;
                    $index++;
                    $node = &$node[$stack];
                    $stack = "";
                } else if(!$skip) {
                     $node[$data{$index}] = null;
                     $node = &$node[$data{$index++}];
                 }
                if($skip) {
                   //$stack.=$data{$index};
                   $index++;
               }


            }

        }

        // New branch
        if(is_null($node)) {
            $node = array("vl"=>$value);
            return true;
        }

        // Assign the new value
        if(!array_key_exists("vl", $node)) {
            $node["vl"] = $value;
            return true;
        }

        else {
            if($node["vl"] === $value) {
                return false;
            } else {
                $node["vl"] = $value;
                return true;
            }
        }

    }

    /**
     * Get the data from a string, returning false if not found
     *
     * $data - A String value
     *
     * @return - mixed
     **/
    public function getVal($data) {

        // Iterate through the characters
        $node = &$this->data;
        for($i=0;$i<strlen($data);$i++) {
            if(!is_null($node) && !array_key_exists($data{$i}, $node) && array_key_exists('{*}', $node)) {
                $node = &$node['{*}'];
                $pos = strpos(substr($data, $i), '/');

                if($pos == 0) {
                    break;
                }
                $i+=$pos;
            }
            if(!is_null($node) && array_key_exists($data{$i}, $node)) {
                $node = &$node[$data{$i}];
            } else {
                return false;
            }
        }

        // Check value
        if(is_null($node) || !array_key_exists("vl",$node)) return false;
        return $node["vl"];
    }

}