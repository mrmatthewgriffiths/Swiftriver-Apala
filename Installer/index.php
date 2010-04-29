<?php include_once("header.php"); ?>
<?php
    
    $message = 
             "Welcome to the Swiftriver installer for the Apala relaease. If you ".
             "have got this far then pat your self on the back. You are not only ".
             "one of the first people in the world to begin using this software ".
             "but you have also correctly deployed this installer somewhere with ".
             "Apache and PHP! . . . . . During the next few mintes we'll run through some ".
             "basic tests and setup steps in an atempt to get you up and running ".
             "with your very own Swiftriver instance.";
?>
<div id="index">
    <script language="javascript" type="text/javascript">
        $(document).ready(function(){
            var time = GetTime("<?php echo($message); ?>");
            DoWriteMessage(
                "div#baloon div.mid div.message",
                "<?php echo($message); ?>",
                time
            );
            setTimeout("$('div.action').show()", (time * 1000) + 500)
        });
    </script>
    <img id="logo-callout" src="Assets/Images/logo-callout.png" />
    <div id="baloon">
        <div class="top">&nbsp;</div>
        <div class="mid">
            <div id="messages">
                <div class="skip"><a href="#" onclick="$('div.action').show(); return false;">skip</a></div>
                <div class="message"></div>
                <div class="action" style="display:none;">
                    <p>"Are your ready? Yes? Then ..."</p>
                    <form action="step-php-checks.php" method="GET">
                        <input type="submit" value="lets go ..." class="button" />
                    </form>
                </div>
            </div>
        </div>
        <div class="bottom">&nbsp;</div>
    </div>

</div>
<?php include_once("footer.php"); ?>