<!DOCTYPE html>
<html lang="en">

    <div class="container-view">
        <div class="content">
            <h1><?= h($plant->name) ?></h1>

            <p><strong>Espèce :</strong> <?= h($plant->species) ?></p>

            <p><strong>Quantité d'eau :</strong> <?= h($plant->water_quantity) ?></p>

            <p><strong>Fréquence d'arrosage :</strong> <?= h($plant->watering_frequency) ?></p>

            <p><strong>Créé le :</strong> <small><?= $plant->created ? $plant->created->format(DATE_RFC850) : 'N/A' ?></small></p>

            <p><strong>Historique d'arrosage :</strong></p>
            <ul>
                <?php foreach ($wateringHistory as $history): ?>
                    <li>
                        Arrosé le <?= $history->watering_date ?>
                    </li>
                <?php endforeach; ?>
            </ul>

            <p><?= $this->Html->link('Edit', ['action' => 'edit', $plant->slug], ['class' => 'button']) ?></p>
            <p><?= $this->Html->link('Delete', ['action' => 'delete', $plant->slug], ['class' => 'button']) ?></p>

        </div>

        <?= $this->Form->create(null, ['url' => ['controller' => 'Plants', 'action' => 'addWateringHistory', $plant->id]]) ?>
    <fieldset>
        <legend><?= __('Add Watering History') ?></legend>
        <?= $this->Form->control('watering_date', ['label' => 'Watering Date', 'type' => 'datetime']) ?>
        <?= $this->Form->button(__('Submit')) ?>
    </fieldset>
    <?= $this->Form->end() ?>

    <?php if (!empty($plant->photo)): ?>
        <div class="image-container">
            <img class="plant-photo" src="<?= $this->Url->image($plant->photo) ?>" alt="Plant Photo">
        </div>
    <?php else: ?>
        <div class="image-container">
            <img class="plant-photo" src="<?= $this->Url->image('default.jpg') ?>" alt="Default Plant Photo">
        </div>
    <?php endif; ?>

    </div>
