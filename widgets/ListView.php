<?php

namespace app\widgets;

use yii\helpers\ArrayHelper;
use yii\widgets\LinkPager;

class ListView extends \yii\widgets\ListView {
    public function renderPager() {
        $pagination = $this->dataProvider->getPagination();
        if ($pagination === false || $this->dataProvider->getCount() <= 0) {
            return '';
        }
        /* @var $class LinkPager */
        $pager = $this->pager;
        $class = ArrayHelper::remove($pager, 'class', LinkPager::className());
        $pager['pagination'] = $pagination;
        $pager['view'] = $this->getView();

        return 
        '<div class="col-sm-12">'.
            '<div class="row">'.
                '<div class="col-sm-offset-3 col-xs-offset-2 col-md-offset-4 col-xl-6">'.
                $class::widget($pager).
                '</div>'.
            '</div>'.
        '</div>';
    }
}
