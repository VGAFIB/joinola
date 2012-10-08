function isEmail(input)
{
	var email = input.value;
	var regx = XRegExp("^[_A-Za-z0-9-]+(\\.[_A-Za-z0-9-]+)*@[A-Za-z0-9]+(\\.[A-Za-z0-9]+)*(\\.[A-Za-z]{2,})$");
	return regx.test(email);
} 

function isName(input)
{
	var value = input.value;
	var unicodeWord = XRegExp("^[\\p{L} ]+$");
	return unicodeWord.test(value);
}

function isNum(input)
{
	var value = input.value;
	var regx = /^[0-9]+$/
	return regx.test(value);
} 

function replaceAt(str, index, ch)
{
	return str.substr(0, index) + ch + str.substr(index+ch.length);
}

function NIE2NIF(num)
{
	return 'X' == num ? '0' : 'Y' == num ? '1' : 'Z' == num ? '2' : num;
}

function isNIF(input)
{
	var value = input.value.toUpperCase();
	value = replaceAt(value, 0, NIE2NIF(value.charAt(0)));
	var regx = /^[0-9]{8}[a-zA-Z]{1}$/;
	if (!regx.test(value)) return false;

	var num = parseInt(value.substring(0,8), 10);
	var checksum = 'TRWAGMYFPDXBNJZSQVHLCKET';
	var ch = checksum.charAt(num % 23);
	return ch == value.charAt(8);
}

function markIncorrect(input)
{
	input.focus();
	input.select();
}

function isNotEmpty(input)
{
	return input.value.length != 0;
}

function inputValidate(input, errorText, checkers) 
{
	checkers = [isNotEmpty].concat(checkers);
	for (var i = 0; i < checkers.length; i++)
	{
		if (!(checkers[i](input)))
		{
			alert(errorText);
			markIncorrect(input);
			return false;
		}
	}

	return true;
}

function validateForm()
{
	var form = document.gjb_registre;

	if (!inputValidate(form['gjb_nom']     , lang['ERR_NO_NAME'], [isName]) ||
	    !inputValidate(form['gjb_cognoms'] , lang['ERR_NO_SURNAME'], [isName]) ||
	    !inputValidate(form['gjb_email']   , lang['ERR_NO_EMAIL'], [isEmail]) ||
	    !inputValidate(form['gjb_edat']    , lang['ERR_NO_AGE'], [isNum]) ||
	    !inputValidate(form['gjb_comarca'] , lang['ERR_NO_COMARCA'], [isName]) ||
	    !inputValidate(form['gjb_coneguts'], lang['ERR_NO_KNOWN_PEOPLE'], [isNum]))
	{
		return false;
	}

	if (form.gjb_dniselect.value == 'dni' &&
		!inputValidate(form['gjb_dni'], lang['ERR_NO_NIF'], [isNIF]))
	{
		return false;
	}
	else if(!inputValidate(form['gjb_dni'], lang['ERR_NO_NIF'], []))
	{
		return false;
	}

	if (!form.gjb_normativa.checked)
	{    
		alert(lang['ERR_NO_RULES']);
		return false;
	}

	form.submit();        
	return true;
}

//window['validateForm'] = validateForm;
