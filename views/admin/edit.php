<?= Services::getSuccessPanel( $message ) ?>
<?= Services::getErrorPanel( $addModel->errors ) ?>
<?= Model_services::getAddForm( $addModel ) ?>
<?php if( $addModel->image ) : ?>
    <div class="form-group"><label for="inputemail">Фото</label><img src="<?= $addModel->image ?>" /></div>
<?php endif; ?>
<input type="hidden" name="<?= $addModel->name_table ?>[id]" id="field_id" value="<?= $addModel->id ?>" />
<input type="hidden" name="edit" id="field_edit" value="<?= $addModel->admin_edit ?>" />
