<?php

require "aps/2/runtime.php";

/**
 * Class UnmanagedVe
 * @author("The Mamasu Agency")
 * @type("http://openstack.parallels.com/unmanagedve/1.0")
 * @implements("http://aps-standard.org/types/core/resource/1.0")
 */
class unmanagedve extends \APS\ResourceBase {

    /**
     * @link("http://openstack.parallels.com/organization")
     * @required
     */
    public $organization;
    /**
     * @link("http://openstack.parallels.com/image")
     * @required
     */
    public $image;
    /**
     * @link("http://openstack.parallels.com/ipassigned[]")
     */
    public $ipassigned;

    public function provision() {

    }

    public function configure($new) {

    }

    public function unprovision() {
        
    }

    public function upgrade() {
        
    }

}

