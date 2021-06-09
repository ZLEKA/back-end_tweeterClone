<?php

const VIEW_NOT_FOUND        ='View does not exists';
class View
{
    /**
     * Render the selected view.
     * @param string $view
     * @param array $data
     * @throws Exception
     */
    public function __construct(string $view, array $data=[]){
        if(!$this->exists($view))
            throw new Exception(VIEW_NOT_FOUND);

        $this->render($view,$data);
    }


    /**
     * Check if the view exists
     * @param $view
     * @return bool
     */
    private function exists($view):bool{
        return in_array($view,scandir(Env::app('VIEWS').'Pages'));
    }

    /**
     * Create the scoped variables and render the View.
     * @param $view
     * @param array $data
     * @return mixed
     */
    private function render($view, $data=[]){
        foreach ($data as $key=>$var){
            ${$key} = $var;
        }
        return require (Env::app('VIEWS').'Pages/'.$view);
    }

}
