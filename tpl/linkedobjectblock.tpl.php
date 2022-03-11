<?php
/* Copyright (C) 2019 ATM Consulting <support@atm-consulting.fr>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

// Protection to avoid direct call of template
if (empty($conf) || ! is_object($conf)) {
    print "Error, template page can't be called as URL";
    exit;
}

?>

<!-- BEGIN PHP TEMPLATE -->

<?php

global $db,$user;

$langs = $GLOBALS['langs'];
$linkedObjectBlock = $GLOBALS['linkedObjectBlock'];

$langs->load("chiffrage@chiffrage");
?>


<?php
$var=true;
$total=0;
foreach($linkedObjectBlock as $key => $objectlink)
{
	$var=!$var;
	/** @var Chiffrage $objectlink */
?>
<tr <?php echo $GLOBALS['bc'][$var]; ?> >
    <td><?php echo $langs->trans('Chiffrage') ?></td>
	<td class="left"><?php echo $objectlink->getNomUrl(1); ?></td>
	<td class="left"></td>
	<td class="center"><?php print $objectlink->showOutputField($objectlink->fields['estimate_date'],'estimate_date', $objectlink->estimate_date) ?></td>
<!--	<td class="right linked-objet-amount" title="Ive">--><?php //print $objectlink->showOutputField($objectlink->fields['amount'],'amount', $objectlink->amount) ?><!--</td>-->
	<td class="right linked-objet-amount" title="Ive"><?php print $objectlink->showOutputField($objectlink->fields['qty'],'qty', $objectlink->qty) ?>&nbsp;JH</td>
	<td class="right"><?php echo $objectlink->getLibStatut(3); ?></td>

	<td class="right"><a href="<?php echo $_SERVER["PHP_SELF"].'?id='.$object->id.'&action=dellink&dellinkid='.$key; ?>"><?php echo img_picto($langs->transnoentitiesnoconv("RemoveLink"), 'unlink'); ?></a></td>
</tr>
<?php
}
?>
<!-- END PHP TEMPLATE -->
