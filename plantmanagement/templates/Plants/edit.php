<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit plant</title>
</head>

    <h1>Edit plant</h1>

    <?php
        $defaultPhotoPath = 'default_photo.jpg';

        echo $this->Form->create($plant, ['type' => 'file']);
        echo $this->Form->control('name');
        echo $this->Form->control('species');
        echo $this->Form->control('water_quantity');
        echo $this->Form->control('watering_frequency');
        
        echo $this->Form->control('photo', ['type' => 'file']);

        echo $this->Form->button('Save plant', ['class' => 'button primary']);
        echo $this->Form->end();
    ?>