<!--
 * productPage.tpl
 *
 * File to display module section in product page
 *
 * @author     Younes Adounis
 * @copyright  2015 Younes Adounis
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    1.0.0
 * @since      File available since Release 1.0.0
 -->
 <div id="deliveryestimation">
	<a href="#deliveryestimation_modal" class="open_model"><img src="/modules/deliveryestimation/views/img/cart_delivery.png"><span>{$product_page_link_text}</span></a>
	<div id="deliveryestimation_modal">
		<div id="cart_page_first_paragraph">{$product_page_first_paragraph}</div>
		<div id="deliveryestimation_content">
			<form id="deliveryestimation_form">
				<label>{l s='Selectionnez votre département :' mod='deliveryestimation'}</label>
				<select id="deliveryestimation_zone" name="deliveryestimation_zone" data-type="product">
					<option value="0">{l s='Choisir' mod='deliveryestimation'}</option>
					{foreach from=$zones key=k item=v}<option value="{$v.id_zone}">{$v.name}</option>{/foreach}
				</select>
			</form>
			<div id="deliveryestimation_tab"></div>
		</div>
		<div id="cart_page_second_paragraph">{$product_page_second_paragraph}</div>
	</div>
</div>