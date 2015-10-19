<?php

/**
 * ajax.php
 *
 * File to hundle ajax calls
 *
 * @author     Younes Adounis
 * @copyright  2015 Younes Adounis
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    1.0.0
 * @since      File available since Release 1.0.0
 */

require_once(dirname(__FILE__).'../../../config/config.inc.php');
require_once(dirname(__FILE__).'../../../init.php');
require_once(dirname(__FILE__).'/deliveryestimation.php');

global $cookie;
global $smarty;
$context = Context::getContext();

$carriers = Carrier::getCarriers($cookie->id_lang, true, false, (int)$_POST['zone_id'], null, Carrier::ALL_CARRIERS);
$DeliveryEstimation = new DeliveryEstimation();
$shippingfee = array();

if (isset($_POST['type']) && $_POST['type'] == 'product'){
	$_POST['id_product_attribute'] = (isset($_POST['id_product_attribute'])) ? (int)$_POST['id_product_attribute'] : NULL;
	foreach ($carriers as $carrier) {
		$fees = $DeliveryEstimation->getShippingFeeForProduct((int)$_POST['zone_id'], (int)$carrier['id_carrier'], (int)$_POST['product_id'], $_POST['id_product_attribute'], $_POST['quantity']);
		if ($fees >= 0) {
			$shippingfee[$carrier['id_carrier']] = array(
				'id'	   => $carrier['id_carrier'],
				'name'     => $carrier['name'],
				'fees'     => $fees,
				'currency' => $context->currency->sign,
				'delay'    => $carrier['delay'],
				'position' => $carrier['position']
			);
		}
	}
}
else {
	foreach ($carriers as $carrier) {
		$fees = $DeliveryEstimation->getShippingFeeForCart((int)$_POST['zone_id'], (int)$carrier['id_carrier'], (int)$carrier['id_reference']);
		if ($fees >= 0) {
			$shippingfee[$carrier['id_carrier']] = array(
				'id'	   => $carrier['id_carrier'],
				'name'     => $carrier['name'],
				'fees'     => $fees,
				'currency' => $context->currency->sign,
				'delay'    => $carrier['delay'],
				'position' => $carrier['position']
			);
		}
	}
}

if (!empty($shippingfee)) {
	usort($shippingfee, function($a, $b) {
		if ($a['fees'] == $b['fees'])
	    	return $a['position'] - $b['position'];
	    else
	    	return $a['fees'] - $b['fees'];
	});
}

$context->smarty->assign(array('shippingfee' => $shippingfee));
$smarty->display(dirname(__FILE__) . '/views/templates/front/table.tpl');

?>