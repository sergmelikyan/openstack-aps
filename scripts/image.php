<?php

require "aps/2/runtime.php";

/**
 * Class Image
 * @author("The Mamasu Agency")
 * @type("http://openstack.parallels.com/image/1.0")
 * @implements("http://aps-standard.org/types/core/resource/1.0")
 */
class image extends \APS\ResourceBase {

    /**
     * @link("http://openstack.parallels.com/unmanagedve[]")
     */
    public $unmanagedve;
    /**
     * @link("http://openstack.parallels.com/dc")
     * @required
     */
    public $dc;

    public function provision() {

    }

    public function configure($new) {

    }

    public function unprovision() {
        
    }

    public function upgrade() {
        
    }

}

