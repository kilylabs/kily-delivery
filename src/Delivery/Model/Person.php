<?php

/**
 * This file is part of the Kily\Delivery package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Kily\Delivery\Model;

use Kily\Delivery\Base\Component;
use Kily\Delivery\Exception\Exception;

/**
 * @author Alexander Bogdanov <ab@dev.kily.ru>
 */
class Person extends Component
{
    protected $_firstname;
    protected $_lastname;
    protected $_middlename;
    protected $_phone;
    protected $_email;

    public function __construct($firstname=null,$lastname=null,$middlename=null,$phone=null,$email=null) {
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->middlename = $middlename;
        $this->email = $email;
        $this->phone = $phone;
    }

    public function getFirstname() {
        return $this->_firstname;
    }

    public function setFirstname($val) {
        $this->_firstname = $val;
        return $this;
    }

    public function getLastname() {
        return $this->_lastname;
    }

    public function setLastname($val) {
        $this->_lastname = $val;
        return $this;
    }

    public function getMiddlename() {
        return $this->_middlename;
    }

    public function setMiddlename($val) {
        $this->_middlename = $val;
        return $this;
    }

    public function getPhone() {
        return $this->_phone;
    }

    public function setPhone($val) {
        $this->_phone = $val;
        return $this;
    }

    public function getEmail() {
        return $this->_email;
    }

    public function setEmail($val) {
        $this->_email = $val;
        return $this;
    }

    public function getFullName() {
        return implode(' ',array_filter([$this->firstname,$this->middlename,$this->lastname]));
    }
}
