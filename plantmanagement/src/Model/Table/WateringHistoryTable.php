<?php
namespace App\Model\Table;

use Cake\ORM\Table;

class WateringHistoryTable extends Table
{
    public function initialize(array $config): void
    {
        $this->setTable('watering_history');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Plants', [
            'foreignKey' => 'plant_id',
            'joinType' => 'INNER', 
        ]);
    }
}
