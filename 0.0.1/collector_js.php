<?php

/**
 * Created on 2016/11/02 by Gary Liu
 * $Id: collector_js.php,v 1.13 2016/10/02 11:38:46 alpha Exp $
 */

header('Content-Type: application/javascript');

if (isset($_GET['id']) && in_array($_GET['id'], $config['CC_IDS'])) {
	$id = $_GET['id'];
	$host = $config['HOST'];
?>

(function() {
    var J = document,
        ga = function() {
            //alert('b');
            var s = J.getElementsByTagName('script')[0],
                c = J.createElement('iframe');

            c.height = 0;
            c.width = 0;
            c.id = 'iframe_<?php echo $id ?>';
            c.style.display = 'none';
            c.style.visibility = 'hidden';
            c.src = 'http://<?php echo $host ?>/collect?id=<?php echo $id ?>';
            s.parentNode.insertBefore(c, s);
        };

    if (!document.getElementById("iframe_<?php echo $id ?>")) {
        ga();
    }
})();

function sendLog(json) {
    var hiddenIframe = document.getElementById("iframe_<?php echo $id ?>");
    hiddenIframe.src = 'http://<?php echo $config['HOST'] ?>/collect?id=<?php echo $id ?>&json=' + JSON.stringify(json);
    console.log(hiddenIframe.src);
    console.log("sendLog");
}

<?php

}

?>
