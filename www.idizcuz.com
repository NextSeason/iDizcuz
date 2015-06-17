    server {
        listen       8004;
        server_name  www.idizcuz.com;


        #charset koi8-r;

        #access_log  logs/host.access.log  main;

        location / {
            autoindex   on;
            root   /Users/lvchengbin/Projects/iDizcuz/public;
            index  index.php index.html index.htm;


            if (!-e $request_filename) {
                rewrite ^/topic(\?.*)?$ /index.php?r=topic/page/topic$1 last;

                rewrite ^/signin(\?.*)?$ /index.php?r=account/page/signin$1 last;
                rewrite ^/signup(\?.*)?$ /index.php?r=account/page/signup$1 last;
                rewrite ^/forget(\?.*)?$ /index.php?r=account/page/forget$1 last;

            }
        }
        
        #error_page  404              /404.html;

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

        # pass the PHP scripts to FastCGI server listening on 127.0.0.1:9000
        #
        location ~ \.php$ {
            fastcgi_pass   127.0.0.1:9000;
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

