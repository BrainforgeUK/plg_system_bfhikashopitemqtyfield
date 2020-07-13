<?php
/**
 * @package    HikaShop for Joomla!
 * @author    https://www.brainforge.co.uk
 * @copyright  (C) 2020 Jonathan Brain. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

use Joomla\CMS\Plugin\CMSPlugin;

defined('_JEXEC') or die('Restricted access');

class Plgsystembfhikashopitemqtyfield extends CMSPlugin
{
	public static $fieldnamekeys;

	public function __construct(&$subject, $config) {
		parent::__construct($subject, $config);

		Plgsystembfhikashopitemqtyfield::$fieldnamekeys = $this->params->get('fieldnamekeys');
	}

	public static function getItemQuantity($product)
	{
		$quantity = @$product->cart_product_quantity;

		if (!empty(Plgsystembfhikashopitemqtyfield::$fieldnamekeys))
		{
			foreach(Plgsystembfhikashopitemqtyfield::$fieldnamekeys as $fieldnamekey)
			{
				if (!empty($product->$fieldnamekey))
				{
					return $quantity * $product->$fieldnamekey;
				}
			}
		}

		return $quantity;
	}
}

if (!function_exists('hikashop_product_price_for_quantity_in_cart') &&
	!function_exists('hikashop_product_price_for_quantity_in_order'))
{
	function hikashop_product_price_for_quantity_in_cart(&$product)
	{
		$currencyClass = hikashop_get('class.currency');
		$currencyClass->quantityPrices($product->prices,
			Plgsystembfhikashopitemqtyfield::getItemQuantity($product),
			$product->cart_product_total_quantity);
	}

	function hikashop_product_price_for_quantity_in_order(&$product)
	{
		$quantity = Plgsystembfhikashopitemqtyfield::getItemQuantity($product);
		$product->order_product_total_price_no_vat = $product->order_product_price * $quantity;
		$product->order_product_total_price = ($product->order_product_price + $product->order_product_tax) * $quantity;
	}
}