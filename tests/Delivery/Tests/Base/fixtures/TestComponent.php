<?php

namespace Kily\Delivery\Base;

class TestComponent extends Component {

    private $thing;
    private $readonly = 1;

    public function getThing() {
        return $this->thing;
    }

    public function setThing($val) {
        $this->thing = $val;
    }

    public function getReadonly() {
        return $this->readonly;
    }


}
