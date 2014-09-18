<?php

require "aps/2/runtime.php";

/**
 * Class IPPool
 * @author("The Mamau Agency")
 * @type("http://openstack.parallels.com/ippool/1.0")
 * @implements("http://aps-standard.org/types/core/resource/1.0")
 */
class ippool extends \APS\ResourceBase {

    /**
     * @link("http://openstack.parallels.com/dc")
     * @required
     */
    public $dc;
    /**
     * @link("http://openstack.parallels.com/ip[]")
     */
    public $ip;

    public function provision() {

    }

    public function configure($new) {

    }

    public function unprovision() {
        
    }

    public function upgrade() {
        
    }

}

