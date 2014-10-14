<?php

require "common.php";

/**
 * Class DC
 * @author("The Mamasu Agency")
 * @type("http://openstack.parallels.com/dc/1.8")
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
     * @type("string")
     * @title("Proxy")
     */
    public $proxy;

    /**
     * @type("string")
     * @title("API Tenant ID")
     */
    public $api_tenant_id;

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
    


    public function provision() {
        logme("provision");
        $subDCavailable = new \APS\EventSubscription(\APS\EventSubscription::Available, "onDCavailable");
        $subDCavailable->source = $this;

        $apsc = \APS\Request::getController();
        $apsc->subscribe($this, $subDCavailable);
    }

    /**
     * @verb(POST)
     * @path("/onDCavailable")
     * @param("http://aps-standard.org/types/core/resource/1.0#Notification",body)
     */
    public function onDCavailable($notification) {
        logme("onDCAvailable");
        $this->updateDatacenter();
    }

    public function configure($new) {
        logme("configure");
        $this->name = $new->name;
        $this->user = $new->user;
        $this->password = $new->password;
        $this->proxy = $new->proxy;
        $this->updateDatacenter();
    }

    public function unprovision() {
        
    }

    public function upgrade() {
        
    }

    /**
     * @verb(GET)
     * @path("/updatedatacenter")
     * @return(string, text/json)
     */
    public function updateDatacenter() {
        logme("updateDatacenter", $this);
        $this->synchIpPools();
        return json_encode($this);
    }

    private function synchIpPools() {
        logme("synchIpPools");
        $apsc = \APS\Request::getController()->impersonate($this->app->aps->id);
        $dc = $apsc->getResource($this->aps->id);
        $previousPools = json_decode($apsc->getIo()->sendRequest(\APS\Proto::GET, "/aps/2/resources/" . $dc->aps->id . "/ippool"));
        $nextsPools = array();

        $os = new OS($this->apiurl, $this->user, $this->password);
        $subnets = $os->getExternalSubnets();

        logme("PREVIOUS POOLS", $previousPools);
        logme("CURRENT POOLS", $subnets);
        for ($i = 0; $i < count($subnets); $i++) {
            //If subnet already exists in DC doesn't creates it
            $createIt = true;
            for ($j = 0; $j < count($previousPools); $j++) {
                if ($previousPools[$j]->id == $subnets[$i]->id) {
                    $createIt = false;
                }
            }
            if ($createIt) {
                $ippool = \APS\TypeLibrary::newResourceByTypeId("http://openstack.parallels.com/ippool/1.2");

                $ippool->id = $subnets[$i]->id;
                $ippool->name = $subnets[$i]->name;
                $ippool->cidr = $subnets[$i]->cidr;
                $ippool->allocation_pools = array();
                for ($j = 0; $j < count($subnets[$i]->allocation_pools); $j++) {
                    $allocPool = array(
                        "start" => $subnets[$i]->allocation_pools[$j]->start,
                        "end" => $subnets[$i]->allocation_pools[$j]->end
                    );
                    $ippool->allocation_pools[] = $allocPool;
                }
                $ippool->gateway_ip = $subnets[$i]->gateway_ip;
                $ippool->aps->link['dc'] = new \APS\Link($this, "dc", $ippool);

                $apsc->linkResource($this, "ippool", $ippool);
                $nextsPools[] = $ippool;
            }
        }

        //If subnet is not in subnets list retrieved from OS, will be tried to be erase it
        for ($j = 0; $j < count($previousPools); $j++) {
            $remove = true;
            for ($i = 0; $i < count($subnets); $i++) {
                /*
                 *  TODO: save conflicts
                 */

                if ($subnets[$i]->id == $previousPools[$j]->id) {
                    $remove = false;
                }
            }
            if ($remove) {
                $apsc->getIo()->sendRequest(\APS\Proto::DELETE, "/aps/2/resources/" . $previousPools[$j]->aps->id);
            }
        }

        $finalIpPools = json_decode($apsc->getIo()->sendRequest(\APS\Proto::GET, "/aps/2/resources/" . $dc->aps->id . "/ippool"));
        $this->numippools = count($finalIpPools);
        $apsc->updateResource($this);
    }

    /**
     * @verb(GET)
     * @path("/listimages")
     * @return(string, text/json)
     */
    public function listImages() {
        logme("ListImages");
        $os = new OS($this->apiurl, $this->user, $this->password);
        try {
            $images = $os->listImages();
            logme("IMAGES", $images);
            $publicImages = array();
            foreach ($images->images as $image) {
                if (($image->visibility == "public") && ($image->status == "active")) {
                    $publicImages[] = $image;
                }
            }
            return json_encode($publicImages);
        } catch (Exception $e) {
            return json_encode($e);
        }
    }
    
    function profileLink(){
    	$apsc = \APS\Request::getController();
    	$this->numprofiles = $this->numprofiles + 1;
    	$apsc->updateResource($this);
    }
    
    function profileUnlink(){
    	$apsc = \APS\Request::getController();
    	$this->numprofiles = $this->numprofiles - 1;
    	$apsc->updateResource($this);
    }
    
    function imageLink(){
    	$apsc = \APS\Request::getController();
    	$this->numimages = $this->numimages + 1;
    	$apsc->updateResource($this);
    }
    
    function imageUnlink(){
    	$apsc = \APS\Request::getController();
    	$this->numimages = $this->numimages - 1;
    	$apsc->updateResource($this);
    }
    
    function ippoolLink(){
    	$apsc = \APS\Request::getController();
    	$this->numippools = $this->numippools + 1;
    	$apsc->updateResource($this);
    }
    
    function ippoolUnlink(){
    	$apsc = \APS\Request::getController();
    	$this->numippools = $this->numippools - 1;
    	$apsc->updateResource($this);
    }
    
    function organizationLink(){
    	$apsc = \APS\Request::getController();
    	$this->numorganizations = $this->numorganizations + 1;
    	$apsc->updateResource($this);
    }
    
    function organizationUnlink(){
    	$apsc = \APS\Request::getController();
    	$this->numorganizations = $this->numorganizations - 1;
    	$apsc->updateResource($this);
    }

}
