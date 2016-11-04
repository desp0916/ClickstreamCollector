# PHP Clickstream Collector for Kafka

This is a simple clickstream collector written in PHP. It can send clickstream (logs) into Kafka directly.

## How it works?

First, paste the following code to the HTML page you would like to collect clickstream. See <a href="demo.php">demo.php</a> for more detail.


``` html
<script>
(function(w, d, s, l, i) {
    w[l] = w[l] || [];
    w[l].push({
        'gtm.start': new Date().getTime(),
        Event: 'crashme.js'
    });
    var f = d.getElementsByTagName(s)[0],
        j = d.createElement(s),
        dl = (l != 'dataLayer') ? '&l=' + l : '';
    j.async = true;
    j.src = '//<?php echo $demo_host; ?>/0.0.1/collector.js?id=' + i + dl;
    f.parentNode.insertBefore(j, f);
})(window, document, 'script', 'dataLayer', 'CC-WEB1');

function runFunction(action) {
    var json = {
        "sysID": "aes3g",
        "logType": "ui",
        "logTime": new Date().toISOString(),
        "apID": "lab",
        "functID": "click",
        "who": "demo-user",
        "at": "172.20.2.2",
        "action": action,
        "result": true,
        "msg": window.location.href
   };
  sendLog(json);
}
</script>

<div>
  <button type="button" onclick="runFunction('add')">Add</button>
  <button type="button" onclick="runFunction('delete')">Delete</button>
  <button type="button" onclick="runFunction('query')">Query</button>
  <button type="button" onclick="runFunction('edit')">Edit</button>
</div>

```

A hidden iframe will be created once the page is loaded by a browser. If the user clicks the buttons on the page, the 'src' attribute of the iframe will be updated. 

The value in the `src` might look like this:

```
http://collector.yourdomain.com/collect?id=CC-WEB1&json={"sysID":"aes3g","logType":"ui","logTime":"2016-11-03T05:11:21.879Z","apID":"lab","functID":"click","who":"demo-user","at":"172.20.2.2","action":"add","result":true,"msg":"http://demo.yourdomain.com/","procTime":3000,"dataCnt":447}
```

That's why the collector can capture the clickstreams. The collector will parse the query string and parameters, then send the log (urlencoded JSON string) into Kafka directly.

If you would like to see how it works, you can follow the instructions below to setup your demo site.

## Pre-Requisites

  1. CentOS 7.2
  1. Apache HTTPd 2.4.6
  2. PHP 5.4.16
  3. mod_php
  4. [php-rdkafka](https://github.com/arnaud-lb/php-rdkafka)
  4. [librdkafka](https://github.com/edenhill/librdkafka) 9.x+
  5. [Kafka](https://kafka.apache.org/) 0.9.0

## Prepare Your Environment 

### 1. Create a topic on Kafka

``` sh
bin/kafka-topics.sh --create --zookeeper zkhost1:2181 --replication-factor 2 --partition 10 --topic clickstream-v1
```

### 2. Setup two name-based virtual hosts on Apache HTTPd.

Assume your demo site's IP is ```192.168.0.1```, add the following line to ```/etc/hosts```. If you will browse the demo page from Windows, also remember add it to ```C:\Windows\System32\drivers\etc\hosts``` .


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

### 3. Install ```librdkafka```

You can refer to [librdkafka](https://github.com/edenhill/librdkafka) on GitHub.

``` sh
yum install -y re2c 
git clone https://github.com/edenhill/librdkafka.git
cd librdkafka
./configure
make
make install
```

### 4. Install ```php-rdkfka```

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

### 5. Clone this project

``` sh
git clone https://github.com/desp0916/ClickstreamCollector.git
```

### 6. Edit ``config.php``

``` php
$config['HOST'] = 'collector.yourdomain.com';
$config['DEMO_HOST'] = 'demo.yourdomain.com';
$config['DEBUG'] = true;
$config['CC_IDS'] = array("CC-WEB1", "CC-WEB2");
$config['KAFKA_BROKERS'] = 'kafka01:9020,kafka02:9200,kafka3:9200,kafka4:9200,kafka5:9200';
$config['KAFKA_TOPIC'] = 'clickstream-v1';
```

### 7. Copy files to the DocumentRoots

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

Done! Open your browser and browse to: <http://demo.yourdomain.com/demo.php>. Click the buttons randomly and the clickstream will ben sent to Kafka.


### 8. Monitor the topic in Kafka

``` sh
bin/kafka-console-consumer.sh --zookeeper zkhost1:2181 --topic clickstream-v1 
```

