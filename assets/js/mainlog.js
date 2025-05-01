// Efface la console pour un débogage plus propre
console.clear();

// Récupération des éléments du DOM pour les boutons de connexion et d'inscription
const loginBtn = document.getElementById('login');
const signupBtn = document.getElementById('signup');

// Gestionnaire d'événement pour le bouton de connexion
loginBtn.addEventListener('click', (e) => {
	// Récupère le parent du bouton (le formulaire de connexion)
	let parent = e.target.parentNode.parentNode;
	// Parcourt les classes du parent pour gérer l'animation
	Array.from(e.target.parentNode.parentNode.classList).find((element) => {
		if(element !== "slide-up") {
			// Ajoute la classe slide-up si elle n'est pas présente
			parent.classList.add('slide-up')
		}else{
			// Si la classe est déjà présente, la retire et ajoute slide-up au formulaire d'inscription
			signupBtn.parentNode.classList.add('slide-up')
			parent.classList.remove('slide-up')
		}
	});
});

// Gestionnaire d'événement pour le bouton d'inscription
signupBtn.addEventListener('click', (e) => {
	// Récupère le parent du bouton (le formulaire d'inscription)
	let parent = e.target.parentNode;
	// Parcourt les classes du parent pour gérer l'animation
	Array.from(e.target.parentNode.classList).find((element) => {
		if(element !== "slide-up") {
			// Ajoute la classe slide-up si elle n'est pas présente
			parent.classList.add('slide-up')
		}else{
			// Si la classe est déjà présente, la retire et ajoute slide-up au formulaire de connexion
			loginBtn.parentNode.parentNode.classList.add('slide-up')
			parent.classList.remove('slide-up')
		}
	});
});