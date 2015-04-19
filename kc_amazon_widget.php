<?PHP
/**
 * PHP Amazon Widget
 *
 * @package 	KC
 * @subpackage 	External Library
 * @category 	Affiliates
 * @author		Khvicha Chikhladze (github.khvicha@gmail.com)
 * @version		1.0.0
 */

include "kc_amazon_class.php";


$access_key = ""; //Your Access KEY
$access_secret = ""; //Your Access SECRET

$default_country_code 	= "US";
$default_aws_region 	= "com";
$default_associate_tag 	= ""; //Your Associate Tag, same as Tracking ID
$default_search_term	= "PC Tools";

$widget_params = array();
$widget_params['show_border']				= "true";
$widget_params['link_opens_in_new_window'] 	= "true";
$widget_params['bg_color']					= "FFFFFF";

//Only for 'US'
$widget_params['price_color']				= "333333";
$widget_params['title_color']				= "0066C0";

//for Other Countrues
$widget_params['text_color']				= "000000";
$widget_params['link_color']				= "0000FF";




/**
 * Get AWS Widget
 * 
 * @param string $search_term
 * @param string $result_count
 * @return string
 */
function kc_aws_get_widget($search_term, $result_count, $country_code="US") {
	global $default_country_code, $default_aws_region, $default_associate_tag, $default_search_term;
	
	$result = "";
	
	switch($country_code) {
		case 'US':
			$aws_region = "com";
			$associate_tag = ""; // Your Associate Tag, same as Tracking ID
		break;
		//--------------
		case 'DE':
			$aws_region = "de";
			$associate_tag = ""; // Your Associate Tag, same as Tracking ID
		break;
		//--------------
		case 'UK':
			$aws_region = "co.uk";
			$associate_tag = ""; // Your Associate Tag, same as Tracking ID
		break;
		//--------------
		case 'ES':
			$aws_region = "es";
			$associate_tag = ""; // Your Associate Tag, same as Tracking ID
		break;
		//--------------
		case 'FR':
			$aws_region = "fr";
			$associate_tag = ""; // Your Associate Tag, same as Tracking ID
		break;
		//--------------
		case 'IT':
			$aws_region = "it";
			$associate_tag = ""; // Your Associate Tag, same as Tracking ID
		break;
		//--------------
		case 'CA':
			$aws_region = "ca";
			$associate_tag = ""; // Your Associate Tag, same as Tracking ID
		break;
		//--------------
		default:
			$country_code 	= $default_country_code;
			$aws_region 	= $default_aws_region;
			$associate_tag 	= $default_associate_tag;
		break;		
	}
	

	//start search
	$arr = kc_aws_get_html($aws_region, $country_code, $associate_tag, $search_term, $result_count);

	if($arr[0] > 0) {
		$result = $arr[1];

		//if number of found items is less then requested quantity, then fill it up to requested quantity using 'default search term'
		if($arr[0] < $result_count) {
			$result_count = $result_count - $arr[0];

			$arr2 = kc_aws_get_html($aws_region, $country_code, $associate_tag, $default_search_term, $result_count);
				if($arr2[0] > 0) {
					$result .= $arr2[1];
				}
		}
		//----------------------
	}
	else { // if nothing was found then try to search by 'default search term'
		$arr = kc_aws_get_html($aws_region, $country_code, $associate_tag, $default_search_term, $result_count);

		if($arr[0] > 0) {
			$result = $arr[1];
		}
		elseif($country_code != $default_country_code) { // if previous searches were in different country and nothing was found then try to find in default country
			$arr = kc_aws_get_html($default_aws_region, $default_country_code, $default_associate_tag, $default_search_term, $result_count);

			if($arr[0] > 0) {
				$result = $arr[1];
			}
		}
	}

	return $result;
}


/**
 * Get Html
 * 
 * @param string $aws_region
 * @param string $country_code
 * @param string $associate_tag
 * @param string $search_term
 * @param string $result_count
 * @return array
 */
function kc_aws_get_html($aws_region, $country_code, $associate_tag, $search_term, $result_count) {
	
	global $access_key, $access_secret;
	
	$amz = new kc__AFF_AMAZON($access_key, $access_secret);
	$xml_obj = $amz->search(trim($search_term), $associate_tag, $aws_region, $result_count);
	
	$result = array();
	$result[0] = 0;  //found items
	$result[1] = ""; //html code


	if($xml_obj && !empty($xml_obj->Items->Item)) {
		$found_items = count($xml_obj->Items->Item);

		if($found_items) {
			$html = "";
				for($i=0; $i<$found_items; $i++) {
					$ASIN = $xml_obj->Items->Item[$i]->ASIN;
					$html .= kc_aws_generate_html($country_code, $associate_tag, $ASIN);
					$html .= "\n";
				}

			$result[0] = $found_items;
			$result[1] = $html;
		}
	}

	return $result;
}


/**
 * Generate Html Code
 * 
 * @param string $country_code
 * @param string $associate_tag
 * @param string $ASIN
 * @return string
 */
