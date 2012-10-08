<?php

function gjb_comprovaNoError($response)
{
	$ack = strtoupper($response['ACK']);
	$noError =
		strcmp($ack, 'SUCCESS') == 0 ||
		strcmp($ack, 'SUCCESSWITHWARNING') == 0;

	global $gjb_errs; $i = 0;
	while (isset($response['L_ERRORCODE'.$i]))
	{
		$gjb_errs[] = "Paypal error code ".$response['L_ERRORCODE'.$i].": ".$response['L_LONGMESSAGE'.$i];
		$i++;
	}
	
	return $noError;
}

function gjb_haIntroduitMetodePagament($response)
{
	return isset($response['PAYERSTATUS']) && isset($response['SHIPTONAME']);
}

function gjb_obteNouPaypalTokenUsuari($year, &$token)
{
	global $current_user;
	global $gjb_cost;
	global $lang_sel;
	global $gjb_online_discount_percent;

	$discount = $gjb_cost*$gjb_online_discount_percent*0.01;
	$total_cost = $gjb_cost - $discount;
	
	$discount = gjb_fl2str($discount);
	$total_cost = gjb_fl2str($total_cost);

	$postcommand_name = GJB_POSTCOMMAND_NAME;
	$returnurl = strstr(gjb_selfURL(), "?", true);
	if (empty($returnurl)) $returnurl = gjb_selfURL();
	$returnurl .= "?lang={$lang_sel}";
	$cancelurl  = $returnurl."&{$postcommand_name}=cancelpago";

	$ppParams = array(
		'METHOD' => 'SetExpressCheckout',
		'PAYMENTACTION' => 'Sale',
		'AMT' => $total_cost,
		'DESC' => 'Game Jam Barcelona '.$year,
		'RETURNURL' => $returnurl,
		'CANCELURL' => $cancelurl
	);
	$response = hashCall($ppParams);

	if (gjb_comprovaNoError($response))
		$token= $response['TOKEN'];
	else
		$token = '';
	
	gjb_updateUserInfo($year, $current_user->ID, array(
		'PaypalToken' => $token)
	);
	
	//echo "<pre> DEBUG ".__FUNCTION__.":\n\n".var_export($response, true)."</pre>";
	return $response;
}

function gjb_obtePaypalTokenInfo($token)
{
	$ppParams = array(
		'METHOD' => 'GetExpressCheckoutDetails',
		'TOKEN' => $token
	);

	$response = hashCall($ppParams);
	//echo "<pre> DEBUG ".__FUNCTION__.":\n\n".var_export($response, true)."</pre>";

	return $response;
}

function gjb_confirmarPagamentUsuari($year, &$token, $CheckoutDetails = null)
{
	if (empty($token)) return false;
	
	global $current_user;
	if ($CheckoutDetails == null)
		$CheckoutDetails = gjb_obtePaypalTokenInfo($token);
	if (!gjb_comprovaNoError($CheckoutDetails))
	{
		gjb_obteNouPaypalTokenUsuari($year, $token);
		return false;
	}
	if (!gjb_haIntroduitMetodePagament($CheckoutDetails)) return false;

	$ppParams = array(
		'METHOD' => 'DoExpressCheckoutPayment',
		'PAYMENTACTION' => 'Sale',
		'AMT' => $CheckoutDetails['AMT'],
		'TOKEN' => $token,
		'PAYERID' => $CheckoutDetails['PAYERID']
	);
	$response = hashCall($ppParams);
	//echo "<pre> DEBUG ".__FUNCTION__.":\n\n".var_export($response, true)."</pre>";

	$status = strtoupper($response['PAYMENTSTATUS']);
	if (gjb_comprovaNoError($response) ||
	    strcmp($status, 'COMPLETED') == 0)
	{
		gjb_updateUserInfo($year, $current_user->ID, array(
			'Confirmat'   => '1')
		);
	}
	else
	{
		gjb_obteNouPaypalTokenUsuari($year, $token);
		return false;
	}

	return true;
}

