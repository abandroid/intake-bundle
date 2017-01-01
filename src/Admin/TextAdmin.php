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

class TextAdmin extends BaseAdmin
{
    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('General')
                ->add('title')
                ->add('content', 'textarea', ['attr' => ['rows' => 5, 'cols' => 150]])
            ->end()
        ;
    }
}
