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
			echo $this->Html->tableCells(array(array(
				$this->Html->tag('strong', 'Universal'),
				$scrape['Scrape']['is_universal'] ? 'Yes' : 'No'
			)));
			echo $this->Html->tableCells(array(array(
				$this->Html->tag('strong', 'Price'),
				$scrape['Scrape']['price']
			)));
			echo $this->Html->tableCells(array(array(
				$this->Html->tag('strong', 'Category'),
				$scrape['Scrape']['category']
			)));
			echo $this->Html->tableCells(array(array(
				$this->Html->tag('strong', $scrape['Scrape']['is_updated'] ? 'Updated' : 'Released'),
				$scrape['Scrape']['updated']
			)));
			echo $this->Html->tableCells(array(array(
				$this->Html->tag('strong', 'Version'),
				$scrape['Scrape']['version']
			)));
			echo $this->Html->tableCells(array(array(
				$this->Html->tag('strong', 'Size'),
				$scrape['Scrape']['size']
			)));
			echo $this->Html->tableCells(array(array(
				$this->Html->tag('strong', 'Languages'),
				$scrape['Scrape']['languages']
			)));
			echo $this->Html->tableCells(array(array(
				$this->Html->tag('strong', 'Seller'),
				$scrape['Scrape']['seller']
			)));
			echo $this->Html->tableCells(array(array(
				$this->Html->tag('strong', 'Copyright'),
				$scrape['Scrape']['copyright']
			)));
			echo $this->Html->tableCells(array(array(
				$this->Html->tag('strong', 'Requirements'),
				$scrape['Scrape']['requirements']
			)));
			if(!empty($scrape['Scrape']['ratings_current']))
				echo $this->Html->tableCells(array(array(
					$this->Html->tag('strong', 'Current Version'),
					$scrape['Scrape']['ratings_current'].' - '.$scrape['Scrape']['ratings_current_count'].' ratings'
				)));
			if(!empty($scrape['Scrape']['ratings_all']))
				echo $this->Html->tableCells(array(array(
					$this->Html->tag('strong', 'All Versions'),
					$scrape['Scrape']['ratings_all'].' - '.$scrape['Scrape']['ratings_all_count'].' ratings'
				)));
			echo $this->Html->tableCells(array(array(
				$this->Html->tag('strong', 'Description'),
				$scrape['Scrape']['description']
			)));
			if(!empty($scrape['Scrape']['link_1_href']))
				echo $this->Html->tableCells(array(array(
					'',
					$this->Html->link(
						$scrape['Scrape']['link_1_name'],
						$scrape['Scrape']['link_1_href'],
						array('target' => '_blank')
					)
				)));
			if(!empty($scrape['Scrape']['link_2_href']))
				echo $this->Html->tableCells(array(array(
					'',
					$this->Html->link(
						$scrape['Scrape']['link_2_name'],
						$scrape['Scrape']['link_2_href'],
						array('target' => '_blank')
					)
				)));
			if(!empty($scrape['Scrape']['link_3_href']))
				echo $this->Html->tableCells(array(array(
					'',
					$this->Html->link(
						$scrape['Scrape']['link_3_name'],
						$scrape['Scrape']['link_3_href'],
						array('target' => '_blank')
					)
				)));
			
		?>
	</tbody>
</table>

<h3>Make it a Lead!</h3>

<?php echo $this->element('lead_form', array('lead' => $scrape['Lead'])); ?>

<script type="text/javascript">
	try {
		init();
	}
	catch(error){
		console.warn(error);
	}
</script>