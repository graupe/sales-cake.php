<?php
foreach ($columns as &$column):
	$column = '"'.preg_replace('/"/','""',$column).'"';
endforeach;
echo implode(',', $columns ). "\n";
foreach ($sales as $sale):
	/* quote the quotes */
	foreach ($sale['Customer'] as &$cell):
		// Escape double quotation marks
		$cell = '"' . preg_replace('/"/','""',$cell) . '"';
	endforeach;
	foreach ($sale['0'] as &$cell):
		// Escape double quotation marks
		$cell = '"' . preg_replace('/"/','""',$cell) . '"';
	endforeach;
	echo implode(',', $sale['Customer']);
	echo implode(',', $sale['0']) . "\n";
endforeach;
?>
