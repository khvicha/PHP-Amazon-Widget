# PHP-Amazon-Widget
PHP Script to show Widgets of Amazon's Products on your Website.

## Installation
Before using this script you must be registered on Amazons Affiliate Program to get your **Tracking ID**: https://affiliate-program.amazon.com  and then get your **Access Key ID** and **Secret Access Key** from here: https://console.aws.amazon.com/iam/home#security_credential

After this fill you credentials in **kc_amazon_widget.php** file:
```php
$access_key = ""; //Your Access KEY
$access_secret = ""; //Your Access SECRET
$default_associate_tag 	= ""; //Your Associate Tag, same as Tracking ID
```

You can also add your Tracking IDs for each country separately (if you have), in this version following countries are supported:
- US
- DE
- UK
- ES
- FR
- IT
- CA


## Usage
This script is very simple to use, just put this code in your HTML file where you want to show Widget:

```php
include "kc_amazon_widget.php";
$result_html = kc_aws_get_widget("PC Tools", 4, "US");
echo $result_html;
```

**kc_aws_get_widget()** function need 3 parameters:
- Search Criteria
- Number of Widgets
- Country Code
 
##Website
http://softcatcher.com/?a=2&pi=39



