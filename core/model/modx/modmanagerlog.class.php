<?php
/**
 * @package modx
 * @subpackage mysql
 */
class modManagerLog extends xPDOSimpleObject {
    function modManagerLog(& $xpdo) {
        $this->__construct($xpdo);
    }
    function __construct(& $xpdo) {
        parent :: __construct($xpdo);
    }
}
?>