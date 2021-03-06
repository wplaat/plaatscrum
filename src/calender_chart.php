<?php

/* 
**  ==========
**  PlaatScrum
**  ==========
**
**  Created by wplaat
**
**  For more information visit the following website.
**  Website : www.plaatsoft.nl 
**
**  Or send an email to the following address.
**  Email   : info@plaatsoft.nl
**
**  All copyrights reserved (c) 2008-2016 PlaatSoft
*/

/*
** ------------------
** POST PARAMETERS
** ------------------
*/
	
/*
** ------------------
** ACTIONS
** ------------------
*/

function plaatscrum_prev_month() {

	global $filter_month;
	global $filter_year;

	$filter_month--;
	if ($filter_month==0) {
		$filter_month = 12;
		$filter_year--;
	}
}

function plaatscrum_next_month() {

	global $filter_month;
	global $filter_year;

	$filter_month++;
	if ($filter_month>12) {
		$filter_month = 1;
		$filter_year++;
	}
}

/*
** ------------------
** UI
** ------------------
*/

function draw_stories( $date ) {

	/* input */
	global $mid;
	global $pid;
	global $user;
	global $access;
	
	global $filter_project;
	global $filter_sprint;
	global $filter_status;
	global $filter_prio;
	global $filter_type;
	global $filter_owner;
	
	$count = 0;	
	$page = "";

	/* Add Sprint information - if any */
	$query  = 'select number as sprint_number, start_date from sprint where ';
	$query .= '"'.convert_date_mysql($date).' " between start_date and end_date ';
	$query .= 'and project_id='.$filter_project.' and deleted=0';
			
	$result = plaatscrum_db_query($query);
	$data = plaatscrum_db_fetch_object($result);
		
	if (isset($data->sprint_number))	{
		$amount =  round((strtotime($date) -  strtotime($data->start_date)) / (60 * 60 * 24));			
		$page .= '<b>'.t('GENERAL_SPRINT').' '.$data->sprint_number.chr((floor($amount/7)+97)).'</b><br/>';
		$count++;
	}

	/* Add Story / Task / Bug / Epic information - if any */
	$query  = 'select a.story_id, a.number, b.number as sprint_number, b.start_date ';
	$query .= 'from story a left join sprint b on a.sprint_id=b.sprint_id ';
	$query .= 'where a.deleted=0 and b.deleted=0 and a.project_id='.$filter_project.' ';
	
	if (strlen($filter_status)>0) {
		$query .= 'and a.status in ('.$filter_status.') ';
	}
	
	if (strlen($filter_type) > 0) {
		$query .= 'and a.type in ('.$filter_type.') ';	
	}
	$query .= 'and date="'.convert_date_mysql($date).'"';
	$result = plaatscrum_db_query($query);
	
	$count2 = 0;
	while ($data=plaatscrum_db_fetch_object($result))	{
		
		$page .= plaatscrum_link_hidden('mid='.$mid.'&pid='.PAGE_STORY.'&eid='.EVENT_STORY_LOAD.'&id='.$data->story_id, '#'.$data->number);
		$page .= ' ';
		$count2++;
		
		if ($count2>2) {		
			$count++;
			$count2=0;
			$page .= '<br/>';
		}
	} 
	
	if ($count>3) {
		$count=3;
	} 
	
	$page .= str_repeat('<p>&nbsp;</p>', (3-$count));
	
	return $page;
}

/* draws a calendar */
function draw_calendar($month,$year){

	/* input */
	global $user;
	global $filter_month;
	
	$project = plaatscrum_db_project($user->project_id);
	
	/* draw table */
	$calendar = '<table cellpadding="0" cellspacing="0" class="calendar">';

	/* table headings */
	$headings = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
	$calendar.= '<tr class="calendar-row"><td class="calendar-day-head">'.implode('</td><td class="calendar-day-head">',$headings).'</td></tr>';

	/* days and weeks vars now ... */
	$running_day = date('w',mktime(0,0,0,$month,1,$year));
	$days_in_month = date('t',mktime(0,0,0,$month,1,$year));
	$days_in_this_week = 1;
	$day_counter = 0;
	$dates_array = array();

	/* row for week one */
	$calendar.= '<tr class="calendar-row">';

	/* print "blank" days until the first of the current week */
	for($x = 0; $x < $running_day; $x++):
		$calendar.= '<td class="calendar-day-np">&nbsp;</td>';
		$days_in_this_week++;
	endfor;

	/* keep going with days.... */
	for($list_day = 1; $list_day <= $days_in_month; $list_day++):
	
		if ( (date("d")==$list_day) && (date("m")==$filter_month)) {
			$calendar .= '<td class="calendar-day-now">';
		} else {
			if (!is_numeric(strpos($project->days, (string) $running_day))) {
				$calendar .= '<td class="calendar-day-np">';		
			} else {
				$calendar .= '<td class="calendar-day">';
			}
		}
		
		/* add in the day number */
		$calendar.= '<div class="day-number">'.$list_day.'</div>';

		$calendar .= draw_stories($list_day.'-'.$month.'-'.$year);
			
		$calendar.= '</td>';
		if($running_day == 6):
			$calendar.= '</tr>';
			if(($day_counter+1) != $days_in_month):
				$calendar.= '<tr class="calendar-row">';
			endif;
			$running_day = -1;
			$days_in_this_week = 0;
		endif;
		$days_in_this_week++; $running_day++; $day_counter++;
	endfor;

	/* finish the rest of the days in the week */
	if($days_in_this_week < 8):
		for($x = 1; $x <= (8 - $days_in_this_week); $x++):
			$calendar.= '<td class="calendar-day-np">&nbsp;</td>';
		endfor;
	endif;

	/* final row */
	$calendar.= '</tr>';

	/* end the table */
	$calendar.= '</table>';
		
	/* all done, return result */
	return $calendar;
}

function plaatscrum_calender_form() {
	
	/* input */
	global $filter_month;
	global $filter_year;
	global $mid;
	global $pid;
	global $eid;
	
	/* output */
	global $page;
	global $title;

	$title = t('CALENDER_TITLE').' ['.$filter_month.'-'.$filter_year.']';
	
	$page .= '<h1>';
	$page .= $title;
	$page .= '</h1>';
	
	$page .= '<p>';
	$page .= t('CALENDER_NOTE');
	$page .= '</p>';

	$page .= '<div class="fl_left">';
	$page .= plaatscrum_link('mid='.$mid.'&pid='.$pid.'&eid='.EVENT_PREV, t('LINK_PREV_MONTH'));
	$page .= '</div>';
	
	$page .= '<div class="fl_right">';
	$page .= plaatscrum_link('mid='.$mid.'&pid='.$pid.'&eid='.EVENT_NEXT, t('LINK_NEXT_MONTH'));
	$page .= '</div>';
	
	$page .= draw_calendar($filter_month, $filter_year);	
}

/*
** ------------------
** HANDLER
** ------------------
*/

function plaatscrum_calender() {
	
	/* input */
	global $mid;
	global $pid;
	global $eid;
	
	/* Event handler */
	//plaatscrum_story_events();
	
	switch ($eid) {
	
		case EVENT_NEXT:
				plaatscrum_next_month();
				break;
				
		case EVENT_PREV:
				plaatscrum_prev_month();
				break;
	}
	
	/* Page handler */
	switch ($pid) {
		
		case PAGE_CALENDER_CHART: 	
				plaatscrum_link_store($mid, $pid);
				plaatscrum_filter();
				plaatscrum_calender_form();
				break;
	}
}

/*
** ------------------
** THE END
** ------------------
*/

?>