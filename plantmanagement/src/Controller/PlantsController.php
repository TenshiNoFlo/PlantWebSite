<?php
namespace App\Controller;

use App\Model\Table\WateringHistoryTable;
use Cake\ORM\TableRegistry;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\I18n\FrozenTime;


class PlantsController extends AppController
{

    protected $WateringHistory;

    public function initialize(): void
    {
        parent::initialize();
        $this->WateringHistory = TableRegistry::getTableLocator()->get('WateringHistory');
    }

    public function index()
    {
        $userId = null;
        if ($this->Authentication->getIdentity()) {
            $userId = $this->Authentication->getIdentity()->id;
        }
    
        $conditions = [];
        if ($userId !== null) {
            $conditions['Plants.user_id'] = $userId;
        }
    
        $query = $this->Plants->find('all', [
            'conditions' => $conditions,
        ]);

        $plants = $this->paginate($query);

        $now = FrozenTime::now();

        foreach ($plants as $plant) {
            $lastWatered = $plant->last_watered;

            if ($lastWatered !== null) {
                preg_match('/\d+/', $plant->watering_frequency, $matches);
                $wateringFrequencyDays = intval($matches[0]);

                $nextWateringDate = $lastWatered->modify('+' . $wateringFrequencyDays . ' days');

                if ($now->gte($nextWateringDate)) {
                    $this->Flash->warning(__('Il est temps d\'arroser votre plante : {0}', $plant->name));
                }
            }
        }
        

        $this->set('plants', $this->paginate($query));
        $this->set('waterHistory', $this->paginate(WateringHistoryTable::class));
    }

    public function view($slug = null)
    {
        try {
            $plant = $this->Plants->findBySlug($slug)->firstOrFail();
        } catch (RecordNotFoundException $e) {
            $this->Flash->error(__('Plant not found.'));
            return $this->redirect(['action' => 'index']);
        }
        
        $wateringHistoryTable = new WateringHistoryTable();
        
        $wateringHistory = $wateringHistoryTable->find()->where(['plant_id' => $plant->id])->all();
        
        if (!empty($wateringHistory)) {
            $this->set('wateringHistory', $wateringHistory);
        }
        
        $this->set('plant', $plant);
    }
    
    public function add()
    {
        $plant = $this->Plants->newEmptyEntity();
        
        if ($this->request->is('post')) {
            $plant = $this->Plants->patchEntity($plant, $this->request->getData());
        
            $plant->user_id = $this->request->getAttribute('identity')->getIdentifier();
        
            $uploadedFile = $this->request->getData('photo-add');
    
            if (!empty($uploadedFile) && $uploadedFile->getError() === UPLOAD_ERR_OK) {
                // Generate a unique filename
                $fileName = time() . '_' . $uploadedFile->getClientFilename();
    
                // Define upload path
                $uploadPath = WWW_ROOT . 'img' . DS . $fileName;

                $uploadedFile->moveTo($uploadPath);

                $plant->photo = $fileName;
            } else {
                $plant->photo = 'default_photo.jpg';
            }
        
            if ($this->Plants->save($plant)) {
                $this->Flash->success("Your plant has been saved.");
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error("Unable to add your plant.");
        }
        
        $this->set(compact('plant'));
    }
    
    
public function edit($slug)
{
    $plant = $this->Plants->findBySlug($slug)->firstOrFail();

    if ($this->request->is(['post', 'put'])) {
        $requestData = $this->request->getData();
        
        $uploadedFile = $this->request->getData('photo');

        if ($uploadedFile !== null && $uploadedFile->getError() === UPLOAD_ERR_OK) {
            $fileName = time() . '_' . $uploadedFile->getClientFilename();
            $targetPath = WWW_ROOT . 'img/' . $fileName;
            
            if ($fileName != NULL){
            $uploadedFile->moveTo($targetPath);
            $requestData['photo'] = $fileName;
            }else{
                $requestData['photo'] = 'default_photo.png';
            }

        } else {
            $requestData['photo'] = $plant->photo;
        }

        $plant = $this->Plants->patchEntity($plant, $requestData, [
            'accessibleFields' => ['user_id' => false]
        ]);

        if ($this->Plants->save($plant)) {
            $this->Flash->success('Your plant has been updated!');
            return $this->redirect(['action' => 'index']);
        } else {
            $this->Flash->error('Unable to update your plant :/');
        }
    }

    $this->set('plant', $plant);
}


    public function delete($slug)
    {
        $this->request->allowMethod(['post', 'delete']);
    
        if (!$this->Authentication->getIdentity()) {
            $this->Flash->error(__('You must be logged in to delete a plant.'));
            return $this->redirect(['action' => 'index']);
        }
    
        $plant = $this->Plants->findBySlug($slug)->firstOrFail();
    
        if ($plant->user_id !== $this->Authentication->getIdentity()->id) {
            $this->Flash->error(__('You are not authorized to delete this plant.'));
            return $this->redirect(['action' => 'index']);
        }
    
        if ($this->Plants->delete($plant)) {
            $this->Flash->success(__('The {0} plant has been deleted.', $plant->name));
            return $this->redirect(['action' => 'index']);
        } else {
            $this->Flash->error(__('The {0} plant could not be deleted. Please, try again.', $plant->name));
            return $this->redirect(['action' => 'index']);
        }
    }
    
    
    

    public function addWateringHistory($plantId)
    {
        $wateringHistoryTable = TableRegistry::getTableLocator()->get('WateringHistory'); 
    
        $wateringHistory = $wateringHistoryTable->newEmptyEntity();
    
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $data['plant_id'] = $plantId;
            $wateringHistory = $wateringHistoryTable->patchEntity($wateringHistory, $data);
            if ($wateringHistoryTable->save($wateringHistory)) {
                $this->Flash->success(__('Watering history has been saved.'));
                return $this->redirect($this->referer()); 
            }
            $this->Flash->error(__('Unable to add watering history.'));
        }   
        $this->set(compact('wateringHistory'));
        $this->set('_serialize', ['wateringHistory']);
    }
    
    public function plantsJson()
    {
        $userId = $this->Authentication->getIdentity() ? $this->Authentication->getIdentity()->id : null;
    
        $userPlants = $this->Plants->find('all', [
            'conditions' => ['Plants.user_id' => $userId],
        ])->toArray();
    
        $upcomingWateringPlants = [];
    
        $wateringHistoryTable = TableRegistry::getTableLocator()->get('WateringHistory');
    
        foreach ($userPlants as $plant) {
            $wateringFrequency = intval(preg_replace('/[^0-9]+/', '', $plant->watering_frequency));
    
            $lastWatered = $wateringHistoryTable->find()
                ->select(['watering_date'])
                ->where(['plant_id' => $plant->id])
                ->order(['watering_date' => 'DESC'])
                ->first();
    
            if ($lastWatered) {
                $nextWateringDate = FrozenTime::parse($lastWatered->watering_date)->addDays($wateringFrequency);
                
                if ($nextWateringDate->isPast()) {
                    $upcomingWateringPlants[] = $plant;
                }
            }
        }
    
        $responseData = [
            'user_plants' => $userPlants,
            'upcoming_watering_plants' => $upcomingWateringPlants,
        ];
    
        $this->response = $this->response
            ->withType('application/json')
            ->withStringBody(json_encode($responseData));
    
        return $this->response;
    }
    
    
}