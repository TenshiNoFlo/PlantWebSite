<!DOCTYPE html>
<html lang="en">
<h1>My plants</h1>
<?= $this->Flash->render() ?>
<?= $this->Html->link('Add Plant', ['action' => 'add'], ['class' => 'button']) ?>
<?= $this->Html->link('View as JSON', ['action' => 'plantsJson'], ['class' => 'button']) ?>
<table>
    <tr>
        <th>Name</th>
        <th>Last Modification</th>
        <th>Action</th>
    </tr>
        <?php foreach ($plants as $plant): ?>
            <tr>
                <td>
                    <?= $this->Html->link($plant->name, ['action' => 'view', $plant->slug]) ?>
                </td>
                <td>
                    <?= $plant->modified->format(DATE_RFC850) ?>
                </td>
                <td>
                    <?= $this->Html->link('Edit', ['action' => 'edit', $plant->slug]) ?>
                    <?= $this->Form->postLink(
                    'Delete',
                    ['action' => 'delete', $plant->slug],
                    ['confirm' => 'Are you sure?'])
                ?>
                </td>
            </tr>
        <?php endforeach ?>

    </table>
