<?php
defined('_JEXEC') or die;

require_once JPATH_ADMINISTRATOR. '/components/com_j2store/library/calculators/standardcalculator.php';
require_once JPATH_ADMINISTRATOR .'/components/com_j2store/helpers/j2store.php';

class SquareCalculator extends JObject
{
	private $config;
	private $standardCalculator;
    private static $widh, $height;
    
	public function __construct($config = array())
	{
		$this->config = $config;
		$this->standardCalculator = new StandardCalculator($config);
		parent::__construct($config);
	}

	function calculate()
	{
		$pricing = $this->standardCalculator->calculate();
		$pricing->calculator = 'square';
		$width = JFactory::getApplication()->input->getInt('drobe_width');
		$height = JFactory::getApplication()->input->getInt('drobe_height');

		if($width > 0 and $height > 0)	{
			 $pricing->price *= $width * $height / 100;
			 return $pricing;
		}

		$cart = F0FModel::getTmpInstance('Carts', 'J2StoreModel');
		$cart->getCart();

		$cartId = $cart->getCartId();
		$variantId = $this->config['variant']->j2store_variant_id;
		

		$query = JFactory::getDbo()->getQuery(true);
		$query = "SELECT cartitem_params FROM #__j2store_cartitems WHERE variant_id = $variantId AND cart_id = $cartId";			
		$str = JFactory::getDbo()->setQuery($query)->loadResult();
		if(!empty($str))
		{
			$dimmensions = json_decode($str);
		
			$pricing->price *= (int)$dimmensions->width * (int)$dimmensions->height / 100;
		}
		
		return $pricing;
	}
}
