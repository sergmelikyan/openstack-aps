<?php

require "common.php";


class os_metric {
	
	/**
	* @type("string")
	* @title("Measure")
	*/
	public $measure;
	/**
	* @type("string")
	* @title("value")
	*/
	public $value;
	
	public function __construct($measure, $value){
		$this->measure = $measure;
		$this->value = $value;
	}
}

class statistics_historic{
	/**
	 * @type("string")
	 * @title("Date")
	 */
	public $date;
	
	/**
	 * @type("os_metric")
	 * @title("Cpu")
	 */
	public $cpu;
	
	/**
	 * @type("os_metric")
	 * @title("Memory")
	 */
	public $memory;
	
	/**
	 * @type("os_metric")
	 * @title("Disk Size")
	 */
	public $disk_size;
	
	/**
	 * @type("os_metric")
	 * @title("IPs")
	 */
	public $ips;
	
	/**
	 * @type("os_metric")
	 * @title("Traffic Out")
	 */
	public $traffic_out;
	
	

	public function __construct($cpu, $memory, $disk_size, $ips, $traffic_out){
		$this->cpu = $cpu;
		$this->memory = $memory;
		$this->disk_size = $disk_size;
		$this->ips = $ips;
		$this->traffic_out = $traffic_out;
		$this->date = strtotime("now");
	}
}


