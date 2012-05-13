<table>
<tr><th>Sequence</th><th>Name</th><th>Memo</th></tr>
<?php

 //exercise_problems
 foreach($exercise->exercise_problems as $exercise_problem): 
 ?>
<tr>

<td>
<div class='sequence'>
	<?php echo CHtml::encode($exercise_problem->sequence); ?>
</div>
</td>
<td>
	<div class="title">
		<?php echo CHtml::link(nl2br(CHtml::encode($exercise_problem->title)),$exercise_problem->getUrl(null)); ?>
	</div>
</td>
<td>
	<div class="memo">
		<?php echo CHtml::encode($exercise_problem->memo); ?>
	</div>
</td>
<!-- exercise_problem -->
</tr>
<?php

endforeach; ?>
</table>
