<div class="panel panel-default">
    <div class="panel-body">
        <nav role="navigation" class="navbar navbar-default">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <span class="navbar-brand">Сортировка</span>
            </div>
            <!-- Collection of nav links, forms, and other content for toggling -->
            <div id="navbarCollapse" class="collapse navbar-collapse">
                <ul class="nav navbar-nav">
                    <li <?= $sortField == "id" ? 'class="active"' : '' ?>><a href="<?= Services::getUrl(["main", "index", "sort", "id", "type", ( $sortType == "asc" ? "desc" : "asc" )]) ?>">По дате</a></li>
                    <li <?= $sortField == "name" ? 'class="active"' : '' ?>><a href="<?= Services::getUrl(["main", "index", "sort", "name", "type", ( $sortType == "asc" ? "desc" : "asc" )]) ?>">По автору</a></li>
                    <li <?= $sortField == "email" ? 'class="active"' : '' ?>><a href="<?= Services::getUrl(["main", "index", "sort", "email", "type", ( $sortType == "asc" ? "desc" : "asc" )]) ?>">По Email</a></li>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <li><a href="<?= Services::getUrl(["admin"]) ?>">Войти</a></li>
                </ul>
            </div>
        </nav>
        <h1>Отзывы</h1>
        <?php if( sizeof( $listMessages ) >0 ) : ?>
            <?php for( $i=0;$i<sizeof( $listMessages );$i++ ) : ?>
                <div class="panel panel-success">
                    <div class="panel-heading"><?= $listMessages[$i]->name." (".$listMessages[$i]->email.")" ?></div>
                    <div class="panel-body">
                        <?php if( $listMessages[$i]->image ) : ?><div class='imagePrev'><img src='<?= $listMessages[$i]->image ?>' /></div><?php endif; ?>
                        <?= $listMessages[$i]->message ?>
                    </div>
                    <?php if( $listMessages[$i]->admin_edit == 1 ) : ?>
                        <div class="panel-footer">Изменен администратором</div>
                    <?php endif; ?>
                </div>
            <?php endfor; ?>
        <?php else: ?>
            <div class="panel panel-default">
                <div class="panel-heading">Отзывов пока нет</div>
            </div>
        <?php endif; ?>
        <br/>
        <h3>Добавить отзыв</h3>
        <?= Services::getSuccessPanel( $message ) ?>
        <?= Services::getErrorPanel( $addModel->errors ) ?>
        <form method="post" action="" enctype="multipart/form-data">
            <?= Model_services::getAddForm( $addModel ) ?>
            <button type="button" class="btn btn-default preview">Предварительный просмотр</button>
            <button type="submit" class="btn btn-success">Отправить</button>
        </form>
        <div id="myModalBox" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content">
                    <!-- Заголовок модального окна -->
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title">Предварительный просмотр</h4>
                    </div>
                    <!-- Основное содержимое модального окна -->
                    <div class="modal-body">
                        <div class="panel panel-success">
                            <div class="panel-heading"></div>
                            <div class="panel-body"><div id="PBText"></div></div>
                        </div>
                    </div>
                    <!-- Футер модального окна -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $(".preview").click( function() {
            $("#myModalBox .panel-body").html(); // Очищаем форму

            var name = $("#field_name").val();
            var email = $("#field_email").val();
            var message = $("#field_message").val();
            var file = $("#field_image").val();

            if( $("#field_image")[0].files && $("#field_image")[0].files[0] ){
                $("#myModalBox .panel-body").prepend("<div class='imagePrev'><img class='IPresize' src='' /></div>");
                readURL( $("#field_image")[0] );
            }

            $("#myModalBox .panel-heading").text( name+ ' ( '+email+' )' );
            $("#myModalBox .panel-body #PBText").text( message );
            $("#myModalBox").modal('show');
        });
    });

    function readURL(input) {

        var reader = new FileReader();

        reader.onload = function (e) {
            $('.imagePrev img').attr('src', e.target.result);
        };

        reader.readAsDataURL(input.files[0]);
    }
</script>