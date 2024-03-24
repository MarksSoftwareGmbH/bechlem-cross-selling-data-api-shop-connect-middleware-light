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
namespace BechlemConnectLight\Controller\Admin;

use BechlemConnectLight\Controller\Admin\AppController;
use BechlemConnectLight\Utility\BechlemConnectLight;
use Cake\Event\EventInterface;
use Cake\Http\CallbackStream;
use Cake\I18n\DateTime;
use Cake\Utility\Inflector;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * Products Controller
 *
 * @property \BechlemConnectLight\Model\Table\ProductsTable $Products
 */
class ProductsController extends AppController
{

    /**
     * Locale
     *
     * @var string
     */
    private string $locale;

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
            'product_type_id',
            'product_condition_id',
            'product_delivery_time_id',
            'product_manufacturer_id',
            'product_tax_class_id',
            'foreign_key',
            'employee_key',
            'manufacturer_key',
            'manufacturer_name',
            'manufacturer_sku',
            'category_key',
            'category_name',
            'sku',
            'ean',
            'name',
            'slug',
            'stock',
            'price',
            'promote_start',
            'promote_end',
            'promote',
            'promote_position',
            'promote_new_start',
            'promote_new_end',
            'promote_new',
            'promote_new_position',
            'status',
            'view_counter',
            'created',
            'modified',
            'ProductTypes.title',
            'ProductConditions.title',
            'ProductManufacturers.title',
        ],
        'order' => ['Products.modified' => 'DESC']
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

        $session = $this->getRequest()->getSession();
        $this->locale = $session->check('Locale.code')? $session->read('Locale.code'): 'de_DE';
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $query = $this->Products
            ->find('search', search: $this->getRequest()->getQueryParams())
            ->contain([
                'ProductConditions',
                'ProductDeliveryTimes',
                'ProductManufacturers',
                'ProductTaxClasses',
                'ProductTypes.ProductTypeAttributes',
                'ProductProductTypeAttributeValues.ProductTypeAttributes',
            ])
            ->orderBy(['Products.created' => 'DESC']);

        $productTypes = $this->Products->ProductTypes
            ->find('list',
                order: ['ProductTypes.alias' => 'ASC'],
                keyField: 'alias',
                valueField: 'title'
            )
            ->toArray();

        $productConditions = $this->Products->ProductConditions
            ->find('list',
                order: ['ProductConditions.alias' => 'ASC'],
                keyField: 'alias',
                valueField: 'title'
            )
            ->toArray();

        $productManufacturers = $this->Products->ProductManufacturers
            ->find('list',
                order: ['ProductManufacturers.slug' => 'ASC'],
                keyField: 'slug',
                valueField: 'name'
            )
            ->toArray();

        BechlemConnectLight::dispatchEvent('Controller.Admin.Products.beforeIndexRender', $this, [
            'Query'                 => $query,
            'ProductTypes'          => $productTypes,
            'ProductConditions'     => $productConditions,
            'ProductManufacturers'  => $productManufacturers,
        ]);

        $this->set('products', $this->paginate($query));
        $this->set(compact(
            'productTypes',
            'productConditions',
            'productManufacturers'
        ));
    }

    /**
     * View method
     *
     * @param int|null $id Product id.
     * @return \Cake\Http\Response|void
     */
    public function view(int $id = null)
    {
        $product = $this->Products->get($id, contain: [
            'ProductBrands',
            'ProductCategories',
            'ProductConditions',
            'ProductDeliveryTimes',
            'ProductManufacturers',
            'ProductTaxClasses',
            'ProductTypes.ProductTypeAttributes' => function ($q) {
                return $q
                    ->contain([
                        'ProductTypeAttributeChoices' => function ($q) {
                            return $q->orderBy(['ProductTypeAttributeChoices.value' => 'ASC']);
                        }
                    ])
                    ->orderBy(['ProductTypeAttributes.alias' => 'ASC']);
            },
            'ProductProductTypeAttributeValues.ProductTypeAttributes' => function ($q) {
                return $q->orderBy(['ProductTypeAttributes.alias' => 'ASC']);
            }
        ]);

        BechlemConnectLight::dispatchEvent('Controller.Admin.Products.beforeViewRender', $this, [
            'Product' => $product
        ]);

        $this->set('product', $product);
    }

    /**
     * Add method
     *
     * @param string|null $productTypeAlias
     *
     * @return \Cake\Http\Response|void|null
     */
    public function add(string $productTypeAlias = null)
    {
        // Get ProductType
        $productType = $this->Products->ProductTypes
            ->find('all',
                conditions: ['ProductTypes.alias' => $productTypeAlias],
                contain: [
                    'ProductTypeAttributes' => function ($q) {
                        return $q
                            ->contain([
                                'ProductTypeAttributeChoices' => function ($q) {
                                    return $q->orderBy(['ProductTypeAttributeChoices.value' => 'ASC']);
                                },
                            ])
                            ->orderBy(['ProductTypeAttributes.alias' => 'ASC']);
                    },
                ]
            )
            ->first();

        if (!$productType) {
            $this->Flash->set(
                __d(
                    'bechlem_connect_light',
                    '{productType} could not be found. Please, try again.',
                    ['productType' => Inflector::singularize($productTypeAlias)]
                ),
                ['element' => 'default', 'params' => ['class' => 'error']]
            );

            return $this->redirect($this->referer());
        }

        $product = $this->Products->newEmptyEntity();
        if ($this->getRequest()->is('post')) {
            $associated = [
                'ProductTypes',
                'ProductBrands',
                'ProductCategories',
                'ProductConditions',
                'ProductDeliveryTimes',
                'ProductManufacturers',
                'ProductTaxClasses',
                'ProductProductTypeAttributeValues',
            ];
            $product = $this->Products->patchEntity(
                $product, $this->getRequest()->getData(),
                ['associated' => $associated]
            );
            BechlemConnectLight::dispatchEvent('Controller.Admin.Products.beforeAdd', $this, [
                'ProductType' => $productType,
                'Product' => $product
            ]);
            if ($this->Products->save($product)) {
                BechlemConnectLight::dispatchEvent('Controller.Admin.Products.onAddSuccess', $this, [
                    'ProductType' => $productType,
                    'Product' => $product
                ]);
                $this->Flash->set(
                    __d('bechlem_connect_light', 'The product has been saved.'),
                    ['element' => 'default', 'params' => ['class' => 'success']]
                );

                return $this->redirect(['action' => 'index']);
            } else {
                BechlemConnectLight::dispatchEvent('Controller.Admin.Products.onAddFailure', $this, [
                    'ProductType' => $productType,
                    'Product' => $product
                ]);

                $errors = '';
                $validationErrors = $product->getErrors();
                foreach ($validationErrors as $key => $validationError) {
                    $errors .= $validationError . ' ' . $key;
                }

                $this->Flash->set(
                    __d('bechlem_connect_light', 'The product could not be saved. Please, try again. We detected following errors: {errors}', ['errors' => $errors]),
                    ['element' => 'default', 'params' => ['class' => 'error']]
                );
            }
        }

        $productBrands = $this->Products->ProductBrands
            ->find('list',
                order: ['ProductBrands.name' => 'ASC'],
                keyField: 'id',
                valueField: 'name'
            )
            ->where(['ProductBrands.status' => 1]);

        $productCategories = $this->Products->ProductCategories
            ->find('treeList',
                keyPath: 'id',
                valuePath: 'name_locale',
                spacer: '-> '
            )
            ->where(['ProductCategories.status' => 1]);

        $productConditions = $this->Products->ProductConditions
            ->find('list',
                order: ['ProductConditions.title' => 'ASC'],
                keyField: 'id',
                valueField: 'title'
            );

        $productDeliveryTimes = $this->Products->ProductDeliveryTimes
            ->find('list',
                order: ['ProductDeliveryTimes.title' => 'ASC'],
                keyField: 'id',
                valueField: 'title'
            );

        $productManufacturers = $this->Products->ProductManufacturers
            ->find('list',
                order: ['ProductManufacturers.name' => 'ASC'],
                keyField: 'id',
                valueField: 'name'
            )
            ->where(['ProductManufacturers.status' => 1]);

        $productTaxClasses = $this->Products->ProductTaxClasses
            ->find('list',
                order: ['ProductTaxClasses.title' => 'ASC'],
                keyField: 'id',
                valueField: 'title'
            );

        BechlemConnectLight::dispatchEvent('Controller.Admin.Products.beforeAddRender', $this, [
            'ProductType' => $productType,
            'Product' => $product,
            'ProductBrands' => $productBrands,
            'ProductCategories' => $productCategories,
            'ProductConditions' => $productConditions,
            'ProductDeliveryTimes' => $productDeliveryTimes,
            'ProductManufacturers' => $productManufacturers,
            'ProductTaxClasses' => $productTaxClasses,
        ]);

        $this->set(compact(
            'product',
            'productType',
            'productBrands',
            'productCategories',
            'productConditions',
            'productDeliveryTimes',
            'productManufacturers',
            'productTaxClasses'
        ));
    }

    /**
     * Edit method
     *
     * @param int|null $id
     *
     * @return \Cake\Http\Response|void|null
     */
    public function edit(int $id = null)
    {
        $product = $this->Products->get($id, contain: [
            'ProductBrands',
            'ProductCategories',
            'ProductConditions',
            'ProductDeliveryTimes',
            'ProductManufacturers',
            'ProductTaxClasses',
            'ProductTypes.ProductTypeAttributes' => function ($q) {
                return $q
                    ->contain([
                        'ProductTypeAttributeChoices' => function ($q) {
                            return $q->orderBy(['ProductTypeAttributeChoices.value' => 'ASC']);
                        }
                    ])
                    ->orderBy(['ProductTypeAttributes.alias' => 'ASC']);
            },
            'ProductProductTypeAttributeValues.ProductTypeAttributes' => function ($q) {
                return $q->orderBy(['ProductTypeAttributes.alias' => 'ASC']);
            }
        ]);

        if ($this->getRequest()->is(['patch', 'post', 'put'])) {
            $associated = [
                'ProductTypes',
                'ProductBrands',
                'ProductCategories',
                'ProductConditions',
                'ProductDeliveryTimes',
                'ProductManufacturers',
                'ProductTaxClasses',
                'ProductProductTypeAttributeValues',
            ];
            $product = $this->Products->patchEntity(
                $product,
                $this->getRequest()->getData(),
                ['associated' => $associated]
            );
            BechlemConnectLight::dispatchEvent('Controller.Admin.Products.beforeEdit', $this, [
                'Product' => $product
            ]);
            if ($this->Products->save($product)) {
                BechlemConnectLight::dispatchEvent('Controller.Admin.Products.onEditSuccess', $this, [
                    'Product' => $product
                ]);
                $this->Flash->set(
                    __d('bechlem_connect_light', 'The product has been saved.'),
                    ['element' => 'default', 'params' => ['class' => 'success']]
                );

                return $this->redirect(['action' => 'index']);
            } else {
                BechlemConnectLight::dispatchEvent('Controller.Admin.Products.onEditFailure', $this, [
                    'Product' => $product
                ]);

                $errors = '';
                $validationErrors = $product->getErrors();
                foreach ($validationErrors as $key => $validationError) {
                    $errors .= $validationError . ' ' . $key;
                }

                $this->Flash->set(
                    __d('bechlem_connect_light', 'The product could not be saved. Please, try again. We detected following errors: {errors}', ['errors' => $errors]),
                    ['element' => 'default', 'params' => ['class' => 'error']]
                );
            }
        }

        $productBrands = $this->Products->ProductBrands
            ->find('list',
                order: ['ProductBrands.name' => 'ASC'],
                keyField: 'id',
                valueField: 'name'
            )
            ->where(['ProductBrands.status' => 1]);

        $productCategories = $this->Products->ProductCategories
            ->find('treeList',
                keyPath: 'id',
                valuePath: 'name_locale',
                spacer: '-> '
            )
            ->where(['ProductCategories.status' => 1]);

        $productConditions = $this->Products->ProductConditions
            ->find('list',
                order: ['ProductConditions.title' => 'ASC'],
                keyField: 'id',
                valueField: 'title'
            );

        $productDeliveryTimes = $this->Products->ProductDeliveryTimes
            ->find('list',
                order: ['ProductDeliveryTimes.title' => 'ASC'],
                keyField: 'id',
                valueField: 'title'
            );

        $productManufacturers = $this->Products->ProductManufacturers
            ->find('list',
                order: ['ProductManufacturers.name' => 'ASC'],
                keyField: 'id',
                valueField: 'name'
            )
            ->where(['ProductManufacturers.status' => 1]);

        $productTaxClasses = $this->Products->ProductTaxClasses
            ->find('list',
                order: ['ProductTaxClasses.title' => 'ASC'],
                keyField: 'id',
                valueField: 'title'
            );

        BechlemConnectLight::dispatchEvent('Controller.Admin.Products.beforeEditRender', $this, [
            'Product' => $product,
            'ProductBrands' => $productBrands,
            'ProductCategories' => $productCategories,
            'ProductConditions' => $productConditions,
            'ProductDeliveryTimes' => $productDeliveryTimes,
            'ProductManufacturers' => $productManufacturers,
            'ProductTaxClasses' => $productTaxClasses,
        ]);

        $this->set(compact(
            'product',
            'productBrands',
            'productCategories',
            'productConditions',
            'productDeliveryTimes',
            'productManufacturers',
            'productTaxClasses'
        ));
    }

    /**
     * Copy method
     *
     * @param int|null $id
     *
     * @return \Cake\Http\Response|null
     */
    public function copy(int $id = null)
    {
        $this->getRequest()->allowMethod(['post']);

        $product = $this->Products->get($id, contain: [
            'ProductBrands',
            'ProductCategories',
            'ProductConditions',
            'ProductDeliveryTimes',
            'ProductManufacturers',
            'ProductTaxClasses',
            'ProductTypes.ProductTypeAttributes' => function ($q) {
                return $q
                    ->contain([
                        'ProductTypeAttributeChoices' => function ($q) {
                            return $q->orderBy(['ProductTypeAttributeChoices.value' => 'ASC']);
                        }
                    ])
                    ->orderBy(['ProductTypeAttributes.alias' => 'ASC']);
            },
            'ProductProductTypeAttributeValues.ProductTypeAttributes' => function ($q) {
                return $q->orderBy(['ProductTypeAttributes.alias' => 'ASC']);
            }
        ]);

        $product->setNew(true);
        $product->unset('id');
        $product->promote_start = null;
        $product->promote_end = null;
        $product->promote = 0;
        $product->promote_position = 0;
        $product->status = 0;

        foreach ($product->product_product_type_attribute_values as $attributeValue) {
            $attributeValue->setNew(true);
            $attributeValue->unset('id');
            switch ($attributeValue->product_type_attribute->alias) {
                case (
                    $attributeValue->product_type_attribute->alias == 'title' ||
                    $attributeValue->product_type_attribute->alias == 'name'
                ):
                    $attributeValue->value = $attributeValue->value . ' ' . __d('bechlem_connect_light', '(Copy)');
                    break;
                case 'slug':
                    $attributeValue->value = $attributeValue->value . '-' . __d('bechlem_connect_light', 'copy');
                    break;
            }
        }

        BechlemConnectLight::dispatchEvent('Controller.Admin.Products.beforeCopy', $this, [
            'Product' => $product
        ]);
        if ($this->Products->save($product)) {
            BechlemConnectLight::dispatchEvent('Controller.Admin.Products.onCopySuccess', $this, [
                'Product' => $product
            ]);
            $this->Flash->set(
                __d('bechlem_connect_light', 'The product has been copied.'),
                ['element' => 'default', 'params' => ['class' => 'success']]
            );
        } else {
            BechlemConnectLight::dispatchEvent('Controller.Admin.Products.onCopyFailure', $this, [
                'Product' => $product
            ]);
            $this->Flash->set(
                __d('bechlem_connect_light', 'The product could not be copied. Please, try again.'),
                ['element' => 'default', 'params' => ['class' => 'error']]
            );
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Delete method
     *
     * @param int|null $id
     *
     * @return \Cake\Http\Response|null
     */
    public function delete(int $id = null)
    {
        $this->getRequest()->allowMethod(['post', 'delete']);
        $product = $this->Products->get($id);
        BechlemConnectLight::dispatchEvent('Controller.Admin.Products.beforeDelete', $this, [
            'Product' => $product
        ]);
        if ($this->Products->delete($product)) {
            BechlemConnectLight::dispatchEvent('Controller.Admin.Products.onDeleteSuccess', $this, [
                'Product' => $product
            ]);
            $this->Flash->set(
                __d('bechlem_connect_light', 'The product has been deleted.'),
                ['element' => 'default', 'params' => ['class' => 'success']]
            );
        } else {
            BechlemConnectLight::dispatchEvent('Controller.Admin.Products.onDeleteFailure', $this, [
                'Product' => $product
            ]);
            $this->Flash->set(
                __d('bechlem_connect_light', 'The product could not be deleted. Please, try again.'),
                ['element' => 'default', 'params' => ['class' => 'error']]
            );
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Export xlsx method
     *
     * @return \Cake\Http\Response|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function exportXlsx()
    {
        $products = $this->Products->find('all');
        $header = $this->Products->tableColumns;

        $productsArray = [];
        foreach($products as $product) {
            $productArray = [];
            $productArray['id'] = $product->id;
            $productArray['product_type_id'] = $product->product_type_id;
            $productArray['product_condition_id'] = $product->product_condition_id;
            $productArray['product_delivery_time_id'] = $product->product_delivery_time_id;
            $productArray['product_manufacturer_id'] = $product->product_manufacturer_id;
            $productArray['product_tax_class_id'] = $product->product_tax_class_id;
            $productArray['foreign_key'] = $product->foreign_key;
            $productArray['employee_key'] = $product->employee_key;
            $productArray['manufacturer_key'] = $product->manufacturer_key;
            $productArray['manufacturer_name'] = $product->manufacturer_name;
            $productArray['manufacturer_sku'] = $product->manufacturer_sku;
            $productArray['category_key'] = $product->category_key;
            $productArray['category_name'] = $product->category_name;
            $productArray['sku'] = $product->sku;
            $productArray['ean'] = $product->ean;
            $productArray['name'] = $product->name;
            $productArray['slug'] = $product->slug;
            $productArray['stock'] = $product->stock;
            $productArray['price'] = $product->price;
            $productArray['promote_start'] = empty($product->promote_start)? NULL: $product->promote_start->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $productArray['promote_end'] = empty($product->promote_end)? NULL: $product->promote_end->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $productArray['promote'] = ($product->promote == 1)? 1: 0;
            $productArray['promote_position'] = $product->promote_position;
            $productArray['promote_new_start'] = empty($product->promote_new_start)? NULL: $product->promote_new_start->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $productArray['promote_new_end'] = empty($product->promote_new_end)? NULL: $product->promote_new_end->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $productArray['promote_new'] = ($product->promote_new == 1)? 1: 0;
            $productArray['promote_new_position'] = $product->promote_new_position;
            $productArray['status'] = ($product->status == 1)? 1: 0;
            $productArray['view_counter'] = $product->view_counter;
            $productArray['created'] = empty($product->created)? NULL: $product->created->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $productArray['modified'] = empty($product->modified)? NULL: $product->modified->i18nFormat('yyyy-MM-dd HH:mm:ss');
            
            $productsArray[] = $productArray;
        }
        $products = $productsArray;

        $objSpreadsheet = new Spreadsheet();
        $objSpreadsheet->setActiveSheetIndex(0);

        $rowCount = 1;
        $colCount = 1;
        foreach ($header as $headerAlias) {
            $col = 'A';
            switch ($colCount) {
                case 2: $col = 'B'; break;
                case 3: $col = 'C'; break;
                case 4: $col = 'D'; break;
                case 5: $col = 'E'; break;
                case 6: $col = 'F'; break;
                case 7: $col = 'G'; break;
                case 8: $col = 'H'; break;
                case 9: $col = 'I'; break;
                case 10: $col = 'J'; break;
                case 11: $col = 'K'; break;
                case 12: $col = 'L'; break;
                case 13: $col = 'M'; break;
                case 14: $col = 'N'; break;
                case 15: $col = 'O'; break;
                case 16: $col = 'P'; break;
                case 17: $col = 'Q'; break;
                case 18: $col = 'R'; break;
                case 19: $col = 'S'; break;
                case 20: $col = 'T'; break;
                case 21: $col = 'U'; break;
                case 22: $col = 'V'; break;
                case 23: $col = 'W'; break;
                case 24: $col = 'X'; break;
                case 25: $col = 'Y'; break;
                case 26: $col = 'Z'; break;
                case 27: $col = 'AA'; break;
                case 28: $col = 'AB'; break;
                case 29: $col = 'AC'; break;
                case 30: $col = 'AD'; break;
                case 31: $col = 'AE'; break;
                case 32: $col = 'AF'; break;
            }

            $objSpreadsheet->getActiveSheet()->setCellValue($col . $rowCount, $headerAlias);
            $colCount++;
        }

        $rowCount = 1;
        foreach ($products as $dataEntity) {
            $rowCount++;

            $colCount = 1;
            foreach ($dataEntity as $dataProperty) {
                $col = 'A';
                switch ($colCount) {
                    case 2: $col = 'B'; break;
                    case 3: $col = 'C'; break;
                    case 4: $col = 'D'; break;
                    case 5: $col = 'E'; break;
                    case 6: $col = 'F'; break;
                    case 7: $col = 'G'; break;
                    case 8: $col = 'H'; break;
                    case 9: $col = 'I'; break;
                    case 10: $col = 'J'; break;
                    case 11: $col = 'K'; break;
                    case 12: $col = 'L'; break;
                    case 13: $col = 'M'; break;
                    case 14: $col = 'N'; break;
                    case 15: $col = 'O'; break;
                    case 16: $col = 'P'; break;
                    case 17: $col = 'Q'; break;
                    case 18: $col = 'R'; break;
                    case 19: $col = 'S'; break;
                    case 20: $col = 'T'; break;
                    case 21: $col = 'U'; break;
                    case 22: $col = 'V'; break;
                    case 23: $col = 'W'; break;
                    case 24: $col = 'X'; break;
                    case 25: $col = 'Y'; break;
                    case 26: $col = 'Z'; break;
                    case 27: $col = 'AA'; break;
                    case 28: $col = 'AB'; break;
                    case 29: $col = 'AC'; break;
                    case 30: $col = 'AD'; break;
                    case 31: $col = 'AE'; break;
                    case 32: $col = 'AF'; break;
                }

                $objSpreadsheet->getActiveSheet()->setCellValue($col . $rowCount, $dataProperty);
                $colCount++;
            }
        }

        foreach (range('A', $objSpreadsheet->getActiveSheet()->getHighestDataColumn()) as $col) {
            $objSpreadsheet
                ->getActiveSheet()
                ->getColumnDimension($col)
                ->setAutoSize(true);
        }
        $objSpreadsheetWriter = IOFactory::createWriter($objSpreadsheet, 'Xlsx');
        $stream = new CallbackStream(function () use ($objSpreadsheetWriter) {
            $objSpreadsheetWriter->save('php://output');
        });

        return $this->response
            ->withType('xlsx')
            ->withHeader('Content-Disposition', 'attachment;filename="' . strtolower($this->defaultTable) . '.' . 'xlsx"')
            ->withBody($stream);
    }

    /**
     * Export csv method
     *
     * @return \Cake\Http\Response|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function exportCsv()
    {
        $products = $this->Products->find('all');
        $delimiter = ';';
        $enclosure = '"';
        $header = $this->Products->tableColumns;
        $extract = [
            'id',
            'product_type_id',
            'product_condition_id',
            'product_delivery_time_id',
            'product_manufacturer_id',
            'product_tax_class_id',
            'foreign_key',
            'employee_key',
            'manufacturer_key',
            'manufacturer_name',
            'manufacturer_sku',
            'category_key',
            'category_name',
            'sku',
            'ean',
            'name',
            'slug',
            'stock',
            'price',
            function ($row) {
                return empty($row['promote_start'])? NULL: $row['promote_start']->i18nFormat('yyyy-MM-dd HH:mm:ss');
            },
            function ($row) {
                return empty($row['promote_end'])? NULL: $row['promote_end']->i18nFormat('yyyy-MM-dd HH:mm:ss');
            },
            function ($row) {
                return ($row['promote'] == 1)? 1: 0;
            },
            'promote_position',
            function ($row) {
                return empty($row['promote_new_start'])? NULL: $row['promote_new_start']->i18nFormat('yyyy-MM-dd HH:mm:ss');
            },
            function ($row) {
                return empty($row['promote_new_end'])? NULL: $row['promote_new_end']->i18nFormat('yyyy-MM-dd HH:mm:ss');
            },
            function ($row) {
                return ($row['promote_new'] == 1)? 1: 0;
            },
            'promote_new_position',
            function ($row) {
                return ($row['status'] == 1)? 1: 0;
            },
            'view_counter',
            function ($row) {
                return empty($row['created'])? NULL: $row['created']->i18nFormat('yyyy-MM-dd HH:mm:ss');
            },
            function ($row) {
                return empty($row['modified'])? NULL: $row['modified']->i18nFormat('yyyy-MM-dd HH:mm:ss');
            },
        ];

        $this->setResponse($this->getResponse()->withDownload(strtolower($this->defaultTable) . '.' . 'csv'));
        $this->set(compact('products'));
        $this
            ->viewBuilder()
            ->setClassName('CsvView.Csv')
            ->setOptions([
                'serialize' => 'products',
                'delimiter' => $delimiter,
                'enclosure' => $enclosure,
                'header'    => $header,
                'extract'   => $extract,
            ]);
    }

    /**
     * Export xml method
     *
     * @return \Cake\Http\Response|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function exportXml()
    {
        $products = $this->Products->find('all');

        $productsArray = [];
        foreach($products as $product) {
            $productArray = [];
            $productArray['id'] = $product->id;
            $productArray['product_type_id'] = $product->product_type_id;
            $productArray['product_condition_id'] = $product->product_condition_id;
            $productArray['product_delivery_time_id'] = $product->product_delivery_time_id;
            $productArray['product_manufacturer_id'] = $product->product_manufacturer_id;
            $productArray['product_tax_class_id'] = $product->product_tax_class_id;
            $productArray['foreign_key'] = $product->foreign_key;
            $productArray['employee_key'] = $product->employee_key;
            $productArray['manufacturer_key'] = $product->manufacturer_key;
            $productArray['manufacturer_name'] = $product->manufacturer_name;
            $productArray['manufacturer_sku'] = $product->manufacturer_sku;
            $productArray['category_key'] = $product->category_key;
            $productArray['category_name'] = $product->category_name;
            $productArray['sku'] = $product->sku;
            $productArray['ean'] = $product->ean;
            $productArray['name'] = $product->name;
            $productArray['slug'] = $product->slug;
            $productArray['stock'] = $product->stock;
            $productArray['price'] = $product->price;
            $productArray['promote_start'] = empty($product->promote_start)? NULL: $product->promote_start->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $productArray['promote_end'] = empty($product->promote_end)? NULL: $product->promote_end->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $productArray['promote'] = ($product->promote == 1)? 1: 0;
            $productArray['promote_position'] = $product->promote_position;
            $productArray['promote_new_start'] = empty($product->promote_new_start)? NULL: $product->promote_new_start->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $productArray['promote_new_end'] = empty($product->promote_new_end)? NULL: $product->promote_new_end->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $productArray['promote_new'] = ($product->promote_new == 1)? 1: 0;
            $productArray['promote_new_position'] = $product->promote_new_position;
            $productArray['status'] = ($product->status == 1)? 1: 0;
            $productArray['view_counter'] = $product->view_counter;
            $productArray['created'] = empty($product->created)? NULL: $product->created->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $productArray['modified'] = empty($product->modified)? NULL: $product->modified->i18nFormat('yyyy-MM-dd HH:mm:ss');
            
            $productsArray[] = $productArray;
        }
        $products = ['Products' => ['Product' => $productsArray]];

        $this->setResponse($this->getResponse()->withDownload(strtolower($this->defaultTable) . '.' . 'xml'));
        $this->set(compact('products'));
        $this
            ->viewBuilder()
            ->setClassName('Xml')
            ->setOptions(['serialize' => 'products']);
    }

    /**
     * Export json method
     *
     * @return \Cake\Http\Response|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function exportJson()
    {
        $products = $this->Products->find('all');

        $productsArray = [];
        foreach($products as $product) {
            $productArray = [];
            $productArray['id'] = $product->id;
            $productArray['product_type_id'] = $product->product_type_id;
            $productArray['product_condition_id'] = $product->product_condition_id;
            $productArray['product_delivery_time_id'] = $product->product_delivery_time_id;
            $productArray['product_manufacturer_id'] = $product->product_manufacturer_id;
            $productArray['product_tax_class_id'] = $product->product_tax_class_id;
            $productArray['foreign_key'] = $product->foreign_key;
            $productArray['employee_key'] = $product->employee_key;
            $productArray['manufacturer_key'] = $product->manufacturer_key;
            $productArray['manufacturer_name'] = $product->manufacturer_name;
            $productArray['manufacturer_sku'] = $product->manufacturer_sku;
            $productArray['category_key'] = $product->category_key;
            $productArray['category_name'] = $product->category_name;
            $productArray['sku'] = $product->sku;
            $productArray['ean'] = $product->ean;
            $productArray['name'] = $product->name;
            $productArray['slug'] = $product->slug;
            $productArray['stock'] = $product->stock;
            $productArray['price'] = $product->price;
            $productArray['promote_start'] = empty($product->promote_start)? NULL: $product->promote_start->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $productArray['promote_end'] = empty($product->promote_end)? NULL: $product->promote_end->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $productArray['promote'] = ($product->promote == 1)? 1: 0;
            $productArray['promote_position'] = $product->promote_position;
            $productArray['promote_new_start'] = empty($product->promote_new_start)? NULL: $product->promote_new_start->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $productArray['promote_new_end'] = empty($product->promote_new_end)? NULL: $product->promote_new_end->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $productArray['promote_new'] = ($product->promote_new == 1)? 1: 0;
            $productArray['promote_new_position'] = $product->promote_new_position;
            $productArray['status'] = ($product->status == 1)? 1: 0;
            $productArray['view_counter'] = $product->view_counter;
            $productArray['created'] = empty($product->created)? NULL: $product->created->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $productArray['modified'] = empty($product->modified)? NULL: $product->modified->i18nFormat('yyyy-MM-dd HH:mm:ss');
            
            $productsArray[] = $productArray;
        }
        $products = ['Products' => ['Product' => $productsArray]];

        $this->setResponse($this->getResponse()->withDownload(strtolower($this->defaultTable) . '.' . 'json'));
        $this->set(compact('products'));
        $this
            ->viewBuilder()
            ->setClassName('Json')
            ->setOptions(['serialize' => 'products']);
    }
}
