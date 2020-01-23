<?php
/**
 * @since: 23/01/2020
 * @author: Sarwar Hasan
 * @version 1.0.0
 */
namespace App\libraries;

use App\models\Mtasks;
use GuzzleHttp\Client;

/*
 * @property RemoteUser[] $userList
 */
class RemoteUserList
{
    /*
     * @var RemoteUser []
     */
    private static $userList=[];
    private static $hasError=false;
    private static $isLoaded=false;

    public static function loadUsers()
    {
        if (! self::$hasError) {
            $ci = get_instance();
            $userUrl = $ci->config->item("user_url");
            $client = new Client();
            $res = $client->request(
                'GET',
                $userUrl,
                ['verify' => FCPATH.'certificate/cacert.pem']
            );
            if ($res->getStatusCode() == 200) {
                self::$isLoaded=true;
                $responseData = @json_decode($res->getBody());
                if (! empty($responseData->data)
                     && is_array($responseData->data)) {
                    foreach ($responseData->data as $userGeneralObject) {
                        self::$userList[]
                        = RemoteUser::getFromGeneralObject($userGeneralObject);
                    }
                } else {
                    self::$hasError = true;
                }
            } else {
                self::$hasError = true;
            }
        }
        return !self::$hasError;
    }
    /**
     * It returns the remote user array
     * @return RemoteUser []
     */
    public static function getUserList()
    {

        return self::$userList;
    }

    public static function hasLoadError()
    {
        return self::$hasError;
    }

    /**
     * @param $id
     *
     * @return RemoteUser|null
     */
    public static function getUserBy($id)
    {
        if (!self::$hasError && self::$isLoaded) {
            foreach (self::$userList as $remoteUser) {
                if ($remoteUser->id==$id) {
                    return $remoteUser;
                }
            }
        }
        return null;
    }

    /**
     * @param $id
     * @param string $email, its optional if you pass the email then email will be check
     *
     * @return bool
     */
    public static function isExistsUser($id, $email = null)
    {
        $userData=self::getUserBy($id);
        if (!empty($userData) && ($email===null || ($email==$userData->email))) {
            return true;
        }
        return false;
    }

    public static function getUserTaskUL($userId, $parent_id = 0, &$completed = 0, &$total = 0)
    {
        $tasks=Mtasks::findAllBy("user_id", $userId, ["parent_id"=>$parent_id]);
        if (count($tasks)>0) {
            ob_start();
            ?>

            <ul id="task<?php echo $parent_id; ?>">
                <?php foreach ($tasks as $task) {
                    $chCompleted=0;
                    $chTotal=0;
                    $childHtml= self::getUserTaskUL($task->user_id, $task->id, $chCompleted, $chTotal);
                    $chTotal=$chTotal>0?$chTotal:$task->points;
                    $total+=$chTotal;
                    $completed+=$chCompleted>0?$chCompleted:(((int)$task->is_done===0?0:$task->points));
                    ?>
                <li> (<?php echo (int)$task->is_done===0?"X":"V"; ?>)
                        <?php echo $task->title." (".$chTotal.")";
                        echo $childHtml;
                        ?>

                    </li>
                    <?php
                } ?>
            </ul>

            <?php
            return ob_get_clean();
        }
    }
    /**
     * it will display the user tree view
     * @param RemoteUser $user
     */
    public static function getUserTaskHtml($user)
    {
        $mtg=new Mtasks();
        $mtg->user_id($user->id);
        $total=$mtg->countAll();
        if ($total==0) {
            return;
        }
        $totalTasks=0;
        $totalCompleted=0;

        $htmlBody= self::getUserTaskUL($user->id, 0, $totalCompleted, $totalTasks);
        ?>
        <div class="col-4">
            <div class="card mb-3">
                <div class="card-header"><?php echo $user->first_name." ".$user->last_name." (".$totalCompleted."/".$totalTasks.")"; ?></div>
                <div class="card-body">
                   <?php echo $htmlBody; ?>
                </div>
            </div>
        </div>
        <?php
    }

    public static function getAllUsersTasks()
    {
        self::loadUsers();
        if (!self::hasLoadError()) {
            $users = self::getUserList();
            foreach ($users as &$user) {
                self::getUserTaskHtml($user);
            }
        } else {
        }
    }
}
