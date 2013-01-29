<?php
require_once 'Parsing.php';
//'Indiafreestuff',
class Parsingcoupon extends Parsing
{
	public function getCouponWebsites()
	{
		return array('FacebookSites','Coupons27','CouponDunia','Freekaamaal','Indiafreestuff');
	}

	public function isCoupon($msg){

		$pattern = array('/[0-9]+%/','/.*offer.*/i','/.*coupon.*/i','/.*promo.*/i','/.*sale.*/i','/.*discount.*/i','/.*price.*/i');
		foreach($pattern as $p){
			if(preg_match($p, $msg)){
				return true;
			}
		}
		return false;
	}
	public function shouldParse($text,$span)
	{
		$text = strtolower($text);
		$span = strtolower($span);
		$sites = $this->getWebsites();
		$sites = array_map('strtolower', $sites);
		foreach($sites as $site)
		{
			/* echo "***site=>".$site;
				echo "***text=>".$text;
			echo "***span=>".$span; */
			if(!strpos($span, $site))
			{
				if(strpos($text, $site) || strpos($text, $site)===0)
					return  $should = array('0'=>TRUE, '1'=>$site);
			}
			else return $should = array('0'=>TRUE, '1'=>$site);
		}
		return $should = array('0'=>FALSE);
	}
}