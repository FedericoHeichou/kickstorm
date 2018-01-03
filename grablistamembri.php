<?php
	
	include_once('grablistamembri/config.php');
    
	$lista_membri = array ();
	$lista_white = array (); //LISTA ID DA NON KICKARE
	$lista_temp = 'array_utenti=( ';
	//$lista_secret = array ();
	$lista = 'array_utenti=( ';
	$pos=0;
	$divisore="----------------------------------------------------------------------------\n";
	$willkick=0;
	$maybekick=0;
	$hiddenkick=0;
	$hiddenfila=0;
	$ignorati=0;
	
//////////////////////////////////////////////////////////////////
//////////////////////INIZIO ELENCO FUNZIONI//////////////////////
//////////////////////////////////////////////////////////////////
	function cartella($dir){
		if ( !file_exists($dir) )
    	mkdir($dir);
	}
	
	function build_url($token, $fields, $limit, $group_id, $api, $list){
		return("https://graph.facebook.com/$api/$group_id/$list/?fields=".implode('%2C',$fields)."&limit=$limit&access_token=$token");
	}
	
	function http_request($URL, $max_tries, $delay = 100, $size){
		$tries = 0;
		$out = false;
		while(($tries < $max_tries) && ($out == false)){
			$tries++;
			echo "Provando ad aprire $URL (tentativo $tries di $max_tries)...\n";
			$out = @file_get_contents($URL);
			if($out == true) {
				echo "Successo (Membri prima di ora: $size)!\n\n";
				return $out;
			} else echo "Fallito (Membri prima di ora: $size)!\n\n";
			usleep($delay * 1000);
		}
		
		return $out;	
	}
	
	function parse_lista($json_response, $tipo){		
		if(!$json_response) return false;
		$out = json_decode($json_response);
				
		foreach($out->{'data'} as $utente){			
			global $lista_membri;
			if($tipo=="group")
				array_push($lista_membri, $utente->{'id'});
			elseif($tipo=="event")
				build_whitelist($utente->{'id'});
		}
		
		
		if(isset($out->{'paging'}->{'next'})) return $out->{'paging'}->{'next'};
		return false;
	}	
	
	function build_whitelist($elem){
		global $lista_white;
		$lista_white["$elem"] = 1;
	}
	
	function curl_facebook($membro){
		global $tot, $pos, $tentativo, $http_tries, $fb_cookies;
		$code=0;
		$ch_errno=0;
		$ch_error="";
		
		while(($code>399 || $code==0) && $tentativo<$http_tries){
			if($code>399 || $ch_errno>0){
				$tentativo++;
				echo "\nGET https://www.facebook.com/app_scoped_user_id/$membro fallito: " . $code . " \n";
				echo $ch_error . "\n";
				echo "Riprovo. Tentativo $tentativo di $http_tries.\n";
				sleep(3);
			}			
			
			$ch = curl_init();
			curl_setopt_array($ch, array(
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_HEADER => 1,
				CURLOPT_URL => 'https://www.facebook.com/app_scoped_user_id/' . $membro,
			));
			
			curl_setopt($ch, CURLOPT_HTTPHEADER, array("User-Agent: Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:57.0) Gecko/20100101 Firefox/57.0", "Referer: https://www.facebook.com/", "origin: https://www.facebook.com/", "Cookie: $fb_cookies"));
		
			$exec = curl_exec($ch);
			$code=curl_getinfo($ch, CURLINFO_HTTP_CODE);
			$ch_errno = curl_errno($ch);
			$ch_error = curl_error($ch);
			curl_close($ch);
			
			$drop = substr(strtok($exec, "\n"), 0, -1);
			$location=strtok("\n");
			$fburi = urlencode(substr($location, 10, -1));
			
		}
		
		return array("exec"=>$exec, "code"=>$code, "curl_errno"=>$ch_errno, "curl_error"=>$ch_error, "drop"=>$drop, "location"=>$location, "fburi"=>$fburi);
	}
	
	function curl_lookup($membro, $fburi, $location, $fb_errno){
		global $tot, $pos, $tentativo, $http_tries, $fb_cookies, $lista, $lista_temp, $nomedir, $willkick, $hiddenkick, $hiddenfila;
		$code=0;
		$curl_errno=0;
		$curl_error="";
	
		while(($code>399 || $code==0) && $tentativo<$http_tries){
			if($code>399 || $curl_errno>0){
				$tentativo++;
				echo "POST per $fburi fallito: " . $code . " \n";
				echo $curl_error . "\n";
				echo "Riprovo. Tentativo $tentativo di $http_tries.\n\n";
				sleep(3);
			}
		
			$curl = curl_init();
			curl_setopt_array($curl, array(
					CURLOPT_RETURNTRANSFER => 1,
					CURLOPT_URL => 'https://lookup-id.com/',
					CURLOPT_POST => 1,
					CURLOPT_POSTFIELDS => "fburl=$fburi&check=Lookup"
			));
			
			curl_setopt($curl, CURLOPT_HTTPHEADER, array("User-Agent: Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:57.0) Gecko/20100101 Firefox/57.0", "Accept: application/json", "Referer: https://lookup-id.com/", "origin: https://lookup-id.com/"));

			$resp = curl_exec($curl);
			$risp = substr(filter_var((substr($resp, 3674, -5473)), FILTER_SANITIZE_NUMBER_INT), 1);
			$code=curl_getinfo($curl, CURLINFO_HTTP_CODE);
			$curl_errno = curl_errno($curl);
			$curl_error = curl_error($curl);
			curl_close($curl);
			
			echo "[$pos/$tot] $membro (" . substr($location, 35, -1) . ") => " . $risp . "\n";
			
			//CONTROLLA CHE NON ABBIA DATO ALTRO E IN CASO LOGGALO
			if(is_numeric($risp)){
				//array_push($lista_secret, $risp);
				$lista = $lista . '"' . $risp . '" ';
				$lista_temp = $lista_temp . '"' . $risp . '" '; //BACKUP				
				$willkick++;
				$hiddenfila=0;
			}elseif($curl_errno==0 && $fb_errno==0){
				$hiddenkick++;
				$hiddenfila++;
				cartella("grablistamembri/log/$nomedir/Blacklist/Nascosti");
				file_put_contents('grablistamembri/log/' . $nomedir . '/Blacklist/Nascosti/Nascosto_' . date("Ymd-His") . '_' . $membro . '.html', print_r($resp, true));
				echo "[$pos/$tot] $membro saltato, " . ($http_tries - $hiddenfila) . " errori consecutivi rimanenti.\n"; 
			}
			
		}
		
		if(isset($resp))
			return array("resp"=>$resp, "risp"=>$risp, "code"=>$code, "curl_errno"=>$curl_errno, "curl_error"=>$curl_error);
		else
			return array("resp"=>"", "risp"=>"", "code"=>$code, "curl_errno"=>$curl_errno, "curl_error"=>$curl_error);
	}
