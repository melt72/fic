$(function () {
	'use strict'

	// ______________LOADER
	$("#global-loader").fadeOut("slow");


	// This template is mobile first so active menu in navbar
	// has submenu displayed by default but not in desktop
	// so the code below will hide the active menu if it's in desktop
	if (window.matchMedia('(min-width: 992px)').matches) {
		$('.main-navbar .active').removeClass('show');
		$('.main-header-menu .active').removeClass('show');
	}
	// Shows header dropdown while hiding others
	$('.main-header .dropdown > a').on('click', function (e) {
		e.preventDefault();
		$(this).parent().toggleClass('show');
		$(this).parent().siblings().removeClass('show');
		$(this).find('.drop-flag').removeClass('show');
	});
	$('.country-flag1').on('click', function (e) {

		$(this).parent().toggleClass('show');
		$('.main-header .dropdown > a').parent().siblings().removeClass('show');
	});

	// ______________Full screen
	$(document).on("click", ".full-screen", function toggleFullScreen() {
		$('html').addClass('fullscreen-button');
		if ((document.fullScreenElement !== undefined && document.fullScreenElement === null) || (document.msFullscreenElement !== undefined && document.msFullscreenElement === null) || (document.mozFullScreen !== undefined && !document.mozFullScreen) || (document.webkitIsFullScreen !== undefined && !document.webkitIsFullScreen)) {
			if (document.documentElement.requestFullScreen) {
				document.documentElement.requestFullScreen();
			} else if (document.documentElement.mozRequestFullScreen) {
				document.documentElement.mozRequestFullScreen();
			} else if (document.documentElement.webkitRequestFullScreen) {
				document.documentElement.webkitRequestFullScreen(Element.ALLOW_KEYBOARD_INPUT);
			} else if (document.documentElement.msRequestFullscreen) {
				document.documentElement.msRequestFullscreen();
			}
		} else {
			$('html').removeClass('fullscreen-button');
			if (document.cancelFullScreen) {
				document.cancelFullScreen();
			} else if (document.mozCancelFullScreen) {
				document.mozCancelFullScreen();
			} else if (document.webkitCancelFullScreen) {
				document.webkitCancelFullScreen();
			} else if (document.msExitFullscreen) {
				document.msExitFullscreen();
			}
		}
	})


	// ______________Cover Image
	$(".cover-image").each(function () {
		var attr = $(this).attr('data-bs-image-src');
		if (typeof attr !== typeof undefined && attr !== false) {
			$(this).css('background', 'url(' + attr + ') center center');
		}
	});


	// ______________Toast
	$(".toast").toast();

	/* Headerfixed */
	$(window).on("scroll", function (e) {
		if ($(window).scrollTop() >= 66) {
			$('main-header').addClass('fixed-header');
		}
		else {
			$('.main-header').removeClass('fixed-header');
		}
	});

	// ______________Search
	$('body, .main-header form[role="search"] button[type="reset"]').on('click keyup', function (event) {
		if (event.which == 27 && $('.main-header form[role="search"]').hasClass('active') ||
			$(event.currentTarget).attr('type') == 'reset') {
			closeSearch();
		}
	});
	function closeSearch() {
		var $form = $('.main-header form[role="search"].active')
		$form.find('input').val('');
		$form.removeClass('active');
	}
	// Show Search if form is not active // event.preventDefault() is important, this prevents the form from submitting
	$(document).on('click', '.main-header form[role="search"]:not(.active) button[type="submit"]', function (event) {
		event.preventDefault();
		var $form = $(this).closest('form'),
			$input = $form.find('input');
		$form.addClass('active');
		$input.focus();
	});
	// if your form is ajax remember to call `closeSearch()` to close the search container
	$(document).on('click', '.main-header form[role="search"].active button[type="submit"]', function (event) {
		event.preventDefault();
		var $form = $(this).closest('form'),
			$input = $form.find('input');
		$('#showSearchTerm').text($input.val());
		closeSearch()
	});



	/* ----------------------------------- */

	// Showing submenu in navbar while hiding previous open submenu
	$('.main-navbar .with-sub').on('click', function (e) {
		e.preventDefault();
		$(this).parent().toggleClass('show');
		$(this).parent().siblings().removeClass('show');
	});
	// this will hide dropdown menu from open in mobile
	$('.dropdown-menu .main-header-arrow').on('click', function (e) {
		e.preventDefault();
		$(this).closest('.dropdown').removeClass('show');
	});
	// this will show navbar in left for mobile only
	$('#mainNavShow, #azNavbarShow').on('click', function (e) {
		e.preventDefault();
		$('body').addClass('main-navbar-show');
	});
	// this will hide currently open content of page
	// only works for mobile
	$('#mainContentLeftShow').on('click touch', function (e) {
		e.preventDefault();
		$('body').addClass('main-content-left-show');
	});
	// This will hide left content from showing up in mobile only
	$('#mainContentLeftHide').on('click touch', function (e) {
		e.preventDefault();
		$('body').removeClass('main-content-left-show');
	});
	// this will hide content body from showing up in mobile only
	$('#mainContentBodyHide').on('click touch', function (e) {
		e.preventDefault();
		$('body').removeClass('main-content-body-show');
	})
	// navbar backdrop for mobile only
	$('body').append('<div class="main-navbar-backdrop"></div>');
	$('.main-navbar-backdrop').on('click touchstart', function () {
		$('body').removeClass('main-navbar-show');
	});
	// Close dropdown menu of header menu
	$(document).on('click touchstart', function (e) {
		e.stopPropagation();
		// closing of dropdown menu in header when clicking outside of it
		var dropTarg = $(e.target).closest('.main-header .dropdown').length;
		if (!dropTarg) {
			$('.main-header .dropdown').removeClass('show');
		}
		// closing nav sub menu of header when clicking outside of it
		if (window.matchMedia('(min-width: 992px)').matches) {
			// Navbar
			var navTarg = $(e.target).closest('.main-navbar .nav-item').length;
			if (!navTarg) {
				$('.main-navbar .show').removeClass('show');
			}
			// Header Menu
			var menuTarg = $(e.target).closest('.main-header-menu .nav-item').length;
			if (!menuTarg) {
				$('.main-header-menu .show').removeClass('show');
			}
			if ($(e.target).hasClass('main-menu-sub-mega')) {
				$('.main-header-menu .show').removeClass('show');
			}
		} else {
			//
			if (!$(e.target).closest('#mainMenuShow').length) {
				var hm = $(e.target).closest('.main-header-menu').length;
				if (!hm) {
					$('body').removeClass('main-header-menu-show');
				}
			}
		}
	});
	$('#mainMenuShow').on('click', function (e) {
		e.preventDefault();
		$('body').toggleClass('main-header-menu-show');
	})
	$('.main-header-menu .with-sub').on('click', function (e) {
		e.preventDefault();
		$(this).parent().toggleClass('show');
		$(this).parent().siblings().removeClass('show');
	})
	$('.main-header-menu-header .close').on('click', function (e) {
		e.preventDefault();
		$('body').removeClass('main-header-menu-show');
	})

	$(".card-header-right .card-option .fe fe-chevron-left").on("click", function () {
		var a = $(this);
		if (a.hasClass("icofont-simple-right")) {
			a.parents(".card-option").animate({
				width: "35px",
			})
		} else {
			a.parents(".card-option").animate({
				width: "180px",
			})
		}
		$(this).toggleClass("fe fe-chevron-right").fadeIn("slow")
	});

	// ___________TOOLTIP
	var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
	var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
		return new bootstrap.Tooltip(tooltipTriggerEl)
	})


	// __________POPOVER
	var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
	var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
		return new bootstrap.Popover(popoverTriggerEl)
	})


	// Enable Eva-icons with SVG markup
	eva.replace();


	// ______________Horizontal-menu Active Class
	$(document).ready(function () {
		$(".horizontalMenu-list li a").each(function () {
			var pageUrl = window.location.href.split(/[?#]/)[0];
			if (this.href == pageUrl) {
				$(this).addClass("active");
				$(this).parent().addClass("active"); // add active to li of the current link
				$(this).parent().parent().prev().addClass("active"); // add active class to an anchor
				$(this).parent().parent().prev().click(); // click the item to make it drop
			}
		});
	});


	// ______________Active Class
	$(document).ready(function () {
		$(".horizontalMenu-list li a").each(function () {
			var pageUrl = window.location.href.split(/[?#]/)[0];
			if (this.href == pageUrl) {
				$(this).addClass("active");
				$(this).parent().addClass("active"); // add active to li of the current link
				$(this).parent().parent().prev().addClass("active"); // add active class to an anchor
				$(this).parent().parent().prev().click(); // click the item to make it drop
			}
		});
		$(".horizontal-megamenu li a").each(function () {
			var pageUrl = window.location.href.split(/[?#]/)[0];
			if (this.href == pageUrl) {
				$(this).addClass("active");
				$(this).parent().addClass("active"); // add active to li of the current link
				$(this).parent().parent().parent().parent().parent().parent().parent().prev().addClass("active"); // add active class to an anchor
				$(this).parent().parent().prev().click(); // click the item to make it drop
			}
		});
		$(".horizontalMenu-list .sub-menu .sub-menu li a").each(function () {
			var pageUrl = window.location.href.split(/[?#]/)[0];
			if (this.href == pageUrl) {
				$(this).addClass("active");
				$(this).parent().addClass("active"); // add active to li of the current link
				$(this).parent().parent().parent().parent().prev().addClass("active"); // add active class to an anchor
				$(this).parent().parent().prev().click(); // click the item to make it drop
			}
		});
	});


	// ______________ Back to Top

	var btn = $('#back-to-top');

	$(window).scroll(function () {
		if ($(window).scrollTop() > 300) {
			$('#back-to-top').fadeIn();
		} else {
			$('#back-to-top').fadeOut();
		}
	});

	btn.on('click', function (e) {
		e.preventDefault();
		$('html, body').animate({ scrollTop: 0 }, '300');
	});

	//materialize input
	$('document').ready(function() {
        $('body').materializeInputs();
    });

	//log in
	$("#loginform").validate({
		rules: {
			loginmail: {
				required: true,
				email: true
			},
			loginpassword: {
				required: true,
				minlength: 8
			}
		},
		messages: {
			loginmail: {
				required: "Please enter a email address",
				email: "Please enter a vaild email address"
			},
			loginpassword: {
				required: "Please provide a password",
				minlength: "Your password must be at least 5 characters long"
			}
		},
		errorElement: 'span',
		errorPlacement: function(error, element) {
			error.addClass('invalid-feedback');
			element.closest('.form-group').append(error);
		},
		highlight: function(element, errorClass, validClass) {
			$(element).addClass('is-invalid');
		},
		unhighlight: function(element, errorClass, validClass) {
			$(element).removeClass('is-invalid');
		},
		submitHandler: function(form) {
			var dati = $("#loginform").serialize();
			$.ajax({
				type: "post",
				url: "include/login.php",
				data: dati,
				dataType: "html",
				success: function(response) {
					if (response == 'ok') {
						window.location.replace("home.php");
					} else {
						$('#messaggio').html(response);
						$('#messaggio').show();
						setTimeout(function() {
							$('#messaggio').hide();
						}, '4000');
					}

				}
			});
		}
	});

	//logout
	$('.logout').click(function(e) {
		e.preventDefault();
		$('#modal-logout').modal('show');
	});

	
        // $('.select2').select2({
        //     placeholder: 'Choose one',
        //     searchInputPlaceholder: 'Search',
        //     width: '100%'
        // });

        $('.select2-no-search').select2({
            minimumResultsForSearch: Infinity,
            placeholder: 'Scegli una opzione',
            width: '100%'
        });

		//utente crea e modifica

		$(document).on('click', '#utentesubmitButton', function(event) {
			event.preventDefault();
			var forms = document.getElementsByClassName('needs-validation');
	
			// Loop over them and prevent submission
			var validation = Array.prototype.filter.call(forms, function(form) {
	
				if (form.checkValidity() === false) {
					event.preventDefault();
					event.stopPropagation();
				} else {
					event.preventDefault();
					$('.btn-salva').prop('disabled', true);
	
					var file_data = $('#immagineprofilo').prop('files')[0];
					var form_data = new FormData($('form')[0]);
					form_data.append('file', file_data);
	
					$.ajax({
						type: "post",
						url: "include/utente_crea.php",
						data: form_data,
						processData: false,
						contentType: false,
						dataType: "json",
						success: function(response) {
							//se il json torna con errori
							if (response.status) {
								notif({
									type: "success",
									msg: response.messaggio,
									position: "right",
									fade: true
								});
								setTimeout(function() {
									window.location = 'autorizzazioni.php';
								}, '3000');
							} else {
								notif({
									type: "error",
									msg: response.messaggio,
									position: "right",
									fade: true
								});
							}
						}
					});
				}
				form.classList.add('was-validated')
			});
		});

		// blocca utente
		$(document).on('click', '.bloccautente', function(e) {
			e.preventDefault();
			var tipo = $(this).attr("data-tipo");
			if (tipo == 1) {
				$(this).removeClass('btn-warning');
				$(this).addClass('btn-secondary');
				$(this).html('<span class="fe fe-unlock"> </span>');
				$(this).attr("data-tipo", 2);
			} else {
				$(this).removeClass('btn-secondary');
				$(this).addClass('btn-warning');
				$(this).html('<span class="fe fe-lock"> </span>');
				$(this).attr("data-tipo", 1);
			}
			$.ajax({
				type: "POST",
				url: "include/utente_blocca.php",
				data: {
					id: $(this).attr("data-id"),
					tipo: tipo
				},
			});
		});

		//cancella utente
		$(document).on('click', '.cancellautente', function(e) {
			e.preventDefault();
			var datid = $(this).attr("data-id");
			swal({
					title: "Sei sicuro?",
					text: "L'utente non potrà più essere recuperato!",
					type: "warning",
					showCancelButton: true,
					showLoaderOnConfirm: true,
					confirmButtonClass: "btn btn-danger",
					confirmButtonText: "Si, cancella!",
					closeOnConfirm: false
				},
				function() {
	
					setTimeout(function() {
						$.ajax({
							type: "POST",
							url: "include/utente_cancella.php",
							data: {
								id: datid
							},
							success: function(data) {
								$('#' + datid).closest("tr").remove();
								swal("Cancellato!", "L'utente è stato cancellato.", "success");
							}
						});
					}, 2000);
				});
	
			});
		
	 // configura mail

	 $(document).on('click', '#test', function(e) {
        e.preventDefault();
        $.ajax({
            type: "post",
            url: "include/parametri_mail_test.php",
            dataType: "html",
            success: function(response) {
                if (response == 'ok') {
                    notif({
                        type: "success",
                        msg: "Test inviato con successo",
                        position: "right",
                        fade: true
                    });
                } else {
                    alert(response);
                    notif({
                        type: "error",
                        msg: "Errore nell'invio della mail",
                        position: "right",
                        fade: true
                    });
                }
            }

        });
	});

	$(document).on('click', '#salva_mail_conf', function(event) {
        event.preventDefault();
        // Fetch all the forms we want to apply custom Bootstrap validation styles to
        var forms = document.getElementsByClassName('needs-validation');
        // Loop over them and prevent submission
        var validation = Array.prototype.filter.call(forms, function(form) {

            if (form.checkValidity() === false) {
                event.preventDefault();
                event.stopPropagation();
            } else {
                event.preventDefault();
                // $('.btn-salva').prop('disabled', true);
                var formdati = $("#form_configurazione_email").serializeArray();

                $.ajax({
                    type: "post",
                    url: "include/parametri_mail_salva.php",
                    data: {
                        dat: formdati,
                    },
                    dataType: "html",
                    success: function(response) {
                        notif({
                            type: "success",
                            msg: "Salvataggio effettuato con successo",
                            position: "right",
                            fade: true
                        });
                    }
                });
            }
            form.classList.add('was-validated');
        });
    });

	//abilita modifiche
	$(document).on('click', '.abilitamodifiche', function(e) {
        e.preventDefault();
        $('#modifica').hide();
        $('#salvataggio').show();
        // elimino l'attributo disabled da tutti gli input, i select e i checkbox
        $('input').removeAttr('disabled');
        $('select').removeAttr('disabled');
        $('checkbox').removeAttr('disabled');
    });

	//anagrafica
	$(document).on('click', '#privato', function() {
        if (document.getElementById('privato').checked) {
            $('#rs').prop('required', false);
            $('#nome').prop('required', true);
            $('#cognome').prop('required', true);
            $("#datiprivato").show();
            $("#datirs").hide();
        } else {
            $('#rs').prop('required', true);
            $('#nome').prop('required', false);
            $('#cognome').prop('required', false);
            $("#datiprivato").hide();
            $("#datirs").show();
        }
    });
	
	/**
 * *Aggiungi e modifica cliente
 */
	 $(document).on('click', '.addmodcliente', function(event) {
        event.preventDefault();
		var tipodiinserimento=$(this).attr("data-tipo");	
		    var ritorno = $(this).attr("data-bk");
        // Fetch all the forms we want to apply custom Bootstrap validation styles to
        var forms = document.getElementsByClassName('needs-validation');
        // Loop over them and prevent submission
        var validation = Array.prototype.filter.call(forms, function(form) {

            if (form.checkValidity() === false) {
                event.preventDefault();
                event.stopPropagation();
            } else {
                event.preventDefault();
                // $('.btn-salva').prop('disabled', true);
                var formdati = $("#formclienti").serializeArray();
            $.ajax({
                type: "post",
                url: "include/add_mod_cliente.php",
                data: {
                    dat: formdati,
                    tipodiinserimento: tipodiinserimento,
                    id: id
                },
                dataType: "html",
                success: function(response) {
					notif({
						type: "success",
						msg: "Salvataggio effettuato con successo",
						position: "right",
						fade: true
					});
                    if (ritorno == 'se') {
                        setTimeout(function() {
                            window.location.replace("anagrafica.php");
                        }, '3000');
                    }
                }
            });
            }
            form.classList.add('was-validated');
        });
    });

	/**
	 * *Aggiungi zona
	 */
	 $(document).on('click', '.addzona', function(event) {
		event.preventDefault();
		//Apro il model zona .modal('show')
		$('#modal-zone').modal('show');

		//Se schiaccio su .salvazona Invio i dati tramite ajax
		$(document).on('click', '.salvazona', function(event) {
		event.preventDefault();
		var nome= $('#nome_zona').val();
		//Valore del select provvigione
		var prov= $('#provv').val();
		$.ajax({
			type: "post",
			url: "include/zone.php",
			data: {
				nome: nome,
				provv: prov,
				tipo: 'add'
			},
			dataType: "json",
			success: function(response) {
				//Inserisco i valori json nel div #tab-zone e #tab-content-zone
				$('#tab-zone').html(response.tab);
				$('#tab-content-zone').html(response.tabcontent);
				notif({
					type: "success",
					msg: "Zona aggiunta con successo .",
					position: "right",
					fade: true
				});
				//Chiudo il model
				$('#modal-zone').modal('hide');
			}
		});
		});
	});
	
//Modifica provvigione alla zona sì cambio il select prov_tipo Leggo il valore e il data ID e mando tutto tramite ajax
$(document).on('change', '.prov_tipo', function(event) {
	event.preventDefault();
	var prov_tipo= $(this).val();
	var idzona= $(this).attr("data-id");
	$.ajax({
		type: "post",
		url: "include/zone.php",
		data: {
			idzona: idzona,
			prov_tipo: prov_tipo,
			tipo: 'mod'
		},
		success: function(response) {
			notif({
				type: "success",
				msg: "Provvigione modificata con successo .",
				position: "right",
				fade: true
			});
		}
	});
	});


	/**
	 * *Associa zona al cliente
	 */
	//  $(document).on('click', '.associazona', function(event) {
	// 	event.preventDefault();
	// 	//Leggo la zona  tramite data ID
	// 	var idzona= $(this).attr("data-id");
	// 	//Apro il model zona .modal('show')
	// 	$('#modal-cliente').modal('show');

	// 	//Se schiaccio su .aggiungicliente Invio i dati tramite ajax
	// 	$(document).on('click', '.associacliente', function(event) {
	// 	event.preventDefault();
	// 	var idcliente= $(this).attr("data-cliente");
	// 	//Modifico il colore del bottone da .btn-primary a .btn-danger
	// 	$(this).removeClass('btn-primary');
	// 	$(this).addClass('btn-danger');

	// 	//Modifico la classe del bottone da .associacliente a .disassociacliente
	// 	$(this).removeClass('associacliente');
	// 	$(this).addClass('disassociacliente');
	// 	//Modifico il nome del bottone da Associa a Disassocia
	// 	$(this).html('Disassocia');
		
	// 	//Valore del select provvigione
	// 	$.ajax({
	// 		type: "post",
	// 		url: "include/zone.php",
	// 		data: {
	// 			idcliente: idcliente,
	// 			idzona: idzona,
	// 			tipo: 'associa'
	// 		},
	// 		dataType: "json",
	// 		success: function(response) {
	
	// 			//Inserisco i valori json nel div #tab-zone e #tab-content-zone
	// 			// $('#tab-content-zone').html(response.tabcontent);
	// 			 $('#cl' + idcliente).html(response.zona);
	// 			notif({
	// 				type: "success",
	// 				msg: "Zona associata con successo .",
	// 				position: "right",
	// 				fade: true
	// 			});
	// 		}
	// 	});
	// 	});
	// 		//Se schiaccio sulla classe .disassociacliente Invio i dati tramite ajax e disassocia il cliente
	// 	$(document).on('click', '.disassociacliente', function(event) {
	// 	event.preventDefault();
	// 	var idcliente= $(this).attr("data-cliente");
	// 	// Cambio la classe del bottone da .disassociacliente a .associacliente
	// 	$(this).removeClass('disassociacliente');
	// 	$(this).addClass('associacliente');
	// 	// Cambio il colore del bottone da .btn-danger a .btn-success
	// 	$(this).removeClass('btn-danger');
	// 	$(this).addClass('btn-primary');
	// 	// Cambio il nome del bottone da Disassocia a Associa
	// 	$(this).html('Associa');
	// 	$.ajax({
	// 		type: "post",
	// 		url: "include/zone.php",
	// 		data: {
	// 			idcliente: idcliente,
	// 			idzona: idzona,
	// 			tipo: 'disassocia'
	// 		},
	// 		dataType: "json",
	// 		success: function(response) {
	// 			//Inserisco i valori json nel div #tab-zone e #tab-content-zone
	// 			// $('#tab-content-zone').html(response.tabcontent);
	// 			 $('#cl' + idcliente).html(response.zona);
	// 			notif({
	// 				type: "success",
	// 				msg: "Zona disassociata con successo .",
	// 				position: "right",
	// 				fade: true
	// 			});
	// 		}
	// 	});
	// 	});
	// });


 //______Basic Data Table
//  $('#clienti-datatable').DataTable({
// 	language: {
// 		searchPlaceholder: 'Search...',
// 		sSearch: '',
// 	}
// });

/**
 * *Aggiungi e modifica agente
 */
$(document).on('click', '#agentesubmitButton', function(event) {
	event.preventDefault();
	var tipo = $(this).attr("data-tipo");
	var forms = document.getElementsByClassName('needs-validation');

	// Loop over them and prevent submission
	var validation = Array.prototype.filter.call(forms, function(form) {

		if (form.checkValidity() === false) {
			event.preventDefault();
			event.stopPropagation();
		} else {
			event.preventDefault();
			$('.btn-salva').prop('disabled', true);

			$.ajax({
				type: "post",
				url: "include/agente.php",
				data: {
					tipo: tipo,
					id: id,
					nome: $('#nome_agente').val(),
					prov: $('#provv').val(),
					sigla: $('#sigla').val(),
					descrizione: $('#descrizione').val()
				},
				dataType: "json",
				success: function(response) {
					//se il json torna con errori
					if (response.status) {
						notif({
							type: "success",
							msg: response.messaggio,
							position: "right",
							fade: true
						});
						setTimeout(function() {
							window.location = 'lista_agenti.php';
						}, '3000');
					} else {
						// riattiavo il bottone
						$('.btn-salva').prop('disabled', false);
						notif({
							type: "error",
							msg: response.messaggio,
							position: "right",
							fade: true
						});
					}
				}
			});
		}
		form.classList.add('was-validated')
	});
});

//cancella agente
$(document).on('click', '.cancellagente', function(e) {
	e.preventDefault();
	var datid = $(this).attr("data-id");
	swal({
			title: "Sei sicuro?",
			text: "L'agente non potrà più essere recuperato!",
			type: "warning",
			showCancelButton: true,
			showLoaderOnConfirm: true,
			confirmButtonClass: "btn btn-danger",
			confirmButtonText: "Si, cancella!",
			cancelButtonText: "Annulla",
			closeOnConfirm: false
		},
		function() {
			setTimeout(function() {
				$.ajax({
					type: "POST",
					url: "include/agente.php",
					data: {
						id: datid,
						tipo:'del'
					},
					success: function(data) {
						$('#' + datid).closest("tr").remove();
						swal("Cancellato!", "L'agente è stato cancellato.", "success");
					}
				});
			}, 2000);
		});
	});

	//abilita modifiche
	$(document).on('click', '.abilitamodifiche', function(e) {
		e.preventDefault();
		$('#modifica').hide();
		$('#salvataggio').show();
		// elimino l'attributo disabled da tutti gli input, i select e i checkbox
		$('input').removeAttr('disabled');
		$('select').removeAttr('disabled');
		$('checkbox').removeAttr('disabled');
	});

/**
 * *Provvigioni
 */

//Se cambio il valore del select . Ricarico la pagina con il valore del select
$(document).on('change', '#provv_agente', function(event) {
	event.preventDefault();
	var id_agente = $(this).val();
	window.location = 'provv_agenti.php?id=' + id_agente;
});

$(document).on('change', '#provv_zona', function(event) {
	event.preventDefault();
	var id_zona = $(this).val();
	window.location = 'roma.php?id_zona=' + id_zona;
});
//provvigioni edit

var prov1 = new BSTable("basic-edittable22",{
	advanced: {
			  columnLabel:'Actions',
			  buttonHTML: `<div class="btn-group pull-right">
			  <button id="bEdit" type="button" class="btn btn-sm btn-primary">
			  <span class="fe fe-edit"> </span>
		  </button>
		  <button id="bAcep" type="button" class="btn btn-sm btn-warning" style="display:none;">
          <span class="fa fa-check-circle" > </span>
        </button>
		  </div>`,
	},
		
	editableColumns:"1,2",
	onEdit:function() {
		alert("edit");		
	},

	});
    prov1.init();

	$(document).on('click', '.annofatture', function(event) {
		event.preventDefault();
		$('.annofatture').removeClass('btn-primary');
		$('.annofatture').addClass('btn-secondary');
		$(this).addClass('btn-primary');
		$(this).removeClass('btn-secondary');
		var anno = $(this).attr("data-anno");
		$.ajax({
			type: "post",
			url: "include/fatture_agente.php",
			data: {
				anno: anno,
				tipo: 'lista',
				id: id
			},
			dataType: "html",
			success: function(response) {
				$('#dati_fatture').html(response);
				$('#basic-edittable a').editable({
					type: 'select',
					name: 'provv_percent',
					value: 16,
					source: [{
							value: 12,
							text: '12 %'
						},
						{
							value: 13,
							text: '13 %'
						},
						{
							value: 14,
							text: '14 %'
						},
						{
							value: 15,
							text: '15 %'
						},
						{
							value: 16,
							text: '16 %'
						},
						{
							value: 17,
							text: '17 %'
						},
						{
							value: 18,
							text: '18 %'
						},
						{
							value: 19,
							text: '19 %'
						},
						{
							value: 20,
							text: '20 %'
						},
					],
					name: 'status',
					url: 'include/provv_agenti.php',
					title: 'Provv %',
					success: function(response, newValue) {
						var pk = $(this).data('pk');
						$('#prov_' + pk).html(response);
					}
				});
			}
		});
	});

	//cancella zona
	// $(document).on('click', '.cancellazona', function(e) {
	// 	e.preventDefault();
	// 	var datid = $(this).attr("data-id");
	// 	swal({
	// 			title: "Sei sicuro?",
	// 			text: "La zona non potrà più essere recuperata!",
	// 			type: "warning",
	// 			showCancelButton: true,
	// 			showLoaderOnConfirm: true,
	// 			confirmButtonClass: "btn btn-danger",
	// 			confirmButtonText: "Si, cancella!",
	// 			closeOnConfirm: false
	// 		},
	// 		function() {

	// 			setTimeout(function() {
	// 				$.ajax({
	// 					type: "POST",
	// 					url: "include/zone.php",
	// 					data: {
	// 						id: datid,
	// 						tipo: 'cancella'
	// 					},
	// 					success: function(data) {
	// 						$('#


	//Se schiaccio la classe .liquidazione Apro il modal modal-liquidazione
	$(document).on('click', '.liquidazione', function(event) {
		event.preventDefault();
		$('#modal-liquidazione').modal('show');
	});

	$('#data_liquidazione').bootstrapdatepicker({
        format: "dd/mm/yyyy",
        viewMode: "date",
        multidate: false,
        multidateSeparator: "/",
    })

    var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth() + 1; //Gennaio è 0!
    var yyyy = today.getFullYear();
    if (dd < 10) {
        dd = '0' + dd
    }
    if (mm < 10) {
        mm = '0' + mm
    }
    today = dd + '/' + mm + '/' + yyyy;
    $('#data_liquidazione').val(today);


    $(document).on('click', '.li-scelta', function(e) {
        e.preventDefault();
        var button = $(this);
        if (button.hasClass('text-success')) {
            // Cambia lo stato del bottone e chiama l'API con AJAX
            toggleButtonState(button, 'text-danger', 'text-success', 'fe-square', 'fe-check-square');
            button.removeClass('inclusa');
        } else {
            // Cambia lo stato del bottone e chiama l'API con AJAX
            toggleButtonState(button, 'text-success', 'text-danger', 'fe-check-square', 'fe-square');
            button.addClass('inclusa');
        }
        // Aggiorna la somma degli importi
        var sommaImporti = calcolaSommaImporti();
        $('#importo_liquidazione').text(sommaImporti.toFixed(2));
        //Se importo liquidazSe importo liquidazione è zero disattivo
        if (sommaImporti == 0) {
            $('.liquida_provv').prop('disabled', true);
        } else {
            $('.liquida_provv').prop('disabled', false);
        }
    });

    function toggleButtonState(button, addClass, removeClass, addIconClass, removeIconClass) {
        button.removeClass(removeClass).addClass(addClass);
        button.removeClass(removeIconClass).addClass(addIconClass);
    }

    function calcolaSommaImporti() {
        var sommaImporti = 0;

        $('.inclusa').each(function() {
            var importo = parseFloat($(this).data('importo')) || 0;
            sommaImporti += importo;
        });

        return sommaImporti;
    }


    //Quando schiaccio sul bottone liquida provvigione Invio i dati tramite ajax
    $(document).on('click', '.liquida_provv', function(e) {
        e.preventDefault();
        //Disabilito il bottone
        $('.liquida_provv').prop('disabled', true);
        var metodo_pagamento = $('#metodo_pagamento').val();
        var note = $('#note').val();
        var data_liquidazione = $('#data_liquidazione').val();
        var fatture = [];
        $('.inclusa').each(function() {
            var id_fattura = $(this).data('id');
            fatture.push(id_fattura);
        });
        $.ajax({
            url: 'include/liquidazione.php',
            type: 'POST',
            data: {
                id_fattura: fatture,
                id_agente: id,
                metodo_pagamento: metodo_pagamento,
                note: note,
                data_liquidazione: data_liquidazione,
                tipo: 'liquida'
            },
            success: function(response) {
                var id_fattura = response;
                //Chiudo il model
                $('#modal-liquidazione').modal('hide');
                //Success Message
                Swal.fire({
                    title: "Well done!",
                    text: 'Liquidazione registrata!',
                    icon: 'success',
                    showCancelButton: true,
                    confirmButtonText: 'Vedi PDF',
                    cancelButtonText: 'Esci',
                    confirmButtonColor: '#57a94f'
                }).then((result) => {
                    if (result.isConfirmed) {
                        //apro  una nuova scheda con il pdf
                        window.open('pdf.php?id_liquidazione=' + id_fattura, '_blank');
                    }
                });
            },
            error: function(error) {
                console.error('Errore durante la chiamata AJAX:', error);
            }
        });
    });

	/**
	 * *Liquidazione zona
	 */
	$(document).on('click', '.liquidazione_zona', function(event) {
		event.preventDefault();
		//Genero i dati tramite ajax
		$.ajax({
			type: "post",
			url: "include/liquidazione.php",
			data: {
				tipo: 'lista_roma'
			},
			dataType: "html",
			success: function(response) {
				//Inserisco i valori json nel div #tab-zone e #tab-content-zone
				$('#dati_fatture_modal').html(response);
			}
		});
		$('#modal-liquidazione-zona').modal('show');
	});
	
	$('#data_liquidazione_zona').bootstrapdatepicker({
        format: "dd/mm/yyyy",
        viewMode: "date",
        multidate: false,
        multidateSeparator: "/",
    })

    var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth() + 1; //Gennaio è 0!
    var yyyy = today.getFullYear();
    if (dd < 10) {
        dd = '0' + dd
    }
    if (mm < 10) {
        mm = '0' + mm
    }
    today = dd + '/' + mm + '/' + yyyy;
    $('#data_liquidazione_zona').val(today);


	$(document).on('click', '.li-scelta-zona', function(e) {
        e.preventDefault();
        var button = $(this);
        if (button.hasClass('text-success')) {
            // Cambia lo stato del bottone e chiama l'API con AJAX
            toggleButtonState(button, 'text-danger', 'text-success', 'fe-square', 'fe-check-square');
            button.removeClass('inclusa_zona');
        } else {
            // Cambia lo stato del bottone e chiama l'API con AJAX
            toggleButtonState(button, 'text-success', 'text-danger', 'fe-check-square', 'fe-square');
            button.addClass('inclusa_zona');
        }
        // Aggiorna la somma degli importi
        var sommaImporti = calcolaSommaImporti();
        $('#importo_liquidazione').text(sommaImporti.toFixed(2));
        //Se importo liquidazSe importo liquidazione è zero disattivo
        if (sommaImporti == 0) {
            $('.liquida_provv').prop('disabled', true);
        } else {
            $('.liquida_provv').prop('disabled', false);
        }
    });

    function toggleButtonState(button, addClass, removeClass, addIconClass, removeIconClass) {
        button.removeClass(removeClass).addClass(addClass);
        button.removeClass(removeIconClass).addClass(addIconClass);
    }

    function calcolaSommaImporti() {
        var sommaImporti = 0;

        $('.inclusa_zona').each(function() {
            var importo = parseFloat($(this).data('importo')) || 0;
            sommaImporti += importo;
        });

        return sommaImporti;
    }

//Quando schiaccio sul bottone liquida_provv_roma Invio i dati tramite ajax
$(document).on('click', '.liquida_provv_roma', function(e) {
	e.preventDefault();
	//Disabilito il bottone
	$('.liquida_provv_roma').prop('disabled', true);
	var metodo_pagamento = $('#metodo_pagamento').val();
	var note = $('#note').val();
	var data_liquidazione = $('#data_liquidazione_zona').val();
	var fatture = [];
	$('.inclusa_zona').each(function() {
		var id_fattura = $(this).data('id');
		fatture.push(id_fattura);
	});
	console.log(fatture);
	$.ajax({
		url: 'include/liquidazione.php',
		type: 'POST',
		data: {
			id_fattura: fatture,
			metodo_pagamento: metodo_pagamento,
			note: note,
			data_liquidazione: data_liquidazione,
			tipo: 'liquida_zona'
		},
		success: function(response) {
			var id_fattura = response;
			//Chiudo il model
			$('#modal-liquidazione-zona').modal('hide');
			//Success Message
			Swal.fire({
				title: "Well done!",
				text: 'Liquidazione registrata!',
				icon: 'success',
				showCancelButton: true,
				confirmButtonText: 'Vedi PDF',
				cancelButtonText: 'Esci',
				confirmButtonColor: '#57a94f'
			}).then((result) => {
				if (result.isConfirmed) {
					//apro  una nuova scheda con il pdf
					window.open('pdf.php?id_liquidazione=' + id_fattura, '_blank');
				}
			});
		},
		error: function(error) {
			console.error('Errore durante la chiamata AJAX:', error);
		}
	});
});

//Quando schiaccio sul bottone Un classe vedi scaduti Apro il model generico
$(document).on('click', '.vedi_scaduti', function(event) {
	event.preventDefault();
	//Genero i dati tramite ajax
	$.ajax({
		type: "post",
		url: "include/imponibile.php",
		data: {
			tipo: 'lista_scaduti',
			anno: anno,
		},
		dataType: "html",
		success: function(response) {
			//Inserisco i valori json nel div #tab-zone e #tab-content-zone
			//Cambio il titolo del model
			$('#modal-generico .modal-title').html('Fatture scadute');
			$('#modal-generico .modal-body').html(response);
		}
	});
	$('#modal-generico').modal('show');
});

//Quando schiaccio sul bottone Un classe vedi_provincia Apro il model generico
$(document).on('click', '.vedi-provincia', function(event) {
	event.preventDefault();
	//Leggo il valore data PV
	var pv = $(this).attr("data-pv");
	//Genero i dati tramite ajax
	$.ajax({
		type: "post",
		url: "include/imponibile.php",
		data: {
			tipo: 'vedi_provincia',
			pv: pv,
			anno: anno,
		},
		dataType: "html",
		success: function(response) {
			//Inserisco i valori json nel div #tab-zone e #tab-content-zone
			//Cambio il titolo del model
			$('#modal-generico .modal-title').html('Clienti provincia ' + pv);
			$('#modal-generico .modal-body').html(response);
		}
	});
	$('#modal-generico').modal('show');
});

//Quando schiaccio sul bottone Un classe vedi_scaduti_cliente Apro il model generico
$(document).on('click', '.vedi_scaduti_cliente', function(event) {
	event.preventDefault();
	//Leggo il valore data PV
	var id_cliente = $(this).attr("data-id");
	//Genero i dati tramite ajax
	$.ajax({
		type: "post",
		url: "include/imponibile.php",
		data: {
			tipo: 'lista_scaduti_cliente',
			cliente: id_cliente
		},
		dataType: "html",
		success: function(response) {
			//Inserisco i valori json nel div #tab-zone e #tab-content-zone
			//Cambio il titolo del model
			$('#modal-generico .modal-title').html('Fatture scadute cliente');
			$('#modal-generico .modal-body').html(response);
		}
	});
	$('#modal-generico').modal('show');
});
});
