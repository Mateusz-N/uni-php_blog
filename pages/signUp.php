<!DOCTYPE html>
<html>
    <head>
        <title>console.blog()</title>
        <link rel = "stylesheet" href = "./style.css" type = "text/css">
    </head>
    <body>
        <main id = "contentPreview">
            <form action = "index.php" method = "post" id = "signupForm" enctype = "multipart/form-data"> <!-- enctype umożliwia przesłanie pliku -->
                <div class = "formField">
                    <label for = "userName">
                        <span class = "required">*&nbsp;</span>Nazwa użytkownika:&nbsp;
                    </label>
                    <input name = "userName" placeholder = "JohnDoe2" required>
                </div>
                <div class = "formField">
                    <label for = "password">
                        <span class = "required">*&nbsp;</span>Hasło:&nbsp;
                    </label>
                    <input type = "password" name = "password" placeholder = "********" required>
                </div>
                <div class = "formField">
                    <label for = "passwordConfirm">
                        <span class = "required">*&nbsp;</span>Potwierdź hasło:&nbsp;
                    </label>
                    <input type = "password" name = "passwordConfirm" placeholder = "********" required>
                </div>
                <div class = "formField">
                    <label for = "email">
                        <span class = "required">*&nbsp;</span>E-mail:&nbsp;
                    </label>
                    <input type = "email" name = "email" type = "email" placeholder = "john.doe@example.com" required>
                </div>
                <div class = "formField">
                    <label for = "avatar">
                        Wybierz awatar (opcjonalne):&nbsp;
                    </label>
                    <input type = "file" name = "avatar" accept = ".jpg">
                </div>
                <div id = "formButtons">
                    <input type = "reset" value = "Wyczyść" class = "button">
                    <input type = "submit" name = "signUp" value = "Utwórz konto" class = "button">
                </div>
            </form>
        </main>
        <footer>
            <p>Uwaga! Moduł kont użytkowników w trakcie budowy (jak zresztą widać)!</p>
        </footer>
    </body>
</html>