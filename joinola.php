<?php
/**
 * @package Joinola
 */
/*
Plugin Name: Joinola
Plugin URI: http://vgafib.upc.es/
Description: Afegeix la cadena de text '[gjb year="XXXX"]' a una entrada on XXXX es l'any i es substituira per el formulari d'inscripcio. Es necesari instalar el plugin "Paypal Framework" per el correcte funcionament.
Version: 0.1
Author: BuD
Author URI: http://github.com/budsan
License: GPLv2 or later
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

define('WP_DEBUG', true);
define('GJB_PLUGIN_URL', plugin_dir_url( __FILE__ ));

//Aqui s'acomulen els errors i es mostren tots al final.
$gjb_errs = array();

include_once dirname(__FILE__)."/global.php";
include_once dirname(__FILE__)."/lang.php";
include_once dirname(__FILE__)."/user.php";
include_once dirname(__FILE__)."/admin.php";
include_once dirname(__FILE__)."/form.php";

function gjb_displayErrors()
{
	global $gjb_errs;
	if (count($gjb_errs) == 0) return "";

	$errDisplay = "";
	for ($i = 0; $i < count($gjb_errs); $i++)
	{
		if ($i > 0) $errDisplay .= "<br/>";
		$errDisplay .= "<tt class='gjb_err'>".$gjb_errs[$i]."</tt>";
	}

	return "<div class='clearfix gjb_errors'> {$errDisplay}</div>";
}

function gjb_install()
{
	global $wpdb;
	global $gjb_table_sufix_name;
	$table_name = $wpdb->prefix.$gjb_table_sufix_name;
	$sql = "CREATE TABLE {$table_name} (
	Year int(11) NOT NULL,
	ID int(11) NOT NULL,
	Nom varchar(255) NOT NULL,
	Cognoms varchar(255) NOT NULL,
	NIF varchar(255) NOT NULL,
	Email varchar(255) NOT NULL,
	Edat int(11) DEFAULT 0 NOT NULL,
	Sexe tinyint(1) DEFAULT 1 NOT NULL COMMENT '0 mujer 1 hombre',
	Comarca varchar(255) DEFAULT '' NOT NULL,
	Coneguts int(11) DEFAULT 0 NOT NULL,
	TipusPC varchar(255) DEFAULT '' NOT NULL,
	Especialitat varchar(255) DEFAULT '' NOT NULL,
	Estudis varchar(255) DEFAULT '' NOT NULL,
	FinEstudis tinyint(1) DEFAULT 0 NOT NULL COMMENT '0 no 1 sí',
	Treball varchar(255) DEFAULT '' NOT NULL,
	BuscoFeina tinyint(1) DEFAULT 0 NOT NULL COMMENT '0 no 1 sí',
	Fecha varchar(255) DEFAULT '' NOT NULL,
	PaypalToken varchar(255) DEFAULT '' NOT NULL,
	Confirmat tinyint(1) DEFAULT 0 NOT NULL COMMENT '0 no 1 sí',
	PRIMARY KEY (Year, ID),
	UNIQUE KEY NIF (NIF));";

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta($sql);

/*	$gjb_db_version = 0.1;
	add_option("gjb_db_version", $gjb_db_version);
	$installed_ver = get_option( "gjb_db_version" );
	if( $installed_ver != $gjb_db_version )
	{
		update_option( "gjb_db_version", $gjb_db_version );
	}
*/
}
register_activation_hook(__FILE__,'gjb_install');
add_action('plugins_loaded', 'gjb_install');

function gjb_init()
{
	$plugin_dir = basename(dirname(__FILE__));
	load_plugin_textdomain( 'gjb', false, $plugin_dir );

	wp_register_script('xregexp.js', GJB_PLUGIN_URL.'xregexp.js');
	wp_enqueue_script('xregexp.js');

	wp_register_script('xregexp-unicode-base.js', GJB_PLUGIN_URL.'xregexp-unicode-base.js');
	wp_enqueue_script('xregexp-unicode-base.js');

	wp_register_script('form.js', GJB_PLUGIN_URL.'form.js');
	wp_enqueue_script('form.js');
	
	wp_register_style('gjb-reg.css', GJB_PLUGIN_URL.'gjb-reg.css');
	wp_enqueue_style('gjb-reg.css');
}
add_action('init', 'gjb_init');