/**
 * Class Organization
 * @author("The Mamasu Agency")
 * @type("http://openstack.parallels.com/organization/1.3")
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
    
    /**
     * @type("statistics_historic[]")
     * @title("Statistics Historic")
     */
    public $statistics_historic;
    
    
    public function retrieve() {
    	
        $apsc = \APS\Request::getController();
        $dc = $apsc->getResource($this->dc->aps->id);
        $os = new OS($dc->apiurl, $dc->user, $dc->password);
        
        
        
        error_log("----------Entering retrieve---------"."\r\n", 3, '/var/www/html/openstack/controllers/retrieve.txt');
        
        
        $query = array();
        $query[] = array("field" => "timestamp", "op" => "ge", "value" => date("Y-m-d\T00:00:00", strtotime("yesterday")));
        $query[] = array("field" => "timestamp", "op" => "lt", "value" => date("Y-m-d\T00:00:00", strtotime("today")));
        $query[] = array(
        		"field" => "project_id",
        		"op" => "eq",
        		"value" => $this->tenant_id
        );
        
     
        
        if($this->tenant_id != null and $os->checkProjectExits($this->tenant_id)){
	        /*
	         * Check if it is a Flat or a PAYG subscription
	         */
        	error_log("----------NEW PROJECT ".$this->tenant_id."---------"."\r\n", 3, '/var/www/html/openstack/controllers/retrieve.txt');
	        if($this->cpu_counter->limit == -1 AND $this->memory_counter->limit == -1 
	        		AND $this->disk_size_counter->limit == -1 AND $this->ips_counter->limit == -1 
	        		AND $this->traffic_out_counter->limit == -1){
	        	/*
	        	 * PAYG
	        	 */

	        	$this->cpu_usage->usage += intval($os->getMeterStatistics("vcpus", $query, array("resource_id"), null));
	        	$this->memory_usage->usage += intval($os->getMeterStatistics("memory", $query, array("resource_id"), null));
	        	$this->disk_size_usage->usage += intval($os->getMeterStatistics('disk.root.size', $query, array("resource_id"), null));
	        	$this->ips_usage->usage += intval($os->getMeterStatistics('ip.floating', $query, array("resource_id"), null));
	        	$this->traffic_out_usage->usage += intval($os->getMeterStatistics('network.outgoing.bytes', $query, array("resource_id"), null));
	        	
	        	error_log("---------------PAYG----------------"."\r\n", 3, '/var/www/html/openstack/controllers/retrieve.txt');
	        	error_log("----------".$this->tenant_id."---------"."\r\n", 3, '/var/www/html/openstack/controllers/retrieve.txt');
				error_log("CPU usage = ". $this->cpu_usage->usage."\r\n", 3, '/var/www/html/openstack/controllers/retrieve.txt');
				error_log("Memory usage = ". $this->memory_usage->usage."\r\n", 3, '/var/www/html/openstack/controllers/retrieve.txt');
				error_log("Disk size usage = ". $this->disk_size_usage->usage."\r\n", 3, '/var/www/html/openstack/controllers/retrieve.txt');
				error_log("Ips usage = ". $this->ips_usage->usage."\r\n", 3, '/var/www/html/openstack/controllers/retrieve.txt');
				error_log("Traffic out usage = ". $this->traffic_out_usage->usage."\r\n", 3, '/var/www/html/openstack/controllers/retrieve.txt');
	        	error_log("-------------------------------------"."\r\n", 3, '/var/www/html/openstack/controllers/retrieve.txt');
	        	
	        	
	        	
	        	
	        	$cpu = new os_metric("vcpus",$this->cpu_usage->usage);
	        	$memory = new os_metric("memory",$this->memory_usage->usage);
	        	$disk_size = new os_metric("disk.root.size",$this->disk_size_usage->usage);
	        	$ips = new os_metric("ip.floating",$this->ips_usage->usage);
	        	$traffic_out = new os_metric("network.outgoing.bytes", $this->traffic_out_usage->usage);
	        	$this->statistics_historic[] = new statistics_historic($cpu, $memory, $disk_size, $ips, $traffic_out);
	        	

	        }else{
	        	
	        	
	        	/*
	        	 * Flat
	        	 */
	        	
	        	$this->cpu_counter->usage = intval($os->getMeterStatistics("vcpus", $query, array("resource_id"), null));
	        	$this->memory_counter->usage = intval($os->getMeterStatistics("memory", $query, array("resource_id"), null));
	        	$this->disk_size_counter->usage = intval($os->getMeterStatistics('disk.root.size', $query, array("resource_id"), null));
	        	$this->ips_counter->usage = intval($os->getMeterStatistics('ip.floating', $query, array("resource_id"), null));
	        	$this->traffic_out_counter->usage = intval($os->getMeterStatistics('network.outgoing.bytes', $query, array("resource_id"), null));
	        	
	        	error_log("---------------FLAT----------------"."\r\n", 3, '/var/www/html/openstack/controllers/retrieve.txt');
	        	error_log("----------".$this->tenant_id."---------"."\r\n", 3, '/var/www/html/openstack/controllers/retrieve.txt');
	        	error_log("CPU usage = ". $this->cpu_counter->usage."\r\n", 3, '/var/www/html/openstack/controllers/retrieve.txt');
	        	error_log("Memory usage = ". $this->memory_counter->usage."\r\n", 3, '/var/www/html/openstack/controllers/retrieve.txt');
	        	error_log("Disk size usage = ". $this->disk_size_counter->usage."\r\n", 3, '/var/www/html/openstack/controllers/retrieve.txt');
	        	error_log("Ips usage = ". $this->ips_counter->usage."\r\n", 3, '/var/www/html/openstack/controllers/retrieve.txt');
	        	error_log("Traffic out usage = ". $this->traffic_out_counter->usage."\r\n", 3, '/var/www/html/openstack/controllers/retrieve.txt');
	        	error_log("-------------------------------------"."\r\n", 3, '/var/www/html/openstack/controllers/retrieve.txt');
	        	
	        	
	        	
				$cpu = new os_metric("vcpus",$this->cpu_counter->usage);
	        	$memory = new os_metric("memory",$this->memory_counter->usage);
	        	$disk_size = new os_metric("disk.root.size",$this->disk_size_counter->usage);
	        	$ips = new os_metric("ip.floating",$this->ips_counter->usage);
	        	$traffic_out = new os_metric("network.outgoing.bytes", $this->traffic_out_counter->usage);
	        	$this->statistics_historic[] = new statistics_historic($cpu, $memory, $disk_size, $ips, $traffic_out);
	        	
	        }
        }
    }

    public function provision() {


        $apsc = clone \APS\Request::getController();
        $dc = $apsc->getResource($this->dc->aps->id);
        $os = new OS($dc->apiurl, $dc->user, $dc->password);

        try {
           
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
                $apscAdmin = clone $apscAccount;
                $admin = $apscAdmin->getResource($admins[0]->aps->id);
            }


            $checkUserExists = $os->checkUserExists($admin->login);
            
            $this->name = "Proj" . strtotime("now") . $admin->userId;
            $this->email = $admin->email;
            $this->username = $admin->login;
            $Tenant = $os->createTenant($this->name, "Tenant description " . $this->name, true);
            $this->tenant_id = $Tenant->tenant->id;
            
            if (count($checkUserExists->users) > 0) {

                $User = $checkUserExists->users[0];
                $uid = $User->id;
                $organizations = json_decode($apscAccount->getIo()->sendRequest(\APS\Proto::GET, "/aps/2/resources/?implementing(http://openstack.parallels.com/organization)&eq(aps.status,aps:ready)"),true);
				$this->password = $organizations[0]['password'];
				if(!isset($organizations[0]['password'])) throw new Exception("Unable to find previous password");
				
            } else {
            
                $admin->password = \APS\generatePassword(10);
                $this->password = $admin->password;
                $User = $os->createUsers($Tenant->tenant->id, "Default user " . $Tenant->tenant->id, "default", $this->email, $this->username, $this->password);
                $uid = $User->user->id;
            }
            
            /*
             * IMPORTANTE
             * Save the administrator role, and the ROLES ID for member, adminitrator
             * IMPORTANTE
             */
            $Roles = $os->grantRoleToProjectUser($Tenant->tenant->id, $uid, 'f3ffbe27d94741e9baeada7086a3b159');
            $Roles = $os->grantRoleToProjectUser($Tenant->tenant->id, 'fd1673ac1cb247baa18004e494db28e7', '19161422093d482fb6540bcecde60f89');            
            
            $os->updateQuotasStorage($this->tenant_id, array("gigabytes" => $this->disk_size_counter->limit));
            $os->updateQuotasCompute($this->tenant_id, array('ram' => $this->memory_counter->limit, 'cores' => $this->cpu_counter->limit));
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
        
        $os->updateQuotasStorage($this->tenant_id, array("gigabytes" => $this->disk_size_counter->limit));
        $os->updateQuotasCompute($this->tenant_id, array('ram' => $this->memory_counter->limit, 'cores' => $this->cpu_counter->limit));
        $os->updateQuotasNetwork($this->tenant_id, array('floatingips' => $this->ips_counter->limit));
    }

    public function unprovision() {
        return true;
    }

    public function upgrade() {
        
    }

}
