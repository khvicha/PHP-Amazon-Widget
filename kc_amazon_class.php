<?PHP

class kc__AFF_AMAZON {
	private $ACCESS_KEY;
	private $ACCESS_SECRET;
	private $REGION;
	
	private $URL_PARAMS = array();
	
	private $AWS_HOST = "webservices.amazon.";
	private $AWS_URI = "/onca/xml";

	
	/**
	 * Constructor
	 * 
	 * @param string $access_key
	 * @param string $access_secret
	 * @param string $associate_tag
	 * @param string $region
	 */
	public function __construct($access_key, $access_secret) {
		$this->ACCESS_KEY 		= $access_key;
		$this->ACCESS_SECRET 	= $access_secret;
				
		$this->URL_PARAMS["Service"]        = "AWSECommerceService";
		$this->URL_PARAMS["AWSAccessKeyId"] = $this->ACCESS_KEY;
		$this->URL_PARAMS["Version"]        = "2011-08-01";
		$this->URL_PARAMS["ResponseGroup"]	= "ItemAttributes";
		$this->URL_PARAMS["Operation"] 		= "ItemSearch";
	}
	
	
	
	/**
	 * Search Items
	 * 
	 * @param string $search_query
	 * @param string $associate_tag
	 * @param string $region
	 * @param number $result_couunt
	 * @return boolean|SimpleXMLElement
	 */
	public function search($search_query, $associate_tag, $region, $result_count=4) {
		
		if($search_query && $associate_tag && $region) {	
			$this->REGION = $region;
			$this->URL_PARAMS['AssociateTag'] 	= $associate_tag;
			$this->URL_PARAMS['Count'] 			= $result_count;
			$this->URL_PARAMS['Keywords'] 		= $search_query;
			$this->URL_PARAMS['SearchIndex'] 	= "All";
			
			return $this->aws_signed_request();
		}
		else 
			return false;
	}
	
	
	
	/**
	 * Signed Request
	 * 
	 * @return boolean|SimpleXMLElement 
	 */
	private function aws_signed_request() {
	
		$method = "GET";
		$host = $this->AWS_HOST.$this->REGION;
	
		$this->URL_PARAMS['Timestamp'] = gmdate("Y-m-d\TH:i:s\Z");
	
		ksort($this->URL_PARAMS);
	
		$canonicalized_query = array();
	
		foreach($this->URL_PARAMS as $param=>$value) {
			$param = str_replace("%7E", "~", rawurlencode($param));
			$value = str_replace("%7E", "~", rawurlencode($value));
			$canonicalized_query[] = $param."=".$value;
		}
	
		$canonicalized_query = implode("&", $canonicalized_query);
		$string_to_sign = $method."\n".$host."\n".$this->AWS_URI."\n".$canonicalized_query;
		
	
		$signature = base64_encode(hash_hmac("sha256", $string_to_sign, $this->ACCESS_SECRET, true));
		$signature = str_replace("%7E", "~", rawurlencode($signature));
	
		$request = "http://".$host.$this->AWS_URI."?".$canonicalized_query."&Signature=".$signature;

		$xml_response = file_get_contents($request);
	
			if ($xml_response === false) {
				return false;
			}
			else {
				return simplexml_load_string($xml_response);
			}
	
	}	
}

?>