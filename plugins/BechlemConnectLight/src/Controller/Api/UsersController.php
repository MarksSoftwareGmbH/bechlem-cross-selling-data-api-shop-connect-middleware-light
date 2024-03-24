<?php
declare(strict_types=1);

/* 
 * MIT License
 *
 * Copyright (c) 2018-present, Marks Software GmbH (https://www.marks-software.de/)
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */
namespace BechlemConnectLight\Controller\Api;

use Cake\Event\Event;
use Cake\Event\EventInterface;
use Cake\Http\Exception\UnauthorizedException;
use Cake\Utility\Security;
use Cake\View\JsonView;
use Cake\View\XmlView;
use Firebase\JWT\JWT;

/**
 * Class UsersController
 *
 * @package Api\Controller
 */
class UsersController extends AppController
{

    /**
     * Pagination
     *
     * @var array
     */
    public array $paginate = [
        'limit' => 25,
        'maxLimit' => 50,
        'sortableFields' => [
            'id',
            'role_id',
            'locale_id',
            'foreign_key',
            'username',
            'name',
            'email',
            'status',
            'activation_date',
            'last_login',
            'created',
            'modified',
        ],
        'order' => ['created' => 'DESC']
    ];

    /**
     * Called before the controller action. You can use this method to configure and customize components
     * or perform logic that needs to happen before each controller action.
     *
     * @param \Cake\Event\EventInterface $event An Event instance
     * @return \Cake\Http\Response|null|void
     * @link https://book.cakephp.org/4/en/controllers.html#request-life-cycle-callbacks
     */
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);

        $this->Authentication->allowUnauthenticated(['token']);
        $this->FormProtection->setConfig('unlockedActions', ['token']);
    }

    /**
     * Get the View classes this controller can perform content negotiation with.
     *
     * Each view class must implement the `getContentType()` hook method
     * to participate in negotiation.
     *
     * @see \Cake\Http\ContentTypeNegotiation
     * @return list<string>
     */
    public function viewClasses(): array
    {
        return [JsonView::class, XmlView::class];
    }

    /**
     * JWT token method
     *
     * @throws UnauthorizedException
     */
    public function token()
    {
        $result = $this->Authentication->getResult();
        if ($result->isValid()) {
            $user = $result->getData();
            $expires = time() + 10800;
            $payload = [
                'sub' => $user->id,
                // 'sub' => $user->username,
                'exp' => $expires,
            ];

            $token = JWT::encode($payload, Security::getSalt(), 'HS256');

            $json = [
                'success' => true,
                'data' => [
                    'token' => $token,
                    'expires' => $expires,
                ],
            ];
        } else {
            $this->response = $this->response->withStatus(401);
            $json = [];
        }

        $this->set(compact('json'));
        $this->viewBuilder()->setOption('serialize', 'json');
    }

    /**
     * Index method
     *
     * @return mixed
     */
    public function index()
    {
        $this->Crud->on('beforePaginate', function (Event $event) {
            if (
                ($this->getRequest()->getQuery('fields') !== null) &&
                !empty($this->getRequest()->getQuery('fields'))
            ) {
                $select = explode(',', $this->getRequest()->getQuery('fields'));
                $this->paginate($event->getSubject()->query->select($select));
            }
        });

        return $this->Crud->execute();
    }

    /**
     * View method
     *
     * @param int|null $id
     * @return mixed
     */
    public function view(int $id = null)
    {
        $this->Crud->on('beforeFind', function (Event $event) {
            if (
                ($this->getRequest()->getQuery('fields') !== null) &&
                !empty($this->getRequest()->getQuery('fields'))
            ) {
                $select = explode(',', $this->getRequest()->getQuery('fields'));
                $event->getSubject()->query->select($select);
            }
            if (
                ($this->getRequest()->getQuery('contain') !== null) &&
                ($this->getRequest()->getQuery('contain') == 1)
            ) {
                $event->getSubject()->query->contain(['Roles', 'Locales']);
            }
        });

        return $this->Crud->execute();
    }
}
