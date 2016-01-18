<?php echo $this->Form->create(); ?>
<?php $minYear = '2005'; ?>
<?php $dateTimeAttr = array(
	'empty' => false,
	'Title' => 'Start Datum',
	'minYear' => $minYear,
	'maxYear' => date('Y')
); ?>
<div>
	<label for='SaleFrom'>Start Datum</label>
	<?php echo $this->Form->dateTime('from', 'DMY', '', $dateTimeAttr); ?>
</div>
<div>
	<label for='SaleTill'>End Datum</label>
	<?php echo $this->Form->dateTime('till', 'DMY', '', $dateTimeAttr); ?>
</div>
<div>
	<?php echo $this->Form->button('show'); ?>
</div>

<?php echo $this->Form->end(); ?>
<table>
	<tr>
		<?php foreach($columns as $k=>$column): ?>
		<th><?php echo $column; ?></th>
		<?php endforeach; ?>
	</tr>
	<?php foreach($sales as $sale): ?>
	<tr>
		<td><?php echo $sale['Customer']['firstname']. " " . $sale['Customer']['lastname']; ?></td>
		<td><?php echo $sale[0]['sales_count']; ?></td>
		<td><?php echo $sale[0]['sales_sum']; ?></td>
		<td><?php echo $sale[0]['sale_date']; ?></td>
	</tr>
	<?php endforeach; ?>
</table>
<?php echo $this->Html->link('export csv', array('action' => 'export', $from.','.$till), array('escape'=>false)); ?>
