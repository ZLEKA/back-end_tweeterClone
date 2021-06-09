<?php

class Tweet extends Model
{
    public function comments() {
        return $this->hasMany('Comment');
    }

}