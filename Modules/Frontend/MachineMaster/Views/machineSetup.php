<?php
$config = config('AppConfig');
?>

<form method="POST" action="#" autocomplete="off"
    class="autoCrudForm"
    data-resource="api/MachineMaster/getMachineSetup"
    data-record-id="1"
    data-dropdowns=''>

    <div class="row">

        <?php
        foreach ($machineDetails as $head) {

            // Assuming each head has a 'headName' field
            if ($head['headType'] == "Marking") {
                for ($i = 1; $i <= $head['markingCassets']; $i++) {

                    echo '<div class="mb-1">';
                    echo "<label class='form-label'>" . $head['headName'] . " " . $i . "</label><br>";
                    echo "<input class='form-control' name='" . $head["machineDetailId"] . "||" . $i . "' type='text' value=''/>";
                    echo '</div>';
                }
            } else if ($head['headType'] == "Cutting") {
                // echo '<div class="mb-1">';
                // echo "<label class='form-label'>" . $head['headName'] . " Radious</label><br>";
                // echo "<input class='form-control numberInput' name='" . $head["machineDetailId"] . "||0' type='text' value=''/>";
                // echo '</div>';
            } else {
                echo '<div class="mb-1">';
                echo "<label class='form-label'>" . $head['headName'] . "</label><br>";
                echo "<input class='form-control numberInput' name='" . $head["machineDetailId"] . "||0' type='text' value=''/>";
                echo '</div>';
            }
        }

        ?>
    </div>
</form>