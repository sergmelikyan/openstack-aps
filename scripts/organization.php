<?php

require "aps/2/runtime.php";

/**
 * Class Organization
 * @author("The Mamasu Agency")
 * @type("http://openstack.parallels.com/organization/1.0")
 * @implements("http://aps-standard.org/types/core/resource/1.0")
 */
class organization extends \APS\ResourceBase {

    /**
     * @link("http://openstack.parallels.com/app")
     * @required
     */
    public $app;

    /**
     * @link("http://openstack.parallels.com/dc")
     * @required
     */
    public $dc;

    /**
     * @link("http://openstack.parallels.com/heatstack[]")
     */
    public $heatstack;

    /**
     * @link("http://openstack.parallels.com/unmanagedve[]")
     */
    public $unmanagedve;

    /**
     * @link("http://aps-standard.org/types/core/subscription/1.0")
     * @required
     */
    public $subscription;

    /**
     * @link("http://aps-standard.org/types/core/account/1.0")
     * @required
     */
    public $account;

    public function provision() {
        
    }

    public function configure($new) {
        
    }

    public function unprovision() {
        
    }

    public function upgrade() {
        
    }

}

