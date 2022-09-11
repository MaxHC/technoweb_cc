"use strict";

function validateConnectionForm(){

	let formConnection = document.getElementById("connection");
	let formError = document.getElementById("formError");

	let formCreate = document.getElementById("create");
	let createError = document.getElementById("createError");

	let username = document.getElementById("username");
	let password = document.getElementById("password");

	let createUsername = document.getElementById("createUsername");
	let createPassword = document.getElementById("createPassword");
	let confirmPassword = document.getElementById("confirmPassword")

	formConnection.addEventListener("submit", (event) => {
		if(username.value == "" || password.value == ""){
			event.preventDefault();
			formError.textContent = "Nom d'utilisateur ou mot de passe incorrect !"
		}
	});

	formCreate.addEventListener("submit", (event) => {
		if(createUsername.value == "" || createPassword.value == "" || confirmPassword.value == ""){
			event.preventDefault();
			createError.textContent = "Nom d'utilisateur ou mot de passe vide !";
		} else if(createPassword.value != confirmPassword.value){
			event.preventDefault();
			createError.textContent = "Les mots de passe ne sont pas identique !";
		}
	});
}