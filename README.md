# Kickstorm
Massive kick members of a Facebook's group via HTTP REQUEST POST.
This code is comment in Italian, so, if you don't know Italian, I suggest to use a translator.

## How to use on Linux:
Install PHP 7.2 (In theory you can use older version too).
```
sudo add-apt-repository ppa:ondrej/php
sudo apt-get update
sudo apt-get install -y php7.2
```

Install cURL's repository for PHP.
```
sudo apt-get install libcurl3 php7.2-curl 
```

Install cURL.
```
sudo apt install curl
```

Edit the grablistamembri/config.php reading the comments.
Remember to add your Facebook APP ID to the whitelist.

Enter in the directory of this repository. Es:
```
cd kickstorm
```

Run grablistamembri.php to create the blacklist (member to kick).
```
php grablistamembri.php
```

Run kickstorm.sh to run the kick for every member in the array generated from grablistamembri.php
```
./kickstorm.sh
```

Please quote me.

Federico Sabbatini
