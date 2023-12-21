<?php
declare(strict_types=1);

namespace App\Controller\Api;
use App\Controller\AppController;
use Cake\Http\Response;
use Cake\Event\EventInterface;
use Cake\Utility\Security;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class UsersController extends AppController
{
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        
        $this->Authentication->addUnauthenticatedActions(['login', 'add']);
    }

    public function login()
    {
        $result = $this->Authentication->getResult();

        if ($result->isValid()) {
            $privateKey = file_get_contents(CONFIG . '/jwt.key');
            $publicKey = file_get_contents(CONFIG . '/jwt.pem');
            $user = $result->getData();

            $payload = [
                'iss' => 'miles',
                'sub' => $user->id,
                'user' => $user,
                'exp' => strtotime("+1 week"),
            ];

            $status = 200;
            $json = [
                'token' => JWT::encode($payload, $privateKey, 'RS256'),
            ];
            $decoded = JWT::decode($json['token'], new Key($publicKey, 'RS256'));
            print_r((array) $decoded);
        } else {
            $status = 401;
            $json = [];
        }

        return $this->Response->json($json, $status);
    }

    public function logout()
    {
        $result = $this->Authentication->getResult();
        if ($result && $result->isValid()) {
            $this->Authentication->logout();
            return $this->Response->write('User has been logged out.');
        }
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        /*$result = $this->Authentication->getResult();
        if ($result->isValid()) {*/
            $users = $this->paginate($this->Users);
            return $this->Response->json($users);
        //}
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $user = $this->Users->newEmptyEntity();
        if ($this->request->is('post')) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            if ($this->Users->save($user)) {
                return $this->Response->write('The user has been saved.');
            }
            return $this->Response->write('The user could not be saved. Please, try again.', 400);
        }
        return $this->Response->write('POST only.', 400);
    }
}
