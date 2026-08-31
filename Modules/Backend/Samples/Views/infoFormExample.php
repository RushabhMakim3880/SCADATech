<form action="<?php echo 'api/newSample/infoFormExample/0'; ?>" method="post">
    <div class="row">
        <div class="form-group col-md-6">
            <label for="name">Date:</label>
            <input type="text" name="date" class="form-control datePicker" value="" />
        </div>

        <div class="col-md-3 form-group">
            <label for="price">Price</label>
            <input id="price" type="text" name="price" class="form-control internationalNumber" placeholder="Enter Price " required value="+919408665522" />
        </div>

        <div class="col-md-3 form-group">
            <label for="location">Location</label>
            <input id="location" type="text" name="location" class="form-control locationPicker" placeholder="Select Location" required value="" />
        </div>

        <div class="form-group col-md-6">
            <label for="name">Icon:</label>
            <input type="text" name="date" class="form-control iconPicker" value="" />
        </div>

        <div class="form-group col-md-6">
            <label for="name">Color:</label>
            <input type="text" name="date" class="form-control colorPicker" value="" />
        </div>

        <div class="form-group col-md-6">
            <label for="name">Type:</label>
            <select name="type" class="form-control select2">
                <option value="1">Type 1</option>
                <option value="2">Type 2</option>
                <option value="3">Type 3</option>
            </select>
        </div>
    </div>
</form>