/* Walidacja po stronie klienta */
const walidacjaJS = (event) => {
    const formularz = event.target;
    let wystapilBlad = false;
    if(formularz.id === "articleForm") {
        const tytul = formularz["articleTitle"].value.trim();
        const kategoria = formularz["articleCategory"].value.trim();
        const tresc = formularz["articleText"].value.trim();
        if(tytul.length === 0) {
            wyswietlBlad(formularz, "articleTitleError", "Tytuł", "pustePole", "y");
            wystapilBlad = true;
        }
        if(kategoria.length === 0) {
            wyswietlBlad(formularz, "articleCategoryError", "Kategoria", "pustePole", "a");
            wystapilBlad = true;
        }
        if(tresc.length === 0) {
            wyswietlBlad(formularz, "articleTextError", "Treść artykułu", "pustePole", "a");
            wystapilBlad = true;
        }
    }
    else if(formularz.classList.contains("commentForm")) {
        const tresc = formularz["commentText"].value.trim();
        const uzytkownik = formularz["commentUsername"].value.trim();
        const email = formularz["commentEmail"].value.trim();
        if(tresc.length === 0) {
            wyswietlBlad(formularz, "commentTextError", "Treść komentarza", "pustePole", "a");
            wystapilBlad = true;
        }
        // if(uzytkownik.length === 0) {
        //     wyswietlBlad(formularz, "commentUsernameError", "Nazwa użytkownika", "pustePole", "a");
        //     wystapilBlad = true;
        // }
        // if(email.length === 0) {
        //     wyswietlBlad(formularz, "commentEmailError", "E-mail", "pustePole", "y");
        //     wystapilBlad = true;
        // }
        // if(!email.match(/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/)) {
        //     wyswietlBlad(formularz, "commentEmailError", "E-mail", "nieprawidlowyFormat", "y");
        //     wystapilBlad = true;
        // }
    }
    return !wystapilBlad;   // True (nie wystąpił błąd) pozwala przesłać dane formularza, False (wystąpił błąd) je zatrzymuje
}
/* Funkcja wyświetlająca dymek z odpowiadającym komunikatem o zaistniałym błędzie po naciśnięciu przycisku "Skomentuj" */
const wyswietlBlad = (formularz, klasaKomunikatu, pole, typ, zaimek) => {
    const komunikat = formularz.getElementsByClassName(klasaKomunikatu)[0];
    let blad;
    switch(typ) {
        case "pustePole":
            blad = `${pole} nie może być pust${zaimek}!`;
            break;
        default:
            blad = `Nieprawidłow${zaimek} ${pole}!`;
    }
    komunikat.style.visibility = "visible";
    komunikat.innerHTML = blad;
}
/* Funkcja ukrywająca dymek z błędem, gdy użytkownik zmieni zawartość pola, w którym wystąpił błąd */
const ukryjBlad = (event) => {
    event.target.parentElement.getElementsByClassName("formFieldError")[0].style.visibility = "hidden";
}