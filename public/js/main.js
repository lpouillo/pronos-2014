// Fonction principale permettant de mettre à jour le contenu de la page via AJAX
function affElement(page,section,id,action,div_target) {
	$('form_nav').id.value=id;
	$('form_nav').page.value=page;
	$('form_nav').action.value=action;
	$('form_nav').section.value=section;

	//pour debuguer
	//	$('form_nav').target='fenetre_debug';
	//	$('form_nav').submit();
	//en cas de debug ne pas exectuer

	$('form_nav').set('send', 	{
				url: 'index.php',
				method: 'post',
				onSuccess: function(transport) {
					$(div_target).innerHTML = transport;
					if ($('update_titre')) {
						window.document.title=$('update_titre').innerHTML;
						$('span_titre').innerHTML=$('update_titre').innerHTML;
					}
			/*		if ($('update_url')) {
						alert($('update_url').innerHTML);
						window.url=$('update_url').innerHTML;
					}*/
					
					check_redirection();
					
					$$('input.DatePicker').each( function(el){
						new DatePicker(el);
					});
				},
				onRequest: function(transport) {
					$(div_target).innerHTML = '<div class="loader1">Chargement en cours ...<br/><br/> <img src="public/images/ajax-loader.gif"/></div>';
				},
				onFailure: function() {
					$(div_target).innerHTML = 'Impossible d\'afficher la page ... ';
				}
			}
		);
	$('form_nav').send();
}

// Fonction permettant de soummettre un formulaire via AJAX
function submitForm(id_form) {
	
	var div_target='page';
	$(id_form).set('send', 	{
				url: 'index.php',
				method: 'post',
				onSuccess: function(transport) {
					$(div_target).innerHTML = transport;
					if ($('update_titre')) {
						window.document.title=$('update_titre').innerHTML;
						$('span_titre').innerHTML=$('update_titre').innerHTML;
					}
			/*		if ($('update_url')) {
						alert($('update_url').innerHTML);
						window.url=$('update_url').innerHTML;
					}*/
					check_redirection();
					$$('input.DatePicker').each( function(el){
						new DatePicker(el);
					});
				},
				onRequest: function(transport) {
					$(div_target).innerHTML = '<div class="loader1">Chargement en cours ...<br/><br/> <img src="public/images/ajax-loader.gif"/></div>';
				},
				onFailure: function() {
					$(div_target).innerHTML = 'Failed to submit the form ...';
				}
			}
		);
	
	$(id_form).send();
}


// Vérification que l'on doit pas rediriger la page
function check_redirection() {
//	alert('coucou');
	if ($('redirection')) {
		window.setInterval('redirigeAccueil();',500);
	}
}

// Fonction permettant de rediriger vers l'accueil pour les pages non-trouvées ou non-autorisées
function redirigeAccueil() {
	document.location.href='index.php';
}
