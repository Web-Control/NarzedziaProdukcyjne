/**
 * @author Web-Control
 */
function pole_klient()
{
   var x = document.getElementById("klient");
   var y = document.getElementById("wlasne");
   var z = document.getElementById("wszyscy");
	var a = document.getElementById("klienci");

    if (x.checked) {
       document.getElementById("klient_nazwa").readOnly=false;
       document.getElementById("klient_nazwa").style.backgroundColor="white";
		document.getElementById("klienci").style.display="block";
    }


     if (y.checked) {
       document.getElementById("klient_nazwa").readOnly=true;
		document.getElementById("klient_nazwa").style.backgroundColor="silver";
		document.getElementById("klienci").style.display="none";
}

if (z.checked) {
       document.getElementById("klient_nazwa").readOnly=true;
		document.getElementById("klient_nazwa").style.backgroundColor="silver";
		a.style.display = "none";
}



}


