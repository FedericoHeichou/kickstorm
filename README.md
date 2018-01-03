# Kickstorm
Massive kick members of a Facebook's group via HTTP REQUEST POST.
You can use a whitelist wrote manually or generated automatically via API using an attending list of a Facebook's Event.

This code is comment in Italian, so, if you don't know Italian, I suggest to use a translator.

## How works, problems and suggestion. READ THIS
#### How works
Using Facebook's API GRAPH, the program grabs the list of every member's APP ID. To kick a user you need to convert the APP ID into the real ID. How can you do this? You can use a online service providing the profile's URL (I used https://lookup-id.com in this program) or, if you like do it by yourself, you can see the profile's URL if is like "https://www.facebook.com/profile.php?id=123456" and the ID in this case is "123456". Sometimes you can't be so lucky, so you need to see in the HTML. For example you can see the "<a href='https://www.facebook.com/ajax/privacy/block_user.php?uid=100002345678912&is_nfx=1'>" in the "block user", but you can find the real ID in the URL obteined by clicking the profile picture, sending a friend request... ecc. In every case you need the profile URL.

#### Problems
Facebook is a bad guy. API don't create the real URL (maaaaan please, how many tricks do you like do?) of a profile, but create a redirect. For example "https://www.facebook.com/app_scoped_user_id/987654321987654" ("987654321987654" is the APP ID used by Facebook API Graph). Here the problem: if you visit more than 100-105 times in short time a "app_scoped_user_id" URL, Facebook become angry and deactivates (for about 1 hour) the redirect from "app_scoped_user_id" URL to the real profile URL and give you a page like if the user blocked you. This is a nice shit, because often you can't create a list bigger than 100 users. So you have to use about 15-20 accounts and use them in rotation, changing every times the "fb_cookies" and "fb_data" variables into config.php.

#### Suggestion and Solutions
I am lazy, but if you want you can edit the code. Else you can use the "15-20 accounts in rotation" tacttics, as I did. If you want you can integrate the kick phase in the PHP execution too, maybe you can alternate scraping and kicking fase, so maybe Facebook can't see you. When you get the error introduced by Facebook's protection of "app_scoped_user_id" url, you can create in config.php an array of "fb_cookies" and "fb_data" with 15-20 accounts' data, and when you get too many errors, instead of "break;" the for-each, you can go back to the first user that generated the error and use the next element of arrays "fb_cookies" and "fb_data". When the array ends, you can restart from "fb_cookies[0]" and "fb_data[0]". If you want do this "automatic" rotation, you will have a very big array, so I suggest to use a garbage collector. Before go to the next elements of "fb_cookies[]" and "fb_data[]", you should run the kickstorm, at the end of that, you can unset() the array of real IDs (used instead of the file) and then proceed to next "fb_cookies[]" and "fb_data[]" elements. Unsetting every single whitelist element when used isn't a bad idea too.

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
I suggest to use a moderator account for cookies and data, so he can't kick the staff.

Enter in the directory of this repository. Es:
```
cd kickstorm
```

Run grablistamembri.php to create the blacklist (member to kick).
```
php grablistamembri.php
```

Grant to kickstorm.sh permission to run if necessary.
```
sudo chmod 770 kickstorm.sh
```

Run kickstorm.sh to run the kick for every member in the array generated from grablistamembri.php
```
./kickstorm.sh
```

In the end you can delete every files in the "kickstorm/log/" and "grablistamembri/log/" folders.

Please quote me.

Federico Sabbatini
