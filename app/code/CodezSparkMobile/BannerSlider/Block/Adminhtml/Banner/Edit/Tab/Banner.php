<?php

namespace CodezSparkMobile\BannerSlider\Block\Adminhtml\Banner\Edit\Tab;

/**
 * Class Banner
 * @package Mageplaza\BannerSlider\Block\Adminhtml\Banner\Edit\Tab
 */
class Banner extends \Mageplaza\BannerSlider\Block\Adminhtml\Banner\Edit\Tab\Banner
{
    protected function _prepareForm()
    {
        $form = parent::_prepareForm()->getForm();

        $fieldset = $form->getElement('base_fieldset');

        $fieldset->addField('carousel_image_type', 'select', [
            'name' => 'carousel_image_type',
            'label' => __('Carousel Image Type'),
            'title' => __('Carousel Image Type'),
            'required' => false,
            'values' => [
                ['value' => 'product', 'label' => __('Product')],
                ['value' => 'category', 'label' => __('Category')],
                ['value' => 'none', 'label' => __('Not Clickable')],
            ],
        ]);

        $fieldset->addField('entity_id', 'text', [
            'name' => 'entity_id',
            'label' => __('Product/Category ID'),
            'title' => __('Product/Category ID'),
            'required' => false,
            'class' => 'validate-number'
        ]);

        $model = $this->_coreRegistry->registry('mpbannerslider_banner');
        $form->addValues($model->getData());

        $this->setForm($form);
        return $this;
    }

}
