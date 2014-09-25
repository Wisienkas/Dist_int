<?php

class Addressbook {

	private $_content;

	public function __construct($document){
		$document = simplexml_load_file($document);
		$document = json_encode($document);
		$document = json_decode($document,true);

		$this->parseInformation($document);
	}

	public function printAllPretty(){
		
		print("Printing all entries in addressbook!\n\n");
		
		foreach($this->_content as $contact){
			$this->printAddress($contact);

			print("\n");
		}
	}

	public function search($keyword = null){

		print("Starting a Search for keyword: $keyword!\n\n");

		if($keyword == null){
			print('No keyword given, displaying all contacts!' . "\n\n");
			$this->printAllPretty();
			return;
		}

		$result = array();

		$this->getResults($keyword, $result);
		

		foreach($result as $entry){
			$this->printAddress($entry);
			print("\n");
		}
	}

	private function getResults($keyword, &$result){
		foreach($this->_content as $contact){
			if($this->hasKey($keyword, $contact)){
				$result[] = $contact;
			}
		}
	}

	private function hasKey($key, $element){
		if(is_array($element)){
			foreach($element as $child){
				if($this->hasKey($key, $child)){
					return true;
				}
			}
		}else {
			return strpos($element, $key) !== false;
		}
	}

	private function printAddress($contact){
		foreach($contact as $key => $val){
			$this->printKeyVal($key, $val);
		}
	}

	private function printKeyVal($key, $val){
		if(is_array($val)){
			foreach($val as $i => $v){
				print("\t$key $i: $v\n");
			}
		}else{
			print("\t$key: $val\n");
		}
	}

	private function parseInformation($doc){
		
		$this->_content = array();

		foreach($doc['entry'] as $entry){
			$this->addEntry($entry);
		}
	}

	private function addEntry($entry){
		$id = $entry['@attributes']['id'];
		$this->_content[$id] = array();
		
		$this->addSingle($entry, $key = 'forename', $this->_content[$id]);
		$this->addSingle($entry, $key = 'lastname', $this->_content[$id]);
		$this->addSingle($entry, $key = 'birthday', $this->_content[$id]);
		$this->addSingle($entry, $key = 'sex', $this->_content[$id]);

		$this->addMulti($entry, $key = 'mail', $this->_content[$id]);
		$this->addMulti($entry, $key = 'phone', $this->_content[$id]);
	}

	private function addSingle($source, $key, &$destination){
		if(isset($source[$key])){
			$destination[$key] = $source[$key];
		}
	}

	private function addMulti($source, $key, &$destination){
		if(isset($source[$key])){
			$destination[$key] = $source[$key];
		}
	}
}

$longOpts = array(
	'file:',
	'keyword::',
	'printall',
	'addContact::'
);

$option = getopt(null,$longOpts);

$book = new Addressbook($option['file']);

if(isset($option['printall'])){
	$book->printAllPretty();
}

if(isset($option['keyword'])){
	$book->search($keyword = $option['keyword']);
}
?>
