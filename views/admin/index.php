<div class="panel panel-default">
    <div class="panel-body">
        <h1 class="text-center">Администрирование</h1>
        <br />
        <?php if( $app->user->id == 0 ) : ?>
        <div class="panel panel-success col-xs-5 margin-auto">
            <div class="panel-heading text-center"><b>Авторизация</b></div>
            <div class="panel-body">
                <form method="post" action="">
                    <?= Services::getErrorPanel( $error ) ?>
                    <div class="form-group"><label for="inputLogin">Логин:</label>
                        <input type="text" required name="login" value="<?= $login ?>" class="form-control"/>
                    </div>
                    <div class="form-group"><label for="inputLogin">Пароль:</label>
                        <input type="password" required name="password" value="" class="form-control"/>
                    </div>
                    <div class="form-group text-center">
                        <input type="submit" name="submit" value="Войти" class="btn btn-success"/>
                    </div>
                </form>
            </div>
        </div>
        <?php else : ?>
            <a href="<?= Services::getUrl( ["admin", "index", "logOut"] ) ?>" class="float-right btn btn-danger">Выйти</a>
            <h2>Список отзывов</h2>
            <?php if( sizeof( $listMessages ) >0 ) : ?>
                <?php for( $i=0;$i<sizeof( $listMessages );$i++ ) : ?>
                    <div class="panel <?= $listMessages[$i]->status == 1 ? "panel-success" : "panel-danger" ?> p<?= $listMessages[$i]->id ?>">
                        <div class="panel-heading"><?= $listMessages[$i]->name." (".$listMessages[$i]->email.")".( $listMessages[$i]->status == 1 ? " - опубликован" : " - не опубликован" ) ?>
                            <div class="float-right"><a href="" data-id="<?= $listMessages[$i]->id ?>" class="btn btn-sm btn-danger btn-small">Редактировать</a></div>
                        </div>
                        <div class="panel-body">
                            <?php if( $listMessages[$i]->image ) : ?><div class='imagePrev'><img src='<?= $listMessages[$i]->image ?>' /></div><?php endif; ?>
                            <div id="PBText"><?= $listMessages[$i]->message ?></div>
                        </div>
                        <div class="panel-footer"><?php if( $listMessages[$i]->admin_edit == 1 ) : ?>Изменен администратором<?php endif; ?></div>

                    </div>
                <?php endfor; ?>
            <?php else: ?>
                <div class="panel panel-default">
                    <div class="panel-heading">Отзывов пока нет</div>
                </div>
            <?php endif; ?>
            <div id="myModalBox" class="modal fade">
                <div class="modal-dialog">
                    <form method="post" action="" enctype="multipart/form-data">
                        <div class="modal-content">
                            <!-- Заголовок модального окна -->
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                <h4 class="modal-title">Редактировать отзыв</h4>
                            </div>
                            <!-- Основное содержимое модального окна -->
                            <div class="modal-body">
                                ...
                            </div>
                            <!-- Футер модального окна -->
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                                <button type="submit" class="btn btn-primary">Сохранить изменения</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        <?php endif; ?>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        function btnSmallClick(){
            $(".btn-small").click( function(){
                var xhr = new XMLHttpRequest();
                xhr.open( "get", '<?= Services::getUrl(["admin", "edit"] )?>/id/' + $( this ).attr("data-id") +'/', false );
                xhr.send();

                if( xhr.status == 200 ){
                    $("#myModalBox .modal-body").html( xhr.responseText );
                } else {
                    $("#myModalBox .modal-body").html( xhr.statusText );
                }

                $("#myModalBox").modal('show');

                return false;
            });
        }
        btnSmallClick();

        $("#myModalBox").submit( function(){
            if( $("#myModalBox form #field_name").val() && $("#myModalBox form #field_email").val() && $("#myModalBox form #field_message").text()) {

                var xhr = new XMLHttpRequest();
                xhr.open( "post", '<?= Services::getUrl(["admin", "update"] )?>/', false );
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
                var data = "name="+ $("#myModalBox form #field_name").val()+"&email="+ $("#myModalBox form #field_email").val()+"&message="+ $("#myModalBox form #field_message").val()+"&status="+ ( $("#myModalBox form #field_status").prop("checked") == true ? "1" : "0" )+"&id="+ $("#myModalBox form #field_id").val();
                xhr.send( data );

                if( xhr.status == 200 ){
                    $("#myModalBox .modal-body").html( xhr.responseText );
                    var id = $("#myModalBox form #field_id").val();
                    var heading = $("#myModalBox form #field_name").val() + ' ('+$("#myModalBox form #field_email").val()+')';
                    heading += ( $("#myModalBox form #field_status").prop("checked") ? " - опубликован" : " - не опубликован" );
                    heading += '<div class="float-right"><a href="" data-id="'+id+'" class="btn btn-sm btn-danger btn-small">Редактировать</a></div>';
                    $(".p"+id).find(".panel-heading").html( heading );

                    $(".p"+id).find(".panel-body .PBText").text( $("#myModalBox form #field_message").val() );
                    if( $("#myModalBox form #field_status").prop("checked") ) $(".p"+id).removeClass("panel-danger").addClass("panel-success");
                        else $(".p"+id).addClass("panel-danger").removeClass("panel-success");

                    if( $("#myModalBox form #field_edit").val() == 1 )$(".p"+id).find(".panel-footer").text( 'Изменен администратором' );

                    btnSmallClick();
                } else {
                    $("#myModalBox .modal-body").html( xhr.statusText );
                }
            }
                else $("#myModalBox .modal-body").html( "Произошла ошибка поробуйде пожалуйста заново" );

            return false;
        });
    });
</script>