/* Efekt wciśnięcia wybranej komórki (i odznaczenia poprzedniej) */
const zaznaczKomorke = (komorka, idKomorki) => {
    let odznaczanaKomorka = document.getElementsByClassName("imageCaptchaSelectedCell")[0];
    if(odznaczanaKomorka) {
        odznaczanaKomorka.classList.remove("imageCaptchaSelectedCell");
    }
    komorka.classList.add("imageCaptchaSelectedCell");
    document.getElementById("imageCaptchaAnswer").value = idKomorki;
}
/* Funkcja wysyłająca formularz do API reCAPTCHA po zatwierdzeniu komentarza, w celu weryfikacji */
function triggerRecaptcha(token) {  // Z jakiegoś powodu niedozwolona jest tutaj funkcja strzałkowa...
    document.getElementById("captchaForm").submit();
}