function gjb_obteVistaUsuariConfirmat($year, $userinfo)
{
	global $lang;

	$name = $userinfo[0]->Nom;
	$lastname = $userinfo[0]->Cognoms;
	$email = $userinfo[0]->Email;
	$NIF =  $userinfo[0]->NIF;

	return $lang['FORM_SUCCESS'].
"<br/><div class='clearfix'>
<b style='font-size: 14pt;'>{$lang['REGISTER_DATA']}:</b><br/>
{$name} {$lastname}<br/>
{$email}<br/>
{$NIF}</div>";
}

function gjb_obteVistaUsuariPerConfirmar($year, $token, $response, $userinfo)
{
	global $lang;
	global $lang_sel;
	global $gjb_cost;

	$wpPayPalFramework = wpPayPalFramework::getInstance();
	$currency = $wpPayPalFramework->getSetting('currency');
	$postcommand_name = GJB_POSTCOMMAND_NAME;
	$url = strstr($_SERVER['REQUEST_URI'], "?", true);
	if (empty($url)) $url = $_SERVER['REQUEST_URI'];
	$url .= "?lang={$lang_sel}";

	$name = $userinfo[0]->Nom;
	$lastname = $userinfo[0]->Cognoms;
	$email = $userinfo[0]->Email;
	$NIF =  $userinfo[0]->NIF;

	if (!empty($lang['FORM_CONFIRM']))
		$header = "<p>".$lang['FORM_CONFIRM']."</p><hr/>";
	return $header.
//"<pre>".var_export($response, true)."</pre>".
"<div class='clearfix'>
<div style='float:right;width:50%;'>
<b style='font-size: 14pt;'>{$lang['REGISTER_DATA']}:</b><br/>
{$name} {$lastname}<br/>
{$email}<br/>
{$NIF}</div>
<div style='float:left;width:50%;'>
<b style='font-size: 14pt;'>{$lang['FACTURATION_DATA']}:</b><br/>
{$response['SHIPTONAME']}<br/>
{$response['EMAIL']}<br/>
{$response['SHIPTOSTREET']}<br/>
{$response['SHIPTOZIP']} {$response['SHIPTOCITY']}, {$response['SHIPTOCOUNTRYNAME']}
</div>
</div>
<hr/>
<div class='clearfix'>
	<div style='float:right;margin: 5px;'> <h3>Total: {$response['AMT']} {$response['CURRENCYCODE']}</h3></div>
</div>
<div class='clearfix'>
	<div style='float:right;margin: 4px'>
		<form action={$url} method='post'>
			<input type='hidden' name='{$postcommand_name}' value='confirmar'/>
			<input type='submit' value='{$lang['BUTTON_CONFIRM']}'/>
		</form>
	</div>
	<div style='float:right;margin: 4px'>
		<form action={$url} method='post'>
			<input type='hidden' name='{$postcommand_name}' value='cancelpago'/>
			<input type='submit' value='{$lang['BUTTON_CANCEL']}'/>
		</form>
	</div>
</div>";

}

function gjb_obteVistaUsuariPerPagar($year, $token)
{
	global $gjb_cost;
	global $lang;
	global $gjb_online_discount_percent;

	$discount = $gjb_cost*$gjb_online_discount_percent*0.01;
	$total_cost = $gjb_cost - $discount;
	
	$discount = gjb_fl2str($discount);
	$total_cost = gjb_fl2str($total_cost);

	$locale = get_locale();

	$wpPayPalFramework = wpPayPalFramework::getInstance();
	$currency = $wpPayPalFramework->getSetting('currency');
	if (GJB_PAY_ON_PAYPAL) $useraction = "<input type='hidden' name='useraction' value='commit'>";
	else $useraction = "";

	return $lang['FORM_TOPAY'].
"<hr/>
<div class='clearfix' style='margin:3px'>
<div class='gjb_costdiv'>{$gjb_cost} {$currency}</div>
<div class='gjb_costdiv'>{$lang['COST']}:</div>
</div>
<div class='clearfix' style='margin:3px'>
<div class='gjb_costdiv'>- {$discount} {$currency}</div>
<div class='gjb_costdiv'>{$lang['DISCOUNT']}:</div>
</div>
<div class='clearfix' style='margin:3px'>
<div class='gjb_costdiv'><b>{$total_cost} {$currency}</b></div>
<div class='gjb_costdiv'><b>{$lang['TOTAL_COST']}:</b></div>
</div>
<div class='clearfix'>
	<div style='float:right;margin: 10px'>
		<form action='{$wpPayPalFramework->getUrl()}' method='get'>
			<input type='hidden' name='cmd' value='_express-checkout'>
			<input type='hidden' name='token' value='{$token}'>
			{$useraction}
			<input type='image' src='https://fpdbs.paypal.com/dynamicimageweb?cmd=_dynamic-image&locale={$locale}' border='0' name='submit' alt='PayPal Checkout.'>
		</form>
	</div>
</div>";
}

