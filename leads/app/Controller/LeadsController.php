<?php
App::uses('AppController', 'Controller');

class LeadsController extends AppController {

	var $uses = array('Lead','Note');

	public function index() {
		$types_raw = $this->Lead->find('all', array(
			'fields' => array(
				'DISTINCT type'
			)
		));
		$types = array('' => 'Any');
		foreach($types_raw as $type) {
			$types[$type['Lead']['type']] = $type['Lead']['type'];
		}
		$this->set('types', $types);
		
		$groups = array('' => 'New');
		$groups += $this->Lead->Group->find('list');
		$this->set('groups', $groups);
		
		$tags = $this->Lead->Tag->find('all');
		$this->set('tags', $tags);
		
		if(!isset($_GET['IncludeTag']) && !isset($_GET['form'])) {
			foreach($tags as $tag) {
				if($tag['Tag']['filter_default'] == 'Included')
					$_GET['IncludeTag'][] = $tag['Tag']['id'];
			}
		}
		if(!isset($_GET['ExcludeTag']) && !isset($_GET['form'])) {
			foreach($tags as $tag) {
				if($tag['Tag']['filter_default'] == 'Excluded')
					$_GET['ExcludeTag'][] = $tag['Tag']['id'];
			}
		}
		
		$limit = (!empty($_GET['limit']) ? $_GET['limit'] : 50);
		$page = (!empty($_GET['page']) ? $_GET['page'] : 1);
		$order = (!empty($_GET['order']) ? $_GET['order'] : 'rating').' '.(!empty($_GET['direction']) ? $_GET['direction'] : 'desc');
		
		$conditions = array();
		$conditions[] = 'Lead.status > 0';
		
		//search conditions
		if(!empty($_GET['type'])) {
			$conditions['Lead.type'] = $_GET['type'];
		}
		if(!empty($_GET['search'])) {
			$conditions['OR']['Lead.name LIKE'] = '%'.$_GET['search'].'%';
			$conditions['OR']['Lead.email LIKE'] = '%'.$_GET['search'].'%';
			$conditions['OR']['Lead.twitter LIKE'] = '%'.$_GET['search'].'%';
			$conditions['OR']['Lead.facebook LIKE'] = '%'.$_GET['search'].'%';
			$conditions['OR']['Lead.linkedin LIKE'] = '%'.$_GET['search'].'%';
		}
		if(!empty($_GET['IncludeTag'])) {
			$conditions[] = array('Lead.id IN (SELECT `lead_id` from `leads_tags` WHERE `tag_id` IN ('.implode(',', $_GET['IncludeTag']).'))');
		}
		if(!empty($_GET['ExcludeTag'])) {
			$conditions[] = array('Lead.id NOT IN (SELECT `lead_id` from `leads_tags` WHERE `tag_id` IN ('.implode(',', $_GET['ExcludeTag']).'))');
		}
		if(!empty($_GET['RatingAtLeast'])) {
			$conditions[] = array('Lead.rating >= '.$_GET['RatingAtLeast']);
		}
		if(!empty($_GET['RatingLessThan'])) {
			$conditions[] = array('Lead.rating < '.$_GET['RatingLessThan']);
		}
		if(!empty($_GET['NotableProjectsNotIdentified'])) {
			$conditions[] = array('Lead.notable_projects' => '');
		}
		
		$leads = $this->Lead->find('all', array(
			'conditions' => $conditions,
			'order' => $order,
			'page' => $page,
			'limit' => $limit
		));
		$this->set('leads', $leads);
		
		$count = $this->Lead->find('count', array('conditions' => $conditions));
		$this->set('count', $count);
		
		/*
		if($mailman_export == 1) {
			//CSV EXPORT TO MAILMAN
			$leads = $this->Lead->find('all', array(
				'conditions' => $conditions,
				'order' => $order
			));
			
			$filename = ROOT.'/app/tmp/logs/leads_mailman_export_'.time().'.csv';
			$qexport = fopen($filename, 'w');
			
			foreach($leads as $lead) {
				if(!empty($lead['Lead']['email'])) {
					$fields = array($lead['Lead']['email'], $lead['Lead']['name']);
					fputcsv($qexport, $fields);
				}
			}
			
			fclose($qexport);
			header('Content-disposition: attachment; filename=leads_mailman_export_'.time().'.csv');
			header('Content-type: application/csv');
			readfile($filename);
			exit;
		}
		*/
	}

	public function gather() {
	}
	
	public function qualify() {
		
	}
	
	public function update() {
		$this->layout = false;
		if($this->request->is('post')) {
			if(!empty($this->request->data['Lead']['id'])) {
				if(!isset($this->request->data['Lead']['is_chrome_extension'])) {
					//if no notes is entererd, don't save a blank note
					foreach($this->request->data['Note'] as $key => $note) {
						if(empty($note['text']))
							unset($this->request->data['Note'][$key]);
					}
					
					//if no contact is entererd, don't save a blank contact
					foreach($this->request->data['Contact'] as $key => $contact) {
						if(empty($contact['first_name']) && empty($contact['last_name']))
							unset($this->request->data['Contact'][$key]);
					}
				}
				if($this->Lead->saveAll($this->request->data)) {
					if(isset($this->request->data['Lead']['is_chrome_extension']))
						die('1');
					die('updated');
				}
			}
			return $this->render('/Elements/lead_form');
		}
		die('Only Post');
	}
	
	public function ajaxExportToHighrise($lead_id) {
		$lead = $this->Lead->findById($lead_id);
		if(!empty($lead)/* && empty($lead['Lead']['highrise_id'])*/) {
			App::import('Vendor', 'highrise');
			$highrise = new Highrise();
			//save company to highrise
			$highrise_id = $highrise->pushCompany($lead['Lead']);
			//save each contact as person in highrise
			foreach($lead['Contact'] as $contact) {
				$highrise->pushPerson($contact, $lead['Lead']['company']);
			}
			/* //no deals for now!
			//save a deal
			$highrise->pushDeal($lead, $highrise_id);
			*/
			
			//save highrise_id to lead
			$this->Lead->id = $lead_id;
			$this->Lead->saveField('highrise_id', $highrise_id);
			echo $highrise_id;
		}
		exit;
	}
	
	public function ajaxExportPersonToHighrise($contact_id) {
		$contact = $this->Contact->findById($contact_id);
		pr($contact);
		exit;
		
		if(!empty($lead)/* && empty($lead['Lead']['highrise_id'])*/) {
			App::import('Vendor', 'highrise');
			$highrise = new Highrise();
			//save company to highrise
			$highrise_id = $highrise->pushCompany($lead['Lead']);
			//save each contact as person in highrise
			foreach($lead['Contact'] as $contact) {
				$highrise->pushPerson($contact, $lead['Lead']['company']);
			}
			//save a deal
			$highrise->pushDeal($lead, $highrise_id);
			//save highrise_id to lead
			$this->Lead->id = $lead_id;
			$this->Lead->saveField('highrise_id', $highrise_id);
			echo $highrise_id;
		}
		exit;
	}
}
