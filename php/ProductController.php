<?php

namespace Realmdigital\Web\Controller;

use DDesrosiers\SilexAnnotations\Annotations as SLX;
use Silex\Application;

/**
 * @SLX\Controller(prefix="product/")
 */
class ProductController {

	/**
	 * @SLX\Route(
	 *      @SLX\Request(method="GET", uri="/{id}")
	 * )
	 * @param Application $app
	 * @param $name
	 * @return
	 */
	public function getById_GET(Application $app, $id){
		$requestData = array();
		$requestData['id'] = $id;

		return $app->render('products/product.detail.twig', $result);
	}

	/**
	 * @SLX\Route(
	 *      @SLX\Request(method="GET", uri="/search/{name}")
	 * )
	 * @param Application $app
	 * @param $name
	 * @return
	 */
	public function getByName_GET(Application $app, $name){
		$requestData = array();
		$requestData['names'] = $name;

		$result = $this->curlCall($requestData);

		return $app->render('products/products.twig', $result);	
	}


	// Private function doing a curl call to 192.138.0.241/eanlist return product information
	private function curlCall($data) {
		$curl = curl_init();

		// if this is used all over the site, I would have the $url in a config, especially if there is a dev and production environment
		$url = 'http://192.168.0.241/eanlist?type=Web';
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $requestData);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$response = curl_exec($curl);
		$response = json_decode($response);
		curl_close($curl);

		$result = array();
		for ($i = 0; $i < count($response); $i++) {
			$product = array();
			$product['ean'] = $response[$i]['barcode'];
			$product["name"] = $response[$i]['itemName'];
			$product["prices"] = array();
			for ($j = 0; $j < count($response[$i]['prices']); $j++) {
				if ($response[$i]['prices'][$j]['currencyCode'] != 'ZAR') {
					$productPrice = array();
					$productPrice['price'] = $response[$i]['prices'][$j]['sellingPrice'];
					$productPrice['currency'] = $response[$i]['prices'][$j]['currencyCode'];
					$product["prices"][] = $productPrice;
				}
			}
			$result[] = $product;
		}
	}

}
