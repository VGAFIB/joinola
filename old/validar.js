String.prototype.replaceAt = function(index, char)
{
      return this.substr(0, index) + char + this.substr(index+char.length);
}

function isEmail(input)
{
	var email = input.value;
	var regx = /^[_A-Za-z0-9-]+(\\.[_A-Za-z0-9-]+)*@[A-Za-z0-9]+(\\.[A-Za-z0-9]+)*(\\.[A-Za-z]{2,})$/;
	return regx.test(email);
} 

function isNum(input)
{
	var value = input.value;
	var regx = /^[0-9]+$/
	return regx.test(value);
} 

function NIE2NIF(num)
{
	     if (num == "X") return "0";
	else if (num == "Y") return "1";
	else if (num == "Z") return "2";
	return num;
}

function isNIF(input)
{
	var value = input.value.toUpperCase();
	value.replaceAt(0, NIE2NIF(num.charAt(0));
	var regx = /^[0-9]{8}[a-zA-Z]{1}$/;
	if (!regx.test(value)) return false;

	var num = parseInt(value.substring(0,7));
	var checksum = "TRWAGMYFPDXBNJZSQVHLCKET";
	var ch = checksum.charAt(num % 23);
	return ch == value.charAt(8);
}

function markIncorrect(input)
{
	input.focus();
	input.select();
}

function inputValidate(input, errorText) 
{
	if (input.value.length == 0)
	{
		alert(errorText);
		markIncorrect(input);
		return false;
	}
	return true;
}

function inputValidate(input, errorText, checkers) 
{
	if (!inputValidate(input, errorText)) return false;
	for (checker in checkers)
	{
		if (input.value.length == 0)
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

	if (!inputValidate(form["gjb_nom"]     , "No s'ha introduit el nom.") ||
	    !inputValidate(form["gjb_cognoms"] , "No s'ha introduit els cognoms.") ||
	    !inputValidate(form["gjb_dni"]     , "El DNI/NIE introduit no es correcte.", [isNIF]) ||
	    !inputValidate(form["gjb_edat"]    , "La edat introduida no es correcte.", [isNum]) ||
	    !inputValidate(form["gjb_comarca"] , "No s'ha introduit comarca.") ||
	    !inputValidate(form["gjb_email"]   , "El correu electrònic introduit no es valid.", [isEmail]) ||
	    !inputValidate(form["gjb_coneguts"], "El número d'assistents no es valid.", [isNum]))
	{
		return false;
	}


	if (!form.normativa.checked)
	{    
		alert("S'ha d'acceptar la normativa per registrar-se.")
		return false;
	}

	form.submit();        
	return true;
}
