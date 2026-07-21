<span class="h4" style="float:right;"><?php echo ucwords("Item Recipe"); ?>: #<?php echo $itemRecipeId; ?></span>

<p>&nbsp;</p>
<?php if (isset($details) and !empty($details)) { ?>
    <div class="row">
        <div class="row">
            <div class="table-responsive">
                <table class="table table-borderless table-striped text-sm">
                    <thead class="thead-light text-xs">
                        <tr>
                            <!-- <th>Item Code Name </th> -->
                            <!-- <th>Item Name</th> -->
                            <th>Op Type</th>
                            <th>Side</th>
                            <th>opValue</th>
                            <th>X Pos</th>
                            <th>Y Pos</th>
                            <th>MeasurementType</th>


                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($details as $i => $p) {
                            echo "<tr>";
                            echo "<td>" . $p->opType . "</td>";
                            echo "<td>" . $p->side . "</td>";
                            echo "<td>" . $p->opValue . "</td>";
                            echo "<td>" . $p->xPos . "</td>";
                            echo "<td>" . $p->yPos . "</td>";
                            echo "<td>" . $p->measurementType . "</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

    <?php } else {
    echo "<div class='alert alert-info text-center'>No information here!</div>";
    ?>

    <?php
}
    ?>