<!--
 * table.tpl
 *
 * File to display estimations
 *
 * @author     Younes Adounis
 * @copyright  2015 Younes Adounis
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    1.0.0
 * @since      File available since Release 1.0.0
 -->

<table>
	<thead>
		<td width="48%">{l s='Transporteur' mod='deliveryestimation'}</td>
		<td width="7%">{l s='Prix T.T.C' mod='deliveryestimation'}</td>
		<td width="45%">{l s='Délais et conditions' mod='deliveryestimation'}</td>
	</thead>
	<tbody>
	{foreach from=$shippingfee item=i}
		<tr>
			<td><img width="120px" src="/img/s/{$i.id}.jpg" alt="logo {$i.name}">{$i.name}</td>
			<td>{$i.fees} {$i.currency}</td>
			<td>{$i.delay}</td>
		</tr>
	{/foreach}
	</tbody>
</table>