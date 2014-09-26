<?php

require "common.php";

/**
 * Class DC
 * @author("The Mamasu Agency")
 * @type("http://openstack.parallels.com/dc/1.4")
 * @implements("http://aps-standard.org/types/core/resource/1.0")
 */
class dc extends \APS\ResourceBase {

    /**
     * @link("http://openstack.parallels.com/app")
     * @required
     */
    public $app;

    /**
     * @link("http://openstack.parallels.com/organization[]")
     */
    public $organization;

    /**
     * @link("http://openstack.parallels.com/profile[]")
     */
    public $profile;

    /**
     * @link("http://openstack.parallels.com/image[]")
     */
    public $image;

    /**
     * @link("http://openstack.parallels.com/ippool[]")
     */
    public $ippool;

    /**
     * @type("string")
     * @title("API Connection")
     * @description("API URL")
     */
    public $apiurl;

    /**
     * @type("string")
     * @title("Tenant name")
     */
    public $name;

    /**
     * @type("string")
     * @title("User name")
     */
    public $user;

    /**
     * @type("string")
     * @title("Password")
     * @encrypted
     */
    public $password;

    /**
     * @type("integer")
     * @title("Num Organizations")
     */
    public $numorganizations = 0;

    /**
     * @type("integer")
     * @title("Num Profiles")
     */
    public $numprofiles = 0;

    /**
     * @type("integer")
     * @title("Num Images")
     */
    public $numimages = 0;

    /**
     * @type("integer")
     * @title("Num IPPools")
     */
    public $numippools = 0;

    public function organizationLink() {
        $this->numorganizations += 1;
    }

    public function organizationUnlink() {
        $this->numorganizations -= 1;
    }

    public function profileLink() {
        $this->numprofiles += 1;
    }

    public function profileUnlink() {
        $this->numprofiles -= 1;
    }

    public function imageLink() {
        $this->numimages += 1;
    }

    public function imageUnlink() {
        $this->numimages -= 1;
    }

    public function ippoolLink() {
        $this->numippools += 1;
    }

    public function ippoolUnlink() {
        $this->numippools -= 1;
    }

    public function provision() {
        $subDCavailable = new \APS\EventSubscription(\APS\EventSubscription::Available, "onDCavailable");
        $subDCavailable->source->id = $this->aps->id;

        $apsc = \APS\Request::getController();
        $apsc->subscribe($this, $subDCavailable);
    }

    /**
     * @verb(POST)
     * @path("/onDCavailable")
     * @param("http://aps-standard.org/types/core/resource/1.0#Notification",body)
     */
    public function onDCavailable($notification) {
        $os = new OS($this->apiurl, $this->user, $this->password);
        $subnets = $os->getExternalSubnets();

        for ($i = 0; $i < count($subnets); $i++) {
            $apsc = \APS\Request::getController();
            $dc = $apsc->getResource($this->aps->id);
            $ippool = \APS\TypeLibrary::newResourceByTypeId("http://openstack.parallels.com/ippool/1.2");

            $ippool->id = $subnets[$i]->id;
            $ippool->name = $subnets[$i]->name;
            $ippool->cidr = $subnets[$i]->cidr;
            $ippool->allocation_pools = array();
            logme($subnets[$i]->allocation_pools);
            for ($j = 0; $j < count($subnets[$i]->allocation_pools); $j++) {
                $allocPool = array(
                    "start" => $subnets[$i]->allocation_pools[$j]->start,
                    "end" => $subnets[$i]->allocation_pools[$j]->end
                );
                $ippool->allocation_pools[] = $allocPool;
            }
            $ippool->gateway_ip = $subnets[$i]->gateway_ip;

            $apsc->linkResource($dc, 'ippool', $ippool);
        }
    }

    public function configure($new) {
        
    }

    public function unprovision() {
        
    }

    public function upgrade() {
        
    }

    /**
     * @verb(GET) 
     * @path("/canbedeleted")
     * @return(string,text/json) 
     */
    public function canBeDeleted() {
        $os = new OS($this->apiurl, $this->user, $this->password);
        $projects = $os->getProjects();
        $return = array("success" => true);
        if (count($projects->projects) > 0) {
            $return['success'] = false;
        }
        return json_encode($return);
    }

    /**
     * @verb(GET)
     * @path("/listippools")
     * @return(string, text/json)
     */
    public function listIpPools() {
        $os = new OS($this->apiurl, $this->user, $this->password);
        $ipPools = $os->getExternalSubnets();
        return json_encode($ipPools);
    }

}
