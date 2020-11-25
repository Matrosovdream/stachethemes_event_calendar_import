<?php
Class ImportCalendar {
	
	
	function GetRemoteCalendar( $url = false ) {
		
		if( !$url ) { return false; }
		
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);

		$data = curl_exec($ch);

		curl_close($ch);
		
		$items = json_decode( $data, true );
		
		$items = array_slice( $items, 0, 10000 );
		
		return $items;		
				
	}
	
	
	function GetAllEvents() {
		
		$args = array( 'post_type' => 'stec_event', 'posts_per_page' => 500000 );
		$posts = get_posts( $args, ARRAY_N );
		
		foreach( $posts as $item ) {
			
			$calendar_id = get_post_meta( $item->ID, 'calid' )[0];
			$start_date = get_post_meta( $item->ID, 'start_date' )[0];
			$end_date = get_post_meta( $item->ID, 'end_date' )[0];
			$original_title = get_post_meta( $item->ID, 'original_title' )[0];
			
			$key_arr = array();
			$key_arr[] = $calendar_id;
			$key_arr[] = sanitize_title( $original_title );
			$key_arr[] = strtotime( $start_date );
			$key_arr[] = strtotime( $end_date );
			
			$key = implode( '_', $key_arr );
			
			$events[ $key ] = array( 
								"ID" => $item->ID,
								"post_title" => $item->post_title,
								);
			
		}
		
		
		
		return $events;
		
	}
	
	
	function CalculateOffDays( $events ) {
		
		$start = strtotime( $events[0]['start'] );
		$end = strtotime( $events[ count($events) - 1 ]['start'] );
		
		foreach( $events as $e ) {
			$date = date( 'Y-m-d', strtotime($e['start']) );
			$isset_dates[ $date ] = $date;
		}
		
		while( $start < $end ) {
			
			$date = date( 'Y-m-d', $start );
			
			if( !$isset_dates[ $date ] ) {
				$off_days[] = $date;
			}

			$start += 86400; // One day
		}
		
		return $off_days;
		
		echo "<pre>";
		print_r($off_days);
		echo "</pre>";
		
	}
	
	
	function UpdateCalendarByID( $post_id = false ) {
		
		if( !$post_id ) { return false; }
		
		$all_events = $this->GetAllEvents();
	
		$calendar_id = get_post_meta( $post_id, 'calendar_id' )[0];
		$url = get_post_meta( $post_id, 'import_url' )[0];
		
		if( $url != '' ) {
			$events = $this->GetRemoteCalendar( $url );
		}
		
		//$events = array_slice( $events, 0, 5 );
		
		$days_off = $this->CalculateOffDays( $events );
		
		echo "<pre>";
		print_r($days_off);
		echo "</pre>";
		
		/*$events_new = array();
		foreach( $events as $e ) {
			if( 
				date('Y-m-d', strtotime($e['start']) ) == '2020-10-02' ||
				date('Y-m-d', strtotime($e['start']) ) == '2020-10-03' ||
				date('Y-m-d', strtotime($e['start']) ) == '2020-10-04' 
				) {
					$events_new[] = $e;
				}
				
		}	
		$events = $events_new;*/
		
		$sections = array();
		foreach( $events as $e ) {
			
			$day = date( 'Y-m-d', strtotime( $e['start'] ) );
			$sections[ $day ][] = $e;
			
		}	
		
		//$all_hours[] = 7;
		//$all_hours[] = 8;
		$all_hours[] = 9;
		$all_hours[] = 10;
		$all_hours[] = 11;
		$all_hours[] = 12;
		$all_hours[] = 13;
		$all_hours[] = 14;
		$all_hours[] = 15;
		$all_hours[] = 16;
		$all_hours[] = 17;
		$all_hours[] = 18;
		$all_hours[] = 19;
		$all_hours[] = 20;
		$all_hours[] = 21;
		$all_hours[] = 22;
		
		$groups = array();
		
		foreach( $sections as $date=>$items ) {
			
			//unset( $items[0] );
			//unset( $items[1] );
			
			
			
			$busy_hours = array();
			
			foreach( $items as $item ) {
				
				$start = date( 'H', strtotime($item['start']) );
				$end = date( 'H', strtotime($item['end']) );
				
				$hour = $start;
				while ($hour <= $end) {
					$busy_hours[ $hour ] = $hour; 
					$hour++;
				}
				
				//$busy_hours[] = $start.' - '.$end;
				
			}
			
			
			$free_hours = array_diff( $all_hours, $busy_hours );
			$free_hours = array_values( $free_hours );
			

			echo "<pre>";
			print_r($all_hours);
			echo "</pre>";
			
			echo "<pre>";
			print_r($busy_hours);
			echo "</pre>";

			
			$start = $free_hours[0];
			foreach( $free_hours as $key=>$time ) {
				
				$diff = $free_hours[ $key+1 ] - $time;
				
				if( $diff != 1 ) {
					$end = $time;
					//$groups[] = array( $date$start, $end );
					
					$start = $start - 1;
					$end = $end + 1;
					
					$groups[] = array(
						"title" => 'Free time',
						"description" => 'Free time',
						"start" => $date.' '.$start.':00:00',
						"end" => $date.' '.$end.':00',
						"type" => 'free',
					);

					$start = $free_hours[ $key+1 ];
				} 

			} 
			
		}	
		
		foreach( $days_off as $date ) {
			
			$start = 8;
			$end = 23;
			
			$groups[] = array(
						"title" => 'Free time',
						"description" => 'Free time',
						"start" => $date.' '.$start.':00:00',
						"end" => $date.' '.$end.':00',
						"type" => 'free',
					);
			
		}
		
		foreach( $events as $key=>$item ) {
			$events[ $key ]['type'] = 'busy';
		}
		
		$events = array_merge( $events, $groups );
		
		foreach( $events as $key=>$item ) {
			if( date('H:i', strtotime($item['start']) ) == date('H:i', strtotime($item['end']) ) ) { unset( $events[ $key ] ); }
		}
		
		/*echo "<pre>";
		print_r($groups);
		echo "</pre>";*/
		
		echo "<pre>";
		print_r($events);
		echo "</pre>";

		//die();
		
		if( !$calendar_id ) { return false; }
		if( !$url ) { return false; }
		
		// Let's add or update
		if(  is_array($events) && count($events) > 0 ) {
			
			foreach( $events as $e ) {
				
				$key_arr = array();
				$key_arr[] = $calendar_id;
				$key_arr[] = sanitize_title( $e['title'] );
				$key_arr[] = strtotime( $e['start'] );
				$key_arr[] = strtotime( $e['end'] );
				
				$key = implode( '_', $key_arr );
				
				echo $key; echo "<br/>";

				if( $all_events[ $key ] ) { continue; }
				
				$content = array();
				$content[] = '<span class="calendar-title-date">'.date( 'H:i', strtotime($e['start']) ).' - '.date( 'H:i', strtotime($e['end']) ).'</span>';
				$content[] = $e['description'];
				
				$content2 = array();
				$content2[] = '<span class="calendar-title-date">'.date( 'H:i', strtotime($e['start']) ).' - '.date( 'H:i', strtotime($e['end']) ).'</span>';
				$content2[] = $e['title'];
				//$content[] = $e['title'];
				
				$post_data = array(
					//'post_title'    => wp_strip_all_tags( $e['title'] ),
					'post_title'    => implode('<br>', $content),
					//'post_content'  => $e['description'],
					'post_content'  => implode('<br>', $content2),
					'post_status'   => 'publish',
					'post_type'   => 'stec_event',
					'post_author'   => 1,
					//'post_category' => array( 8,39 )
				);

				// Вставляем запись в базу данных
				$post_id = wp_insert_post( $post_data );
				
				update_post_meta( $post_id, 'calid', $calendar_id );
				update_post_meta( $post_id, 'original_title', $e['title'] );
				update_post_meta( $post_id, 'start_date', $e['start'] );
				update_post_meta( $post_id, 'end_date', $e['end'] );
				update_post_meta( $post_id, 'approved', 1 );
				
				if( $e['type'] == 'busy' ) { $color = '#1793e2'; }
				if( $e['type'] == 'free' ) { $color = '#a5a5a5'; }
				
				update_post_meta( $post_id, 'color', $color );
				
				echo $post_id;
				
			}
			
		}
		
	}
	
	
	function UpdateAllCalendars() {
		
		$args = array( 'post_type' => 'calendar_import', 'posts_per_page' => 5000 );
		$posts = get_posts( $args, ARRAY_A );
		
		foreach( $posts as $item ) {
			
			$this->UpdateCalendarByID( $item->ID );
			
		} 
		
		/*echo "<pre>";
		print_r($posts);
		echo "</pre>";*/
		
	}
	
	
	function removeAllEvents() {
		
		$events = $this->GetAllEvents();
		
		foreach( $events as $item ) {
			wp_delete_post( $item['ID'], $force_delete=true );
		}
		
	}
	
	
}

?>