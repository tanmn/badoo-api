<?php
App::uses('AppModel', 'Model');
/**
 * MsInterestCategory Model
 *
 */
class MsInterestCategory extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'category_name' => array(
			'notEmpty' => array(
				'rule' => array('notEmpty'),
				'message' => 'Please enter category name',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);

    public $hasMany = array(
        'Interest' => array(
            'className' => 'Interest',
            'foreignKey' => 'category_id',
            'dependent' => false,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        )
    );

    public function afterDelete(){
        $this->Interest->updateAll(array('category_id' => NULL), array('Interest.category_id' => $this->id));
    }
}
