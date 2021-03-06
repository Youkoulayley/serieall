<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use Dingo\Api\Http\Response;
use Dingo\Api\Routing\Helpers;
use Illuminate\Routing\Controller;
use Marcelgwerder\ApiHandler\Facades\ApiHandler;
use App\Http\Transformers\UsersListTransformer;
use Illuminate\Support\Facades\Input;


/**
 * Class UsersListController
 * @package App\Http\Controllers\Api\V1
 */
class UsersListController extends Controller
{
    use Helpers;
    protected $users;

    /**
     * UsersListController constructor.
     * @param User $users
     */
    public function __construct(User $users){
        $this->users = $users;
    }

    /**
     * @return \Dingo\Api\Http\Response
     */
    public function index() : Response
    {
        $users = $this->users
            ::select('id', 'username')
            ->orderBy('username');

        //Limit result, if needed
        if (Input::has('_limit')){
            $limit = intval(Input::get('_limit'));
            if($limit > 0){
                $users = $users->limit($limit);
            }
        }

        $users = ApiHandler::parseMultiple($users, ['username'])->getResult();

        return $this->response->collection($users, new UsersListTransformer());
    }
}
