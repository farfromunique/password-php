function addCharToList(char) {
	var ul = document.querySelector('#all-characters');
	var liToAdd = document.createElement('li');
	liToAdd.innerText = char;
	ul.appendChild(liToAdd);
}

function removeExcludedChars() {
	let all = document.querySelectorAll('#all-characters > li');
	let exclude = document.querySelector('#exclude').value;
	let ex = exclude.split('');
	ex.forEach(
		function (char) {
			for (let i = 0; i < all.length; i++) {
				if (all[i].innerText == char) {
					all[i].remove();
					console.log('Added ' + char + ' to exclude');
				}
			}
		}
	);
}

function makeList() {
	let symbols = document.querySelectorAll('#all-characters > li');
	symbols.forEach(function (el) {
		el.remove();
	}, this);
	let min = 33;
	let max = 126;

	for (let i = min; i < max; i++) {
		addCharToList(String.fromCharCode(i));
	}
	removeExcludedChars();
}

$('#exclude').keyup(function () {
	makeList();
	
});

document.addEventListener('load', makeList());

document.querySelector('form').addEventListener('submit',function mySubmitFunction(evt) {
	evt.preventDefault();
	return false;
});