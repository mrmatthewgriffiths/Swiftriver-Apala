<?php include_once("header.php"); ?>
<?php
    /*
     * So this is the index page of the installer ...
     * Here we are going to do some basic checking to see if everything
     * is present and correct in terms of the environment that you
     * are running.
     */

     $checks = array();

     //first check that the php version is at least 5.3
     $check->check = "First I need to check that you have the correct version of php installed.";
     $check->result = (version_compare(PHP_VERSION, '5.3.0', '>='));
     $check->message = $check->result
                         ? "Phew! its fine."
                         : "Oops, you are running PHP Version: ".
                           PHP_VERSION." and Swiftriver requires version 5.3 or above.";
     $checks[] = $check;
     unset($check);
     
     //Check that the pear dependency package can be loded
     $check->check = "Now I'm going to check that you have PEAR installed and in your global include paths.";
     $check->result = (include_once("PEAR.php"));
     $check->message = $check->result
                       ? "Phew! thats lucky, I found it!"
                       : "Oops, you dont seem to have PEAR ".
                         "installed - or pehaps it just not in your include paths. ".
                         "I need to use some PEAR packages so you will have to fix ".
                         "if before we go on.";
                         
     $checks[] = $check;
     
     $shouldContinue = $check->result;
     unset($check);

     //If pear is installed, check the required packages
     if($shouldContinue) {

         //Check for the existance of the Log package
         $check->check = "Now that we know PEAR is installed, let me check for the PEAR Log package.";
         $check->result = (include_once("Log.php"));
         $check->message = $check->result
                            ? "Yippee! its there."
                            : "Oops, I couldn't include the file Log.php. This ".
                              "normally means that you havent got the PEAR Log package installed. ".
                              "You need to run '# pear install Log' before ".
                              "we can go any further.";
         $checks[] = $check;
         unset($check);
     }
?>
<div id="php-checks">
<script language="javascript" type="text/javascript">
    var data = {"checks":<?php echo(json_encode($checks)); ?>};
    var checks = data.checks;
    var counter = 0;
    $(document).ready(function(){
        DoMessage("check");
    });

    function DoMessage(messageType) {
        if(messageType == "check") {
            ClearMessages()
            DoWriteMessage("div#messages div.check", checks[counter].check, GetTime(checks[counter].check));
            setTimeout("DoMessage('wait')", 5000);
        } else if (messageType == "wait") {
            DoWriteMessage("div#messages div.wait", " . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . .", 5);
            setTimeout("DoMessage('message')", 5000);
        } else if (messageType == "message") {
            var time = GetTime(checks[counter].message);
            DoWriteMessage("div#messages div.message", checks[counter].message, time);
            if(checks[counter].result != 1) {
                setTimeout('DoMessage("fail")', (time * 1000) + 500);
            }else if(counter < checks.length - 1) {
                counter++;
                setTimeout('DoMessage("check")', (time * 1000) + 500);
            } else {
                setTimeout('DoMessage("sucess")', (time * 1000) + 500);
            }
        } else if (messageType == "sucess") {
            ClearMessages()
            DoWriteMessage(
                "div#messages div.message",
                "Ok, happy days, thats all the checks in this step passed.",
                5);
            setTimeout('$("div#messages div.action").show();', 5500);
        } else if (messageType == "fail") {
            setTimeout('$("div#messages div.fail").show();', 1000);
        }
    }

    function ClearMessages() {
        $("div#messages div:not(.action, .fail)").each(function() {
            $(this).html("");
        });
    }
</script>
    <img id="logo-callout" src="Assets/Images/logo-callout.png" />
    <div id="baloon">
        <div class="top">&nbsp;</div>
        <div class="mid">
            <div id="messages">
                <div class="check"></div>
                <div class="wait"></div>
                <div class="message"></div>
                <div class="action" style="display:none;">
                    <p>Lets move on to checking the kind of directory access I need to work.</p>
                    <form action="step-db-intro.php" method="GET">
                        <input type="submit" value="lets go ..." class="button" />
                    </form>
                </div>
                <div class="fail" style="display:none;">
                    <p>Sorry about that! One of the tets I carried out didn't pass.</p>
                    <p>Can you go and try to fix it and then start the installation again?</p>
                </div>
            </div>
        </div>
        <div class="bottom">&nbsp;</div>
    </div>
</div>
<?php include_once("footer.php"); ?>