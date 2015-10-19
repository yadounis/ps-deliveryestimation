<?php

/**
 * deliveryestimation.php
 *
 * Main module file
 *
 * @author     Younes Adounis
 * @copyright  2015 Younes Adounis
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    1.0.0
 * @since      File available since Release 1.0.0
 */

if (!defined('_PS_VERSION_'))
  	exit;

class DeliveryEstimation extends Module {

	public function __construct() {
		$this->name                   = 'deliveryestimation';
		$this->tab                    = 'front_office_features';
		$this->version                = '1.0.0';
		$this->author                 = 'AdounisYounes.com';
		$this->need_instance          = 0;
		$this->ps_versions_compliancy = array('min' => '1.4', 'max' => '1.6');
		$this->module_key 			  = "3ff6a03962a09a970cc681827c721736";

		parent::__construct();

		$this->displayName      = $this->l('Shipping cost estimation');
		$this->description      = $this->l('This module displays an estimation of the shipping cost of a specified product.');
		$this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
		
		if (!Configuration::get('MYMODULE_NAME'))      
			$this->warning = $this->l('No name provided');
	}

	public function install() {
		if (parent::install() 
			&& $this->registerHook('header') 
			&& $this->registerHook('extraRight')
			&& $this->registerHook('shoppingCart') 
			&& Configuration::updateValue('product_page_link_text', "Estimate this product shipping cost") 
			&& Configuration::updateValue('product_page_first_paragraph', "") 
			&& Configuration::updateValue('product_page_second_paragraph', "") 
			&& Configuration::updateValue('cart_page_link_text', "Estimate the cart shipping cost") 
			&& Configuration::updateValue('cart_page_first_paragraph', "") 
			&& Configuration::updateValue('cart_page_second_paragraph', "")
		) {
			return true;
		}
		return false;
	}

	public function uninstall() {
		return parent::uninstall();
	}

	public function getContent() {
	    $output = null;	 
	    if (Tools::isSubmit('submit'.$this->name)) {
	        $product_page_link_text = Tools::getValue('product_page_link_text');
	        $cart_page_link_text 	= Tools::getValue('cart_page_link_text');
	        if (!$product_page_link_text || empty($product_page_link_text) || !Validate::isCleanHtml($product_page_link_text) 
	        	&& !$cart_page_link_text || empty($cart_page_link_text) || !Validate::isCleanHtml($cart_page_link_text)
	        ) {
	            $output .= $this->displayError($this->l('Invalid Configuration value'));
	        }
	        else {
	        	// 1st fieldset
	            Configuration::updateValue('product_page_link_text', $product_page_link_text, true);
	            Configuration::updateValue('product_page_first_paragraph', Tools::getValue('product_page_first_paragraph'), true);
	            Configuration::updateValue('product_page_second_paragraph', Tools::getValue('product_page_second_paragraph'), true);
	            // 2nd fieldset
	            Configuration::updateValue('cart_page_link_text', $cart_page_link_text, true);
	            Configuration::updateValue('cart_page_first_paragraph', Tools::getValue('cart_page_first_paragraph'), true);
	            Configuration::updateValue('cart_page_second_paragraph', Tools::getValue('cart_page_second_paragraph'), true);
	            // Output
	            $output .= $this->displayConfirmation($this->l('Settings updated'));
	        }
	    }
	    return $output.$this->displayForm();
	}

