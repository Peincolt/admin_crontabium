require('../css/login.css');

document.addEventListener('DOMContentLoaded',function() {
    var inputCreationAcount = document.getElementById('user_demand_password_first');
    var inputCreationAccountDeux = document.getElementById('user_demand_password_second');
    if (inputCreationAcount && inputCreationAccountDeux) {
        inputCreationAcount.oninput = checkPassword;
        inputCreationAccountDeux.oninput = checkSame;
    }
},false);

function checkPassword()
{
    var inputCreationAccountDeux = document.getElementById('user_demand_password_second');
    var spanError = document.getElementById('error_password');
    spanError.innerHTML = "";
    if (inputCreationAccountDeux.value && this.value != inputCreationAccountDeux.value) {
        spanError.innerHTML = "Les deux mots de passe doivent correspondre";
        spanError.classList.add('error');
    } else {
        var message = "";
        var lengthRegex = new RegExp("^.{8,}$");
        var majRegex = new RegExp("[A-Z]+");
        var numberRegex = new RegExp("[0-9]+");
        var specialCharRegex = new RegExp("\\W+");
        if (!lengthRegex.test(this.value)) {
            message = "Your password must contains at least 8 characters";
        }

        if (!majRegex.test(this.value)) {
            if (message == "") {
                message = "Your password must contains at least a capital letter";
            } else {
                message += " and including a capital letter";
                var length = true;
            }
        }

        if (!numberRegex.test(this.value)) {
            if (message == "") {
                message = "Your password must at least contains a digit";
            } else {
                number = true;
                if (length != undefined) {
                    message += ", a digit";
                } else {
                    message += " and including a digit";
                }
            }
        }

        if (!specialCharRegex.test(this.value)) {
            if (message == "") {
                message = "Your password must at least a special char";
            } else {
                if (length || number) {
                    message += ", a special char";
                } else {
                    message += " and including a special char";
                }
            }
        }

        spanError.innerHTML = message;
    }
}

function checkSame()
{
    var inputCreationAccount = document.getElementById('user_demand_password_first');
    var spanError = document.getElementById('error_password');
    spanError.innerHTML = "";
    if (inputCreationAccount.value && this.value != inputCreationAccount.value) {
        spanError.innerHTML = "The two passwords must match";
        spanError.classList.add('error');
    }
}
