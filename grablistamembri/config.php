<?php
	// Prendi il tuo access token da qui: https://developers.facebook.com/tools/explorer/
	// Metti come permesso "user_managed_groups"
	$fb_access_token = 'EAACE***';
	
	// L'id del gruppo puoi ottenerlo dall'url del gruppo, se ha un url modificato
	// lo trovi dall'url della sezione "Gestisci gruppo" o tramite le API con il tool sopra linkato
	$fb_group_id = '123456789123456';
	
	// Numero di utenti per richiesta, non troppo altro altrimenti fb rifiuta la connessione
	// Default $fb_limit = 300;
	$fb_limit = 300;
	
	// Numero di tentativi prima di rinunciare
	// Default $http_tries = 10;	
	$http_tries = 10;	
	
	// Ritardo tra una richiesta e l'altra (millisecondi)			
	// Default $http_delay = 100;
	$http_delay = 100;
	
	// Parametri della richiesta, meglio non toccare
	// Default $fb_fields = ['id'];
	$fb_fields = ['id'];
	
	// Versione API che si vuole utilizzare
	// Default $fb_api = 'v2.11';
	$fb_api = 'v2.11';	

	// Incollare il next link da cui si vuole partire (usare solo se il processo è stato interrotto)
	// Default $next = '';
	$next = '';

	// Ogni quanti membri fare un backup
	// Default $backup=20;
	$backup = 20;
	
	// Elenco degli APPID utenti da non bannare (usa questo) https://developers.facebook.com/tools/explorer/
	// Example $whitelist = array("12345", "67890");
	// Default $whitelist = array();
	$whitelist = array("104567891234567");
	
	// Elenco degli APPID utenti da bannare (usa questo) https://developers.facebook.com/tools/explorer/ (INSERIRE SE SI VOGLIONO KICKARE SOLO QUESTI UTENTI)
	// Default $blacklist = array();
	$blacklist = array();
	
	// ID dell'evento da cui prendere la lista degli ID da aggiungere automaticamente alla whitelist insieme a quelli manuali
	// Lasciare come di default se si vuole applicare SOLO la $whitelist sopra (vuota o riempita che sia)
	// Ricorda di mettere come permesso dell'access token anche "user_events"
	// Default $fb_event_id = '';
	$fb_event_id = '987654321987654';
	
	// Vai sulla lista membri del gruppo e apri "ispeziona elemento" (o "analizza elemento")
	// Vai nella sezione "network" (o "rete")
	// Kicka un utente a caso
	// Clicca sul secondo "POST" inviato come XHR chiamato "remove.php?ecc"
	// Copia i parametri POST
	// Example $fb_data = 'fb_dtsg=AQGt***r&confirm=true&__user=1000***&__a=1&__dyn=7A***-4amaxx2u6***-78O5UlwnoCidwBx62q3O***-74ubyEky8nyES3m6r***&__req=9h&__be=1&__pc=PHASED%3ADEFAULT&__rev=35***&jazoest=2658171***&__spin_r=35***&__spin_b=trunk&__spin_t=15***';
	// Default $fb_data = '';
	$fb_data = '';
	
	// Cookies di FB puoi ottenerli da un qualsiasi GET/POST dopo "Cookie: "
	// Puoi ottenerli anche dallo stesso POST che hai usato per ottenere $fb_data (meglio utilizzare quello per sicurezza, potrebbe dare problemi)
	// Example $fb_cookies='datr=wkdRWSNkH6v***; sb=10d***-t6AUUw5Gzd***; fr=0477qB***.AWXUzoxfwehqJzAp9a1DGq***.Ba***.n***.A***.0.0.Ba***.AWW***; c_user=1000***; xs=44%3AAivs***oXwkg%3A***39637***%3A9570%3***; pl=n; act=151***; wd=1920x980; presence=E***timeF15139***userFA21B10591***state**sb2F151***atF151***thread_3a100226120***utc3F15***_5f1B10***';
	$fb_cookies = '';
		
?>
