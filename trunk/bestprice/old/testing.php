<?php
	function urlRequest($url)
	{
		
		$cacheKey = md5($url);
		
		if(file_exists('cache/'.$cacheKey)){
			return file_get_contents('cache/'.$cacheKey);
		}
		
		$userAgent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)';
	 	$target_url = $url;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
		curl_setopt($ch, CURLOPT_URL,$target_url);
		curl_setopt($ch, CURLOPT_FAILONERROR, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_AUTOREFERER, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
		$html = curl_exec($ch);
		curl_close($ch);
		if (!$html) {
			echo "<br />cURL error number:" .curl_errno($ch);
			echo "<br />cURL error:" . curl_error($ch);
			exit;
		}
		file_put_contents('cache/'.$cacheKey,$html);
		return $html;
	}
	
	
	require_once 'phpQuery-onefile.php';
	
	$target_url = "http://www.shopclues.com/?subcats=Y&status=A&pname=Y&product_code=Y&match=all&pkeywords=Y&search_performed=Y&cid=0&dispatch=products.search&q=samsung+5600";
	$html = urlRequest($target_url);
	
	
	phpQuery::newDocumentHTML($html);
	foreach(pq('div.box_GridProduct') as $div){
		
		$image = pq($div)->find('a.box_metacategory_image')->children('img')->attr('src2');
		$a_link = pq($div)->find('.box_metacategory_name');
		$name = $a_link->html();
		$url = $a_link->attr('href');
		$org_price = pq($div)->find('.box_metacategory_price')->html();
		$disc_price = pq($div)->find('.box_metacategory_priceoffer')->html();
		echo $name;
		echo $image;
		echo $org_price;
		echo $disc_price;
		echo $url;
		echo '<br>';
	}
	
	die;
	//below abhishank code
	$target_url = "http://www.dealsandyou.com/Noida-East-Delhi-Deals";
	$html = urlRequest($target_url);
	$dom = new DOMDocument();
	@$dom->loadHTML($html);
	$xpath = new DOMXPath($dom);
	$parentnode = $xpath->evaluate(".//*[@class='todays-deals']/ul/li");
	?>
	<div border="1">
	<?php 
		$testnode = $xpath->evaluate("//div[contains(concat(' ', @class, ' '), ' todays-deals ')]//ul//li[1]//div[2]//div//a//span");
		echo $testnode->item(0)->nodeValue;
	?>
	
	</div>
	<table border="1">
		<tr>
			<th>Deal image</th>
			<th>Deal Title</th>
			<th>Deal Description</th>
			<th>Deal Origional Price</th>
			<th>Deal Final Price</th>
			<th>Deal Detail Link</th>
			<th>Deal Detail</th>
		</tr>
	<?php 
		for($i=0; $i<($parentnode->length); $i++) {
			$event = $parentnode->item($i);
			@$title = $xpath->query(".//*[@class='deal-title']/span", $event)->item(0)->nodeValue;
			@$dealImage = $xpath->query(".//*[@class='deal-thmb']/img/@src", $event)->item(0)->nodeValue;
			@$delDesc =  $xpath->query(".//*[@class='dl-sh-ds']", $event)->item(0)->nodeValue;
			@$orgPrice =  $xpath->query(".//*[@class='org-price']", $event)->item(0)->nodeValue;
			@$finalPrice =  $xpath->query(".//*[@class='deal-price']/span[2]", $event)->item(0)->nodeValue;
			if (!$finalPrice) {
				@$finalPrice = $xpath->query(".//*[@class='deal-price']/span", $event)->item(0)->nodeValue;
			}
			//for getting extra information 
			@$detaillink = $xpath->query(".//*[@class='dl-vw']/@href", $event)->item(0)->nodeValue;
			$extraData = urlRequest($detaillink);
			$extraDom = new DOMDocument();
			@$extraDom->loadHTML($extraData);
			$extraXpath = new DOMXPath($extraDom);
			$extraPnode = $extraXpath->evaluate(".//*[@class='fineprint1']/div//ul/li");
			$temp = "";
			$temp .="<ul>";
			for ($j = 0; $j < $extraPnode->length; $j++) {
				$etraEvent = $extraPnode->item($j);
				$temp .= "<li>".$etraEvent->nodeValue."</li>";
			}
			$temp .="</ul>";
	?>
		<tr>
			<td><img alt="<?php echo $title ?>" src="<?php echo $dealImage ?>"</td>
			<td><?php echo $title ?></td>
			<td><?php echo $delDesc ?></td>
			<td><?php echo $orgPrice ?></td>
			<td><?php echo $finalPrice ?></td>
			<td><?php echo $detaillink ?></td>
			<td><?php echo $temp ?></td>
		</tr>
	<?php 		
		}
	?>
	</table>
?>