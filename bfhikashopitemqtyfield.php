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
	public static $_decimalpoint = null;

	public function __construct(&$subject, $config) {
		parent::__construct($subject, $config);

		Plgsystembfhikashopitemqtyfield::$fieldnamekeys = $this->params->get('fieldnamekeys');
		Plgsystembfhikashopitemqtyfield::$_decimalpoint = $this->params->get('decimalpoint', '.');
	}

	public static function getItemQuantity($product, $quantity)
	{
		if (!empty(Plgsystembfhikashopitemqtyfield::$fieldnamekeys))
		{
			foreach(Plgsystembfhikashopitemqtyfield::$fieldnamekeys as $fieldnamekey)
			{
				if (!empty($product->$fieldnamekey))
				{
					$value = $product->$fieldnamekey;
					if (Plgsystembfhikashopitemqtyfield::$_decimalpoint != '.')
					{
						$value = str_replace(Plgsystembfhikashopitemqtyfield::$_decimalpoint, '.', $value);
					}

					return $quantity * $value;
				}
			}
		}

		return $quantity;
	}

	public static function getDecimalPoint()
	{
		return self::$_decimalpoint;
	}
}

if (!function_exists('hikashop_product_price_for_quantity_in_cart') &&
	!function_exists('hikashop_product_price_for_quantity_in_order'))
{
	function hikashop_product_price_for_quantity_in_cart(&$product)
	{
		$currencyClass = hikashop_get('class.currency');
		$currencyClass->quantityPrices($product->prices,
			Plgsystembfhikashopitemqtyfield::getItemQuantity($product, @$product->cart_product_quantity),
			$product->cart_product_total_quantity);
	}

	function hikashop_product_price_for_quantity_in_order(&$product)
	{
		$quantity = Plgsystembfhikashopitemqtyfield::getItemQuantity($product, @$product->order_product_quantity);
		$product->order_product_total_price_no_vat = $product->order_product_price * $quantity;
		$product->order_product_total_price = ($product->order_product_price + $product->order_product_tax) * $quantity;
	}
}