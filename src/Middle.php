<?php

namespace G;

interface Middle
{
    function before();
    function after($data);

}