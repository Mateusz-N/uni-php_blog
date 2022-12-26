/* Wygenerowanie edytora artykułu pod zadanym elementem; opcjonalnie — m.in. w przypadku powrotu do edycji po wyświetleniu podglądu — z predefiniowanymi tytułem i treścią */
const pokazEdytorArtykulu = (target, action, title, category, text, id) => {
    let submitBtnName, submitBtnValue;
    if(action === "create") {
        submitBtnName = "submitArticle";
        submitBtnValue = "Podgląd";
    }
    else if(action === "edit") {
        submitBtnName = "editArticle";
        submitBtnValue = "Zapisz";
    }
    target.insertAdjacentHTML("afterend", `
        <div id = "articleEditor">
            <form action = "index.php" method = "post" onsubmit = "return walidacjaJS(event);" id = "articleForm" novalidate>
                <div id = "articleTitleContainer">
                    <label for = "articleTitle" class = "labelAbove"><span class = "required">*</span> Tytuł</label>
                    <div id = "articleTitleFieldContainer">
                        <input name = "articleTitle" value = "${title}" placeholder = "O obrotach sfer niebieskich" oninput = "ukryjBlad(event);" class = "formInput" id = "articleTitleField">
                        <p class = "formFieldError formFieldErrorOver articleTitleError"></p>
                    </div>
                    <label for = "articleCategory" class = "labelAbove"><span class = "required">*</span> Kategoria</label>
                    <div id = "articleCategoryFieldContainer">
                        <input name = "articleCategory" value = "${category}" placeholder = "Misc." oninput = "ukryjBlad(event);" class = "formInput" id = "articleCategoryField">
                        <p class = "formFieldError formFieldErrorOver articleCategoryError"></p>
                    </div>
                </div>
                <div id = "articleTextContainer">
                    <label for = "articleText" class = "labelAbove"><span class = "required">*</span> Treść artykułu</label>
                    <div id = "articleTextSubcontainer">
                        <div id = "articleEditor_toolbar">
                            <input type = "button" title = "Pogrubienie" class = "articleEditor_toolbarButton" id = "bbButton_bold">
                            <input type = "button" title = "Kursywa" class = "articleEditor_toolbarButton" id = "bbButton_italic">
                            <input type = "button" title = "Podkreślenie" class = "articleEditor_toolbarButton" id = "bbButton_underline">
                            <input type = "button" title = "Przekreślenie" class = "articleEditor_toolbarButton" id = "bbButton_strikethrough">
                        </div>
                        <div id = "articleTextAreaContainer">
                            <textarea name = "articleText" rows = "16" placeholder = "[b][i]Hello world![/i][/b]" class = "formTextArea" id = "articleText">${text}</textarea>
                            <p class = "formFieldError formFieldErrorOver articleTextError"></p>
                        </div>
                    </div>
                </div>
                <input type = "hidden" name = "articleID" value = "${id}">
                <input type = "submit" name = "${submitBtnName}" value = "${submitBtnValue}" class = "submitButton submitButton_article">
            </form>
        </div>
    `);
}

/* Ustawienie paska narzędzi edytora artykułu */
const ustawPasekNarzedzi = () => {
    const articleEditor_toolbarButtons = document.getElementsByClassName("articleEditor_toolbarButton");
    const articleText = document.getElementById("articleText");
    /* Otocz zaznaczony tekst odpowiednim znacznikiem */
    Array.from(articleEditor_toolbarButtons).forEach((toolbarButton) => {
        toolbarButton.addEventListener("click", () => {
            let tag;
            switch(toolbarButton.id.split("_").pop()) { // część łancucha po ostatnim znaku '_'
                case "bold":
                    tag = "b";
                    break;
                case "italic":
                    tag = "i";
                    break;
                case "underline":
                    tag = "u";
                    break;
                case "strikethrough":
                    tag = "s";
                    break;
                default:
                    tag = "";
            }
            let selectionStart = articleText.selectionStart;
            let selectionEnd = articleText.selectionEnd;
            let articleContents = articleText.value;
            let selectedText = articleContents.substring(selectionStart, selectionEnd);
            let wrappedText = "[" + tag + "]" + selectedText + "[/" + tag + "]";
            articleText.value = articleContents.substring(0, selectionStart) + wrappedText + articleContents.substr(selectionEnd);
        });
    });
}

/* Wyświetl/ukryj formularz dodający artykuł */
const przelaczEdytorArtykulu = (event, action, articleID) => {
    const articleEditor = document.getElementById("articleEditor");
    if(articleEditor) {
        articleEditor.remove();
    }
    if(action === "create") {
        pokazEdytorArtykulu(event.target, "create", "", "", "", 0);
        document.querySelectorAll("article").forEach(article => {
            article.style.display = "block";
        });
    }
    else if(action === "edit") {
        wlaczEdycjeArtykulu(event.target, articleID);
    }
    ustawPasekNarzedzi();
}

/* Edytuj wybrany artykuł */
const wlaczEdycjeArtykulu = (target, articleID) => {
    const article = target.closest("article");
    const title = article.querySelector(".articleTitle").textContent;
    const category = article.querySelector(".articleCategory").textContent;
    const text = article.querySelector(".articleText").textContent;
    article.style.display = "none";
    pokazEdytorArtykulu(article, "edit", title, category, text, articleID);
}