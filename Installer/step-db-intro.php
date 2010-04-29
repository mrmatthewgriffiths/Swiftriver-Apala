<?php include_once("header.php"); ?>
<?php
    
    $message = 
             "So you know that applications normally need a database, right? . . . ".
             "Well I'm no different, well acutually I am different: you see I ".
             "only need one database (nothing odd there) . . . But I'm going to have ".
             "to ask you to enter the details for it twice. Sorry about that. . . ".
             "The reason is that at the moment I'm a bit like two peas in a pod ".
             "rather then just one pea. . . So I need to take the database details ".
             "from you, then pass you on to my good friend, the Ushahidi installer ".
             "who will need to ask you for them again - you can give him the same ".
             "details though so its not all bad . . . So are you ready . . ."

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
                    <form action="step-db-setup.php" method="GET">
                        <input type="submit" value="lets go ..." class="button" />
                    </form>
                </div>
            </div>
        </div>
        <div class="bottom">&nbsp;</div>
    </div>

</div>
<?php include_once("footer.php"); ?>