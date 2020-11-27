#Animerush.tv Crawler

Pulls latest anime episodes of your choice from animerush.tv and display them with a timer for upcoming episodes. Using PHP DOM, jquery http://hilios.github.io/jQuery.countdown/. Mostly a test for myself but if someone finds this useful feel free to use it.

# Animerush-Crawler



get it working on debian 10


apt install nginx php-fpm


change php.ini to enable

        ";extension=openssl"
        
        
extend buffers on nginx

        fastcgi_buffers 16 16k;
        
        fastcgi_buffer_size 32k;