function kc_aws_generate_html($country_code, $associate_tag, $ASIN) {
	global $widget_params;
	
	$border = ($widget_params['show_border'] == "true") ? "&bc1=000000" : "&bc1=FFFFFF";
	$newwin = ($widget_params['link_opens_in_new_window'] == "true") ? "lt1=_blank" : "lt1=_top";
	$bg_color = "&bg1=".$widget_params['bg_color'];
	$text_color = "&fc1=".$widget_params['text_color'];
	$link_color = "&lc1=".$widget_params['link_color'];
	
	
	switch($country_code) {
		case 'US':
			return "<iframe style=\"width:120px;height:240px;\" marginwidth=\"0\" marginheight=\"0\" scrolling=\"no\" frameborder=\"0\" src=\"//ws-na.amazon-adsystem.com/widgets/q?ServiceVersion=20070822&OneJS=1&Operation=GetAdHtml&MarketPlace=".$country_code."&source=ac&ref=tf_til&ad_type=product_link&tracking_id=".$associate_tag."&marketplace=amazon&region=".$country_code."&asins=".$ASIN."&show_border={$widget_params['show_border']}&link_opens_in_new_window={$widget_params['link_opens_in_new_window']}&price_color={$widget_params['price_color']}&title_color={$widget_params['title_color']}&bg_color={$widget_params['bg_color']}\"></iframe>";
		break;
		//-------------------
		case 'DE':
			return "<iframe src=\"http://rcm-eu.amazon-adsystem.com/e/cm?".$newwin.$border."&IS2=1&nou=1".$bg_color.$text_color.$link_color."&t=".$associate_tag."&o=3&p=8&l=as1&m=amazon&f=ifr&ref=tf_til&asins=".$ASIN."\" style=\"width:120px;height:240px;\" scrolling=\"no\" marginwidth=\"0\" marginheight=\"0\" frameborder=\"0\"></iframe>";
		break;
		//-------------------
		case 'FR':
			return "<iframe src=\"http://rcm-eu.amazon-adsystem.com/e/cm?".$newwin.$border."&IS2=1&nou=1".$bg_color.$text_color.$link_color."&t=".$associate_tag."&o=8&p=8&l=as1&m=amazon&f=ifr&ref=tf_til&asins=".$ASIN."\" style=\"width:120px;height:240px;\" scrolling=\"no\" marginwidth=\"0\" marginheight=\"0\" frameborder=\"0\"></iframe>";
		break;
		//-------------------
		case 'UK':
			return "<iframe src=\"http://rcm-eu.amazon-adsystem.com/e/cm?".$newwin.$border."&IS2=1&nou=1".$bg_color.$text_color.$link_color."&t=".$associate_tag."&o=2&p=8&l=as1&m=amazon&f=ifr&ref=tf_til&asins=".$ASIN."\" style=\"width:120px;height:240px;\" scrolling=\"no\" marginwidth=\"0\" marginheight=\"0\" frameborder=\"0\"></iframe>";
		break;
		//-------------------
		case 'CA':
			return "<iframe src=\"http://rcm-na.amazon-adsystem.com/e/cm?".$newwin.$border."&IS2=1&nou=1".$bg_color.$text_color.$link_color."&t=".$associate_tag."&o=15&p=8&l=as1&m=amazon&f=ifr&ref=tf_til&asins=".$ASIN."\" style=\"width:120px;height:240px;\" scrolling=\"no\" marginwidth=\"0\" marginheight=\"0\" frameborder=\"0\"></iframe>";
		break;
		//-------------------
		case 'IT':
			return "<iframe src=\"http://rcm-eu.amazon-adsystem.com/e/cm?".$newwin.$border."&IS2=1&nou=1".$bg_color.$text_color.$link_color."&t=".$associate_tag."&o=29&p=8&l=as1&m=amazon&f=ifr&ref=tf_til&asins=".$ASIN."\" style=\"width:120px;height:240px;\" scrolling=\"no\" marginwidth=\"0\" marginheight=\"0\" frameborder=\"0\"></iframe>";
		break;
		//-------------------
		case 'ES':
			return "<iframe src=\"http://rcm-eu.amazon-adsystem.com/e/cm?".$newwin.$border."&IS2=1&nou=1".$bg_color.$text_color.$link_color."&t=".$associate_tag."&o=30&p=8&l=as1&m=amazon&f=ifr&ref=tf_til&asins=".$ASIN."\" style=\"width:120px;height:240px;\" scrolling=\"no\" marginwidth=\"0\" marginheight=\"0\" frameborder=\"0\"></iframe>";
		break;
		//-------------------
		default: //same as 'US'
			return "<iframe style=\"width:120px;height:240px;\" marginwidth=\"0\" marginheight=\"0\" scrolling=\"no\" frameborder=\"0\" src=\"//ws-na.amazon-adsystem.com/widgets/q?ServiceVersion=20070822&OneJS=1&Operation=GetAdHtml&MarketPlace=".$country_code."&source=ac&ref=tf_til&ad_type=product_link&tracking_id=".$associate_tag."&marketplace=amazon&region=".$country_code."&asins=".$ASIN."&show_border={$widget_params['show_border']}&link_opens_in_new_window={$widget_params['link_opens_in_new_window']}&price_color={$widget_params['price_color']}&title_color={$widget_params['title_color']}&bg_color={$widget_params['bg_color']}\"></iframe>";
		break;
	}
}
?>