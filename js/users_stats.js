// Gestion de la modal
const createUserBtn = document.getElementById('createUserBtn');
const createUserModal = document.getElementById('createUserModal');
const closeModalBtn = document.getElementById('closeModalBtn');
const cancelBtn = document.getElementById('cancelBtn');

// Ouvrir la modal
createUserBtn.addEventListener('click', () => {
    createUserModal.style.display = 'flex';
});

// Fermer la modal
function closeModal() {
    createUserModal.style.display = 'none';
}

closeModalBtn.addEventListener('click', closeModal);
cancelBtn.addEventListener('click', closeModal);

// Fermer la modal en cliquant à l'extérieur
window.addEventListener('click', (e) => {
    if (e.target === createUserModal) {
        closeModal();
    }
});

// Validation du formulaire
const createUserForm = document.getElementById('createUserForm');

createUserForm.addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    
    if (password !== confirmPassword) {
        e.preventDefault();
        alert('Les mots de passe ne correspondent pas!');
        return false;
    }
    
    // Ici vous pouvez ajouter une requête AJAX si vous voulez soumettre le formulaire sans recharger la page
    // return true pour soumettre normalement
    return true;
});