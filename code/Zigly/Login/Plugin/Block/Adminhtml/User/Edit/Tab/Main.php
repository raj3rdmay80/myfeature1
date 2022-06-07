<?php

namespace Zigly\Login\Plugin\Block\Adminhtml\User\Edit\Tab;

class Main
{
    /**
     * Get form HTML
     *
     * @return string
     */
    public function aroundGetFormHtml(
        \Magento\User\Block\User\Edit\Tab\Main $subject,
        \Closure $proceed
    )
    {
        // $form = $subject->getForm();
        // $form->getElement('firstname')->setData('label','Full Name');
        // $form->removefield('lastname');


        return $proceed();
    }
}