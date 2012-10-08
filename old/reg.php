<?php
include_once "lang.php";




function gjb_obteFormulariRegistre()
{
	return <<<EOD
<form name="registre" action="inscripcio.php?lang={$lang_sel}" method="post">
	<p>{$lang['REG_TEXT1']}
		<table summary="Taula Formulari">
			<tr>
				<td>{$lang['REG_NAME']}:</td>
				<td><input type="text" name="nom" maxlength="50" size="20"></td>
			</tr>
			<tr class="alt">
				<td>{$lang['REG_SURNAME']}:</td>
				<td><input type="text" name="cognoms" maxlength="100" size="20"></td>
			</tr>
			<tr>
				<td>{$lang['REG_DNI']} (44444444Z):</td>
				<td><input type="text" name="dni" maxlength="9" size="20"></td>
			</tr>
			<tr class="alt">
				<td>{$lang['REG_EMAIL']}:</td>
				<td><input type="text" name="email" maxlength="50" size="20"></td>
			</tr>
		</table>
	</p>
	<p> {$lang['REG_TEXT2']}
		<table summary="Taula Formulari dades estudi">
			<tr>
				<td>{$lang['REG_AGE']}:</td>
				<td><input type="text" name="edat" maxlength="3" size="3"></td>
			</tr>
			<tr class="alt">
				<td>{$lang['REG_GENDER']}:</td>
				<td>
					<select name="sexe">
						<option value = "1">{$lang['REG_GENDER_MALE']}</option>
						<option value = "0">{$lang['REG_GENDER_FEMALE']}</option>
					</select>
				</td>
			</tr>
			<tr>
				<td>{$lang['REG_COMARCA']}:</td>
				<td><input type="text" name="comarca" maxlength="100" size="20"></td>
			</tr>
			<tr class="alt">
				<td>{$lang['REG_KNOWN_PEOPLE']}:</td>
				<td><input type="text" name="coneguts" maxlength="3" size="3"></td>
			</tr>
			<tr>
				<td>{$lang['REG_COMPUTER']}:</td>
				<td>
					<select name="t_pc">
						<option value = "taula">{$lang['REG_COMPUTER_DESKTOP']}</option>
						<option value = "portatil">{$lang['REG_COMPUTER_LAPTOP']}</option>
						<option value = "cap">{$lang['REG_COMPUTER_NOCOMP']}</option>
					</select>
				</td>
			</tr>
			<tr class="alt">
				<td>{$lang['REG_SKILLS']}:</td>
				<td>
					<select name="especialitat">
						<option value = "Diseny">{$lang['REG_SKILLS_GAMEDES']}</option>
						<option value = "Prog">{$lang['REG_SKILLS_DEVEL']}</option>
						<option value = "Graf2D">{$lang['REG_SKILLS_2DART']}</option>
						<option value = "Graf3D">{$lang['REG_SKILLS_3DMOD']}</option>
						<option value = "So">{$lang['REG_SKILLS_AUDIO']}</option>
						<option value = "Altres">{$lang['REG_SKILLS_OTHERS']}</option>
					</select>
				</td>
			</tr>
			<tr>
				<td>{$lang['REG_STUDIES']}:</td>
				<td>
					<select name="estudis">
						<option value = "ESO">{$lang['REG_STUDIES_ESO']}</option>
						<option value = "FP_M">{$lang['REG_STUDIES_CFGM']}/option>
						<option value = "Batxillerat">{$lang['REG_STUDIES_BATX']}</option>
						<option value = "FP_S">{$lang['REG_STUDIES_CFGS']}</option>
						<option value = "Uni_FIB">{$lang['REG_STUDIES_FIB']}</option>
						<option value = "Uni">{$lang['REG_STUDIES_UNI']}</option>
						<option value = "EE">{$lang['REG_STUDIES_EE']}</option>
						<option value = "Master">{$lang['REG_STUDIES_MASTER']}</option>
					</select>
				</td>
			</tr>
			<tr class="alt">
				<td>{$lang['REG_STUDIES_OVER']}</td>
				<td><input type="checkbox" name="FinEst" value="1"></td>
			</tr>
			<tr>
				<td>{$lang['REG_JOB']}</td>
				<td>
					<select name="treball">
						<option value = "no">{$lang['REG_JOB_NO']}</option>
						<option value = "tparcial">{$lang['REG_JOB_YES_PART']}</option>
						<option value = "tcomplet">{$lang['REG_JOB_YES_FULL']}</option>
						<option value = "tpuntual">{$lang['REG_JOB_YES_FREE']}</option>
					</select>
				</td>
			</tr>
			<tr class="alt">
				<td>{$lang['REG_JOB_LOOKING']}</td>
				<td><input type="checkbox" name="BuscFeina" value="1"></td>
			</tr>
			 <tr>
				<td>
					{$lang['REG_RULES']}: <a href="http://vgafib.com/ggj/?page_id=86" target="_blank">link</a>
				</td>
				<td>
					<input type="checkbox" name="normativa">
				</td>
			</tr>
			<tr>
				<td>
					<input type="hidden" name="rand" value="{mt_rand()}">
					<input type="button" onclick="validaFormulari()" value="{$lang['REG_SEND']}" /> 
					<input type="reset" name="Borrar" value="{$lang['REG_CLEAR']}" /> 
				</td>
			</tr>
		</table>
	</p>
</form>
EOD;
}

?>