	public function displayForm() {
	    // Get default language
	    $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
	    // Init Fields form array
	    $fields_form[0]['form'] = array(
	        'legend' => array(
	            'title' => $this->l('Product page'),
	        ),
	        'input' => array(
	            array(
					'type'         => 'textarea',
					'label'        => $this->l('Link text'),
					'name'         => 'product_page_link_text',
					'size'         => 80,
					'required'     => true,
					'class'        => 'rte',
					'autoload_rte' => true,
					'cols'         => 100,
					'rows'         => 5,
	            ),
	            array(
					'type'         => 'textarea',
					'label'        => $this->l('1st paragraph text'),
					'name'         => 'product_page_first_paragraph',
					'required'     => false,
					'cols'         => 100,
					'rows'         => 5,
					'class'        => 'rte',
					'autoload_rte' => true,
	            ),
	            array(
					'type'         => 'textarea',
					'label'        => $this->l('2nd paragraph text'),
					'name'         => 'product_page_second_paragraph',
					'required'     => false,
					'cols'         => 100,
					'rows'         => 5,
					'class'        => 'rte',
					'autoload_rte' => true,
	            ),
	        ),
	    );
	    $fields_form[1]['form'] = array(
	        'legend' => array(
	            'title' => $this->l('Cart page'),
	        ),
	        'input' => array(
	            array(
					'type'         => 'textarea',
					'label'        => $this->l('Link text'),
					'name'         => 'cart_page_link_text',
					'size'         => 80,
					'required'     => true,
					'class'        => 'rte',
					'autoload_rte' => true,
					'cols'         => 100,
					'rows'         => 5,
	            ),
	            array(
					'type'         => 'textarea',
					'label'        => $this->l('1st paragraph text'),
					'name'         => 'cart_page_first_paragraph',
					'required'     => false,
					'cols'         => 100,
					'rows'         => 5,
					'class'        => 'rte',
					'autoload_rte' => true,
	            ),
	            array(
					'type'         => 'textarea',
					'label'        => $this->l('2nd paragraph text'),
					'name'         => 'cart_page_second_paragraph',
					'required'     => false,
					'cols'         => 100,
					'rows'         => 5,
					'class'        => 'rte',
					'autoload_rte' => true,
	            ),
	        ),
	        'submit' => array(
	            'title' => $this->l('Save'),
	            'class' => 'button'
	        )
	    );
	    $helper = new HelperForm();
	    // Module, token and currentIndex
		$helper->module          = $this;
		$helper->name_controller = $this->name;
		$helper->token           = Tools::getAdminTokenLite('AdminModules');
		$helper->currentIndex    = AdminController::$currentIndex.'&configure='.$this->name;
	    // Language
		$helper->default_form_language    = $default_lang;
		$helper->allow_employee_form_lang = $default_lang;
	    // Title and toolbar
		$helper->title          = $this->displayName;
		$helper->show_toolbar   = true;
		$helper->toolbar_scroll = true;
		$helper->submit_action  = 'submit'.$this->name;
		$helper->toolbar_btn    = array(
	        'save' => array(
	            'desc' => $this->l('Save'),
	            'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'),
	        ),
	        'back' => array(
	            'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
	            'desc' => $this->l('Back to list')
	        )
	    );
	    // Load current value
	    $helper->fields_value = array(
			'product_page_link_text'        => Configuration::get('product_page_link_text'),
			'product_page_first_paragraph'  => Configuration::get('product_page_first_paragraph'),
			'product_page_second_paragraph' => Configuration::get('product_page_second_paragraph'),
			'cart_page_link_text'           => Configuration::get('cart_page_link_text'),
			'cart_page_first_paragraph'     => Configuration::get('cart_page_first_paragraph'),
			'cart_page_second_paragraph'    => Configuration::get('cart_page_second_paragraph'),
	    );
	    // Return
	    return $helper->generateForm($fields_form);
	}

	public function hookDisplayHeader() {
		$this->context->controller->addCSS($this->_path . 'views/css/deliveryestimation.css', 'all');
		$this->context->controller->addJS($this->_path . 'views/js/deliveryestimation.js');
		$this->context->controller->addJS($this->_path . 'views/js/jquery.cookie.js');
	}

	public function hookProductTab($params) {
		$this->context->smarty->assign(
			array(
				'product_page_link_text' => Configuration::get('product_page_link_text'),
			)
		);
        return $this->display(__FILE__, 'views/templates/hook/productTab.tpl');
    }

	public function hookProductTabContent($params) {
		$this->context->smarty->assign(
			array(
				'product_page_link_text'        => Configuration::get('product_page_link_text'),
				'product_page_first_paragraph'  => Configuration::get('product_page_first_paragraph'),
				'product_page_second_paragraph' => Configuration::get('product_page_second_paragraph'),
				'zones'							=> $this->getZones(),
			)
		);
		return $this->display(__FILE__, 'views/templates/hook/productTabContent.tpl');
	}

	public function hookExtraRight($params) {
		$this->context->smarty->assign(
			array(
				'product_page_link_text'        => Configuration::get('product_page_link_text'),
				'product_page_first_paragraph'  => Configuration::get('product_page_first_paragraph'),
				'product_page_second_paragraph' => Configuration::get('product_page_second_paragraph'),
				'zones'							=> $this->getZones(),
			)
		);
		return $this->display(__FILE__, 'views/templates/hook/productPage.tpl');
	}

	public function hookShoppingCart($params) {
		$this->context->smarty->assign(
			array(
				'cart_page_link_text'        => Configuration::get('cart_page_link_text'),
				'cart_page_first_paragraph'  => Configuration::get('cart_page_first_paragraph'),
				'cart_page_second_paragraph' => Configuration::get('cart_page_second_paragraph'),
				'zones'						 => $this->getZones(),
			)
		);
		return $this->display(__FILE__, 'views/templates/hook/shoppingCart.tpl');
	}

	/*
	Calculate the shipping price for a product
	*/

	public function getShippingFeeForProduct($id_zone, $id_carrier, $id_product, $id_product_attribute = NULL, $quatity = 1) {
		$carrier = new Carrier($id_carrier);
		$product = new Product($id_product, $id_product_attribute);
		if ($id_product_attribute) {
			$product_attribute = $product->getAttributeCombinationsById($id_product_attribute, $this->context->language->id);
			$product->weight += $product_attribute[0]['weight'];
		}
		$shipping_method = $carrier->getShippingMethod();
		$carrier_zone = Carrier::checkCarrierZone($id_carrier, $id_zone);
		if(empty($carrier_zone))
			return -1;	
		if (($shipping_method == Carrier::SHIPPING_METHOD_WEIGHT && $carrier->getMaxDeliveryPriceByWeight($id_zone) === false)
			|| ($shipping_method == Carrier::SHIPPING_METHOD_PRICE && $carrier->getMaxDeliveryPriceByPrice($id_zone) === false)) {
			return -1;
		}
		if (!$this->checkProductCarriers($product, $id_carrier)) {
			return -1;
		}
		if(version_compare('1.5', _PS_VERSION_, '<')) {
			//Check if one product contains a dimension which is bigger than carrier
			$productArray = array(
				'width'  => $product->width * $quatity,
				'height' => $product->height * $quatity,
				'depth'  => $product->depth * $quatity,
				'weight' => $product->weight * $quatity,
			);
			if(!$this->checkProduct($productArray, $carrier))
				return -1;
		}
		if ($shipping_method != Carrier::SHIPPING_METHOD_FREE) {
			if ($carrier->range_behavior) {
				// Get only carriers that have a range compatible with cart
				if (($shipping_method == Carrier::SHIPPING_METHOD_WEIGHT && (!Carrier::checkDeliveryPriceByWeight($carrier->id, $product->weight, $id_zone)))
					|| ($shipping_method == Carrier::SHIPPING_METHOD_PRICE
					&& (!Carrier::checkDeliveryPriceByPrice($carrier->id, round($product->getPrice(), 2) * $quatity, $id_zone)))) {
					return -1;
				}
			}	
		}
		//Calculate price of the shipping 
		if($shipping_method == Carrier::SHIPPING_METHOD_FREE) {
			$shipping_fee = 0;
		}
		else if($shipping_method == Carrier::SHIPPING_METHOD_WEIGHT) {
			$shipping_fee = $carrier->getDeliveryPriceByWeight($product->weight * $quatity, $id_zone);
		}
		else if($shipping_method == Carrier::SHIPPING_METHOD_PRICE) {
			$shipping_fee = $carrier->getDeliveryPriceByPrice(round($product->getPrice(), 2) * $quatity, $id_zone);
		}
		//Calculate price for the handling
		if($carrier->shipping_handling)
			$shipping_handling = Configuration::get('PS_SHIPPING_HANDLING');
		else
			$shipping_handling = 0;
		//Calculate global shipping fees
		$tax_rate = Tax::getCarrierTaxRate($id_carrier);		
		$global_shipping_fee = $shipping_fee + $shipping_handling;
		return round($global_shipping_fee * (1 + ($tax_rate / 100)), 2);
	}

	/*
	Calculate the cart shipping price
	*/

	public function getShippingFeeForCart($id_zone, $id_carrier, $id_reference) {
		$carrier = new Carrier($id_carrier);
		$shipping_method = $carrier->getShippingMethod();
		$carrier_zone = Carrier::checkCarrierZone($id_carrier, $id_zone);
		if(empty($carrier_zone))
			return -1;	
		if (($shipping_method == Carrier::SHIPPING_METHOD_WEIGHT && $carrier->getMaxDeliveryPriceByWeight($id_zone) === false)
			|| ($shipping_method == Carrier::SHIPPING_METHOD_PRICE && $carrier->getMaxDeliveryPriceByPrice($id_zone) === false))
		{
			return -1;
		}
		//var_dump($this->checkCartCarriers($this->context->cart, $id_carrier, $id_reference));
		if (!$this->checkCartCarriers($this->context->cart, $id_carrier, $id_reference)) {
			return -1;
		}
		if(version_compare('1.5', _PS_VERSION_, '<')) {
			//Check if one product contains a dimension which is bigger than carrier
			if($this->context->cart->id) {
				foreach ($this->context->cart->getProducts() as $product) {
					if(!$this->checkProduct($product, $carrier))
						return -1;
				}
			}
			else {
				return -1;
			}
		}
		if ($shipping_method != Carrier::SHIPPING_METHOD_FREE)
		{
			if ($carrier->range_behavior) {
				// Get only carriers that have a range compatible with cart
				if (($shipping_method == Carrier::SHIPPING_METHOD_WEIGHT && (!Carrier::checkDeliveryPriceByWeight($carrier->id, $this->context->cart->getTotalWeight(), $id_zone)))
					|| ($shipping_method == Carrier::SHIPPING_METHOD_PRICE
					&& (!Carrier::checkDeliveryPriceByPrice($carrier->id, $this->context->cart->getOrderTotal(true, Cart::BOTH_WITHOUT_SHIPPING), $id_zone))))
				{
					return -1;
				}
			}
		}
		//Calculate price of the shipping 
		if($shipping_method == Carrier::SHIPPING_METHOD_FREE) {
			$shipping_fee = 0;
		}
		else if($shipping_method == Carrier::SHIPPING_METHOD_WEIGHT) {
			$shipping_fee = $carrier->getDeliveryPriceByWeight($this->context->cart->getTotalWeight(), $id_zone);
		}
		else if($shipping_method == Carrier::SHIPPING_METHOD_PRICE) {
			$shipping_fee = $carrier->getDeliveryPriceByPrice($this->context->cart->getOrderTotal(true, Cart::BOTH_WITHOUT_SHIPPING), $id_zone);
		}
		//Calculate price for the handling
		if($carrier->shipping_handling)
			$shipping_handling = Configuration::get('PS_SHIPPING_HANDLING');
		else
			$shipping_handling = 0;
		//Calculate global shipping fees
		$tax_rate = Tax::getCarrierTaxRate($id_carrier);		
		$global_shipping_fee = $shipping_fee + $shipping_handling;
		return round($global_shipping_fee * (1 + ($tax_rate / 100)), 2);
	}

	/*
	Check the product and the carrier compatibility
	*/

	private function checkProduct($product, $carrier) {
		if($product['width'] > $carrier->max_width && $carrier->max_width != 0)
			return false;
		if($product['height'] > $carrier->max_height && $carrier->max_height != 0)
			return false;
		if($product['depth'] > $carrier->max_depth && $carrier->max_depth != 0)
			return false;
		if($product['weight'] > $carrier->max_weight && $carrier->max_weight != 0)
			return false;
		return true;
	}

	private function getZones() {
		$zones = Zone::getZones();
		foreach ($zones as $key => $value) {
			$zones[$key]['id'] = $zones[$key]['id_zone'];
		}
		return $zones;
	}

	/*
	Check if a product has a specified carrier
	*/

	private function checkProductCarriers($product, $id_carrier) {
		$carriers = $product->getCarriers();
		if (!empty($carriers)) {
			foreach ($product->getCarriers() as $carrier) {
				if ($id_carrier == $carrier['id_carrier']) {
					return true;
				}
			}
			return false;
		}
		return true;
	}

	private function checkCartCarriers($cart, $id_carrier, $id_reference) {
		$products = $cart->getProducts();
		if (!empty($products)) {
			foreach ($products as $product) {
				$count = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT COUNT(pc.`id_product`) AS `count` FROM `'._DB_PREFIX_.'product_carrier` pc WHERE pc.`id_product` = '.(int)$product['id_product']);
				if ($count[0]['count']) {
					$carriers = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT c.* FROM `'._DB_PREFIX_.'product_carrier` pc INNER JOIN `'._DB_PREFIX_.'carrier` c ON (c.`id_reference` =  pc.`id_carrier_reference` AND c.`deleted` = 0) WHERE pc.`id_product` = '.(int)$product['id_product'].' AND pc.`id_carrier_reference` = '.(int)$id_reference);
					foreach ($carriers as $carrier) {
						if ($id_carrier == $carrier['id_carrier']) {
							return true;
						}
					}
				}
				else {
					return true;
				}
			}
			return false;
		}
		return false;
	}

}

?>