//////////////////////////////////////////////////////////////////
///////////////////////FINE ELENCO FUNZIONI///////////////////////
//////////////////////////////////////////////////////////////////
	
	cartella("grablistamembri");  
  cartella("grablistamembri/log");
  $nomedir = 'Gruppo_' . $fb_group_id . '_' . date("Ymd-His");
	cartella("grablistamembri/log/$nomedir");
	cartella("grablistamembri/log/$nomedir/Blacklist");
	cartella("grablistamembri/log/$nomedir/Whitelist");
	cartella("kickstorm");
	cartella("kickstorm/log");
	
	//Creazione automatica file config per kickstorm.sh
	$ora = date("Ymd-His");
	file_put_contents('kickstorm/blacklistfile', 'percorso="grablistamembri/log/' . $nomedir . '/Blacklist/Completo_Gruppo_' . $fb_group_id . '_' . $ora . '.log"'); //genera percorso="grablistamembri/ecc"
	file_put_contents('kickstorm/gruppo', 'gruppo="' . $fb_group_id . '"'); //genera gruppo="123456789012334"
	file_put_contents('kickstorm/cookies', 'cookies="' . $fb_cookies . '"'); //genera cookies="datr:ecc"
	file_put_contents('kickstorm/data', 'data="' . $fb_data . '"'); //genera data="fb_dtsg=ecc"

//////////////////////////////////////////////////////////////////
///////////////////INIZIO CREAZIONE WHITELIST/////////////////////
//////////////////////////////////////////////////////////////////
	if(sizeof($whitelist)>0){
		foreach($whitelist as $elemento){
			build_whitelist($elemento);
		}
	}

	if($fb_event_id!=''){
		$prossimo = build_url($fb_access_token, $fb_fields, $fb_limit, $fb_event_id, $fb_api, "attending");
		
		while($prossimo){
			$prossimo = parse_lista(http_request($prossimo, $http_tries, $http_delay, sizeof($lista_white)), "event");
		}
		
		echo "$divisore";
		echo "Lista APP ID partecipanti all'evento completata. Partecipanti totali: " . sizeof($lista_white) . "\n" ;
		echo "$divisore\n";
				
		file_put_contents('grablistamembri/log/' . $nomedir . '/Whitelist/APP_ID_Evento_' . $fb_event_id . '_' . date("Ymd-His") . '.log', print_r($lista_white, true));
		
	}
//////////////////////////////////////////////////////////////////
//////////////////////FINE CREAZIONE WHITELIST/////////////////////
//////////////////////////////////////////////////////////////////



