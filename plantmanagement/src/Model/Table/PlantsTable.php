<?php

namespace App\Model\Table;

use Cake\Event\EventInterface;
use Cake\ORM\Table;
use Cake\Utility\Text;

class PlantsTable extends Table
{
    public function initialize(array $config): void
    {
        $this->addBehavior('Timestamp');
    }

    public function beforeSave(EventInterface $event, $entity, $options)
    {
        if($entity->isNew() && !$entity->slug){
            $sluggedName = Text::slug($entity->name);
            $entity->slug = substr($sluggedName,0,191);
    
            $existingCount = $this->find()->where(['slug' => $entity->slug])->count();
            if ($existingCount > 0) {
                $entity->slug .= '-' . $entity->id;
            }
        }
    }
}