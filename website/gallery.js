var currimage = 1;
var imgnumber = 6;
var image;
var previmage;
var nextimage;

function init() {
	//recupero l'immagine image e setto la prima immagine (anche se c'è già)
	image = document.getElementById("image");
	setImageTo("img/1.jpg", image);
	//creo due oggetti di supporto per fare il fade delle immagini.
	previmage = image.cloneNode(-1);
	nextimage = image.cloneNode(-1);
	setImageTo("img/2.jpg", nextimage);
	setImageTo("img/0.jpg", previmage);
	
	//setto l'altezza di image div
	var imagediv = document.getElementById("imagediv");
	imagediv.setAttribute("style", "height:" + image.height + "px;");
}

var interval;
var opacity = 0;
var fading = false;
function next() {
	if(fading) return;
	fading = true;
	setImageTo("img/" + currimage + ".jpg", previmage);
	//seleziono l'immagine corrente
	currimage = (currimage+1)%imgnumber;
	//recupero il div imagediv e aggiungo nextimage
	var imagediv = document.getElementById("imagediv");
	nextimage.setAttribute("id", "nextimage");
	imagediv.appendChild(nextimage);
	
	interval = setInterval("fadenext()", 50);
}

function fadenext() {
	opacity++;
	if(opacity == 10) { //finito il fade
		image.setAttribute("style", "opacity: 0.0;");
		nextimage.setAttribute("style", "opacity: 1.0;");
		clearInterval(interval); //tolgo l'esecuzione automatica.
		//setto l'altezza di image div
		var imagediv = document.getElementById("imagediv");
		imagediv.setAttribute("style", "height:" + nextimage.height + "px;");
		//scambio le immagini per avere quella primcipale su image.
		imagediv.removeChild(image); //rimuovo la vecchia immagine
		nextimage.setAttribute("id", "image"); //cambio id a next image
		img = image; image = nextimage; nextimage = img; //scambio le variabili
		
		setImageTo("img/" + (currimage+1)%imgnumber + ".jpg", nextimage);
		if(nextimage.height > nextimage.width) {
			nextimage.setAttribute("style", "width: auto;height: 600px;")
		}
		opacity = 0; //torno in modalità di attesa.
		fading = false;
	} else {
		image.setAttribute("style", "opacity: 0." + (10 - opacity) + ";");
		nextimage.setAttribute("style", "opacity: 0." + (opacity) + ";");
	}
}

function setImageTo(imagepath, imgelement) {
	imgelement.setAttribute("src", imagepath);
	if(imgelement.height > imgelement.width)
		imgelement.setAttribute("class", "vertical");
	else
		imgelement.setAttribute("class", "horizontal");
}

function prev() {
	if(fading) return;
	fading = true;
	setImageTo("img/" + currimage + ".jpg", nextimage);
	//seleziono l'immagine corrente
	currimage = (currimage+imgnumber-1)%imgnumber;
	//recupero il div imagediv e aggiungo nextimage
	var imagediv = document.getElementById("imagediv");
	previmage.setAttribute("id", "previmage");
	imagediv.appendChild(previmage);
	
	interval = setInterval("fadeprev()", 10);
}

function fadeprev() {
	opacity++;
	if(opacity == 10) { //finito il fade
		image.setAttribute("style", "opacity: 0.0;");
		previmage.setAttribute("style", "opacity: 1.0;");
		clearInterval(interval); //tolgo l'esecuzione automatica.
		//setto l'altezza di image div
		var imagediv = document.getElementById("imagediv");
		imagediv.setAttribute("style", "height:" + previmage.height + "px;");
		//scambio le immagini per avere quella primcipale su image.
		imagediv.removeChild(image); //rimuovo la vecchia immagine
		previmage.setAttribute("id", "image"); //cambio id a next image
		img = image; image = previmage; previmage = img; //scambio le variabili
		
		setImageTo("img/" + (currimage-1+imgnumber)%imgnumber + ".jpg", previmage);
		if(previmage.height > previmage.width) {
			previmage.setAttribute("style", "width: auto;height: 600px;")
		}
		opacity = 0; //torno in modalità di attesa.
		fading = false;
	} else {
		image.setAttribute("style", "opacity: 0." + (10 - opacity) + ";");
		previmage.setAttribute("style", "opacity: 0." + (opacity) + ";");
	}
}