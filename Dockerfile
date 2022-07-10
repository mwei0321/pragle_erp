FROM registry-vpc.cn-hongkong.aliyuncs.com/domedeahk/erp_pragle_runtime:latest

COPY . /var/www/html/
COPY ./vhost.conf /etc/apache2/sites-enabled/

RUN chmod -R 777 ./ && composer update && crontab cron

EXPOSE 8080

CMD printenv | grep -v "no_proxy" >> /etc/environment \
    && service cron start \
    && apache2-foreground
