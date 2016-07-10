<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\Bundle\IntakeBundle\Admin;

use AdminBundle\Admin\BaseAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;

class LevelAdmin extends BaseAdmin
{
    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('General')
                ->add('name')
                ->add('doubtErrorCount', null, array('label' => 'Errors for doubt'))
                ->add('failureErrorCount', null, array('label' => 'Errors for failure'))
                ->add('texts', 'sonata_type_collection', array(
                    'type_options' => array(
                        'delete' => false,
                        'delete_options' => array(
                            'type' => 'hidden',
                            'type_options' => array(
                                'mapped' => false,
                                'required' => false,
                            ),
                        ),
                    ),
                    'label' => 'Texts',
                    'required' => false,
                    'by_reference' => false,
                ), array(
                    'edit' => 'inline',
                    'inline' => 'table',
                    'sortable' => 'position',
                ))
            ->add('extras', 'sonata_type_collection', array(
                'type_options' => array(
                    'delete' => false,
                    'delete_options' => array(
                        'type' => 'hidden',
                        'type_options' => array(
                            'mapped' => false,
                            'required' => false,
                        ),
                    ),
                ),
                'label' => 'Extras',
                'required' => false,
                'by_reference' => false,
            ), array(
                'edit' => 'inline',
                'inline' => 'table',
                'sortable' => 'position',
            ))
            ->end()
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name')
            ->add('intake')
        ;
    }
}
