document.addEventListener('DOMContentLoaded', () => {
    // Recup du password
    const passwordFields = document.querySelectorAll('input[type="password"]');

    passwordFields.forEach(field => {
       
        // Div pour le message
        const message = document.createElement('div');
        message.classList.add('password-message', 'mt-1');
        message.style.fontSize = '0.9em';
        field.parentNode.appendChild(message);

        // La barre pour le visuel
        const strengthBar = document.createElement('div');
        strengthBar.style.height = '6px';
        strengthBar.style.borderRadius = '4px';
        strengthBar.style.marginTop = '4px';
        strengthBar.style.transition = 'width 0.3s ease';
        field.parentNode.appendChild(strengthBar);

        field.addEventListener('input', () => {
            // je recup la valeur 
            const value = field.value;
            // score de 0 a 3 
            let strength = 0;
            // les regles pas respecter
            const errors = [];

            // Longueur je veux min 8 caracteres
            if (value.length >= 8) strength++;
            else errors.push('• Au moins 8 caractères');

            // Au moin 1 maj
            if (/[A-Z]/.test(value)) strength++;
            else errors.push('• Au moins une lettre majuscule');

            // 1 caractere spe
            if (/[!@#$%^&*(),.?":{}|<>]/.test(value)) strength++;
            else errors.push('• Au moins un caractère spécial');

            // Affichage des messages
            if (errors.length > 0) {
                message.innerHTML = errors.join('<br>');
                message.style.color = 'red';
            } else {
                message.innerHTML = '✅ Mot de passe sécurisé';
                message.style.color = 'green';
            }

            // Mise a jour de la barre 
            if (strength === 0) {
                strengthBar.style.width = '0';
                strengthBar.style.backgroundColor = 'transparent';
            } else if (strength === 1) {
                strengthBar.style.width = '33%';
                strengthBar.style.backgroundColor = 'red';
            } else if (strength === 2) {
                strengthBar.style.width = '66%';
                strengthBar.style.backgroundColor = 'orange';
            } else if (strength === 3) {
                strengthBar.style.width = '100%';
                strengthBar.style.backgroundColor = 'green';
            }
        });
    });

    //  Vérification avant soumission
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', (e) => {
            const passwordInput = form.querySelector('input[type="password"]');
            if (passwordInput) {
                const value = passwordInput.value;
                const isValid =
                    value.length >= 8 &&
                    /[A-Z]/.test(value) &&
                    /[!@#$%^&*(),.?":{}|<>]/.test(value);

                if (!isValid) {
                    // Si c'est pas valid j'empeche l'envoie!!!
                    e.preventDefault();
                    alert('⚠️ Votre mot de passe n’est pas assez sécurisé.');
                }
            }
        });
    });
});
