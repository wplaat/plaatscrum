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

$language = plaatscrum_post("language", 0);

/*
** ------------------
** ACTIONS
** ------------------
*/

function plaatscrum_settings_save_do() {

	/* input */
	global $user;
	global $language;
		
	$user->language = $language;	
	plaatscrum_db_user_update($user);
				
	plaatscrum_ui_box("info", t('SETTING_SAVED'));
}

/*
** ------------------
** UI
** ------------------
*/

function plaatscrum_settings_general_form() {

	/* input */
	global $mid;
	global $pid;
	global $user;
	global $access;
	
	/* output */
	global $page;
	global $title;
				
	$page .= '<div id="detail">';

	$title = t('SETTING_GENERAL_TITLE');
					
	$page .= '<h1>';
	$page .= $title;
	$page .= '</h1>';
		
	$page .= '<fieldset>' ;
	$page .= '<legend>'.t('SETTINGS_GENERAL').'</legend>';		
	
	$page .= '<p>';
	$page .= '<label>';
	$page .= t('GENERAL_LANGUAGE').':';
	$page .= '</label>';
	$page .= plaatscrum_ui_language('language', $user->language, false);
	$page .= '</p>';
		
	$page .= '<p>';
	$page .= plaatscrum_link('mid='.$mid.'&pid='.$pid.'&eid='.EVENT_SETTING_SAVE, t('LINK_CHANGE'));	
	$page .= '</p>';
	$page .= '</fieldset>';    	

	$page .= '<fieldset>' ;
	$page .= '<legend>'.t('SETTING_ACCOUNT_TITLE').'</legend>';		
	
	$page .= '<p>';
	$page .= t('SETTING_ACCOUNT', plaatscrum_link('mid='.$mid.'&pid='.PAGE_USER.'&id='.$user->user_id, t('LINK_HERE')));	
	$page .= '</p>';
	$page .= '</fieldset>';   
	
	$page .= '</div>';  
}

/*
** ------------------
** HANDLER
** ------------------
*/

function plaatscrum_settings() {

	/* input */
	global $pid;
	global $eid;
	
	/* Event handler */
	switch ($eid) {
				  		  								
		case EVENT_SETTING_SAVE:
					plaatscrum_settings_save_do();
					break;					
	}
		
	/* Page handler */
	switch ($pid) {
	
		case PAGE_GENERAL: 
					plaatscrum_settings_general_form();	
					break;
	}
}

/*
** ------------------
** The End
** ------------------
*/

