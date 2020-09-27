function closeRefresh() {
	window.parent.document.getElementById( 'sbox-window' ).close();
	setTimeout( 'window.parent.location.reload();', 1500 );
}