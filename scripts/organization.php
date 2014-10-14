<?php

require "common.php";

/**
 * Class Organization
 * @author("The Mamasu Agency")
 * @type("http://openstack.parallels.com/organization/1.1")
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

    /**
     * @type("http://aps-standard.org/types/core/resource/1.0#Counter")
     * @title("VCPU Counter Flat")
     * @description("Number of VCPUs")
     * @unit("unit")
     */
    public $cpu_counter;

    /**
     * @type("http://aps-standard.org/types/core/resource/1.0#Counter")
     * @title("Memory Counter Flat")
     * @description("Volume of RAM allocated in MB")
     * @unit("mb")
     */
    public $memory_counter;

    /**
     * @type("http://aps-standard.org/types/core/resource/1.0#Counter")
     * @title("Disk Size Couter Flat")
     * @description("Size of root disk in GB")
     * @unit("gb")
     */
    public $disk_size_counter;

    /**
     * @type("http://aps-standard.org/types/core/resource/1.0#Counter")
     * @title("IPs Counter Flat")
     * @description("Number of IPs")
     * @unit("unit")
     */
    public $ips_counter;

    /**
     * @type("http://aps-standard.org/types/core/resource/1.0#Counter")
     * @title("Traffic Out Counter Flat")
     * @description("Number of outgoing Kbytes on a VM network interface")
     * @unit("kb")
     */
    public $traffic_out_counter;

    /**
     * @type("http://aps-standard.org/types/core/resource/1.0#Usage")
     * @title("VCPU Usage PAYG")
     * @description("Number of used VCPUs")
     * @unit("item-h")
     */
    public $cpu_usage;

    /**
     * @type("http://aps-standard.org/types/core/resource/1.0#Usage")
     * @title("Memory Usage PAYG")
     * @description("Volume of RAM used in MB/h")
     * @unit("mb-h")
     */
    public $memory_usage;

    /**
     * @type("http://aps-standard.org/types/core/resource/1.0#Usage")
     * @title("Disk Size Usage PAYG")
     * @description("Size of root disk used in MB/h")
     * @unit("mb-h")
     */
    public $disk_size_usage;

    /**
     * @type("http://aps-standard.org/types/core/resource/1.0#Usage")
     * @title("IPs Usage PAYG")
     * @description("Use of IPs")
     * @unit("item-h")
     */
    public $ips_usage;

    /**
     * @type("http://aps-standard.org/types/core/resource/1.0#Usage")
     * @title("Traffic Out Usage PAYG")
     * @description("Number of outgoing MB/h used on a VM network interface")
     * @unit("mb-h")
     */
    public $traffic_out_usage;

    /**
     * @type("integer")
     * @title("subscription id")
     */
    public $subscription_id;

    /**
     * @type("string")
     * @title("tenant id")
     */
    public $tenant_id;

    /**
     * @type("string")
     * @title("os")
     */
    public $os;

    /**
     * @type("string")
     * @title("username")
     */
    public $username;

    /**
     * @type("string")
     * @title("password")
     */
    public $password;

    /**
     * @type("string")
     * @title("name")
     */
    public $name;

    /**
     * @type("string")
     * @title("email")
     */
    public $email;

    public function retrieve() {
        $apsc = \APS\Request::getController();
        $dc = $apsc->getResource($this->dc->aps->id);
        
        error_log("Despues de dc");
        $os = new OS($dc->apiurl, $dc->user, $dc->password);

        error_log("Despues de OS");
        
        
        $query = array();
        $query[] = array("field" => "project_id", "op" => "eq", "value" => $this->tenant_id);
        
        
        error_log("Despues de dc".json_encode($query));
        
 
        /*if($this->cpu_counter->limit > $this->cpu_usage->usage){
            $this->cpu_usage->usage = 1;
        }
        
        if($this->memory_counter->limit > $this->memory_usage->usage){
            $this->memory_usage->usage = 1;
        }
        
        if($this->disk_size_counter->limit > $this->disk_size_usage->usage){
            $this->disk_size_usage->usage = 1;
        }
        
        if($this->ips_counter->limit > $this->ips_usage->usage){
            $this->ips_usage->usage = 1;
        }
        
        if($this->traffic_out_counter->limit > $this->ips_usage->usage){
            $this->traffic_out_usage->usage = 10;
        }*/

        $this->cpu_usage->usage = $os->getMeterStatistics("vcpus", $query, array("project_id"), null);
        $this->memory_usage->usage = $os->getMeterStatistics("memory", $query, array("project_id"), null);
        $this->disk_size_usage->usage = $os->getMeterStatistics('disk.root.size', $query, array("project_id"), null);
        $this->ips_usage->usage = $os->getMeterStatistics('ip.floating', $query, array("project_id"), null);
        $this->traffic_out_usage->usage = $os->getMeterStatistics('network.outgoing.bytes', $query, array("project_id"), null);
        
        
    }

    public function provision() {


        $apsc = clone \APS\Request::getController();
        $dc = $apsc->getResource($this->dc->aps->id);
        $os = new OS($dc->apiurl, $dc->user, $dc->password);

        try {
            date_default_timezone_set("Europe/Madrid");

            /*
             * Roles :
             * 1. admin 19161422093d482fb6540bcecde60f89
             * 2. _member_ 9fe2ff9ee4384b1894a90878d3e92bab
             * 3. heat_stack_user b59320c672764885b3c702308b5b5814
             * 4. ResellerAdmin d2477be31eb141649cc02325cb273561
             * 5. Member f3ffbe27d94741e9baeada7086a3b159
             */

            $apscAccount = $apsc->impersonate($this->aps->id);
            $apscAccount->resetSession();

            $admins = json_decode($apscAccount->getIo()->sendRequest(\APS\Proto::GET, "/aps/2/resources/" . $this->account->aps->id . "/users?implementing(http://parallels.com/aps/types/pa/admin-user/1.0)"));

            if (!empty($admins)) {
                $apscAdmin = clone $apscAccount; //->impersonate($this->app->aps->id);$apscAdmin->resetSession();
                $admin = $apscAdmin->getResource($admins[0]->aps->id);
            }

            $this->name = "Proj" . strtotime("now") . $admin->userId;
            $this->email = $admin->email;
            $this->username = $admin->login;
            $Tenant = $os->createTenant($this->name, "Tenant description " . $this->name, true);
            $this->tenant_id = $Tenant->tenant->id;


            $checkUserExists = $os->checkUserExists($this->username);
            
            if (count($checkUserExists->users) > 0) {
                
                $User = $checkUserExists->users[0];
                $uid = $User->id;
            } else {
                
                $admin->password = \APS\generatePassword(10);
                $this->password = $admin->password;
                $User = $os->createUsers($Tenant->tenant->id, "Default user " . $Tenant->tenant->id, "default", $this->email, $this->username, $this->password);
                $uid = $User->user->id;
            }

            /*
             * 
             * Save the administrator role, and the ROLES ID for member, adminitrator
             * 
             */
            $Roles = $os->grantRoleToProjectUser($Tenant->tenant->id, $uid, 'f3ffbe27d94741e9baeada7086a3b159');
            $Roles = $os->grantRoleToProjectUser($Tenant->tenant->id, 'fd1673ac1cb247baa18004e494db28e7', '19161422093d482fb6540bcecde60f89');

            $os->updateQuotasStorage($dc->api_tenant_id, $this->tenant_id, array("gigabytes" => $this->disk_size_counter->limit));
            $os->updateQuotasCompute($dc->api_tenant_id, $this->tenant_id, array('ram' => $this->memory_counter->limit, 'cores' => $this->cpu_counter->limit));
            $os->updateQuotasNetwork($this->tenant_id, array('floatingip' => $this->ips_counter->limit));
            
            
        } catch (Exception $e) {

            error_log("exception " . $e . "\r\n", 3, "/var/www/html/openstack/my-errors.log");
            throw new Exception("Error : " . $e);
        }
    }

    public function configure($new) {

        $apsc = clone \APS\Request::getController();
        $dc = $apsc->getResource($this->dc->aps->id);
        $os = new OS($dc->apiurl, $dc->user, $dc->password);
        error_log(json_encode($this->disk_size_counter));
        error_log("New configure\r\n", 3, "/var/www/html/openstack/my-errors.log");


        
        $os->updateQuotasStorage($dc->api_tenant_id, $this->tenant_id, array("gigabytes" => $this->disk_size_counter->limit));
        $os->updateQuotasCompute($dc->api_tenant_id, $this->tenant_id, array('ram' => $this->memory_counter->limit, 'cores' => $this->cpu_counter->limit));
        $os->updateQuotasNetwork($this->tenant_id, array('floatingips' => $this->ips_counter->limit));
    }

    public function unprovision() {
        return true;
    }

    public function upgrade() {
        
    }

}
