<?php
// vim: set tabstop=4:
defined('_JEXEC') or die;

require_once(JPATH_ADMINISTRATOR . '/components/com_j2store/library/plugins/app.php');

class plgJ2StoreApp_squarepricing extends J2StoreAppPlugin
{
	var $element = 'app_squarepricing';

    private $width, $height;

	function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
		JLoader::register('SquareCalculator', __DIR__ . '/squarecalculator.php');
	}

	function onJ2StoreGetAppView($row)
	{
		if(!$this->_isMe($row)){
			return null;
		}

		$html = $this->viewList();
		return $html;
	}

	function viewList()
	{
		return "";
	}

	function onJ2StoreGetPricingCalculators(&$calculators)
	{
		$calculators['square'] = 'Square';
	}
	

	function onJ2StoreBeforeAddToCartButton($product)
	{
		$html = "";
		if($product->variants->pricing_calculator === "square"){
			JFormHelper::addFormPath(__DIR__ . '/forms');
			$form = new JForm('dimmensions');
			$form->loadFile('dimmensions');
			$width_field = $form->getField('drobe_width');
			$heigth_field = $form->getField('drobe_height');

			$width_field->__set('id', 'drobe_width_'. $product->j2store_product_id);
			$heigth_field->__set('id', 'drobe_heigth_'. $product->j2store_product_id);
			
			$width_field->__set('onchange', "doAjaxPrice($product->j2store_product_id, '#add-to-cart-$product->j2store_product_id')");
			$heigth_field->__set('onchange', "doAjaxPrice($product->j2store_product_id, '#add-to-cart-$product->j2store_product_id')");

			$html .= $width_field->renderField();
			$html .= $heigth_field->renderField();

			$js = "<script type=\"text/javascript\">";
			$js .= "doAjaxPrice($product->j2store_product_id, '#add-to-cart-$product->j2store_product_id');";
			$js .= "</script>";

			$html .= $js;
		}

		return $html;
	}

	function onJ2StoreAfterCreateItemForAddToCart($item, $values)
	{
		$result = [];
		if(isset($values['drobe_width']) and isset($values['drobe_height'])){

				$dimmensions = new JObject;
				$width = $values['drobe_width'];
				$height = $values['drobe_height'];
		
				$dimmensions->width = $width;
				$dimmensions->height = $height;
		
				$result['cartitem_params'] = $this->getParamsJSON($dimmensions);
		}
		return $result;
	}

	private function getParamsJSON($object)
	{
		$params = new JRegistry();
		$params->loadObject($object);
		return $params->toString();
	}
}
