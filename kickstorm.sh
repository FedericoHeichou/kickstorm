#!/bin/bash

# AUTHOR: FEDERICO SABBATINI
###########################################################################################################
# IMPORTANTE																																															#
# Questo file prende le variabili dai file all'interno della cartella "../kickstorm/kickstorm"						#
# Se vuoi inserire le variabili manualmente:																															#
# Uncommenta i "#" nei nomi delle variabili e commenta (mettendo davanti "#") i source e modifica.				#
#																																																					#
# Altrimenti i file vengono generati modificando "grablistamembri/config.php" opportunamente							#
# ed eseguendo nel terminale "php grablistamembri.php"																										#
###########################################################################################################


# Percorso del file contenente l'array:
# percorso="grablistamembri/log/Gruppo_<groupid>_<dataora>/Blacklist/Completo_Gruppo_<groupid>_<dataora>.log"
source "kickstorm/blacklistfile"
source "${percorso}"
# Altrimenti inserisci manualmente l'array qui sotto (commenta sia "percorso" che i "source" in tal caso)
#array_utenti=( "12345" "67890" )


# ID del gruppo:
#gruppo="123456789123456"
source "kickstorm/gruppo"


# Cookies FB:
#cookies="datr=wkdRWSNkH6v***; sb=10d***-t6AUUw5Gzd***; fr=0477qB***.AWXUzoxfwehqJzAp9a1DGq***.Ba***.n***.A***.0.0.Ba***.AWW***; c_user=1000***; xs=44%3AAivs***oXwkg%3A***39637***%3A9570%3***; pl=n; act=151***; wd=1920x980; presence=E***timeF15139***userFA21B10591***state**sb2F151***atF151***thread_3a100226120***utc3F15***_5f1B10***"
source "kickstorm/cookies"


# Parametro POST del kick
#data="fb_dtsg=AQGt***r&confirm=true&__user=1000***&__a=1&__dyn=7A***-4amaxx2u6***-78O5UlwnoCidwBx62q3O***-74ubyEky8nyES3m6r***&__req=9h&__be=1&__pc=PHASED%3ADEFAULT&__rev=35***&jazoest=2658171***&__spin_r=35***&__spin_b=trunk&__spin_t=15***"
source "kickstorm/data"

###########################################################################################################

# Se non funziona:
# kickare un utente e copiare l'XHR (preferibilmente firefox, in tal caso rimuovere --2.0)
# incollarlo tra i due echo
# sostituire tutti gli ' con "
# aggiungere in fondo --output kickstorm/log/Membro-$i.log
# modificare "uid=<idmembro>" con "uid=${i}"
# commentare variabili e source per gruppo, cookies e data

n=0
echo "----------------------------------------------------------------------------"
for i in "${array_utenti[@]}"
do
	n=$((n+1))
	echo "[$n] Kickando ${i}..."
	curl "https://www.facebook.com/ajax/groups/members/remove.php?group_id=${gruppo}&uid=${i}&is_undo=0&source=profile_browser&dpr=1" -H "Host: www.facebook.com" -H "User-Agent: Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:57.0) Gecko/20100101 Firefox/57.0" -H "Accept: */*" -H "Accept-Language: it-IT,it;q=0.8,en-US;q=0.5,en;q=0.3" --compressed -H "Referer: https://www.facebook.com/groups/${gruppo}/members/" -H "Content-Type: application/x-www-form-urlencoded" -H "Cookie: ${cookies}" -H "Connection: keep-alive" --data "${data}" --output kickstorm/log/Membro-$i.log
	echo "----------------------------------------------------------------------------"
done
