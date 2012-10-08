<?php

function gjb_getUserInfo($year, $id)
{
	global $gjb_table_sufix_name;
	global $wpdb;

	$table_name = $wpdb->prefix.$gjb_table_sufix_name;
	return $wpdb->get_results("SELECT * FROM {$table_name} WHERE Year = '{$year}' AND ID = '{$id}'");
}

function gjb_getUserRegistred($year)
{
	global $gjb_table_sufix_name;
	global $wpdb;

	$table_name = $wpdb->prefix.$gjb_table_sufix_name;
	$result = $wpdb->get_results("SELECT count(*) as Num FROM {$table_name} WHERE Year = '{$year}'");
	return intval($result[0]->Num);
}

function gjb_updateUserInfo($year, $id, $info)
{
	global $gjb_table_sufix_name;
	global $wpdb;

	$table_name = $wpdb->prefix.$gjb_table_sufix_name;

	$values['Year'] = $year;
	$values['ID'] = $id;
	$wpdb->update($table_name, $info, $values);
}

?>