function gjb_options()
{
	if (!current_user_can('manage_options'))
		wp_die( __('You do not have sufficient permissions to access this page.') );

	echo gjb_obteOpcions();
}

function gjb_menu()
{
	add_options_page('GJB Options', 'Game Jam Barcelona', 'manage_options', 'gjb', 'gjb_options');
}
add_action('admin_menu', 'gjb_menu');

function gjb_obteFormulari( $year, $closed, $limit)
{
	global $lang;
	global $wpdb;
	global $current_user;

	$command = gjb_getCommand(); 
	if (strcmp($command, "registrar") == 0) gjb_registrarUsuari($year);

	$userinfo = gjb_getUserInfo($year, $current_user->ID);
	if(count($userinfo) == 0)
	{
		$num_users = gjb_getUserRegistred($year);
		if ($limit != 0 && $limit <= $num_users)
			return "Lo siento, ya no quedan plazas libres.";
		return gjb_obteFormulariRegistre($year);
	}

	$token = $userinfo[0]->PaypalToken;
	$confirmat = $userinfo[0]->Confirmat == 1 ? true : false;

	if (!$confirmat)
	{
		if (strcmp($command,"confirmar") == 0)
		{
			$confirmat = gjb_confirmarPagamentUsuari($year, $token);
			if ($confirmat)
			{
				$email = $userinfo[0]->Email;
				$nom = html_entity_decode($userinfo[0]->Nom);
				$mailbody = $lang['EMAIL_HELLO'].
					" ".$nom.",\n\n".$lang['EMAIL_SUCCESS_TEXT'];
				$mailbody = html_entity_decode($mailbody);
				$subject = html_entity_decode($lang['EMAIL_SUCCESS_SUBJECT']);
				wp_mail($email, $subject, $mailbody);
			}
		}
		else if (strcmp($command, "cancelpago") == 0)
			gjb_obteNouPaypalTokenUsuari($year, $token);
	}

	if ($confirmat) return gjb_obteVistaUsuariConfirmat($year, $userinfo);
	if (empty($token)) gjb_obteNouPaypalTokenUsuari($year, $token);
	
	if (!empty($token))
	{
		$response = gjb_obtePaypalTokenInfo($token);
		if (!gjb_comprovaNoError($response))
		{
			gjb_obteNouPaypalTokenUsuari($year, $token);
			if(!empty($token)) gjb_obteVistaUsuariPerPagar($year, $token);
		}
		else
		{
			if (gjb_haIntroduitMetodePagament($response))
			{
				if (GJB_PAY_ON_PAYPAL)
					$confirmat = gjb_confirmarPagamentUsuari($year, $token, $response);

				if ($confirmat) return gjb_obteVistaUsuariConfirmat($year);
				else return gjb_obteVistaUsuariPerConfirmar($year, $token, $response, $userinfo);
			}
			else
			{
				return gjb_obteVistaUsuariPerPagar($year, $token);
			}
		}
	}

	return $lang['ERR_PAYMENT'];
}

function gjb_shortCode($atts)
{
	global $lang;
	global $current_user;
	global $gjb_errs;

	extract(shortcode_atts(array('year' => '0', 'closed' => 'false', 'limit' => '0'), $atts));
	$year = intval($year);
	$limit = intval($limit);

	if ($year == 0) $ret = $lang['ERR_NO_YEAR'];
	else if (empty($current_user->ID)) $ret = $lang['ERR_NO_LOGIN'];
	else $ret = gjb_obteFormulari($year, $closed, $limit);
	
	$err = gjb_displayErrors();
	return $err.$ret;
}

add_shortcode( 'gjb', 'gjb_shortCode');

?>