//////////////////////////////////////////////////////////////////
/////////////////////INIZIO RICHIESTA API_ID//////////////////////
//////////////////////////////////////////////////////////////////
  if(sizeof($blacklist)<=0){
		if($next=='') $next = build_url($fb_access_token, $fb_fields, $fb_limit, $fb_group_id, $fb_api, "members");
		
		while($next){
			$next = parse_lista(http_request($next, $http_tries, $http_delay, sizeof($lista_membri)), "group");
		}
		
		echo "$divisore";
		echo "Lista APP ID membri del gruppo completata. Membri totali: " . sizeof($lista_membri) . "\n" ;
		echo "$divisore\n";
	}else{
		$lista_membri=$blacklist;
		echo "$divisore";
		echo "Calcolo blacklist completata. Membri in blacklist: " . sizeof($lista_membri) . "\n" ;
		echo "$divisore\n";
	}
//////////////////////////////////////////////////////////////////
///////////////////////FINE RICHIESTA API_ID//////////////////////
//////////////////////////////////////////////////////////////////



//////////////////////////////////////////////////////////////////
//////////////////////INIZIO RICHIESTE CURL///////////////////////
//////////////////////////////////////////////////////////////////
	
	foreach($lista_membri as $member){
		$tot = sizeof($lista_membri);
		$pos++;		
		$tentativo=0;
		
		if(!isset($lista_white["$member"])){
			//RICHIEDIAMO IL LINK DEL PROFILO DELL'UTENTE
			$ch_fb = curl_facebook($member);
			
			if($tentativo<$http_tries) $tentativo=0;
			
			//RICHIEDIAMO L'ID DEL LINK DEL PROFILO
			$ch_lookup = curl_lookup($member, $ch_fb['fburi'], $ch_fb['location'], $ch_fb['curl_errno']);
			
			//SE $tentativo>=$http_tries ALLORA FAI UN LOG DELL'ERRORE
			if($tentativo>=$http_tries){
				$errore = array (
					"app_id" => $member,
					"pos" => $pos,
					"fb_errno" => $ch_fb['curl_errno'],
					"fb_error" => $ch_fb['curl_error'],
					"fb_http-code" => $ch_fb['code'],
					"fb_http-header" => $ch_fb['drop'],
					"location" => $ch_fb['location'],
					"lookup_errno" => $ch_lookup['curl_errno'],
					"lookup_error" => $ch_lookup['curl_error'],
					"lookup_http-code" => $ch_lookup['code'],
					"risp" => $ch_lookup['risp']
				
				);
				
				$maybekick++;
				cartella("grablistamembri/log/$nomedir/Blacklist/Errori");
				file_put_contents('grablistamembri/log/' . $nomedir . '/Blacklist/Errori/Errore_' . date("Ymd-His") . '_' . $member . '.log', print_r($errore, true));
			}
		}else{
			$ignorati++;
			echo "[$pos/$tot] $member => IGNORATO (IN WHITELIST)\n";
		}
		
		//FAI UN BACKUP OGNI 20 NOMI
		if($pos%$backup==0 && $lista_temp!=''){
			cartella("grablistamembri/log/$nomedir/Blacklist/Backup");
			file_put_contents('grablistamembri/log/' . $nomedir . '/Blacklist/Backup/Backup_Gruppo_' . $fb_group_id . '_' . date("Ymd-His") . '_PART' . intdiv($pos, $backup) . '.log', $lista_temp);
			$lista_temp='';
		}
		
		//SE HAI FALLITO TROPPO DI FILA (quindi fb ti ha disattivato temporaneamente il redirect location)
		if($hiddenfila>=$http_tries){
			break;
		}
		
	}	
//////////////////////////////////////////////////////////////////
///////////////////////FINE RICHIESTE CURL////////////////////////
//////////////////////////////////////////////////////////////////

	$lista = $lista . ')';
	$lista_temp = $lista_temp . ')'; //BACKUP
	
	if(!($pos%$backup==0)){
		cartella("grablistamembri/log/$nomedir/Blacklist/Backup");
		$parte=intdiv($pos, $backup)+1;
		file_put_contents('grablistamembri/log/' . $nomedir . '/Blacklist/Backup/Backup_Gruppo_' . $fb_group_id . '_' . date("Ymd-His") . '_PART' . $parte . '.log', $lista_temp);
		$lista_temp='';
	}
	
	
	file_put_contents('grablistamembri/log/' . $nomedir . '/Blacklist/Completo_Gruppo_' . $fb_group_id . '_' . $ora . '.log', $lista); //genera array_utenti=( "12345" "67890" )

	$hiddenkick = $hiddenkick - $hiddenfila;
	echo "\n$divisore";
	echo "Utenti da kickare: " . ($willkick + $hiddenkick + $maybekick) . "\n";
	echo "Di cui nascosti al lookup (da controllare a mano): $hiddenkick\n";
	echo "Di cui hanno generato un errore (da controllare a mano): $maybekick\n";
	echo "Ignorati in totale: $ignorati\n";
	echo "\nMembri totali del gruppo: $tot\n";
	echo "Membri totali nella whitelist: " . sizeof($lista_white) . "\n";
	echo $divisore;
?>
