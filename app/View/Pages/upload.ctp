<?php echo $this->Form->create(null, array('type' => 'file', 'url' => '/api/uploadAvatar')); ?>
<?php echo $this->Form->input('avatar', array('type' => 'file', 'label' => 'File')); ?>
<?php echo $this->Form->submit('Upload'); ?>
<?php echo $this->Form->end(); ?>