function gjb_registrarUsuari($year)
{
	global $gjb_table_sufix_name;
	global $lang_sel;
	global $lang;

	global $current_user;
	global $wpdb;

	$table_name = $wpdb->prefix.$gjb_table_sufix_name;
	$userinfo = gjb_getUserInfo($year, $current_user->ID);

	$values = array();
	gjb_ParsePost('gjb_nom', 'Nom', $values);
	gjb_ParsePost('gjb_cognoms', 'Cognoms', $values);
	if (gjb_ParsePost('gjb_dni', 'NIF', $values))
		$values['NIF'] = strtoupper($values['NIF']);
	gjb_ParsePost('gjb_email', 'Email', $values);
	gjb_ParsePost('gjb_edat', 'Edat', $values);
	gjb_ParsePost('gjb_sexe', 'Sexe', $values);
	gjb_ParsePost('gjb_comarca', 'Comarca', $values);
	gjb_ParsePost('gjb_coneguts', 'Coneguts', $values);
	gjb_ParsePost('gjb_t_pc', 'TipusPC', $values);
	gjb_ParsePost('gjb_especialitat', 'Especialitat', $values);
	gjb_ParsePost('gjb_estudis', 'Estudis', $values);
	if (gjb_ParsePost('gjb_FinEst', 'FinEstudis', $values))
		if(!$values['FinEstudis']) $values['FinEstudis'] = 0;
		else $values['FinEstudis'] = 1;
	gjb_ParsePost('gjb_treball', 'Treball', $values);
	if (gjb_ParsePost('gjb_BuscFeina', 'BuscoFeina', $values))
		if(!$values['BuscoFeina']) $values['BuscoFeina'] = 0;
		else $values['BuscoFeina'] = 1;
	$values['Fecha'] = (string)gjb_fecha_atimestamp(date("d/m/Y"));

	if(count($userinfo) > 0) //ACTUALIZAR DATOS
	{
		$where = array("Year" => $year, "ID" => $current_user->ID);
		if($wpdb->update($table_name, $values, $where))
		{

		}
		else
		{
			global $gjb_errs;
			$gjb_errs[] = "BD: Error updating database. DNI in use.";
		}
	}
	else //INSERTAR DATOS
	{
		$values['Year'] = $year;
		$values['ID'] = $current_user->ID;
		
		if($wpdb->insert($table_name, $values))
		{
			//Enviar emails informatorios
			$nom     = html_entity_decode($values['Nom']);
			$cognoms = html_entity_decode($values['Cognoms']);

			$mailbody  = "S'acava de realitzar una nova inscripcio, les dades son:\n";
			$mailbody .= "{$cognoms}, {$nom} amb DNI: {$values['NIF']} i email: {$values['Email']}. El seu idioma preferent es ".strtoupper($lang_sel);
			$subject   = "Nova inscripcio: {$nom} {$cognoms}";
			$admin_email = get_option('admin_email');
			wp_mail($admin_email, $subject, $mailbody);
		}
		else
		{
			global $gjb_errs;
			$gjb_errs[] = "BD: Error inserting to database. DNI in use.";
		}
	}
}

