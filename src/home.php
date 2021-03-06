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


/*
** ------------------
** UI
** ------------------
*/


function plaatscrum_home_form() {

	/* input */
	global $mid;
	global $pid;
	global $user;	
	global $access;	
	global $sort;

	global $filter_project;
	global $filter_sprint;
	global $filter_status;
	global $filter_prio;
	global $filter_type;
	global $filter_owner;
	
	/* output */
	global $page;
	global $title;
				
	$title = t('HOME_TITLE');	
			
	$page .= '<h1>';	
	$page .= $title;
	$page .= '</h1>';

	$page .= '<p>';
	$page .= t('HOME_ASSIGN_STORIES');
	$page .= '</p>';

	$query  = 'select a.number, a.type, a.points, a.story_id, a.summary, a.story_story_id, a.prio, c.number as sprint_number, a.status, ';
	$query .= 'if(a.story_story_id=0,a.story_id, a.story_story_id) as sort2 ';
	$query .= 'from story a ';
	$query .= 'left join sprint c on a.sprint_id=c.sprint_id ';
	$query .= 'where a.user_id='.$user->user_id.' and a.deleted=0 and a.type!=1 ';	
	
	if ($filter_project>0) {
		$query .= 'and a.project_id='.$filter_project.' ';	
	}
	
	if ($filter_sprint>0) {
		$query .= 'and a.sprint_id='.$filter_sprint.' ';	
	}
	
	if (strlen($filter_status)>0) {
		$query .= 'and a.status in ('.$filter_status.') ';
	}
	
	if ($filter_prio > 0) {
		$query .= 'and a.prio in ('.$filter_prio.') ';	
	}	
	
	if (strlen($filter_type) > 0) {
		$query .= 'and a.type in ('.$filter_type.') ';	
	} 
	
	switch ($sort) {
	
	    default:$query .= 'order by sort2 asc, a.story_id';
				   break;
					
		 case 1: $query .= 'order by a.number';
				   break;

		 case 2: $query .= 'order by a.type';
				   break;			
					
	    case 3: $query .= 'order by sprint_number, a.number';
				   break;					
					
		 case 4: $query .= 'order by a.points desc';
				   break;
					
		 case 5: $query .= 'order by a.type';
				   break;
					
		 case 6: $query .= 'order by a.prio desc';
				   break;
					
		 case 7: $query .= 'order by a.status';
				   break;
	}
	
	$result = plaatscrum_db_query($query);
			
	$page .= '<table>';
			
	$page .= '<thead>';
	$page .= '<tr>';
	
	$page .= '<th>';
	$page	.= plaatscrum_link('mid='.$mid.'&pid='.$pid.'&sort=1', t('GENERAL_US'));
	$page .= '</th>';
		
	$page .= '<th>';
	$page	.= plaatscrum_link('mid='.$mid.'&pid='.$pid.'&sort=0', t('GENERAL_SUMMARY'));
	$page .= '</th>';
	
	$page .= '<th>';
	$page	.= plaatscrum_link('mid='.$mid.'&pid='.$pid.'&sort=3', t('GENERAL_SPRINT'));
	$page .= '</th>';
		
	$page .= '<th>';
	$page	.= plaatscrum_link('mid='.$mid.'&pid='.$pid.'&sort=4', t('GENERAL_SP_WORK'));
	$page .= '</th>';
	
	$page .= '<th>';
	$page	.= plaatscrum_link('mid='.$mid.'&pid='.$pid.'&sort=5', t('GENERAL_TYPE'));
	$page .= '</th>';
	
	$page .= '<th>';
	$page	.= plaatscrum_link('mid='.$mid.'&pid='.$pid.'&sort=6', t('GENERAL_PRIORITY'));
	$page .= '</th>';
	
	$page .= '<th>';
	$page	.= plaatscrum_link('mid='.$mid.'&pid='.$pid.'&sort=7', t('GENERAL_STATUS'));
	$page .= '</th>';

	$page .= '</tr>';
	
	$page .= '</thead>';
	$page .= '<tbody>';
		
	$count=0;
		
	while ($data=plaatscrum_db_fetch_object($result)) {		
					
		$page .= '<tr ';
		
		if ((++$count % 2 ) == 1 ) {
			if ($data->type==TYPE_STORY) {
				$page .= 'class="light bold" ';
			} else {
				$page .= 'class="light" ';
			}
		} else {
			if ($data->type==TYPE_STORY) {
				$page .= 'class="dark bold" ';
			} else {
				$page .= 'class="dark" ';
			}
		} 
		$page .='>';
				
		$page .= '<td>';		
		$page .= plaatscrum_link('mid='.$mid.'&pid='.PAGE_STORY.'&eid='.EVENT_STORY_LOAD.'&id='.$data->story_id, $data->number);
		$page .= '</td>';
		
		$page .= '<td>';
		if ($data->story_story_id>0) {
			$page .= plaatscrum_ui_image("link.png",' width="14" heigth="14" ').' ';
		}		
		$page	.= $data->summary;
		$page .= '</td>';
				
		$page .= '<td>';
		$page	.= $data->sprint_number;
		$page .= '</td>';
		
		$page .= '<td >';
		$page	.= $data->points;
		$page .= '</td>';
		
		$page .= '<td >';
		$page	.= t('TYPE_'.$data->type);
		$page .= '</td>';
				
		$page .= '<td >';
		$page	.= t('PRIO_'.$data->prio);
		$page .= '</td>';
		
		$page .= '<td >';
		$page	.= t('STATUS_'.$data->status);
		$page .= '</td>';
		
		$page .= '</tr>';
	}
	$page .= '</tbody>';
	$page .= '</table>';	
	
	$page .= '<p>';		
	if ($access->story_add) {
		$page .= plaatscrum_link('mid='.$mid.'&pid='.PAGE_STORY.'&eid='.EVENT_STORY_NEW.'&type='.TYPE_STORY.'&id=0', t('LINK_ADD_STORY'));
	}
	$page .= '</p>';	
}

/*
** ------------------
** HANDLER
** ------------------
*/

function plaatscrum_home() {

	/* input */
	global $mid;
	global $pid;

	/* Page handler */
	switch ($pid) {
	
		case PAGE_HOME: 
					plaatscrum_link_store($mid, $pid);
					plaatscrum_filter();
					plaatscrum_home_form();	
					break;
	}
}


/*
** ------------------
** The End
** ------------------
*/

