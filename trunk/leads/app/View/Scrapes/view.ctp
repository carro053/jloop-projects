<table>
	<tbody>
		<?php
			
			echo $this->Html->tableCells(array(array(
				$this->Html->tag('strong', 'App'),
				$this->Html->link(
					$scrape['Scrape']['name'],
					$scrape['Scrape']['itunes_link'],
					array('target' => '_blank')
				)
			)));
			
		?>
	</tbody>
</table>