function gjb_obteFormulariRegistre()
{
	global $lang;
	global $current_user;
	global $gjb_normativa_link;

	$postcommand_name = GJB_POSTCOMMAND_NAME;
	$url = $_SERVER['REQUEST_URI'];

	return
"<script type='text/javascript'>
lang = [];
lang['ERR_NO_NAME']         = \"".html_entity_decode($lang['ERR_NO_NAME'],ENT_COMPAT,"UTF-8")."\";
lang['ERR_NO_SURNAME']      = \"".html_entity_decode($lang['ERR_NO_SURNAME'],ENT_COMPAT,"UTF-8")."\";
lang['ERR_NO_NIF']          = \"".html_entity_decode($lang['ERR_NO_NIF'],ENT_COMPAT,"UTF-8")."\";
lang['ERR_NO_EMAIL']        = \"".html_entity_decode($lang['ERR_NO_EMAIL'],ENT_COMPAT,"UTF-8")."\";
lang['ERR_NO_AGE']          = \"".html_entity_decode($lang['ERR_NO_AGE'],ENT_COMPAT,"UTF-8")."\";
lang['ERR_NO_COMARCA']      = \"".html_entity_decode($lang['ERR_NO_COMARCA'],ENT_COMPAT,"UTF-8")."\";
lang['ERR_NO_KNOWN_PEOPLE'] = \"".html_entity_decode($lang['ERR_NO_KNOWN_PEOPLE'],ENT_COMPAT,"UTF-8")."\";
lang['ERR_NO_RULES']        = \"".html_entity_decode($lang['ERR_NO_RULES'],ENT_COMPAT,"UTF-8")."\";
</script>
<form name='gjb_registre' action='{$url}' method='post'>
	<input type='hidden' name='{$postcommand_name}' value='registrar'>
	<p>{$lang['REG_TEXT1']}</p>
	<table class='gjb_table'><tbody>
		<tr>
			<td><label for='gjb_nom'>{$lang['REG_NAME']}:</label></td>
			<td><input type='text' name='gjb_nom' id='gjb_nom' maxlength='50' size='20' value='{$current_user->user_firstname}'></td>
		</tr>
		<tr>
			<td><label for='gjb_cognoms'>{$lang['REG_SURNAME']}:</label></td>
			<td><input type='text' name='gjb_cognoms' id='gjb_cognoms' maxlength='100' size='20' value='{$current_user->user_lastname}'></td>
		</tr>
		<tr>
			<td><label for='gjb_dniselect'>{$lang['REG_ID_TYPE']}:</label></td>
			<td>
				<select name='gjb_dniselect' id='gjb_dniselect' style='width:208px;'>
					<option value='dni'>{$lang['REG_DNI']} (44444444Z)</option>
					<option value='other'>{$lang['REG_PASSPORT']}</option>
					<option value='other'>{$lang['REG_OTHER_ID']}</option>
				</select> 
			</td>
		</tr>
		<tr>
			<td><label for='gjb_dni'>{$lang['REG_ID_NUM']}:</label></td>
			<td><input type='text' name='gjb_dni' id='gjb_dni' size='20'></td>
		</tr>
		<tr>
			<td><label for='gjb_email'>{$lang['REG_EMAIL']}:</label></td>
			<td><input type='text' name='gjb_email' id='gjb_email' maxlength='50' size='20' value='{$current_user->user_email}'></td>
		</tr>
	</tbody></table>
	<p>{$lang['REG_TEXT2']}</p>
	<table class='gjb_table'> <tbody>
		<tr>
			<td><label for='gjb_edat'>{$lang['REG_AGE']}:</label></td>
			<td><input type='text' name='gjb_edat' id='gjb_edat' maxlength='3' size='3'></td>
		</tr>
		<tr>
			<td><label for='gjb_sexe'>{$lang['REG_GENDER']}:</label></td>
			<td>
				<select name='gjb_sexe' id='gjb_sexe'>
					<option value = '1'>{$lang['REG_GENDER_MALE']}</option>
					<option value = '0'>{$lang['REG_GENDER_FEMALE']}</option>
				</select>
			</td>
		</tr>
		<tr>
			<td><label for='gjb_comarca'>{$lang['REG_COMARCA']}:</label></td>
			<td><input type='text' name='gjb_comarca' id='gjb_comarca' maxlength='100' size='20'></td>
		</tr>
		<tr>
			<td><label for='gjb_coneguts'>{$lang['REG_KNOWN_PEOPLE']}:</label></td>
			<td><input type='text' name='gjb_coneguts' id='gjb_coneguts' maxlength='3' size='3'></td>
		</tr>
		<tr>
			<td><label for='gjb_t_pc'>{$lang['REG_COMPUTER']}:</label></td>
			<td>
				<select name='gjb_t_pc' id='gjb_t_pc'>
					<option value = 'taula'>{$lang['REG_COMPUTER_DESKTOP']}</option>
					<option value = 'portatil'>{$lang['REG_COMPUTER_LAPTOP']}</option>
					<option value = 'cap'>{$lang['REG_COMPUTER_NOCOMP']}</option>
				</select>
			</td>
		</tr>
		<tr>
			<td><label for='gjb_especialitat'>{$lang['REG_SKILLS']}:</label></td>
			<td>
				<select name='gjb_especialitat' id='gjb_especialitat'>
					<option value = 'Diseny'>{$lang['REG_SKILLS_GAMEDES']}</option>
					<option value = 'Prog'>{$lang['REG_SKILLS_DEVEL']}</option>
					<option value = 'Graf2D'>{$lang['REG_SKILLS_2DART']}</option>
					<option value = 'Graf3D'>{$lang['REG_SKILLS_3DMOD']}</option>
					<option value = 'So'>{$lang['REG_SKILLS_AUDIO']}</option>
					<option value = 'Altres'>{$lang['REG_SKILLS_OTHERS']}</option>
				</select>
			</td>
		</tr>
		<tr>
			<td><label for='gjb_estudis'>{$lang['REG_STUDIES']}:</label></td>
			<td>
				<select name='gjb_estudis' id='gjb_estudis'>
					<option value = 'ESO'>{$lang['REG_STUDIES_ESO']}</option>
					<option value = 'FP_M'>{$lang['REG_STUDIES_CFGM']}</option>
					<option value = 'Batxillerat'>{$lang['REG_STUDIES_BATX']}</option>
					<option value = 'FP_S'>{$lang['REG_STUDIES_CFGS']}</option>
					<option value = 'Uni_FIB'>{$lang['REG_STUDIES_FIB']}</option>
					<option value = 'Uni'>{$lang['REG_STUDIES_UNI']}</option>
					<option value = 'EE'>{$lang['REG_STUDIES_EE']}</option>
					<option value = 'Master'>{$lang['REG_STUDIES_MASTER']}</option>
				</select>
			</td>
		</tr>
		<tr>
			<td><label for='gjb_FinEst'>{$lang['REG_STUDIES_OVER']}:</label></td>
			<td><input type='checkbox' name='gjb_FinEst' id='gjb_FinEst' value='1'></td>
		</tr>
		<tr>
			<td><label for='gjb_treball'>{$lang['REG_JOB']}:</label></td>
			<td>
				<select name='gjb_treball' id='gjb_treball'>
					<option value = 'no'>{$lang['REG_JOB_NO']}</option>
					<option value = 'tparcial'>{$lang['REG_JOB_YES_PART']}</option>
					<option value = 'tcomplet'>{$lang['REG_JOB_YES_FULL']}</option>
					<option value = 'tpuntual'>{$lang['REG_JOB_YES_FREE']}</option>
				</select>
			</td>
		</tr>
		<tr>
			<td><label for='gjb_BuscFeina'>{$lang['REG_JOB_LOOKING']}</label></td>
			<td><input type='checkbox' name='gjb_BuscFeina' id='gjb_BuscFeina' value='1'></td>
		</tr>
		 <tr>
			<td>
				<label for='gjb_normativa'>{$lang['REG_RULES']}:</label>
				<a href='{$gjb_normativa_link}' target='_blank'>link</a>
			</td>
			<td>
				<input type='checkbox' name='gjb_normativa' id='gjb_normativa'>
			</td>
		</tr>
		<tr>
			<td colspan='2'>
				<input type='button' name='gjb_Enviar' onclick='validateForm()' value='{$lang['REG_SEND']}' /> 
				<input type='reset' name='gjb_Borrar' value='{$lang['REG_CLEAR']}' /> 
			</td>
		</tr>
	</tbody></table>
</form>";
}
