<?php


trait Events_Controllers_Trait 
{

    public function getCategory($model,$categoryId)
    {
        if (is_numeric($categoryId))
        {
            return $model->getEntityManager()->getRepository('ZC\Entity\Category')
                                ->find($categoryId);
        }
        else
        //we define a category for all categories    
        {
            $categoryAll = new stdClass();
            $categoryAll->name = 'all';
            $categoryAll->categories = NULL;
            return $categoryAll;
        }
    }
     
    
   
}











