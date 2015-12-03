<?php
error_reporting(E_ALL);

ini_set('display_errors', 1);

class Generate {
	private $data;
	private $antal;
	private $columns;
	private $latlon;
	private $geocoder;
	private $out;
	private $bevilget;
	private $geodb;

	public function __construct($file, $antal = NULL, $refresh = FALSE) {
		require "fllat/fllat.php";
		$this->geodb = new Fllat("geo");
		$this->antal = $antal ? $antal : 'all';
		$name = "cache/" . explode(".", $file)[0] . '-' . $antal . '.json';
		if (!file_exists($name) || $refresh === 'true') {
			require("../../libs/GoogleMapsGeocoder/src/GoogleMapsGeocoder.php");
			$this->geocoder = new GoogleMapsGeocoder();
			$this->data = json_decode(file_get_contents($file));
			$this->columns = array_shift($this->data);
			file_put_contents($name, json_encode($this->run(), JSON_PRETTY_PRINT));
		}
		readfile($name);
		exit;
	}

	public function run() {
		$a = 0;

		$g = 0;
		$dummy = array();
		foreach ($this->data as $projekt) {
			if (!in_array($projekt[15], $dummy)) {
				$dummy[] = $projekt[15];
				$this->out['groups'][] = array('id' => $g, 'content' => $projekt[15]);
				$g++;
			}
			#$this->out['groups'] = array_values(array_unique($this->out['groups']));
		}

		foreach ($this->data as $index => $projekt) {
			$address = $projekt[16] . ',' . $projekt[19] . ' ' . $projekt[17];
			$this->geocoder->setAddress($address);

			$this->latlon = $this->geodb->get("ll", "prj", $projekt[0]);
			if ($this->latlon != null) {
				$this->latlon = $this->geocoder->geocode();
				$this->latlon = $this->latlon['results'][0]['geometry']['location'];
				$this->geodb->add(
					array(
						'prj' => $projekt[0],
						'll' => $this->latlon
					)
				);
			}

			$this->bevilget[] = $projekt[6];
			/*
						$this->out[$index] = array(
							'id' => $projekt[0],
							'navn' => $projekt[1],
							'start' => $this->convertTime($projekt[3]),
							'end' => $this->convertTime($projekt[4]),
							'bevilling' => $this->convertTime($projekt[5]),
							'latlon' => $this->latlon['results'][0]['geometry']['location']
						);*/

			$this->out['projects'][$index] = array(
				'id' => $index + 1,
				#'content' => $projekt[0],
				'adresse' => $address,
				'navn' => $projekt[1],
				'start' => $this->convertTime($projekt[5]),
				'end' => $this->convertTime($projekt[4]),
				'bevilget' => $projekt[6],
				'type' => 'point',
				'group' => array_search($projekt[15], $dummy),
				'latlon' => $this->latlon
			);


			$a++;
			if ($this->antal != 'all' && $a > $this->antal) {
				break;
			}
		}

		$this->out['bevillinger']['max'] = max($this->bevilget);
		$this->out['bevillinger']['min'] = min($this->bevilget);
		return $this->out;
	}

	private function convertTime($date) {
		return date_format(DateTime::createFromFormat("d-m-Y H:i:s", $date), 'Y-m-d');
	}
}

$dashboard = new Generate('data1.json', $_GET['antal'], $_GET['refresh']);