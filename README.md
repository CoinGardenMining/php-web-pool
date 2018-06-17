# php-web-pool
One Interface to rule them all

# Installation
Upgrade for default Open ETH Pool Setup on Ubuntu 16.04 LTS

Install PHP (if not yet running) + unzip + dependencies
    
    sudo apt-get -y install php7.0-fpm unzip
    sudo apt-get install php7.0-xml php7.0-gd php7.0-mbstring php7.0-curl
    
Configure VHost for PHP Pool

    sudo vim /etc/nginx/sites-enabled/phppool

Add Contents to File

    server {
            server_name etc.cgpools.io www.etc.cgpools.io;
            root /var/www/php-web-pool/public;
            index index.php;
            client_max_body_size    20M;
    
            location / {
                try_files $uri $uri/ /index.php$is_args$args;
             }
    
             location ~ \.php$ {
                 include snippets/fastcgi-php.conf;
    
                 fastcgi_pass unix:/run/php/php7.0-fpm.sock;
                 fastcgi_param  SCRIPT_FILENAME $document_root$fastcgi_script_name;
                 include        fastcgi_params;
            }
    
            location ~ /\.ht {
                    deny all;
            }
    
        listen 80; # managed by Certbot
    }

Download Pool & Install Dependencies

    cd /var/www
    git clone https://github.com/CoinGardenMining/php-web-pool
    cd php-web-pool
    wget https://getcomposer.org/download/1.6.5/composer.phar
    php composer.phar install
