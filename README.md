# Simple Clickstream Collector

This is a simple clickstream collector using iframe. All of the logs will be sent to Kafka.

If you want to see how it works, you can follow the instructions below to setup your demo site.

## Pre-Requisites

  1. CentOS 7.2
  1. Apache HTTPd 2.4.6
  2. PHP 5.4.16
  3. mod_php
  4. php-rdkafka
  4. [librdkafka](https://github.com/edenhill/librdkafka) 9.x+
  5. Kafka 0.9.0

## Environment Preparation

### 1. Setup two name-based virtual hosts on Apache HTTPd.

Assume your demo site's IP is ```192.168.0.1```, add the following line to ```/etc/hosts```.
If you will browse the demo page from Windows, also remember add it to ```C:\Windows\System32\drivers\etc\hosts``` .

```
192.168.0.1	collector.yourdomain.com  demo.yourdomain.com
```

Create two directories:

```
mkdir -p /var/www/{collector,demo}
```

Create ```/etc/httpd/conf.d/vhosts.conf``` and config it.

```
# Collector Site
<VirtualHost *:80>
    ServerName collector.yourdomain.com
    DocumentRoot "/var/www/collector"
    ErrorLog "logs/error_log.collector"
    CustomLog "logs/access_log.cc" common
    <Directory "/var/www/collector">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>

# Demo Site
<VirtualHost *:80>
    ServerName demo.yourdomain.com
    DocumentRoot "/var/www/demo"
    ErrorLog "logs/error_log.demo"
    CustomLog "logs/access_log.demo" common
</VirtualHost>
```

### 2. Install ```librdkafka```

You can refer to [librdkafka](https://github.com/edenhill/librdkafka) on GitHub.

``` sh
yum install -y re2c 
git clone https://github.com/edenhill/librdkafka.git
cd librdkafka
./configure
make
make install
```

### 3. Install ```php-rdkfka```

You can refer to [php-rdkafka](https://github.com/arnaud-lb/php-rdkafka) on GitHub.

``` sh
pecl install channel://pecl.php.net/rdkafka-1.0.0
```

Then add the follwoing line to ```/etc/php.ini``` :

```
extension=rdkafka.so
```

Restart the Apache HTTPd:

``` sh
systemctl restart httpd
```

### 4. Clone this project

``` sh
git clone https://github.com/desp0916/ClickstreamCollector.git
```

### 5. Edit ``config.php``

``` php
$config['HOST'] = 'collector.yourdomain.com';
$config['DEMO_HOST'] = 'demo.yourdomain.com';
$config['DEBUG'] = true;
$config['LC_IDS'] = array("CC-CUSTOMER1", "CC-CUSTOMER2");
$config['KAFKA_BROKERS'] = 'kafka01:9020,kafka02:9200,kafka3:9200,kafka4:9200,kafka5:9200';
$config['KAFKA_TOPIC'] = 'clickstream-v1';
```

### 6. Copy files to the DocumentRoots

Copy all files to the DocumentRoot of the collector site:

``` sh
cp -r ClickstreamCollector/* /var/www/collector
```

Copy ```config.php``` and ```demo.php``` to the DocumentRoot of the demo site:

``` sh
cp ClickstreamCollector/demo.php /var/www/demo
cp ClickstreamCollector/config.php /var/www/demo
```

### 7. Browse the demo site

Now, you can browse to <http://demo.yourdomain.com/demo.php>. Click the buttons randomly and the clickstream will ben sent to Kafka.


### 8. Monitor the topic in Kafka

``` sh
bin/kafka-console-consumer.sh --zookeeper zkhost1:2181 --topic clickstream-v1 
```

