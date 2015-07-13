    server {
        listen       80;
        server_name  www.idizcuz.com;


        #charset koi8-r;

        #access_log  logs/host.access.log  main;
        rewrite ^/$ /topic redirect;

        location / {
            autoindex   on;
            root   /home/iDizcuz/www/public;
            index  index.php index.html index.htm;

            if (!-e $request_filename) {

                rewrite ^/mis(\?.*)?$ /index.php?__r=mis/page/index$1 last;
                rewrite ^/topic(\?.*)?$ /index.php?__r=topic/page/topic$1 last;
                rewrite ^/signin(\?.*)?$ /index.php?__r=account/page/signin$1 last;
                rewrite ^/signup(\?.*)?$ /index.php?__r=account/page/signup$1 last;
                rewrite ^/signout(\?.*)?$ /index.php?__r=account/page/signout$1 last;
                rewrite ^/forget(\?.*)?$ /index.php?__r=account/page/forget$1 last;

                rewrite ^/user/posts(\?.*)?$ /index.php?__r=user/page/home$1&page=posts last;
                rewrite ^/user/agree(\?.*)?$ /index.php?__r=user/page/home$1&page=agree last;
                rewrite ^/user/disagree(\?.*)?$ /index.php?__r=user/page/home$1&page=disagree last;
                rewrite ^/user/mark(\?.*)?$ /index.php?__r=user/page/home$1&page=mark last;
                rewrite ^/user/removed(\?.*)?$ /index.php?__r=user/page/home$1&page=removed last;
                rewrite ^/user(\?.*)?$ /index.php?__r=user/page/home$1 last;

                rewrite ^/settings(\?.*)?$ /index.php?__r=settings/page/info$1 last;
                rewrite ^/message(\?.*)?$ /index.php?__r=message/page/message$1 last;

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
            root /home/iDizcuz/www;
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
		include /etc/nginx/fastcgi_params;
            fastcgi_pass   unix:/var/run/php5-fpm.sock;
            fastcgi_index  index.php;
            fastcgi_param  SCRIPT_FILENAME  /home/iDizcuz/www/public$fastcgi_script_name;
        }

        # deny access to .htaccess files, if Apache's document root
        # concurs with nginx's one
        #
        #location ~ /\.ht {
        #    deny  all;
        #}
    }

