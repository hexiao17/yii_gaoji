<?php 
namespace frontend\components;

use yii\base\Widget;
/**
 * 标签云小部件
 * @author hhs
 *
 */
class TagsCloudWidget extends Widget{
    public $tags;
    
    public function init()
    {
        parent::init();
    }
    
    public function run()
    {
        $tagString='';
        $fontStyle=array("6"=>"danger",
            "5"=>"info",
            "4"=>"warning",
            "3"=>"primary",
            "2"=>"success",
        );
        
        foreach ($this->tags as $tag=>$weight)
        {
            $url = \Yii::$app->urlManager->createUrl(['post/index','PostFrontSearch[tags]'=>$tag]);
            $tagString.='<a href="'.$url.'">'.
                ' <h'.$weight.' style="display:inline-block;"><span class="label label-'
                    .$fontStyle[$weight].'">'.$tag.'</span></h'.$weight.'></a>';
        }
       // sleep(3);
        return $tagString;
        
    }
    
    
}
