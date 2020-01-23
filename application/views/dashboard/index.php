<div class="container-fluid">
    <div class="card mb-4 box-shadow">
        <div class="card-header">
            <h4 class="my-0 font-weight-normal">Tasks</h4>
        </div>
        <div class="card-body">
            <div class="row">
            <?php
                \App\libraries\RemoteUserList::getAllUsersTasks();
            ?>
            </div>
        </div>
    </div>
</div>