<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN">

<?php
include_once "lang.php";
?>

<html>
<head>
<title><?php echo $lang['REG_TITLE'];?></title>
<meta name="Description" content="<?php echo $lang['PAGE_DESCRIPTION'];?>" /> 
<meta name="Keywords" content="Global Game Jam, Barcelona Game Jam, Game
Jam, Barcelona, España, Videojuegos, Desarrollo de Videojuegos, UPC, FIB,
VGAFIB" /> 
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<meta name="Distribution" content="Global" />
<meta name="Author" content="VGAFIB - vgafib@gmail.com" />
<script type="text/javascript" src="validar.js"></script>
<NOSCRIPT>
 <p>El formulario només es pot enviar si Javascript està activat.</p>
</NOSCRIPT>
<LINK href="style.css" rel="stylesheet" type="text/css">
</head>
<body>
    <h2><?php echo $lang['REG_TITLE'];?></h2>
    <form name="registre" action="inscripcio.php?lang=<?php echo $lang_sel;?>" method="post">
    <!--<p> <font color="red">Actualment el proces d'inscripció encara està en fase de proves.</font></p>-->
    <p><?php echo $lang['REG_TEXT1']; ?>
    <table summary="Taula Formulari">
    <tr>
        <td><?php echo $lang['REG_NAME'];?>:</td>
        <td><input type="text" name="nom" maxlength="50" size="20"></td>
    </tr>
    <tr class="alt">
        <td><?php echo $lang['REG_SURNAME'];?>:</td>
        <td><input type="text" name="cognoms" maxlength="100" size="20"></td>
    </tr>
    <tr>
        <td><?php echo $lang['REG_DNI'];?> (44444444Z):</td>
        <td><input type="text" name="dni" maxlength="9" size="20"></td>
    </tr>
    <tr class="alt">
        <td><?php echo $lang['REG_EMAIL'];?>:</td>
        <td><input type="text" name="email" maxlength="50" size="20"></td>
    </tr>
	</table>
	</p>
	<p> <?php echo $lang['REG_TEXT2'];?>
    <table summary="Taula Formulari dades estudi">
    <tr>
        <td><?php echo $lang['REG_AGE'];?>:</td>
        <td><input type="text" name="edat" maxlength="3" size="3"></td>
    </tr>
    <tr class="alt">
        <td><?php echo $lang['REG_GENDER'];?>:</td>
        <td>
        <select name="sexe">
        <option value = "1"><?php echo $lang['REG_GENDER_MALE'];?></option>
        <option value = "0"><?php echo $lang['REG_GENDER_FEMALE'];?></option>
        </select>
        </td>
    </tr>
    <tr>
        <td><?php echo $lang['REG_COMARCA'];?>:</td>
        <td><input type="text" name="comarca" maxlength="100" size="20"></td>
    </tr>
    <tr class="alt">
        <td><?php echo $lang['REG_KNOWN_PEOPLE'];?>:</td>
        <td><input type="text" name="coneguts" maxlength="3" size="3"></td>
    </tr>
    <tr>
        <td><?php echo $lang['REG_COMPUTER'];?>:</td>
        <td>
        <select name="t_pc">
        <option value = "taula"><?php echo $lang['REG_COMPUTER_DESKTOP'];?></option>
        <option value = "portatil"><?php echo $lang['REG_COMPUTER_LAPTOP'];?></option>
        <option value = "cap"><?php echo $lang['REG_COMPUTER_NOCOMP'];?></option>
        </select>
        </td>
    </tr>
    <tr class="alt">
        <td><?php echo $lang['REG_SKILLS'];?>:</td>
        <td>
        <select name="especialitat">
        <option value = "Diseny"><?php echo $lang['REG_SKILLS_GAMEDES'];?></option>
        <option value = "Prog"><?php echo $lang['REG_SKILLS_DEVEL'];?></option>
        <option value = "Graf2D"><?php echo $lang['REG_SKILLS_2DART'];?></option>
        <option value = "Graf3D"><?php echo $lang['REG_SKILLS_3DMOD'];?></option>
        <option value = "So"><?php echo $lang['REG_SKILLS_AUDIO'];?></option>
        <option value = "Altres"><?php echo $lang['REG_SKILLS_OTHERS'];?></option>
        </select>
        </td>
    </tr>
    <tr>
        <td><?php echo $lang['REG_STUDIES'];?>:</td>
        <td>
        <select name="estudis">
        <option value = "ESO"><?php echo $lang['REG_STUDIES_ESO'];?></option>
        <option value = "FP_M"><?php echo $lang['REG_STUDIES_CFGM'];?>/option>
        <option value = "Batxillerat"><?php echo $lang['REG_STUDIES_BATX'];?></option>
        <option value = "FP_S"><?php echo $lang['REG_STUDIES_CFGS'];?></option>
        <option value = "Uni_FIB"><?php echo $lang['REG_STUDIES_FIB'];?></option>
        <option value = "Uni"><?php echo $lang['REG_STUDIES_UNI'];?></option>
        <option value = "EE"><?php echo $lang['REG_STUDIES_EE'];?></option>
        <option value = "Master"><?php echo $lang['REG_STUDIES_MASTER'];?></option>
        </select>
        </td>
    </tr>
    <tr class="alt">
        <td><?php echo $lang['REG_STUDIES_OVER'];?></td>
        <td><input type="checkbox" name="FinEst" value="1"></td>
    </tr>
    <tr>
        <td><?php echo $lang['REG_JOB'];?></td>
        <td>
        <select name="treball">
        <option value = "no"><?php echo $lang['REG_JOB_NO'];?></option>
        <option value = "tparcial"><?php echo $lang['REG_JOB_YES_PART'];?></option>
        <option value = "tcomplet"><?php echo $lang['REG_JOB_YES_FULL'];?></option>
        <option value = "tpuntual"><?php echo $lang['REG_JOB_YES_FREE'];?></option>
        </select>
        </td>
    </tr>
    <tr class="alt">
        <td><?php echo $lang['REG_JOB_LOOKING'];?></td>
        <td><input type="checkbox" name="BuscFeina" value="1"></td>
    </tr>
     <tr>
        <td>
          <?php echo $lang['REG_RULES'];?>: <a href="http://vgafib.com/ggj/?page_id=86" target="_blank">link</a>
        </td>
        <td><input type="checkbox" name="normativa"></td>
    </tr>
    <tr>
        <td>
          <input type="hidden" name="rand" value="<?php echo mt_rand();?>">
          <input type="button" onclick="validaFormulari()" value="<?php echo $lang['REG_SEND'];?>" /> 
          <input type="reset" name="Borrar" value="<?php echo $lang['REG_CLEAR'];?>" /> 
        </td>
    </tr>
	</table>
	</p>
  </form>
</body>
</html>


