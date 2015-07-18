    server {
        listen       8004;
        server_name  www.idizcuz.com;


        #charset koi8-r;

        #access_log  logs/host.access.log  main;

        location / {
            autoindex   on;
            root   /Users/lvchengbin/Projects/iDizcuz/public;
            index  index.php index.html index.htm;

            rewrite ^/(\?.*)?$ /index.php?__r=home/page/index$1 last;

            if (!-e $request_filename) {
                rewrite ^/mis(\?.*)?$ /index.php?__r=mis/page/index$1 last;
                rewrite ^/signin(\?.*)?$ /index.php?__r=account/page/signin$1 last;
                rewrite ^/signup(\?.*)?$ /index.php?__r=account/page/signup$1 last;
                rewrite ^/signout(\?.*)?$ /index.php?__r=account/page/signout$1 last;
                rewrite ^/forget(\?.*)?$ /index.php?__r=account/page/forget$1 last;

                rewrite ^/settings(\?.*)?$ /index.php?__r=settings/page/info$1 last;
                rewrite ^/messages(\?.*)?$ /index.php?__r=message/page/messages$1 last;

                rewrite ^/(.*)  /index.php?$1 last;
            }
        }
        
        error_page  404              /404.html;

        # redirect server error pages to the static page /50x.html
        #
        error_page   500 502 503 504  /50x.html;
        location = /50x.html {
            root   html;
        }

        # proxy the PHP scripts to Apache listening on 127.0.0.1:80
        #
        #location ~ \.php$ {
        #    proxy_pass   http://127.0.0.1;
        #}

        location ~* ^/static {
            root /Users/lvchengbin/Projects/iDizcuz;
            expires 10d;
        }

        location ~* ^/bdunion.txt {
            root /home/iDizcuz/www;
        }

        location ~ \.html {
            root /home/iDizcuz/www;
        }

        # pass the PHP scripts to FastCGI server listening on 127.0.0.1:9000
        #
        location ~ \.php$ {
            fastcgi_pass   127.0.0.1:9000;
            #fastcgi_pass   unix:/var/run/php5-fpm.sock;
            fastcgi_index  index.php;
            fastcgi_param  SCRIPT_FILENAME  /Users/lvchengbin/Projects/iDizcuz/public$fastcgi_script_name;
            include        fastcgi_params;
        }

        # deny access to .htaccess files, if Apache's document root
        # concurs with nginx's one
        #
        #location ~ /\.ht {
        #    deny  all;
        #}
    }

