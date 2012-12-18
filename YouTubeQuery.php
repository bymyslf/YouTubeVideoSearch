<?php	
	class YouTubeVideoQuery extends YouTubeQueryBase
	{
		//Webservice uri
		const HTTP_URI = 'http://gdata.youtube.com/feeds/api/videos';
		
		//Time constants
		const TIME_TODAY = 'today';
		const TIME_WEEK = 'this_week';
		const TIME_MONTH = 'this_month';
		const TIME_ALL_TIME = 'all_time';
		
		//Order constants
		const ORDER_RELEVANCE = 'relevance';
		const ORDER_PUBLISH = 'published';
		const ORDER_VIEW_COUNT = 'viewCount';
		const ORDER_RATING = 'rating';
		
		//Format constants
		const FORMAT_RTSP_H263 = 1;
		const FORMAT_HTTP_SWF = 5;
		const FORMAT_RTSP_MPEG4 = 6;
		
		private $fields = array(
			'q' => '',
			'max-results' => '',
			'start-index' => 1,
			'orderby' => self::ORDER_RELEVANCE,
			'author' => '',
			'time' => self::TIME_ALL_TIME,
			'format' => self::FORMAT_HTTP_SWF
		);
		
		public function __construct() {}
		
        	//Static method to provide method chaining
		public static function getInstance() {
			return new YouTubeVideoQuery();
		}
        
		public function setSearchTerm($value) {
			$this->fields['q'] = $value;
            		return $this;
		}
		
		public function setMaxResults($value) {
			$this->fields['max-results'] = $value;
            		return $this;
		}
		
		public function setStartIndex($value) {
			$this->fields['start-index'] = $value;
            		return $this;
		}
		
		public function setOrderBy($value) {
			$this->fields['orderby'] = $value;
            		return $this;
		}
		
		public function setAuthor($value) {
			$this->fields['author'] = $value;
            		return $this;
		}
		
		public function setTime($value) {
			$this->fields['time'] = $value;
            		return $this;
		}
		
		public function setFormat($value) {
			$this->fields['format'] = $value;
            		return $this;
		}
		
		protected function httpRequest() {
			$link = self::HTTP_URI;
			$auxLink = '';
			
			foreach ($this->fields as $fieldKey => $fieldValue) {
				if (!empty($fieldValue)) {
					if (empty($auxLink)) {
						$auxLink .= $fieldKey . '=' . $fieldValue;
					} else {
						$auxLink .= '&' . $fieldKey . '=' . $fieldValue;
					}
				}
			}
			
			if (!empty($auxLink)) {
				$link = $link . '?' . $auxLink . '&alt=jsonc&v=2';
			} else {
				$link = $link . '?alt=jsonc&v=2';
			}
			
			return json_decode(file_get_contents($link));
		}
		
		public function getVideoEntrys() {
			//API request
			$json = $this->httpRequest();
		
			$entrysArray = array();
			$entrysArray['startIndex'] = $json->data->startIndex;
			$entrysArray['totalItems'] = $json->data->totalItems;
			$entrysArray['items'] = array();
			foreach ($json->data->items as $entry) {
				$embedLink = '';
				if ($entry->accessControl->embed == 'allowed') {
					$embedLink = 'http://www.youtube.com/embed/' . $entry->id;
				}
				
				$entrysArray['items'][] = array(
					'id' => $entry->id,
					'title' => utf8_decode($entry->title),
					'uploaded' => utf8_decode($entry->uploaded),
					'updated' => utf8_decode($entry->updated),
					'uploader' => utf8_decode($entry->uploader),
					'description' => utf8_decode($entry->description),
					'duration' => $entry->duration,
					'viewCount' => $entry->viewCount,
					'favoriteCount' => $entry->favoriteCount,
					'commentCount' => $entry->commentCount,
					'thumbnails' => array(
						'default' => $entry->thumbnail->sqDefault,
						'big' => $entry->thumbnail->hqDefault
					),
					'player' => array(
						'default' => $entry->player->default,
						'mobile' => $entry->player->mobile
					),
					'embedLink' => $embedLink
				);
			}
	
			return $entrysArray;
		}
	}
?>
	
