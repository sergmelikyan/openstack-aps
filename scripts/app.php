<?php

require "aps/2/runtime.php";

/**
 * Class APP
 * @author("The Mamasu Agency")
 * @type("http://openstack.parallels.com/app/1.3")
 * @implements("http://aps-standard.org/types/core/application/1.0")
 * */
class app extends \APS\ResourceBase {

    /**
     * @link("http://openstack.parallels.com/dc[]")
     */
    public $dc;

    /**
     * @link("http://openstack.parallels.com/organization[]")
     */
    public $organization;

    public function provision() {
        
    }

    public function configure($new) {
        
    }

    public function unprovision() {
        
    }

    public function upgrade() {
        
    }

    /**
     * @verb(GET)
     * @path("/dcconnectiontest")
     * @return(string,text/json)
     */
    public function dcConnectionTest() {
        $object = strip_tags($_GET['object']);
        $return = array(
            "success" => true,
            "object" => $object
        );

        return json_encode($return);
    }

}
