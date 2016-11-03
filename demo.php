<?php
$demo_host = $config['DEMO_HOST'];
?>
<!DOCTYPE html>
<html>
<head>
  <meta content="text/html; charset=UTF-8" http-equiv="Content-Type">
  <title>UI Log Collector (alpha) Demo</title>
</head>
<body>
<h1>UI Log Collector (alpha) Demo</h1>
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
})(window, document, 'script', 'dataLayer', 'CC-CUSTOMER1');

function genRandom(min, max, multiple) {
    return Math.floor(Math.random()*(max-min+1)+min) * multiple;
}

function genProcTime() {
    return genRandom(1, 30, 1000);
}

function genDataCnt() {
    return genRandom(1, 1000, 1);
}

function runFunction(action) {
 // alert("here");
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
        "msg": window.location.href,
        "procTime": genProcTime(),
        "dataCnt": genDataCnt()
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

</body>
<html>
